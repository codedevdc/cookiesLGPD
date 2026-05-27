<?php
/**
 * Plugin Name:       LGPD Cookie Consent
 * Plugin URI:        https://seusite.com.br/lgpd-cookie-consent/
 * Description:       Plugin moderno e completo para conformidade com a LGPD. Gerenciamento granular de cookies, banner personalizável, avatar redondo, logs de consentimento e integrações nativas.
 * Version:           1.1.1
 * Author:            CodeDev
 * Author URI:        https://codedev.dev.br
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lgpd-cookie-consent
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'lcc_fs' ) ) {
	// Versão PRO já carregada — apenas registra o basename da versão free
	// para que o Freemius possa desativá-la automaticamente.
	lcc_fs()->set_basename( false, __FILE__ );
} else {
	/**
	 * DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE
	 * `function_exists` CALL ABOVE TO PROPERLY WORK.
	 */
	define( 'LGPD_CC_VERSION',     '1.1.1' );
	define( 'LGPD_CC_PLUGIN_FILE', __FILE__ );
	define( 'LGPD_CC_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
	define( 'LGPD_CC_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );
	define( 'LGPD_CC_OPTION_KEY',  'lgpd_cookie_settings' );

	if ( ! function_exists( 'lcc_fs' ) ) {
		// Freemius integration snippet
		require_once LGPD_CC_PLUGIN_DIR . 'includes/freemius.php';
	}

	// Classes core
	require_once LGPD_CC_PLUGIN_DIR . 'includes/class-pro.php';
	require_once LGPD_CC_PLUGIN_DIR . 'includes/class-plugin.php';
	require_once LGPD_CC_PLUGIN_DIR . 'includes/class-consent-log.php';
	require_once LGPD_CC_PLUGIN_DIR . 'admin/class-admin.php';
	require_once LGPD_CC_PLUGIN_DIR . 'public/class-public.php';

	register_activation_hook(   __FILE__, [ 'LGPD_CC_Plugin', 'activate' ] );
	register_deactivation_hook( __FILE__, [ 'LGPD_CC_Plugin', 'deactivate' ] );
	lcc_fs()->add_action( 'after_uninstall', [ 'LGPD_CC_Plugin', 'uninstall' ] );

	LGPD_CC_Plugin::get_instance();
}
