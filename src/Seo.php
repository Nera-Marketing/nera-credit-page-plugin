<?php
/**
 * Credit page search settings: title, description, canonical, robots, schema and sitemaps.
 *
 * @package Nera\CreditPage
 */

namespace Nera\CreditPage;

defined( 'ABSPATH' ) || exit;

/**
 * Seo — search metadata for the credit page route.
 */
final class Seo {

	const TITLE = 'Website credits | {Client}';

	const DESCRIPTION = "{Client}'s website was designed and built by Nera Marketing, an ecommerce agency that builds platforms for businesses that sell at volume.";

	const CREATOR = array(
		'@type' => 'Organization',
		'@id'   => 'https://neramarketing.co.uk/#organization',
		'name'  => 'Nera Marketing',
		'url'   => 'https://neramarketing.co.uk/',
	);

	/**
	 * Set while the core page sitemap is rebuilt from inside its own filter.
	 *
	 * @var bool
	 */
	private static $in_sitemap = false;

	/**
	 * @return void
	 */
	public static function register() {
		// After the theme's own title for this route (priority 10).
		add_filter( 'pre_get_document_title', array( __CLASS__, 'filter_title' ), 20 );
		add_action( 'wp_head', array( __CLASS__, 'print_head' ), 1 );
		add_filter( 'wp_robots', array( __CLASS__, 'filter_wp_robots' ), 999 );
		add_filter( 'wp_sitemaps_posts_pre_url_list', array( __CLASS__, 'filter_core_sitemap' ), 10, 3 );

		add_filter( 'wpseo_title', array( __CLASS__, 'filter_title' ), 99 );
		add_filter( 'wpseo_metadesc', array( __CLASS__, 'filter_description' ), 99 );
		add_filter( 'wpseo_canonical', array( __CLASS__, 'filter_canonical' ), 99 );
		add_filter( 'wpseo_robots', array( __CLASS__, 'filter_robots_string' ), 99 );
		add_filter( 'wpseo_json_ld_output', array( __CLASS__, 'filter_seo_schema' ), 99 );
		add_filter( 'wpseo_sitemap_page_content', array( __CLASS__, 'yoast_sitemap_entry' ) );

		add_filter( 'slim_seo_meta_title', array( __CLASS__, 'filter_title' ), 99 );
		add_filter( 'slim_seo_meta_description', array( __CLASS__, 'filter_description' ), 99 );
		add_filter( 'slim_seo_canonical_url', array( __CLASS__, 'filter_canonical' ), 99 );
		add_filter( 'slim_seo_robots_index', array( __CLASS__, 'filter_robots_index' ), 99 );
		add_filter( 'slim_seo_schema_graph', array( __CLASS__, 'filter_slim_seo_graph' ), 99 );
	}

	/**
	 * @return bool
	 */
	public static function is_route() {
		return 1 === (int) get_query_var( Plugin::QUERY_VAR );
	}

	/**
	 * @return string
	 */
	public static function url() {
		return home_url( user_trailingslashit( Plugin::slug() ) );
	}

	/**
	 * @return string
	 */
	public static function title() {
		return Content::fill( self::TITLE );
	}

	/**
	 * @return string
	 */
	public static function description() {
		return Content::fill( self::DESCRIPTION );
	}

	/**
	 * Yoast and Slim SEO print their own description and canonical tags.
	 *
	 * @return bool
	 */
	private static function seo_plugin_active() {
		return defined( 'WPSEO_VERSION' ) || defined( 'SLIM_SEO_VER' );
	}

	/**
	 * @param string $title Title from WordPress or an SEO plugin.
	 * @return string
	 */
	public static function filter_title( $title ) {
		// pre_get_document_title output skips core's escaping, and the client name is editable.
		return self::is_route() ? esc_html( self::title() ) : $title;
	}

	/**
	 * @param string $description Description from an SEO plugin.
	 * @return string
	 */
	public static function filter_description( $description ) {
		return self::is_route() ? self::description() : $description;
	}

	/**
	 * @param string $canonical Canonical URL from an SEO plugin.
	 * @return string
	 */
	public static function filter_canonical( $canonical ) {
		return self::is_route() ? self::url() : $canonical;
	}

	/**
	 * The site-wide "discourage search engines" setting still applies.
	 *
	 * @param array $robots Robots directives.
	 * @return array
	 */
	public static function filter_wp_robots( $robots ) {
		if ( self::is_route() && get_option( 'blog_public' ) ) {
			unset( $robots['noindex'], $robots['none'] );
		}
		return $robots;
	}

	/**
	 * @param string $robots Yoast robots content.
	 * @return string
	 */
	public static function filter_robots_string( $robots ) {
		if ( self::is_route() && get_option( 'blog_public' ) ) {
			return str_replace( 'noindex', 'index', (string) $robots );
		}
		return $robots;
	}

	/**
	 * @param bool $index Whether Slim SEO lets the page be indexed.
	 * @return bool
	 */
	public static function filter_robots_index( $index ) {
		return self::is_route() ? true : $index;
	}

	/**
	 * The page prints its own WebPage schema, so an SEO plugin's graph would describe it twice.
	 *
	 * @param mixed $output Whether the plugin prints its schema.
	 * @return mixed
	 */
	public static function filter_seo_schema( $output ) {
		return self::is_route() ? false : $output;
	}

	/**
	 * Slim SEO prints nothing when its graph is empty.
	 *
	 * @param array $graph Slim SEO schema nodes.
	 * @return array
	 */
	public static function filter_slim_seo_graph( $graph ) {
		return self::is_route() ? array() : $graph;
	}

	/**
	 * @return void
	 */
	public static function print_head() {
		if ( ! self::is_route() ) {
			return;
		}

		if ( ! self::seo_plugin_active() ) {
			printf( '<meta name="description" content="%s" />' . "\n", esc_attr( self::description() ) );
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( self::url() ) );
		}

		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebPage',
			'url'      => self::url(),
			// The site title is stored HTML-escaped; JSON-LD wants the plain text.
			'name'     => wp_specialchars_decode( self::title(), ENT_QUOTES ),
			'creator'  => self::CREATOR,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>' . "\n";
	}

	/**
	 * Add the page to the first core page sitemap.
	 *
	 * @param array|null $list      URL list, or null to let core build it.
	 * @param string     $post_type Post type of the sitemap.
	 * @param int        $page_num  Sitemap page number.
	 * @return array|null
	 */
	public static function filter_core_sitemap( $list, $post_type, $page_num ) {
		if ( null !== $list || self::$in_sitemap || 'page' !== $post_type || 1 !== (int) $page_num ) {
			return $list;
		}

		$provider = wp_sitemaps_get_server()->registry->get_provider( 'posts' );
		if ( ! $provider ) {
			return $list;
		}

		self::$in_sitemap = true;
		$list             = $provider->get_url_list( 1, 'page' );
		self::$in_sitemap = false;

		$list[] = array( 'loc' => self::url() );
		return $list;
	}

	/**
	 * @param string $content Extra page sitemap XML.
	 * @return string
	 */
	public static function yoast_sitemap_entry( $content ) {
		return $content . '<url><loc>' . esc_url( self::url() ) . "</loc></url>\n";
	}
}
