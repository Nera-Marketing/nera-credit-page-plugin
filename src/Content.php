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
	 * Default copy, keyed by field.
	 *
	 * @return array<string,string>
	 */
	public static function defaults() {
		return array(
			'client_name'         => '',
			'hero_heading'        => 'Website built by Nera Marketing',
			'hero_intro'          => "{Client}'s website was designed and built by Nera Marketing.",
			'about_heading'       => 'About Nera Marketing',
			'about_body'          => 'At Nera Marketing we build and grow ecommerce platforms for businesses that sell at volume, where payments, regulation and complex integrations have to be right. We create online stores, fully custom platforms, competition websites and web apps. Every platform is built to hold up at peak, get found in Google and AI search, and turn visitors into sales. Once it is live, we keep it fast, secure and improving, so it sells more as your business scales. We have generated over £20M in revenue for more than 500 clients.',
			'built_heading'       => 'What we built for {Client}',
			'built_body'          => '{Client} sells entries online at volume. The platform has to take payments reliably, follow UK competition rules and stay fast when a draw closes. Every build includes custom ticketing, a free entry route, a skills question, countdown timers, draw randomisation and GDPR tools. {Client} owns the platform outright.',
			'ecommerce_heading'   => 'Competition websites are ecommerce platforms',
			'ecommerce_body'      => 'A competition website is an online store with extra rules. It takes card payments at volume, runs a free entry route and a skills question, and has to handle traffic spikes on draw nights. We build them to the same standard as every ecommerce platform we deliver.',
			'faq_heading'         => 'Questions',
			'faq_1_question'      => 'Who built the {Client} website?',
			'faq_1_answer'        => 'Nera Marketing designed and built it.',
			'faq_2_question'      => 'Who owns the {Client} website?',
			'faq_2_answer'        => '{Client} owns the platform outright.',
			'faq_3_question'      => 'What does Nera Marketing build?',
			'faq_3_answer'        => 'Online stores, fully custom platforms, competition websites and web apps, for businesses that sell at volume.',
			'services_link_label' => 'See how we build competition websites',
			'services_link_url'   => 'https://neramarketing.co.uk/competition-websites/',
			'case_study_label'    => 'Read the {Client} case study',
			'case_study_url'      => '',
			'home_link_label'     => 'Visit Nera Marketing',
			'home_link_url'       => 'https://neramarketing.co.uk/',
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
		return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	}

	/**
	 * One field's text with the client name filled in; empty when saved blank.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public static function get( $key ) {
		$text = trim( self::raw( $key ) );
		if ( '' === $text && 'hero_heading' === $key ) {
			$text = self::defaults()['hero_heading'];
		}
		return '' === $text ? '' : self::fill( $text );
	}

	/**
	 * Question and answer pairs, in order.
	 *
	 * @return array<int,array{question:string,answer:string}>
	 */
	public static function faqs() {
		$out = array();
		for ( $i = 1; $i <= 3; $i++ ) {
			$question = self::get( 'faq_' . $i . '_question' );
			$answer   = self::get( 'faq_' . $i . '_answer' );
			if ( '' !== $question && '' !== $answer ) {
				$out[] = array(
					'question' => $question,
					'answer'   => $answer,
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