<?php
defined( 'ABSPATH' ) || exit;

class LGPD_CC_Public {

	private array $settings;

	public function __construct() {
		$saved          = get_option( LGPD_CC_OPTION_KEY, [] );
		$defaults       = LGPD_CC_Plugin::default_settings();
		$this->settings = wp_parse_args( $saved, $defaults );

		// Garante que subitens do i18n nunca fiquem vazios após updates do plugin
		foreach ( [ 'btn_accept_all', 'btn_reject_all', 'btn_customize', 'btn_save_prefs', 'btn_accept_selected', 'withdraw_text', 'title', 'description', 'modal_title' ] as $key ) {
			if ( empty( $this->settings[ $key ] ) ) {
				$this->settings[ $key ] = $defaults[ $key ];
			}
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_footer',          [ $this, 'render_banner' ] );
		add_action( 'wp_head',            [ $this, 'output_inline_css' ], 99 );

		add_shortcode( 'lgpd_withdraw_btn', [ $this, 'shortcode_withdraw_btn' ] );
		add_shortcode( 'lgpd_policy_link',  [ $this, 'shortcode_policy_link' ] );
	}

	public function enqueue_assets(): void {
		wp_enqueue_style(
			'lgpd-cc-public',
			LGPD_CC_PLUGIN_URL . 'public/css/lgpd-cookie-consent.css',
			[],
			LGPD_CC_VERSION
		);

		wp_enqueue_script(
			'lgpd-cc-public',
			LGPD_CC_PLUGIN_URL . 'public/js/lgpd-cookie-consent.js',
			[],
			LGPD_CC_VERSION,
			true
		);

		$policy_url = $this->settings['policy_page_id']
			? get_permalink( $this->settings['policy_page_id'] )
			: home_url( '/politica-de-privacidade' );

		wp_localize_script( 'lgpd-cc-public', 'lgpdCC', [
			'isPro'          => LGPD_CC_Pro::is_active(),
			'restUrl'        => rest_url( 'lgpd-cc/v1/' ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'cookieLifetime' => (int) $this->settings['cookie_lifetime'],
			'blockOnScroll'  => (bool) $this->settings['block_on_scroll'],
			'forceModal'     => (bool) $this->settings['force_modal'],
			'policyUrl'      => esc_url( $policy_url ),
			'position'       => $this->settings['position'],
			'animation'      => $this->settings['animation'],
			'categories'     => $this->get_public_categories(),
			'autoBlock'      => (bool) $this->settings['auto_block_scripts'],
			'integrations'   => $this->get_public_integrations(),

			// Avatar
			'avatar' => [
				'size'   => absint( $this->settings['avatar_size'] ),
				'corner' => sanitize_key( $this->settings['avatar_corner'] ),
				'icon'   => sanitize_key( $this->settings['avatar_icon'] ),
				'pulse'  => (bool) $this->settings['avatar_pulse'],
				'label'  => esc_html( $this->settings['avatar_label'] ),
			],

			// Textos
			'title'       => esc_html( $this->settings['title'] ),
			'description' => wp_kses( str_replace( '{policy_url}', esc_url( $policy_url ), $this->settings['description'] ), [
				'a'      => [ 'href' => [], 'target' => [], 'rel' => [] ],
				'strong' => [],
				'em'     => [],
				'br'     => [],
			] ),
			'modalTitle'  => esc_html( $this->settings['modal_title'] ),
			'showLogo'    => (bool) $this->settings['show_logo'],
			'logoUrl'     => esc_url( $this->settings['logo_url'] ),
			'showReject'  => (bool) $this->settings['show_reject_btn'],
			'showCustom'  => (bool) $this->settings['show_customize_btn'],

			'i18n' => [
				'accept_all'      => esc_html( $this->settings['btn_accept_all'] ),
				'accept_selected' => esc_html( $this->settings['btn_accept_selected'] ),
				'reject_all'      => esc_html( $this->settings['btn_reject_all'] ),
				'customize'       => esc_html( $this->settings['btn_customize'] ),
				'save_prefs'      => esc_html( $this->settings['btn_save_prefs'] ),
				'withdraw'        => esc_html( $this->settings['withdraw_text'] ),
			],
		] );
	}

	private function get_public_categories(): array {
		$cats   = [];
		$is_pro = LGPD_CC_Pro::is_active();

		foreach ( $this->settings['categories'] as $key => $cat ) {
			// No plano free, apenas categorias marcadas como "locked" (obrigatórias) são expostas
			if ( ! $is_pro && empty( $cat['locked'] ) ) {
				continue;
			}

			$cats[ $key ] = [
				'label'       => esc_html( $cat['label'] ),
				'description' => esc_html( $cat['description'] ),
				'enabled'     => (bool) $cat['enabled'],
				'locked'      => (bool) $cat['locked'],
				'icon'        => sanitize_key( $cat['icon'] ),
				'cookies'     => array_map( 'esc_html', $cat['cookies'] ?? [] ),
			];
		}
		return $cats;
	}

	private function get_public_integrations(): array {
		$ints    = $this->settings['integrations'];
		$scripts = [];

		if ( ! empty( $ints['gtm_id'] ) )      $scripts[] = [ 'id' => 'gtm',     'category' => 'analytics', 'gtm_id'   => esc_attr( $ints['gtm_id'] ) ];
		if ( ! empty( $ints['ga4_id'] ) )       $scripts[] = [ 'id' => 'ga4',     'category' => 'analytics', 'ga4_id'   => esc_attr( $ints['ga4_id'] ) ];
		if ( ! empty( $ints['fb_pixel_id'] ) )  $scripts[] = [ 'id' => 'fbpixel', 'category' => 'marketing', 'pixel_id' => esc_attr( $ints['fb_pixel_id'] ) ];
		if ( ! empty( $ints['hotjar_id'] ) )    $scripts[] = [ 'id' => 'hotjar',  'category' => 'analytics', 'hjid'     => esc_attr( $ints['hotjar_id'] ) ];

		foreach ( $ints['custom_scripts'] ?? [] as $cs ) {
			$scripts[] = [
				'id'       => 'custom_' . sanitize_key( $cs['name'] ),
				'category' => $cs['category'],
				'code'     => $cs['code'],
				'position' => $cs['position'],
			];
		}

		return $scripts;
	}

	public function render_banner(): void {
		// Renderizado inteiramente pelo JS
	}

	public function output_inline_css(): void {
		$s  = $this->settings;
		$pc = sanitize_hex_color( $s['primary_color']   ?? '#2563eb' );
		$sc = sanitize_hex_color( $s['secondary_color'] ?? '#7c3aed' );
		$tc = sanitize_hex_color( $s['text_color']      ?? '#1e293b' );
		$bg = sanitize_hex_color( $s['bg_color']        ?? '#ffffff' );
		$br = absint( $s['border_radius'] ?? 16 );
		$fs = absint( $s['font_size']     ?? 14 );
		$av = absint( $s['avatar_size']   ?? 56 );
		$pc_dark = $this->darken_color( $pc, 15 );

		echo "<style id=\"lgpd-cc-vars\">
:root {
  --lgpd-primary:      {$pc};
  --lgpd-primary-dark: {$pc_dark};
  --lgpd-secondary:    {$sc};
  --lgpd-text:         {$tc};
  --lgpd-bg:           {$bg};
  --lgpd-radius:       {$br}px;
  --lgpd-font-size:    {$fs}px;
  --lgpd-avatar-size:  {$av}px;
}
</style>\n";
	}

	public function shortcode_withdraw_btn( array $atts ): string {
		$atts = shortcode_atts( [ 'class' => '', 'text' => $this->settings['withdraw_text'] ], $atts );
		return '<button class="lgpd-cc-withdraw-btn ' . esc_attr( $atts['class'] ) . '" onclick="window.lgpdCC && window.lgpdCC.openPreferences()">'
			. esc_html( $atts['text'] ) . '</button>';
	}

	public function shortcode_policy_link( array $atts ): string {
		$atts = shortcode_atts( [ 'text' => 'Política de Privacidade' ], $atts );
		$url  = $this->settings['policy_page_id'] ? get_permalink( $this->settings['policy_page_id'] ) : '#';
		return '<a href="' . esc_url( $url ) . '" class="lgpd-cc-policy-link">' . esc_html( $atts['text'] ) . '</a>';
	}

	private function darken_color( string $hex, int $amount ): string {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
		}
		$r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - $amount );
		$g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - $amount );
		$b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - $amount );
		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
}
