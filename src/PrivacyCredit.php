<?php
/**
 * Credit section at the end of the privacy policy page.
 *
 * @package Nera\CreditPage
 */

namespace Nera\CreditPage;

defined( 'ABSPATH' ) || exit;

/**
 * PrivacyCredit — appends the "who built this website" section to the privacy policy content.
 */
final class PrivacyCredit {

	const HEADING = 'who built this website';

	/**
	 * Whether the privacy section switch is on. Never saved is off.
	 *
	 * @return bool
	 */
	public static function is_switched_on() {
		return '1' === (string) get_option( 'options_ncp_privacy_credit' );
	}

	/**
	 * @return void
	 */
	public static function register() {
		if ( ! self::is_switched_on() ) {
			return;
		}
		// After do_blocks (9), wpautop (10) and shortcodes (11), so the section is appended to final HTML.
		add_filter( 'the_content', array( __CLASS__, 'append' ), 20 );
	}

	/**
	 * Published privacy policy page id, or 0.
	 *
	 * @return int
	 */
	public static function privacy_page_id() {
		$id = (int) get_option( 'wp_page_for_privacy_policy' );
		if ( $id <= 0 || 'publish' !== get_post_status( $id ) ) {
			return 0;
		}
		return $id;
	}

	/**
	 * Some themes run the_content outside the loop, so the page is matched by id rather than loop state.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public static function append( $content ) {
		if ( ! is_string( $content ) || is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return $content;
		}
		// Excerpts built from content (SEO descriptions, Open Graph) must not carry the section.
		if ( doing_filter( 'get_the_excerpt' ) ) {
			return $content;
		}

		$id = self::privacy_page_id();
		if ( 0 === $id || ! is_page( $id ) || (int) get_the_ID() !== $id ) {
			return $content;
		}

		// A locked page's content is the password form.
		if ( post_password_required( $id ) ) {
			return $content;
		}

		if ( self::is_present( $content ) ) {
			return $content;
		}

		return $content . self::section_html();
	}

	/**
	 * Whether rendered HTML already holds the section: a link to the credit page path (relative or
	 * absolute, trailing slash optional), or an h2–h6 reading "Who built this website" (any case,
	 * trailing punctuation ignored). Block classes are not part of the test.
	 *
	 * @param string $html Rendered HTML.
	 * @return bool
	 */
	public static function is_present( $html ) {
		$html = (string) $html;
		$slug = trim( Plugin::slug(), '/' );

		if ( '' !== $slug && preg_match_all( '/\bhref\s*=\s*(["\'])(.*?)\1/is', $html, $links ) ) {
			foreach ( $links[2] as $href ) {
				$path = wp_parse_url( html_entity_decode( $href, ENT_QUOTES ), PHP_URL_PATH );
				if ( is_string( $path ) && trim( $path, '/' ) === $slug ) {
					return true;
				}
			}
		}

		if ( preg_match_all( '/<h([2-6])\b[^>]*>(.*?)<\/h\1>/is', $html, $headings ) ) {
			foreach ( $headings[2] as $inner ) {
				$text = html_entity_decode( wp_strip_all_tags( $inner ), ENT_QUOTES );
				$text = strtolower( rtrim( trim( preg_replace( '/\s+/u', ' ', $text ) ), " .!?:;\t\n\r" ) );
				if ( self::HEADING === $text ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Rendered HTML of a page's own content, without this plugin's section.
	 *
	 * @param int $id Post id.
	 * @return string
	 */
	public static function privacy_html( $id ) {
		$post = get_post( (int) $id );
		if ( ! $post ) {
			return '';
		}
		return do_shortcode( do_blocks( (string) $post->post_content ) );
	}

	/**
	 * Credit page URL: the theme's helper when it has one.
	 *
	 * @return string
	 */
	public static function credit_url() {
		if ( function_exists( 'nera_get_attribution_page_url' ) ) {
			return (string) nera_get_attribution_page_url();
		}
		return home_url( user_trailingslashit( Plugin::slug() ) );
	}

	/**
	 * Block-editor classes so the theme styles it like the page's own headings and paragraphs.
	 *
	 * @return string
	 */
	public static function section_html() {
		$link = '<strong><a href="' . esc_url( self::credit_url() ) . '">' . esc_html__( 'Nera Marketing', 'nera-credit-page' ) . '</a></strong>';

		return '<h2 class="wp-block-heading">' . esc_html__( 'Who built this website.', 'nera-credit-page' ) . '</h2>'
			. '<p class="wp-block-paragraph">' . sprintf(
				/* translators: %s: linked agency name. */
				esc_html__( 'This website was designed and built by %s. You can read more on our website credits page.', 'nera-credit-page' ),
				$link
			) . '</p>';
	}
}
