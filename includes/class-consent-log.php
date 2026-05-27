<?php
defined( 'ABSPATH' ) || exit;

class LGPD_CC_Consent_Log {

	private string $table;
	private const PER_PAGE = 50;

	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'lgpd_consent_logs';
	}

	public function save( array $data ): bool {
		$settings = wp_parse_args( get_option( LGPD_CC_OPTION_KEY, [] ), LGPD_CC_Plugin::default_settings() );
		if ( empty( $settings['log_enabled'] ) ) {
			return false;
		}

		global $wpdb;
		$result = $wpdb->insert(
			$this->table,
			[
				'action'     => $data['action'],
				'categories' => wp_json_encode( $data['categories'] ),
				'ip'         => $data['ip'],
				'user_agent' => $data['user_agent'],
				'page_url'   => $data['page_url'],
				'created_at' => current_time( 'mysql', true ),
			],
			[ '%s', '%s', '%s', '%s', '%s', '%s' ]
		);

		return $result !== false;
	}

	public function get_logs( int $page = 1, array $filters = [] ): array {
		global $wpdb;

		$where  = '1=1';
		$values = [];

		if ( ! empty( $filters['action'] ) ) {
			$where   .= ' AND action = %s';
			$values[] = sanitize_key( $filters['action'] );
		}

		if ( ! empty( $filters['date_from'] ) ) {
			$where   .= ' AND created_at >= %s';
			$values[] = sanitize_text_field( $filters['date_from'] ) . ' 00:00:00';
		}

		if ( ! empty( $filters['date_to'] ) ) {
			$where   .= ' AND created_at <= %s';
			$values[] = sanitize_text_field( $filters['date_to'] ) . ' 23:59:59';
		}

		$offset = ( $page - 1 ) * self::PER_PAGE;

		if ( ! empty( $values ) ) {
			$where = $wpdb->prepare( $where, $values );
		}

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table} WHERE {$where}" );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			"SELECT * FROM {$this->table} WHERE {$where} ORDER BY created_at DESC LIMIT " . self::PER_PAGE . " OFFSET {$offset}",
			ARRAY_A
		);

		foreach ( $rows as &$row ) {
			$row['categories'] = json_decode( $row['categories'], true ) ?? [];
		}

		return [
			'items'       => $rows,
			'total'       => $total,
			'per_page'    => self::PER_PAGE,
			'total_pages' => (int) ceil( $total / self::PER_PAGE ),
			'page'        => $page,
		];
	}

	public function get_stats(): array {
		global $wpdb;

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$this->table}" );

		$by_action = $wpdb->get_results(
			"SELECT action, COUNT(*) as count FROM {$this->table} GROUP BY action ORDER BY count DESC",
			ARRAY_A
		);

		$last_30 = $wpdb->get_results(
			"SELECT DATE(created_at) as date, COUNT(*) as count
			 FROM {$this->table}
			 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
			 GROUP BY DATE(created_at)
			 ORDER BY date ASC",
			ARRAY_A
		);

		return [
			'total'     => $total,
			'by_action' => $by_action,
			'last_30'   => $last_30,
		];
	}

	public function export_csv(): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $wpdb->get_results(
			"SELECT id, action, categories, ip, page_url, created_at FROM {$this->table} ORDER BY created_at DESC LIMIT 10000",
			ARRAY_A
		);

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="lgpd-consent-logs-' . gmdate( 'Y-m-d' ) . '.csv"' );

		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, [ 'ID', 'Ação', 'Categorias', 'IP', 'Página', 'Data/Hora' ] );

		foreach ( $rows as $row ) {
			$cats = json_decode( $row['categories'], true ) ?? [];
			fputcsv( $output, [
				$row['id'],
				$row['action'],
				implode( ', ', $cats ),
				$row['ip'],
				$row['page_url'],
				$row['created_at'],
			] );
		}

		fclose( $output );
		exit;
	}

	public function cleanup_old_logs(): int {
		$settings = get_option( LGPD_CC_OPTION_KEY, [] );
		$days     = absint( $settings['log_retention_days'] ?? 365 );

		if ( $days <= 0 ) return 0;

		global $wpdb;
		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days
			)
		);

		return (int) $deleted;
	}
}
