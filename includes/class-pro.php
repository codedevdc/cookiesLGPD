<?php
/**
 * Gate de features PRO via Freemius.
 * Uso: LGPD_CC_Pro::is_active()
 *      LGPD_CC_Pro::gate( 'gtm' )  → false se free, true se PRO
 */
defined( 'ABSPATH' ) || exit;

class LGPD_CC_Pro {

	// ── Features e seus rótulos para exibição de upsell ───────────────────────
	private const FEATURES = [
		'gradient'          => 'Gradiente de cores personalizado',
		'secondary_color'   => 'Cor secundária (gradiente do avatar)',
		'theme_dark'        => 'Tema escuro e automático',
		'position_advanced' => 'Posições avançadas (barra superior, modal central)',
		'avatar_icons'      => 'Ícones de avatar adicionais (smile, cookie, lock)',
		'avatar_pulse'      => 'Animação de pulso do avatar',
		'palette_presets'   => 'Presets de paleta de cores',
		'logo'              => 'Logo personalizada no banner',
		'gtm'               => 'Google Tag Manager',
		'fb_pixel'          => 'Facebook Pixel',
		'hotjar'            => 'Hotjar',
		'custom_scripts'    => 'Scripts personalizados por categoria',
		'categories_custom' => 'Categorias de cookies personalizadas',
		'logs_export'       => 'Exportação de logs em CSV',
		'logs_unlimited'    => 'Retenção ilimitada de logs',
		'multisite'         => 'Suporte a multissite WordPress',
		'white_label'       => 'White-label (remover branding)',
		'reports'           => 'Relatórios gráficos de consentimento',
	];

	// ── Link da página de upgrade ──────────────────────────────────────────────
	public static function upgrade_url(): string {
		if ( function_exists( 'lcc_fs' ) && method_exists( lcc_fs(), 'get_upgrade_url' ) ) {
			return lcc_fs()->get_upgrade_url();
		}
		return admin_url( 'admin.php?page=lgpd-cookie-consent-pricing' );
	}

	// ── Verifica se o plano PRO está ativo ────────────────────────────────────
	public static function is_active(): bool {
		// Permite forçar o PRO em ambientes de desenvolvimento via wp-config.php:
		// define( 'LGPD_CC_DEV_PRO', true );
		if ( defined( 'LGPD_CC_DEV_PRO' ) && LGPD_CC_DEV_PRO ) {
			return true;
		}

		if ( ! function_exists( 'lcc_fs' ) ) {
			return false;
		}
		$fs = lcc_fs();
		return method_exists( $fs, 'is_paying_or_trial' ) && $fs->is_paying_or_trial();
	}

	/**
	 * Gate simples: retorna true se PRO, false se Free.
	 * No admin, em modo free, exibe automaticamente o badge PRO inline.
	 */
	public static function gate( string $feature ): bool {
		if ( self::is_active() ) {
			return true;
		}
		return false;
	}

	// ── Rótulo da feature para upsell ─────────────────────────────────────────
	public static function feature_label( string $feature ): string {
		return self::FEATURES[ $feature ] ?? ucfirst( str_replace( '_', ' ', $feature ) );
	}

	// ── Badge "PRO" inline (para usar ao lado de campos bloqueados) ───────────
	public static function badge( string $feature = '' ): string {
		$label = $feature ? esc_html( self::feature_label( $feature ) ) : '';
		$url   = esc_url( self::upgrade_url() );
		return sprintf(
			'<a href="%s" class="lgpd-pro-badge" target="_blank" title="%s — disponível no plano PRO">PRO</a>',
			$url,
			$label
		);
	}

	/**
	 * Bloco de bloqueio de seção inteira.
	 * Chame dentro de um card para exibir overlay de upgrade.
	 */
	public static function lock_overlay( string $feature, string $description = '' ): string {
		$url   = esc_url( self::upgrade_url() );
		$title = esc_html( self::feature_label( $feature ) );
		$desc  = $description
			? '<p class="lgpd-pro-lock__desc">' . esc_html( $description ) . '</p>'
			: '';

		return sprintf(
			'<div class="lgpd-pro-lock">
				<div class="lgpd-pro-lock__inner">
					<span class="lgpd-pro-lock__icon">
						<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
							<path d="M7 11V7a5 5 0 0 1 10 0v4"/>
						</svg>
					</span>
					<strong class="lgpd-pro-lock__title">%s</strong>
					%s
					<a href="%s" class="lgpd-btn lgpd-btn--primary lgpd-btn--sm" target="_blank">
						Fazer upgrade para PRO
					</a>
				</div>
			</div>',
			$title,
			$desc,
			$url
		);
	}

	/**
	 * Banner de upsell contextual (para o topo de sub-menus inteiros).
	 */
	public static function upsell_banner( string $title, string $desc ): void {
		if ( self::is_active() ) return;
		$url = esc_url( self::upgrade_url() );
		printf(
			'<div class="lgpd-upsell-banner">
				<div class="lgpd-upsell-banner__text">
					<strong>%s</strong>
					<span>%s</span>
				</div>
				<a href="%s" class="lgpd-btn lgpd-btn--primary lgpd-btn--sm" target="_blank">
					Upgrade para PRO
				</a>
			</div>',
			esc_html( $title ),
			esc_html( $desc ),
			$url
		);
	}
}
