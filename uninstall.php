<?php
/**
 * Uninstall handler: removes the credit page settings (ACF option values and their field-key references).
 *
 * @package Nera\CreditPage
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

foreach ( array( 'options_ncp_', '_options_ncp_' ) as $nera_credit_page_prefix ) {
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			$wpdb->esc_like( $nera_credit_page_prefix ) . '%'
		)
	);
}

wp_cache_delete( 'alloptions', 'options' );
delete_option( 'rewrite_rules' );
