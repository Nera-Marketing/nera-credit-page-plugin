<?php
/**
 * Hidden ACF editor for the credit page.
 *
 * @package Nera\CreditPage
 */

namespace Nera\CreditPage\Admin;

use Nera\CreditPage\Content;

defined( 'ABSPATH' ) || exit;

/**
 * Fields — options page, field group, case study URL rule and the plugin row link.
 */
final class Fields {

	const SLUG = 'ncp-credit-7q3m';

	const GROUP = 'group_ncp_credit_page';

	/**
	 * Switch value before an editor save, to spot a change after it.
	 *
	 * @var string|null
	 */
	private static $enabled_before = null;

	/**
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'acf/init', array( __CLASS__, 'register' ) );
		add_action( 'admin_menu', array( __CLASS__, 'hide_menu' ), 999 );
		add_filter( 'acf/validate_value/key=field_ncp_case_study_url', array( __CLASS__, 'validate_case_study_url' ), 10, 2 );
		add_filter( 'acf/load_value/key=field_ncp_faqs', array( __CLASS__, 'default_faqs' ), 20, 2 );
		add_action( 'acf/save_post', array( __CLASS__, 'before_save' ), 1 );
		add_action( 'acf/save_post', array( __CLASS__, 'after_save' ), 20 );
		add_filter( 'plugin_action_links_' . plugin_basename( NERA_CREDIT_PAGE_FILE ), array( __CLASS__, 'action_links' ) );
		add_action( 'admin_notices', array( __CLASS__, 'missing_acf_notice' ) );
	}

	/**
	 * @return bool
	 */
	public static function acf_ready() {
		return function_exists( 'acf_add_options_page' ) && function_exists( 'acf_add_local_field_group' );
	}

	/**
	 * @return string
	 */
	public static function url() {
		return admin_url( 'admin.php?page=' . self::SLUG );
	}

	/**
	 * @return void
	 */
	public static function register() {
		if ( ! self::acf_ready() ) {
			return;
		}

		acf_add_options_page(
			array(
				'page_title' => __( 'Credit Page Editor', 'nera-credit-page' ),
				'menu_title' => __( 'Credit Page Editor', 'nera-credit-page' ),
				'menu_slug'  => self::SLUG,
				'capability' => 'manage_options',
				'redirect'   => false,
				'position'   => null,
			)
		);

		acf_add_local_field_group(
			array(
				'key'                   => self::GROUP,
				'title'                 => __( 'Credit Page', 'nera-credit-page' ),
				'fields'                => self::fields(),
				'location'              => array(
					array(
						array(
							'param'    => 'options_page',
							'operator' => '==',
							'value'    => self::SLUG,
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => array( 'the_content', 'featured_image' ),
				'active'                => true,
			)
		);
	}

	/**
	 * Field definitions, tab by tab in page order.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private static function fields() {
		$heading      = __( 'Heading', 'nera-credit-page' );
		$paragraph    = __( 'Paragraph', 'nera-credit-page' );
		$hides        = __( 'Leave blank to hide it on the page.', 'nera-credit-page' );
		$eyebrow      = __( 'Eyebrow', 'nera-credit-page' );
		$eyebrow_help = __( 'Small label above the heading. Leave blank to hide it.', 'nera-credit-page' );

		$fields = array(
			self::tab( 'settings', __( 'Settings', 'nera-credit-page' ) ),
			array(
				'key'     => 'field_ncp_help',
				'label'   => '',
				'name'    => '',
				'type'    => 'message',
				'message' => __( '{Client} in any field is replaced with the client name. A blank badge, address, eyebrow, heading, paragraph or link label hides it on the page; a blank main heading, page title or meta description shows the default one. Questions with no rows hide the Questions section, and a row missing its question or answer is not shown.', 'nera-credit-page' ),
			),
			array(
				'key'           => 'field_ncp_enabled',
				'label'         => __( 'Enable credit page', 'nera-credit-page' ),
				'name'          => 'ncp_enabled',
				'type'          => 'true_false',
				'instructions'  => __( 'On: this plugin serves the credit page. Off: the theme\'s page shows as before.', 'nera-credit-page' ),
				'default_value' => 0,
				'ui'            => 1,
			),
			array(
				'key'          => 'field_ncp_client_name',
				'label'        => __( 'Client name', 'nera-credit-page' ),
				'name'         => 'ncp_client_name',
				'type'         => 'text',
				'instructions' => __( 'Leave blank to use the Site Title.', 'nera-credit-page' ),
				'placeholder'  => (string) get_bloginfo( 'name' ),
			),
			array(
				'key'          => 'field_ncp_case_study_url',
				'label'        => __( 'Case study URL', 'nera-credit-page' ),
				'name'         => 'ncp_case_study_url',
				'type'         => 'url',
				'instructions' => __( 'A full https:// address. Leave blank to hide the case study link.', 'nera-credit-page' ),
			),
			self::tab( 'hero', __( 'Hero', 'nera-credit-page' ) ),
			self::copy( 'hero_badge', __( 'Badge', 'nera-credit-page' ), 'text', $hides ),
			array_merge(
				self::copy( 'hero_heading', __( 'Main heading (H1)', 'nera-credit-page' ), 'textarea', __( 'Visual H1. One phrase per line. The last line is shown in the primary colour.', 'nera-credit-page' ) ),
				array( 'rows' => 3 )
			),
			self::copy( 'hero_intro', __( 'Intro', 'nera-credit-page' ), 'textarea', $hides ),
			self::copy( 'address', __( 'Address', 'nera-credit-page' ), 'text', $hides ),
			self::tab( 'about', __( 'About', 'nera-credit-page' ) ),
			self::copy( 'about_eyebrow', $eyebrow, 'text', $eyebrow_help ),
			self::copy( 'about_heading', $heading, 'text', $hides ),
			self::copy( 'about_body', $paragraph, 'textarea', __( 'Leave blank to hide the whole section. A blank line starts a new paragraph.', 'nera-credit-page' ) ),
			self::tab( 'built', __( 'What we built', 'nera-credit-page' ) ),
			self::copy( 'built_eyebrow', $eyebrow, 'text', $eyebrow_help ),
			self::copy( 'built_heading', $heading, 'text', $hides ),
			self::copy( 'built_body', $paragraph, 'textarea', __( 'Leave blank to hide the whole section. A blank line starts a new paragraph.', 'nera-credit-page' ) ),
			self::tab( 'ecommerce', __( 'Competition websites', 'nera-credit-page' ) ),
			self::copy( 'ecommerce_eyebrow', $eyebrow, 'text', $eyebrow_help ),
			self::copy( 'ecommerce_heading', $heading, 'text', $hides ),
			self::copy( 'ecommerce_body', $paragraph, 'textarea', __( 'Leave blank to hide the whole section. A blank line starts a new paragraph.', 'nera-credit-page' ) ),
			self::tab( 'faq', __( 'Questions', 'nera-credit-page' ) ),
			self::copy( 'faq_eyebrow', $eyebrow, 'text', $eyebrow_help ),
			self::copy( 'faq_heading', $heading, 'text', $hides ),
			array(
				'key'          => 'field_ncp_faqs',
				'label'        => __( 'Questions and answers', 'nera-credit-page' ),
				'name'         => 'ncp_faqs',
				'type'         => 'repeater',
				'instructions' => __( 'No rows hide the Questions section. A row missing its question or answer is not shown.', 'nera-credit-page' ),
				'layout'       => 'block',
				'button_label' => __( 'Add question', 'nera-credit-page' ),
				'min'          => 0,
				'sub_fields'   => array(
					array(
						'key'   => 'field_ncp_faqs_question',
						'label' => __( 'Question', 'nera-credit-page' ),
						'name'  => 'question',
						'type'  => 'text',
					),
					array(
						'key'       => 'field_ncp_faqs_answer',
						'label'     => __( 'Answer', 'nera-credit-page' ),
						'name'      => 'answer',
						'type'      => 'textarea',
						'rows'      => 3,
						'new_lines' => '',
					),
				),
			),
		);

		$fields[] = self::tab( 'links', __( 'Links', 'nera-credit-page' ) );
		$fields[] = self::copy( 'services_link_label', __( 'Services link label', 'nera-credit-page' ), 'text', $hides );
		$fields[] = self::copy( 'case_study_label', __( 'Case study link label', 'nera-credit-page' ), 'text', __( 'Shown only when the Case study URL is set.', 'nera-credit-page' ) );
		$fields[] = self::copy( 'home_link_label', __( 'Home link label', 'nera-credit-page' ), 'text', $hides );

		$fields[] = self::tab( 'seo', __( 'SEO', 'nera-credit-page' ) );
		$fields[] = self::copy( 'seo_title', __( 'Page Title', 'nera-credit-page' ), 'text', __( 'Browser tab title and WebPage schema name.', 'nera-credit-page' ) );
		$fields[] = array_merge( self::copy( 'seo_description', __( 'Meta description', 'nera-credit-page' ), 'textarea', '' ), array( 'rows' => 3 ) );

		return $fields;
	}

	/**
	 * @param string $id    Tab id.
	 * @param string $label Tab label.
	 * @return array<string,mixed>
	 */
	private static function tab( $id, $label ) {
		return array(
			'key'       => 'field_ncp_tab_' . $id,
			'label'     => $label,
			'type'      => 'tab',
			'placement' => 'top',
		);
	}

	/**
	 * A copy field prefilled with its default text.
	 *
	 * @param string $key          Content key.
	 * @param string $label        Field label.
	 * @param string $type         text or textarea.
	 * @param string $instructions Help text.
	 * @return array<string,mixed>
	 */
	private static function copy( $key, $label, $type, $instructions ) {
		$defaults = Content::defaults();
		$field    = array(
			'key'           => 'field_ncp_' . $key,
			'label'         => $label,
			'name'          => 'ncp_' . $key,
			'type'          => $type,
			'instructions'  => $instructions,
			'default_value' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
		);
		if ( 'textarea' === $type ) {
			$field['rows']      = 4;
			$field['new_lines'] = '';
		}
		return $field;
	}

	/**
	 * A repeater ignores default_value, so a never-saved one is prefilled here with the default rows.
	 *
	 * @param mixed $value   Loaded value.
	 * @param mixed $post_id ACF post id.
	 * @return mixed
	 */
	public static function default_faqs( $value, $post_id ) {
		if ( 'options' !== $post_id || null !== get_option( Content::OPTION_PREFIX . 'faqs', null ) ) {
			return $value;
		}
		$rows = array();
		foreach ( Content::defaults()['faqs'] as $faq ) {
			$rows[] = array(
				'field_ncp_faqs_question' => $faq['question'],
				'field_ncp_faqs_answer'   => $faq['answer'],
			);
		}
		return $rows;
	}

	/**
	 * @return void
	 */
	public static function hide_menu() {
		remove_menu_page( self::SLUG );
	}

	/**
	 * ACF's own URL check lets "//host" and other schemes through, so require https with a host.
	 *
	 * @param mixed $valid Current result.
	 * @param mixed $value Submitted value.
	 * @return mixed
	 */
	public static function validate_case_study_url( $valid, $value ) {
		if ( true !== $valid || ! is_string( $value ) || '' === trim( $value ) ) {
			return $valid;
		}
		$parts = wp_parse_url( trim( $value ) );
		if ( ! is_array( $parts ) || empty( $parts['scheme'] ) || 'https' !== strtolower( $parts['scheme'] ) || empty( $parts['host'] ) ) {
			return __( 'The case study URL must be a full https:// address.', 'nera-credit-page' );
		}
		return $valid;
	}

	/**
	 * @param mixed $post_id ACF post id.
	 * @return bool
	 */
	private static function is_editor_save( $post_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- ACF has checked the nonce before acf/save_post.
		return 'options' === $post_id && isset( $_POST['acf'] ) && is_array( $_POST['acf'] ) && array_key_exists( 'field_ncp_enabled', $_POST['acf'] );
	}

	/**
	 * @param mixed $post_id ACF post id.
	 * @return void
	 */
	public static function before_save( $post_id ) {
		if ( self::is_editor_save( $post_id ) ) {
			self::$enabled_before = (string) get_option( 'options_ncp_enabled' );
		}
	}

	/**
	 * The route comes from this plugin on themes without one, so rules are rebuilt when the switch flips.
	 *
	 * @param mixed $post_id ACF post id.
	 * @return void
	 */
	public static function after_save( $post_id ) {
		if ( null === self::$enabled_before || ! self::is_editor_save( $post_id ) ) {
			return;
		}
		if ( self::$enabled_before !== (string) get_option( 'options_ncp_enabled' ) ) {
			delete_option( 'rewrite_rules' );
		}
		self::$enabled_before = null;
	}

	/**
	 * @param array<int|string,string> $links Plugin row links.
	 * @return array<int|string,string>
	 */
	public static function action_links( $links ) {
		if ( ! self::acf_ready() || ! current_user_can( 'manage_options' ) ) {
			return $links;
		}
		array_unshift( $links, '<a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Settings', 'nera-credit-page' ) . '</a>' );
		return $links;
	}

	/**
	 * @return void
	 */
	public static function missing_acf_notice() {
		if ( self::acf_ready() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'plugins' !== $screen->id ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Nera Credit Page: the credit page editor needs ACF Pro. Until it is active, the page shows its default text.', 'nera-credit-page' ) . '</p></div>';
	}
}
