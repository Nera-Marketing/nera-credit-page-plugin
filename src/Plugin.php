<?php
/**
 * Route and template takeover for the credit page.
 *
 * @package Nera\CreditPage
 */

namespace Nera\CreditPage;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin — replaces the theme's attribution page while the switch is on.
 */
final class Plugin {

	const DEFAULT_SLUG = 'competition-website-by-nera-marketing';

	const QUERY_VAR = 'nera_attribution';

	/**
	 * Whether the credit page switch is on. A switch never saved is off.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		return '1' === (string) get_option( 'options_ncp_enabled' );
	}

	/**
	 * @return void
	 */
	public static function boot() {
		if ( is_admin() ) {
			Admin\Fields::register_hooks();
		}
		if ( ! self::is_enabled() ) {
			return;
		}
		// The theme's functions.php loads after plugins_loaded, so route checks wait for init.
		add_action( 'init', array( __CLASS__, 'register_route' ), 11 );
		add_filter( 'template_include', array( __CLASS__, 'template_include' ), 100 );
		Seo::register();
	}

	/**
	 * Rules are regenerated on the next request once the stored set is gone.
	 *
	 * @return void
	 */
	public static function activate() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * @return void
	 */
	public static function deactivate() {
		delete_option( 'rewrite_rules' );
	}

	/**
	 * Register the route and query var only when the theme doesn't provide them.
	 *
	 * @return void
	 */
	public static function register_route() {
		if ( function_exists( 'nera_register_attribution_route' ) ) {
			return;
		}

		add_rewrite_rule(
			'^' . preg_quote( self::slug(), '#' ) . '/?$',
			'index.php?' . self::QUERY_VAR . '=1',
			'top'
		);

		add_filter(
			'query_vars',
			static function ( $vars ) {
				$vars[] = self::QUERY_VAR;
				return $vars;
			}
		);
	}

	/**
	 * Serve the plugin template on the credit page route.
	 *
	 * @param string $template Resolved template path.
	 * @return string
	 */
	public static function template_include( $template ) {
		if ( 1 !== (int) get_query_var( self::QUERY_VAR ) ) {
			return $template;
		}

		$path = NERA_CREDIT_PAGE_DIR . 'templates/credit-page.php';
		if ( ! is_readable( $path ) ) {
			return $template;
		}

		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );

		return $path;
	}

	/**
	 * Public slug of the page: the theme's setting when it has one.
	 *
	 * @return string
	 */
	public static function slug() {
		if ( function_exists( 'nera_get_attribution_route_slug' ) ) {
			return (string) nera_get_attribution_route_slug();
		}
		return self::DEFAULT_SLUG;
	}
}
