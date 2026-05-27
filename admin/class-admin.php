<?php
defined( 'ABSPATH' ) || exit;

class LGPD_CC_Admin {

	public function __construct() {
		add_action( 'admin_menu',            [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_init',            [ $this, 'register_settings' ] );
		add_action( 'admin_init',            [ $this, 'maybe_redirect_after_activation' ] );
		add_action( 'admin_post_lgpd_cc_save_settings',  [ $this, 'save_settings' ] );
		add_action( 'admin_post_lgpd_cc_export_csv',     [ $this, 'handle_export_csv' ] );
		add_action( 'admin_post_lgpd_cc_cleanup_logs',   [ $this, 'handle_cleanup_logs' ] );
		add_action( 'admin_notices',         [ $this, 'admin_notices' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( LGPD_CC_PLUGIN_FILE ), [ $this, 'plugin_action_links' ] );
	}

	public function maybe_redirect_after_activation(): void {
		if ( ! get_transient( 'lgpd_cc_activated' ) ) {
			return;
		}
		delete_transient( 'lgpd_cc_activated' );

		// Não redireciona em ativação em massa (bulk activate)
		if ( isset( $_GET['activate-multi'] ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=lgpd-cookie-consent&welcome=1' ) );
		exit;
	}

	public function register_menu(): void {
		add_menu_page(
			__( 'LGPD Cookies', 'lgpd-cookie-consent' ),
			__( 'LGPD Cookies', 'lgpd-cookie-consent' ),
			'manage_options',
			'lgpd-cookie-consent',
			[ $this, 'render_dashboard' ],
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>' ),
			80
		);

		add_submenu_page( 'lgpd-cookie-consent', __( 'Dashboard', 'lgpd-cookie-consent' ),    __( 'Dashboard', 'lgpd-cookie-consent' ),    'manage_options', 'lgpd-cookie-consent',         [ $this, 'render_dashboard' ] );
		add_submenu_page( 'lgpd-cookie-consent', __( 'Configurações', 'lgpd-cookie-consent' ), __( 'Configurações', 'lgpd-cookie-consent' ), 'manage_options', 'lgpd-cc-settings',            [ $this, 'render_settings' ] );
		add_submenu_page( 'lgpd-cookie-consent', __( 'Aparência', 'lgpd-cookie-consent' ),     __( 'Aparência', 'lgpd-cookie-consent' ),     'manage_options', 'lgpd-cc-appearance',          [ $this, 'render_appearance' ] );
		add_submenu_page( 'lgpd-cookie-consent', __( 'Categorias', 'lgpd-cookie-consent' ),    __( 'Categorias', 'lgpd-cookie-consent' ),    'manage_options', 'lgpd-cc-categories',          [ $this, 'render_categories' ] );
		add_submenu_page( 'lgpd-cookie-consent', __( 'Integrações', 'lgpd-cookie-consent' ),   __( 'Integrações', 'lgpd-cookie-consent' ),   'manage_options', 'lgpd-cc-integrations',        [ $this, 'render_integrations' ] );
		add_submenu_page( 'lgpd-cookie-consent', __( 'Logs', 'lgpd-cookie-consent' ),          __( 'Logs', 'lgpd-cookie-consent' ),          'manage_options', 'lgpd-cc-logs',                [ $this, 'render_logs' ] );
	}

	public function enqueue_assets( string $hook ): void {
		$lgpd_pages = [
			'toplevel_page_lgpd-cookie-consent',
			'lgpd-cookies_page_lgpd-cc-settings',
			'lgpd-cookies_page_lgpd-cc-appearance',
			'lgpd-cookies_page_lgpd-cc-categories',
			'lgpd-cookies_page_lgpd-cc-integrations',
			'lgpd-cookies_page_lgpd-cc-logs',
		];

		if ( ! in_array( $hook, $lgpd_pages, true ) ) return;

		wp_enqueue_style(
			'lgpd-cc-admin',
			LGPD_CC_PLUGIN_URL . 'admin/css/admin.css',
			[],
			LGPD_CC_VERSION
		);

		wp_enqueue_script(
			'lgpd-cc-admin',
			LGPD_CC_PLUGIN_URL . 'admin/js/admin.js',
			[ 'jquery', 'wp-color-picker' ],
			LGPD_CC_VERSION,
			true
		);

		wp_enqueue_style( 'wp-color-picker' );

		wp_localize_script( 'lgpd-cc-admin', 'lgpdAdmin', [
			'restUrl'   => rest_url( 'lgpd-cc/v1/' ),
			'nonce'     => wp_create_nonce( 'wp_rest' ),
			'pluginUrl' => LGPD_CC_PLUGIN_URL,
			'i18n'      => [
				'confirmCleanup' => __( 'Tem certeza que deseja limpar os logs antigos? Esta ação não pode ser desfeita.', 'lgpd-cookie-consent' ),
				'saved'          => __( 'Configurações salvas!', 'lgpd-cookie-consent' ),
				'error'          => __( 'Erro ao salvar. Tente novamente.', 'lgpd-cookie-consent' ),
			],
		] );
	}

	public function register_settings(): void {
		register_setting(
			'lgpd_cc_settings_group',
			LGPD_CC_OPTION_KEY,
			[ 'sanitize_callback' => [ $this, 'sanitize_settings' ] ]
		);
	}

	public function sanitize_settings( $input ): array {
		$defaults = LGPD_CC_Plugin::default_settings();
		$output   = $defaults;
		$pro      = LGPD_CC_Pro::is_active();

		if ( ! is_array( $input ) ) return $defaults;

		// ── Aparência geral ───────────────────────────────────────────────────
		// position: top_bar e modal_center só no PRO
		$all_positions  = [ 'avatar', 'bottom_bar', 'top_bar', 'modal_center' ];
		$free_positions = [ 'avatar', 'bottom_bar' ];
		$raw_position   = $input['position'] ?? 'avatar';
		if ( $pro ) {
			$output['position'] = in_array( $raw_position, $all_positions, true ) ? $raw_position : 'avatar';
		} else {
			$output['position'] = in_array( $raw_position, $free_positions, true ) ? $raw_position : 'avatar';
		}

		// theme: dark e auto só no PRO
		$raw_theme = $input['theme'] ?? 'light';
		$output['theme'] = ( $pro && in_array( $raw_theme, [ 'light', 'dark', 'auto' ], true ) )
			? $raw_theme
			: ( $raw_theme === 'light' ? 'light' : 'light' );

		$output['primary_color']   = sanitize_hex_color( $input['primary_color']   ?? '#2563eb' );
		// secondary_color só no PRO
		$output['secondary_color'] = $pro
			? sanitize_hex_color( $input['secondary_color'] ?? '#7c3aed' )
			: $defaults['secondary_color'];
		$output['text_color']      = sanitize_hex_color( $input['text_color'] ?? '#1e293b' );
		$output['bg_color']        = sanitize_hex_color( $input['bg_color']   ?? '#ffffff' );
		$output['border_radius']   = min( 32, absint( $input['border_radius'] ?? 16 ) );
		$output['font_size']       = min( 20, absint( $input['font_size']     ?? 14 ) );
		$output['animation']       = in_array( $input['animation'] ?? '', [ 'slide', 'fade', 'none' ], true ) ? $input['animation'] : 'slide';

		// logo: só no PRO
		$output['show_logo'] = $pro && ! empty( $input['show_logo'] );
		$output['logo_url']  = $pro ? esc_url_raw( $input['logo_url'] ?? '' ) : '';

		// ── Avatar ───────────────────────────────────────────────────────────
		$output['avatar_size']   = in_array( $input['avatar_size'] ?? '', [ '44', '52', '60', '68' ], true ) ? $input['avatar_size'] : '56';
		$output['avatar_corner'] = in_array( $input['avatar_corner'] ?? '', [ 'left', 'right' ], true ) ? $input['avatar_corner'] : 'right';
		// ícones smile, cookie, lock só no PRO; shield é free
		$all_icons  = [ 'smile', 'shield', 'cookie', 'lock' ];
		$free_icons = [ 'shield' ];
		$raw_icon   = $input['avatar_icon'] ?? 'shield';
		$output['avatar_icon'] = $pro
			? ( in_array( $raw_icon, $all_icons, true ) ? $raw_icon : 'smile' )
			: ( in_array( $raw_icon, $free_icons, true ) ? $raw_icon : 'shield' );
		// pulse só no PRO
		$output['avatar_pulse'] = $pro && ! empty( $input['avatar_pulse'] );
		$output['avatar_label'] = sanitize_text_field( $input['avatar_label'] ?? 'Cookies' );

		// ── Textos ────────────────────────────────────────────────────────────
		$output['title']               = sanitize_text_field( $input['title'] ?? '' );
		$output['description']         = wp_kses_post( $input['description'] ?? '' );
		$output['btn_accept_all']      = sanitize_text_field( $input['btn_accept_all'] ?? '' );
		$output['btn_accept_selected'] = sanitize_text_field( $input['btn_accept_selected'] ?? '' );
		$output['btn_reject_all']      = sanitize_text_field( $input['btn_reject_all'] ?? '' );
		$output['btn_customize']       = sanitize_text_field( $input['btn_customize'] ?? '' );
		$output['btn_save_prefs']      = sanitize_text_field( $input['btn_save_prefs'] ?? '' );
		$output['modal_title']         = sanitize_text_field( $input['modal_title'] ?? '' );
		$output['withdraw_text']       = sanitize_text_field( $input['withdraw_text'] ?? '' );

		// ── Comportamento ─────────────────────────────────────────────────────
		$output['policy_page_id']      = absint( $input['policy_page_id'] ?? 0 );
		$output['show_reject_btn']     = ! empty( $input['show_reject_btn'] );
		$output['show_customize_btn']  = ! empty( $input['show_customize_btn'] );
		$output['cookie_lifetime']     = absint( $input['cookie_lifetime'] ?? 365 );
		$output['auto_block_scripts']  = ! empty( $input['auto_block_scripts'] );
		$output['show_floating_btn']   = ! empty( $input['show_floating_btn'] );
		$output['floating_btn_text']   = sanitize_text_field( $input['floating_btn_text'] ?? 'Cookies' );
		$output['force_modal']         = ! empty( $input['force_modal'] );
		$output['block_on_scroll']     = ! empty( $input['block_on_scroll'] );

		// ── Categorias ────────────────────────────────────────────────────────
		if ( ! empty( $input['categories'] ) && is_array( $input['categories'] ) ) {
			$output['categories'] = [];
			foreach ( $input['categories'] as $key => $cat ) {
				$cat_key = sanitize_key( $key );
				$output['categories'][ $cat_key ] = [
					'label'       => sanitize_text_field( $cat['label'] ?? '' ),
					'description' => sanitize_textarea_field( $cat['description'] ?? '' ),
					'enabled'     => ! empty( $cat['enabled'] ),
					'locked'      => ! empty( $cat['locked'] ),
					'icon'        => sanitize_key( $cat['icon'] ?? 'settings' ),
					'cookies'     => array_map( 'sanitize_text_field', (array) ( $cat['cookies'] ?? [] ) ),
				];
			}
		}

		// ── Integrações ───────────────────────────────────────────────────────
		$output['integrations']['ga4_id'] = sanitize_text_field( $input['integrations']['ga4_id'] ?? '' );
		// GTM, FB Pixel, Hotjar só no PRO
		$output['integrations']['gtm_id']      = $pro ? sanitize_text_field( $input['integrations']['gtm_id']      ?? '' ) : '';
		$output['integrations']['fb_pixel_id'] = $pro ? sanitize_text_field( $input['integrations']['fb_pixel_id'] ?? '' ) : '';
		$output['integrations']['hotjar_id']   = $pro ? sanitize_text_field( $input['integrations']['hotjar_id']   ?? '' ) : '';

		// Custom scripts só no PRO
		$output['integrations']['custom_scripts'] = [];
		if ( $pro && ! empty( $input['integrations']['custom_scripts'] ) && is_array( $input['integrations']['custom_scripts'] ) ) {
			foreach ( $input['integrations']['custom_scripts'] as $script ) {
				if ( empty( $script['name'] ) ) continue;
				$output['integrations']['custom_scripts'][] = [
					'name'     => sanitize_text_field( $script['name'] ),
					'category' => sanitize_key( $script['category'] ?? 'analytics' ),
					'position' => in_array( $script['position'] ?? '', [ 'head', 'body', 'footer' ], true ) ? $script['position'] : 'footer',
					'code'     => wp_strip_all_tags( $script['code'] ?? '' ),
				];
			}
		}

		// ── Logs ──────────────────────────────────────────────────────────────
		$output['log_enabled']        = ! empty( $input['log_enabled'] );
		// Retenção ilimitada só no PRO; free: máximo 30 dias
		$retention = absint( $input['log_retention_days'] ?? 365 );
		$output['log_retention_days'] = $pro ? $retention : min( $retention, 30 );

		// ── Rede ──────────────────────────────────────────────────────────────
		$output['trusted_proxy'] = ! empty( $input['trusted_proxy'] );

		return $output;
	}

	public function save_settings(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Acesso negado.', 'lgpd-cookie-consent' ) );
		}

		check_admin_referer( 'lgpd_cc_save_settings' );

		$input = $_POST['lgpd_settings'] ?? [];
		// Checkboxes não enviadas = false
		$checkboxes = [ 'show_logo', 'show_reject_btn', 'show_customize_btn', 'auto_block_scripts', 'avatar_pulse', 'force_modal', 'block_on_scroll', 'log_enabled', 'trusted_proxy' ];
		foreach ( $checkboxes as $cb ) {
			if ( ! isset( $input[ $cb ] ) ) $input[ $cb ] = '';
		}

		$clean = $this->sanitize_settings( $input );
		update_option( LGPD_CC_OPTION_KEY, $clean );

		$tab = sanitize_key( $_POST['current_tab'] ?? 'general' );
		wp_redirect( add_query_arg( [ 'page' => $this->get_tab_page( $tab ), 'saved' => '1' ], admin_url( 'admin.php' ) ) );
		exit;
	}

	private function get_tab_page( string $tab ): string {
		$map = [
			'general'      => 'lgpd-cc-settings',
			'appearance'   => 'lgpd-cc-appearance',
			'categories'   => 'lgpd-cc-categories',
			'integrations' => 'lgpd-cc-integrations',
		];
		return $map[ $tab ] ?? 'lgpd-cc-settings';
	}

	public function handle_export_csv(): void {
		if ( ! current_user_can( 'manage_options' ) ) wp_die();
		check_admin_referer( 'lgpd_cc_export_csv' );
		if ( ! LGPD_CC_Pro::is_active() ) {
			wp_die( esc_html__( 'A exportação de CSV está disponível apenas no plano PRO.', 'lgpd-cookie-consent' ) );
		}
		( new LGPD_CC_Consent_Log() )->export_csv();
	}

	public function handle_cleanup_logs(): void {
		if ( ! current_user_can( 'manage_options' ) ) wp_die();
		check_admin_referer( 'lgpd_cc_cleanup_logs' );
		$deleted = ( new LGPD_CC_Consent_Log() )->cleanup_old_logs();
		wp_redirect( add_query_arg( [ 'page' => 'lgpd-cc-logs', 'cleaned' => $deleted ], admin_url( 'admin.php' ) ) );
		exit;
	}

	public function admin_notices(): void {
		$screen = get_current_screen();
		if ( ! $screen || strpos( $screen->id, 'lgpd' ) === false ) return;

		if ( isset( $_GET['welcome'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>'
				. '<strong>' . esc_html__( 'LGPD Cookie Consent ativado com sucesso!', 'lgpd-cookie-consent' ) . '</strong> '
				. esc_html__( 'Configure o plugin pelas abas acima para colocar seu banner no ar.', 'lgpd-cookie-consent' )
				. '</p></div>';
		}
		if ( isset( $_GET['saved'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Configurações salvas com sucesso!', 'lgpd-cookie-consent' ) . '</p></div>';
		}
		if ( isset( $_GET['cleaned'] ) ) {
			/* translators: %d: number of deleted logs */
			printf( '<div class="notice notice-success is-dismissible"><p>' . esc_html__( '%d registros de log removidos.', 'lgpd-cookie-consent' ) . '</p></div>', (int) $_GET['cleaned'] );
		}
	}

	public function plugin_action_links( array $links ): array {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=lgpd-cc-settings' ) ) . '">' . __( 'Configurações', 'lgpd-cookie-consent' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	private function get_settings(): array {
		return wp_parse_args( get_option( LGPD_CC_OPTION_KEY, [] ), LGPD_CC_Plugin::default_settings() );
	}

	// -------------------------------------------------------------------------
	// Render pages
	// -------------------------------------------------------------------------

	public function render_dashboard(): void {
		$settings = $this->get_settings();
		$log      = new LGPD_CC_Consent_Log();
		$stats    = $log->get_stats();
		include LGPD_CC_PLUGIN_DIR . 'templates/admin/dashboard.php';
	}

	public function render_settings(): void {
		$settings = $this->get_settings();
		$pages    = get_pages();
		include LGPD_CC_PLUGIN_DIR . 'templates/admin/settings-general.php';
	}

	public function render_appearance(): void {
		$settings = $this->get_settings();
		include LGPD_CC_PLUGIN_DIR . 'templates/admin/settings-appearance.php';
	}

	public function render_categories(): void {
		$settings = $this->get_settings();
		include LGPD_CC_PLUGIN_DIR . 'templates/admin/settings-categories.php';
	}

	public function render_integrations(): void {
		$settings = $this->get_settings();
		include LGPD_CC_PLUGIN_DIR . 'templates/admin/settings-integrations.php';
	}

	public function render_logs(): void {
		$log  = new LGPD_CC_Consent_Log();
		$page = absint( $_GET['paged'] ?? 1 );
		$filters = [
			'action'    => sanitize_key( $_GET['filter_action'] ?? '' ),
			'date_from' => sanitize_text_field( $_GET['date_from'] ?? '' ),
			'date_to'   => sanitize_text_field( $_GET['date_to'] ?? '' ),
		];
		$data     = $log->get_logs( $page, $filters );
		$stats    = $log->get_stats();
		$settings = $this->get_settings();
		include LGPD_CC_PLUGIN_DIR . 'templates/admin/logs.php';
	}
}
