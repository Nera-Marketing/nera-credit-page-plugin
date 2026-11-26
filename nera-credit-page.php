<?php
/**
 * Plugin Name:       Nera Credit Page
 * Description:       Serves the Nera Marketing credit page at the theme's attribution URL while switched on.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Author:            Nera Marketing
 * Author URI:        https://neramarketing.co.uk/
 * Text Domain:       nera-credit-page
 *
 * @package Nera\CreditPage
 */

defined( 'ABSPATH' ) || exit;

define( 'NERA_CREDIT_PAGE_FILE', __FILE__ );
define( 'NERA_CREDIT_PAGE_DIR', plugin_dir_path( __FILE__ ) );
define( 'NERA_CREDIT_PAGE_URL', plugin_dir_url( __FILE__ ) );
define( 'NERA_CREDIT_PAGE_VERSION', '1.0.0' );

spl_autoload_register(
	static function ( $class ) {
		$prefix = 'Nera\\CreditPage\\';
		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}
		$path = NERA_CREDIT_PAGE_DIR . 'src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
		if ( is_readable( $path ) ) {
			require $path;
		}
	}
);

register_activation_hook( __FILE__, array( 'Nera\\CreditPage\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Nera\\CreditPage\\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Nera\\CreditPage\\Plugin', 'boot' ) );
