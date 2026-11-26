<?php
/**
 * Credit page copy.
 *
 * @package Nera\CreditPage
 */

namespace Nera\CreditPage;

defined( 'ABSPATH' ) || exit;

/**
 * Content — default copy and the client name placeholder.
 */
final class Content {

	const OPTION_PREFIX = 'options_ncp_';

	const CLIENT_TOKEN = '{Client}';

	/**
	 * Fields that show their default when saved blank, so the page never loses them.
	 */
	const REQUIRED = array( 'hero_heading', 'seo_title', 'seo_description' );

	/**
	 * Default copy, keyed by field. 'faqs' is the list of question and answer rows; every other value is a string.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		return array(
			'client_name'         => '',
			'hero_badge'          => '',
			'hero_heading'        => "Website built by\nNera Marketing",
			'hero_intro'          => "{Client}'s website was designed and built by Nera Marketing.",
			'address'             => '',
			'about_eyebrow'       => '',
			'about_heading'       => 'About Nera Marketing',
			'about_body'          => 'At Nera Marketing we build and grow ecommerce platforms for businesses that sell at volume, where payments, regulation and complex integrations have to be right. We create online stores, fully custom platforms, competition websites and web apps. Every platform is built to hold up at peak, get found in Google and AI search, and turn visitors into sales. Once it is live, we keep it fast, secure and improving, so it sells more as your business scales. We have generated over £20M in revenue for more than 500 clients.',
			'built_eyebrow'       => '',
			'built_heading'       => 'What we built for {Client}',
			'built_body'          => '{Client} sells entries online at volume. The platform has to take payments reliably, follow UK competition rules and stay fast when a draw closes. Every build includes custom ticketing, a free entry route, a skills question, countdown timers, draw randomisation and GDPR tools. {Client} owns the platform outright.',
			'ecommerce_eyebrow'   => '',
			'ecommerce_heading'   => 'Competition websites are ecommerce platforms',
			'ecommerce_body'      => 'A competition website is an online store with extra rules. It takes card payments at volume, runs a free entry route and a skills question, and has to handle traffic spikes on draw nights. We build them to the same standard as every ecommerce platform we deliver.',
			'faq_eyebrow'         => '',
			'faq_heading'         => 'Questions',
			'faqs'                => array(
				array(
					'question' => 'Who built the {Client} website?',
					'answer'   => 'Nera Marketing designed and built it.',
				),
				array(
					'question' => 'Who owns the {Client} website?',
					'answer'   => '{Client} owns the platform outright.',
				),
				array(
					'question' => 'What does Nera Marketing build?',
					'answer'   => 'Online stores, fully custom platforms, competition websites and web apps, for businesses that sell at volume.',
				),
			),
			'services_link_label' => 'See how we build competition websites',
			'services_link_url'   => 'https://neramarketing.co.uk/competition-websites/',
			'case_study_label'    => 'Read the {Client} case study',
			'case_study_url'      => '',
			'home_link_label'     => 'Visit Nera Marketing',
			'home_link_url'       => 'https://neramarketing.co.uk/',
			'seo_title'           => 'Website credits | {Client}',
			'seo_description'     => "{Client}'s website was designed and built by Nera Marketing, an ecommerce agency that builds platforms for businesses that sell at volume.",
		);
	}

	/**
	 * A field's saved text, or its default when it was never saved.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public static function raw( $key ) {
		$saved = get_option( self::OPTION_PREFIX . $key, null );
		if ( null !== $saved && is_scalar( $saved ) ) {
			return (string) $saved;
		}
		$defaults = self::defaults();
		return isset( $defaults[ $key ] ) && is_string( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}

	/**
	 * One field's text with the client name filled in; empty when saved blank.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public static function get( $key ) {
		$text = trim( self::raw( $key ) );
		if ( '' === $text && in_array( $key, self::REQUIRED, true ) ) {
			$text = self::defaults()[ $key ];
		}
		return '' === $text ? '' : self::fill( $text );
	}

	/**
	 * A body field split into paragraphs at blank lines.
	 *
	 * @param string $key Field key.
	 * @return array<int,string>
	 */
	public static function paragraphs( $key ) {
		$text = self::get( $key );
		if ( '' === $text ) {
			return array();
		}
		return array_values( array_filter( array_map( 'trim', preg_split( '/\R\s*\R/', $text ) ), 'strlen' ) );
	}

	/**
	 * A heading with one line per row: rows are joined with <br> and the last one is in the brand colour.
	 *
	 * @param string $key Field key.
	 * @return string Escaped HTML.
	 */
	public static function heading_html( $key ) {
		$lines = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', self::get( $key ) ) ), 'strlen' ) );
		if ( ! $lines ) {
			return '';
		}
		$last = array_pop( $lines );
		$html = '';
		foreach ( $lines as $line ) {
			$html .= esc_html( $line ) . '<br>';
		}
		return $html . '<span class="text-primary">' . esc_html( $last ) . '</span>';
	}

	/**
	 * Question and answer rows, in order. Never saved shows the default rows; a row missing
	 * its question or answer is skipped.
	 *
	 * @return array<int,array{question:string,answer:string}>
	 */
	public static function faqs() {
		$count = get_option( self::OPTION_PREFIX . 'faqs', null );
		if ( null === $count ) {
			$rows = self::defaults()['faqs'];
		} else {
			$rows = array();
			for ( $i = 0; $i < (int) $count; $i++ ) {
				$rows[] = array(
					'question' => (string) get_option( self::OPTION_PREFIX . 'faqs_' . $i . '_question', '' ),
					'answer'   => (string) get_option( self::OPTION_PREFIX . 'faqs_' . $i . '_answer', '' ),
				);
			}
		}

		$out = array();
		foreach ( $rows as $row ) {
			$question = trim( $row['question'] );
			$answer   = trim( $row['answer'] );
			if ( '' !== $question && '' !== $answer ) {
				$out[] = array(
					'question' => self::fill( $question ),
					'answer'   => self::fill( $answer ),
				);
			}
		}
		return $out;
	}

	/**
	 * @return string
	 */
	public static function client_name() {
		$name = trim( self::raw( 'client_name' ) );
		if ( '' === $name ) {
			$name = trim( (string) get_bloginfo( 'name' ) );
		}
		if ( '' === $name ) {
			$name = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		}
		return $name;
	}

	/**
	 * Replace the client placeholder in a string.
	 *
	 * @param string $text Text that may hold the placeholder.
	 * @return string
	 */
	public static function fill( $text ) {
		return str_replace( self::CLIENT_TOKEN, self::client_name(), (string) $text );
	}
}