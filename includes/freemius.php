<?php
/**
 * Freemius Bootstrap
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'lcc_fs' ) ) {

	function lcc_fs() {
		global $lcc_fs;

		if ( ! isset( $lcc_fs ) ) {
			// Carrega via Composer
			$autoloader = LGPD_CC_PLUGIN_DIR . 'vendor/autoload.php';
			$sdk        = LGPD_CC_PLUGIN_DIR . 'vendor/freemius/wordpress-sdk/start.php';

			if ( ! file_exists( $autoloader ) || ! file_exists( $sdk ) ) {
				return new class {
					// Stub silencioso enquanto o SDK não foi instalado
					public function __call( $name, $args ) { return false; }
					public static function __callStatic( $name, $args ) { return false; }
				};
			}

			require_once $autoloader;
			require_once $sdk;

			$lcc_fs = fs_dynamic_init( array(
				'id'                  => '30603',
				'slug'                => 'lgpd-cookie-consent',
				'type'                => 'plugin',
				'public_key'          => 'pk_debfe1f9e195763a03f4653263cc9',
				'is_premium'          => false,
				'premium_suffix'      => 'Mensal',
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'is_org_compliant'    => true,
				// Automatically removed in the free version. If you're not using the
				// auto-generated free version, delete this line before uploading to wp.org.
				'wp_org_gatekeeper'   => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G',
				'menu'                => array(
					'slug'    => 'lgpd-cookie-consent',
					'support' => false,
				),
				'is_live'             => true,
			) );
		}

		return $lcc_fs;
	}

	// Init Freemius.
	lcc_fs();
	// Signal that SDK was initiated.
	do_action( 'lcc_fs_loaded' );
}
