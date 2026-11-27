<?php
/**
 * Plugin Name:       Nera Credit Page
 * Description:       Serves the Nera Marketing credit page at the theme's attribution URL while switched on.
 * Version:           1.2.0
 * Requires PHP:      7.4
 * Author:            Nera Marketing
 * Author URI:        https://neramarketing.co.uk/
 * Text Domain:       nera-credit-page
 *
 * @package Nera\CreditPage
 */

defined( 'ABSPATH' ) || exit;

use YahnisElsts\PluginUpdateChecker\v5p5\Vcs\GitHubApi;

define( 'NERA_CREDIT_PAGE_FILE', __FILE__ );
define( 'NERA_CREDIT_PAGE_DIR', plugin_dir_path( __FILE__ ) );
define( 'NERA_CREDIT_PAGE_URL', plugin_dir_url( __FILE__ ) );
define( 'NERA_CREDIT_PAGE_VERSION', '1.2.0' );

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

/**
 * Updates from GitHub releases (Plugin Update Checker v5.5).
 *
 * Turn off: define( 'NERA_CREDIT_PAGE_DISABLE_GITHUB_UPDATES', true );
 * Optional GitHub token: define( 'NERA_CREDIT_PAGE_GITHUB_TOKEN', '...' );
 * Other repo: define( 'NERA_CREDIT_PAGE_GITHUB_REPO_URL', 'https://github.com/Owner/repo/' ) or filter nera_credit_page_github_repo_url.
 *
 * PUC compares the Version header of this file on the selected ref, so bump Version and NERA_CREDIT_PAGE_VERSION
 * for every release (release.sh does this, and syncs readme.txt Stable tag and plugin.json).
 *
 * GitHub's /releases/latest returns 404 when no release is marked latest. A custom release filter with
 * maxReleases > 1 makes PUC read the paginated /releases list instead; pre-releases are skipped.
 *
 * PUC reads the remote readme.txt only when one ships with the installed plugin, so keep it in the package.
 *
 * @link https://github.com/YahnisElsts/plugin-update-checker
 */
if ( ! defined( 'NERA_CREDIT_PAGE_DISABLE_GITHUB_UPDATES' ) || ! NERA_CREDIT_PAGE_DISABLE_GITHUB_UPDATES ) {
	$nera_credit_page_repo = 'https://github.com/Nera-Marketing/nera-credit-page-plugin/';
	if ( defined( 'NERA_CREDIT_PAGE_GITHUB_REPO_URL' ) && is_string( NERA_CREDIT_PAGE_GITHUB_REPO_URL ) && '' !== NERA_CREDIT_PAGE_GITHUB_REPO_URL ) {
		$nera_credit_page_repo = NERA_CREDIT_PAGE_GITHUB_REPO_URL;
	}
	$nera_credit_page_repo = apply_filters( 'nera_credit_page_github_repo_url', $nera_credit_page_repo );

	$nera_credit_page_puc_loader = NERA_CREDIT_PAGE_DIR . 'lib/plugin-update-checker/load-v5p5.php';
	if ( is_readable( $nera_credit_page_puc_loader ) ) {
		require_once $nera_credit_page_puc_loader;
		// Fourth argument: check period in hours.
		$nera_credit_page_update_checker = YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
			$nera_credit_page_repo,
			__FILE__,
			'nera-credit-page',
			6
		);
		$nera_credit_page_update_checker->setBranch( 'main' );

		if ( defined( 'NERA_CREDIT_PAGE_GITHUB_TOKEN' ) && is_string( NERA_CREDIT_PAGE_GITHUB_TOKEN ) && '' !== NERA_CREDIT_PAGE_GITHUB_TOKEN ) {
			$nera_credit_page_update_checker->setAuthentication( NERA_CREDIT_PAGE_GITHUB_TOKEN );
		}

		$nera_credit_page_vcs = $nera_credit_page_update_checker->getVcsApi();
		if ( $nera_credit_page_vcs instanceof GitHubApi ) {
			$nera_credit_page_vcs->setReleaseFilter(
				static function ( $version_number, $release_object ) {
					unset( $version_number, $release_object );
					return true;
				},
				\YahnisElsts\PluginUpdateChecker\v5p5\Vcs\Api::RELEASE_FILTER_SKIP_PRERELEASE,
				20
			);
			$nera_credit_page_vcs->enableReleaseAssets();
		}
	}
}
