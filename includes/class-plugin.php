<?php
defined( 'ABSPATH' ) || exit;

class LGPD_CC_Plugin {

	private static $instance = null;

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
		add_action( 'init',           [ $this, 'init' ] );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'lgpd-cookie-consent',
			false,
			dirname( plugin_basename( LGPD_CC_PLUGIN_FILE ) ) . '/languages/'
		);
	}

	public function init(): void {
		new LGPD_CC_Admin();
		new LGPD_CC_Public();
		new LGPD_CC_Consent_Log();

		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	public function register_rest_routes(): void {
		register_rest_route( 'lgpd-cc/v1', '/consent', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'rest_save_consent' ],
			'permission_callback' => function( WP_REST_Request $request ) {
				$nonce = $request->get_header( 'X-WP-Nonce' );
				return $nonce && wp_verify_nonce( $nonce, 'wp_rest' );
			},
			'args'                => [
				'categories' => [
					'required'          => true,
					'sanitize_callback' => function ( $val ) {
						if ( ! is_array( $val ) ) return [];
						return array_map( 'sanitize_key', $val );
					},
				],
				'action' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
				],
			],
		] );

		register_rest_route( 'lgpd-cc/v1', '/logs', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'rest_get_logs' ],
			'permission_callback' => function() {
				return current_user_can( 'manage_options' );
			},
		] );
	}

	public function rest_save_consent( WP_REST_Request $request ): WP_REST_Response {
		$categories = $request->get_param( 'categories' );
		$action     = $request->get_param( 'action' );

		$allowed_actions = [ 'accept_all', 'accept_selected', 'reject_all', 'withdraw' ];
		if ( ! in_array( $action, $allowed_actions, true ) ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Ação inválida.' ], 400 );
		}

		$log = new LGPD_CC_Consent_Log();
		$log->save( [
			'action'     => $action,
			'categories' => $categories,
			'ip'         => $this->get_anonymized_ip(),
			'user_agent' => substr( sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' ), 0, 255 ),
			'page_url'   => sanitize_url( $request->get_header( 'referer' ) ?? '' ),
		] );

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}

	public function rest_get_logs( WP_REST_Request $request ): WP_REST_Response {
		$log  = new LGPD_CC_Consent_Log();
		$page = absint( $request->get_param( 'page' ) ?? 1 );
		$data = $log->get_logs( $page );
		return new WP_REST_Response( $data, 200 );
	}

	private function get_anonymized_ip(): string {
		// Usa REMOTE_ADDR como fonte primária para evitar IP spoofing via X-Forwarded-For.
		// Só confia no header de proxy se explicitamente configurado via filtro.
		$settings      = get_option( LGPD_CC_OPTION_KEY, [] );
		$trusted_proxy = ! empty( $settings['trusted_proxy'] );
		if ( $trusted_proxy && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = trim( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] )[0] );
		} else {
			$ip = $_SERVER['REMOTE_ADDR'] ?? '';
		}

		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			return preg_replace( '/\.\d+$/', '.0', $ip );
		}
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			return substr( $ip, 0, strrpos( $ip, ':' ) ) . ':0';
		}
		return '0.0.0.0';
	}

	public static function activate(): void {
		$defaults = self::default_settings();
		if ( ! get_option( LGPD_CC_OPTION_KEY ) ) {
			add_option( LGPD_CC_OPTION_KEY, $defaults );
		}

		// Sinaliza para redirecionar ao Dashboard após a ativação
		set_transient( 'lgpd_cc_activated', 1, 30 );

		global $wpdb;
		$table   = $wpdb->prefix . 'lgpd_consent_logs';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table} (
			id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			action      VARCHAR(50)         NOT NULL,
			categories  TEXT                NOT NULL,
			ip          VARCHAR(45)         NOT NULL DEFAULT '',
			user_agent  VARCHAR(255)        NOT NULL DEFAULT '',
			page_url    VARCHAR(500)        NOT NULL DEFAULT '',
			created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY idx_action     (action),
			KEY idx_created_at (created_at)
		) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		flush_rewrite_rules();
	}

	public static function deactivate(): void {
		flush_rewrite_rules();
	}

	public static function uninstall(): void {
		delete_option( LGPD_CC_OPTION_KEY );
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}lgpd_consent_logs" );
	}

	public static function default_settings(): array {
		return [
			// Aparência geral
			'theme'              => 'light',     // light | dark | auto
			'primary_color'      => '#2563eb',
			'secondary_color'    => '#7c3aed',   // gradiente do avatar
			'text_color'         => '#1e293b',
			'bg_color'           => '#ffffff',
			'border_radius'      => '16',
			'font_size'          => '14',
			'animation'          => 'slide',     // slide | fade | none

			// Avatar (bolinha)
			'avatar_size'        => '56',        // 44 | 52 | 60 | 68
			'avatar_corner'      => 'right',     // left | right
			'avatar_icon'        => 'smile',     // smile | shield | cookie | lock
			'avatar_pulse'       => true,
			'avatar_label'       => 'Cookies',

			// Popover / Banner
			'position'           => 'avatar',    // avatar | bottom_bar | top_bar | modal_center
			'show_logo'          => false,
			'logo_url'           => '',

			// Textos
			'title'              => 'Sua privacidade importa',
			'description'        => 'Usamos cookies para melhorar sua experiência e personalizar conteúdo. Ao clicar em "Aceitar todos", você concorda com nossa <a href="{policy_url}">Política de Privacidade</a>.',
			'btn_accept_all'     => 'Aceitar todos',
			'btn_accept_selected'=> 'Aceitar selecionados',
			'btn_reject_all'     => 'Rejeitar',
			'btn_customize'      => 'Personalizar',
			'btn_save_prefs'     => 'Salvar preferências',
			'modal_title'        => 'Central de privacidade',
			'withdraw_text'      => 'Gerenciar cookies',

			// Comportamento
			'policy_page_id'     => 0,
			'show_reject_btn'    => true,
			'show_customize_btn' => true,
			'cookie_lifetime'    => 365,
			'auto_block_scripts' => true,
			'consent_before_load'=> false,
			'force_modal'        => false,
			'block_on_scroll'    => false,

			// Categorias
			'categories'         => self::default_categories(),

			// Integrações
			'integrations'       => [
				'gtm_id'         => '',
				'ga4_id'         => '',
				'fb_pixel_id'    => '',
				'hotjar_id'      => '',
				'custom_scripts' => [],
			],

			// Logs
			'log_enabled'        => true,
			'log_retention_days' => 365,

			// Rede
			'trusted_proxy'      => false,
		];
	}

	public static function default_categories(): array {
		return [
			'necessary' => [
				'label'       => 'Necessários',
				'description' => 'Esses cookies são essenciais para o funcionamento do site e não podem ser desativados. Geralmente são definidos em resposta a ações realizadas por você, como definir preferências de privacidade, fazer login ou preencher formulários.',
				'enabled'     => true,
				'locked'      => true,
				'icon'        => 'shield',
				'cookies'     => [ 'wordpress_logged_in_*', 'wp-settings-*', 'PHPSESSID' ],
			],
			'analytics' => [
				'label'       => 'Análise e desempenho',
				'description' => 'Esses cookies nos permitem contar visitas e fontes de tráfego para medir e melhorar o desempenho do site. Todas as informações coletadas são agregadas e anônimas.',
				'enabled'     => false,
				'locked'      => false,
				'icon'        => 'chart',
				'cookies'     => [ '_ga', '_gid', '_gat', '_hjid', '_hjFirstSeen' ],
			],
			'marketing' => [
				'label'       => 'Marketing e publicidade',
				'description' => 'Esses cookies podem ser definidos por nossos parceiros de publicidade. Podem ser usados para criar um perfil dos seus interesses e mostrar anúncios relevantes em outros sites.',
				'enabled'     => false,
				'locked'      => false,
				'icon'        => 'megaphone',
				'cookies'     => [ '_fbp', '_fbc', 'fr', 'ads/ga-audiences' ],
			],
			'preferences' => [
				'label'       => 'Preferências e funcionalidade',
				'description' => 'Esses cookies permitem que o site memorize informações que alteram o comportamento ou a aparência, como seu idioma preferido ou a região em que você está.',
				'enabled'     => false,
				'locked'      => false,
				'icon'        => 'settings',
				'cookies'     => [ 'lang', 'currency', 'theme' ],
			],
		];
	}
}
