<?php
/**
 * Hidden ACF editor for the credit page.
 *
 * @package Nera\CreditPage
 */

namespace Nera\CreditPage\Admin;

use Nera\CreditPage\Content;
use Nera\CreditPage\Plugin;
use Nera\CreditPage\PrivacyCredit;

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
		add_filter( 'acf/validate_value/key=field_ncp_credit_url', array( __CLASS__, 'validate_credit_url' ), 10, 2 );
		add_filter( 'acf/load_value/key=field_ncp_faqs', array( __CLASS__, 'default_faqs' ), 20, 2 );
		add_filter( 'acf/prepare_field/key=field_ncp_privacy_status', array( __CLASS__, 'privacy_status' ) );
		add_action( 'acf/save_post', array( __CLASS__, 'before_save' ), 1 );
		add_action( 'acf/save_post', array( __CLASS__, 'after_save' ), 20 );
		add_filter( 'plugin_action_links_' . plugin_basename( NERA_CREDIT_PAGE_FILE ), array( __CLASS__, 'action_links' ) );
		add_action( 'admin_notices', array( __CLASS__, 'missing_acf_notice' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );
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
				'message' => __( '{Client} in any field is replaced with the client name. A blank badge, address, eyebrow, heading, paragraph or link label hides it on the page; a blank main heading, page title or meta description shows the default one. Questions with no rows hide the Questions section, and a row missing its question or answer is not shown. The Questions intro, CTA and Credit bar fields are empty by default, so they stay hidden until filled.', 'nera-credit-page' ),
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
				'key'           => 'field_ncp_layout',
				'label'         => __( 'Layout', 'nera-credit-page' ),
				'name'          => 'ncp_layout',
				'type'          => 'radio',
				'instructions'  => __( 'How the credit page is arranged. The text in the other tabs is the same for every layout.', 'nera-credit-page' ),
				'choices'       => self::layout_choices(),
				'default_value' => Content::DEFAULT_LAYOUT,
				'layout'        => 'horizontal',
				'return_format' => 'value',
				'allow_null'    => 0,
				'other_choice'  => 0,
			),
			array(
				'key'           => 'field_ncp_privacy_credit',
				'label'         => __( 'Add credit section to privacy policy', 'nera-credit-page' ),
				'name'          => 'ncp_privacy_credit',
				'type'          => 'true_false',
				'instructions'  => __( 'On: the privacy policy page ends with a short section linking to the credit page, unless its content already links there. Works only while "Enable credit page" is on.', 'nera-credit-page' ),
				'default_value' => 0,
				'ui'            => 1,
			),
			array(
				'key'       => 'field_ncp_privacy_status',
				'label'     => '',
				'name'      => '',
				'type'      => 'message',
				'message'   => '',
				'new_lines' => '',
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
			self::copy( 'faq_intro', __( 'Intro', 'nera-credit-page' ), 'textarea', $hides ),
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

		$fields[] = self::tab( 'cta', __( 'CTA', 'nera-credit-page' ) );
		$fields[] = self::copy( 'cta_watermark', __( 'Watermark text', 'nera-credit-page' ), 'text', __( 'Large faded word behind the button band. Leave blank to hide it.', 'nera-credit-page' ) );
		$fields[] = array_merge( self::copy( 'cta_heading', $heading, 'textarea', __( 'Each line break starts a new line. Leave blank to hide it.', 'nera-credit-page' ) ), array( 'rows' => 3 ) );
		$fields[] = self::copy( 'cta_subtitle', __( 'Subtitle', 'nera-credit-page' ), 'textarea', $hides );

		$fields[] = self::tab( 'credit', __( 'Credit bar', 'nera-credit-page' ) );
		$fields[] = self::copy( 'credit_text', __( 'Text', 'nera-credit-page' ), 'textarea', __( 'The bar at the bottom of the page shows when the text or the badge label is set. Leave blank to hide it.', 'nera-credit-page' ) );
		$fields[] = self::copy( 'credit_badge_label', __( 'Badge label', 'nera-credit-page' ), 'text', $hides );
		$fields[] = array(
			'key'          => 'field_ncp_credit_url',
			'label'        => __( 'URL', 'nera-credit-page' ),
			'name'         => 'ncp_credit_url',
			'type'         => 'url',
			'instructions' => __( 'A full https:// address makes the badge a link. Leave blank to hide the link.', 'nera-credit-page' ),
		);

		$fields[] = self::tab( 'seo', __( 'SEO', 'nera-credit-page' ) );
		$fields[] = self::copy( 'seo_title', __( 'Page Title', 'nera-credit-page' ), 'text', __( 'Browser tab title and WebPage schema name.', 'nera-credit-page' ) );
		$fields[] = array_merge( self::copy( 'seo_description', __( 'Meta description', 'nera-credit-page' ), 'textarea', '' ), array( 'rows' => 3 ) );

		return $fields;
	}

	/**
	 * Layout field choices: each layout's thumbnail and name.
	 *
	 * @return array<string,string>
	 */
	private static function layout_choices() {
		$choices = array();
		foreach ( Content::LAYOUTS as $slug => $layout ) {
			$choices[ $slug ] = '<img src="' . esc_url( NERA_CREDIT_PAGE_URL . 'assets/layout-thumbs/' . $layout['thumb'] ) . '" alt="" width="160" height="100"> <span>' . esc_html( $layout['label'] ) . '</span>';
		}
		return $choices;
	}

	/**
	 * Card styles for the layout field, on the editor screen only.
	 *
	 * @param string $hook_suffix Current admin screen.
	 * @return void
	 */
	public static function enqueue_admin_styles( $hook_suffix ) {
		if ( false === strpos( (string) $hook_suffix, self::SLUG ) ) {
			return;
		}
		wp_enqueue_style( 'nera-credit-page-layout-picker', NERA_CREDIT_PAGE_URL . 'assets/admin-layout-picker.css', array(), NERA_CREDIT_PAGE_VERSION );
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
	 * Fills the note under the privacy toggle from the saved settings, using the same checks as the page.
	 *
	 * @param array<string,mixed>|false $field Field being rendered.
	 * @return array<string,mixed>|false
	 */
	public static function privacy_status( $field ) {
		if ( ! is_array( $field ) ) {
			return $field;
		}

		$id = PrivacyCredit::privacy_page_id();
		if ( 0 === $id ) {
			$field['message'] = sprintf(
				/* translators: %s: link to Settings > Privacy. */
				esc_html__( 'No privacy policy page is set (%s), so nothing is added.', 'nera-credit-page' ),
				'<a href="' . esc_url( admin_url( 'options-privacy.php' ) ) . '">' . esc_html__( 'Settings → Privacy', 'nera-credit-page' ) . '</a>'
			);
			return $field;
		}

		$title = get_the_title( $id );
		$title = '' === $title ? '#' . $id : $title;
		$page  = '<a href="' . esc_url( get_permalink( $id ) ) . '">' . esc_html( $title ) . '</a>';

		if ( PrivacyCredit::is_present( PrivacyCredit::privacy_html( $id ) ) ) {
			$edit             = get_edit_post_link( $id, 'raw' );
			$field['message'] = esc_html__( 'Already on the privacy page: auto-add is skipped.', 'nera-credit-page' )
				. ( $edit ? ' <a href="' . esc_url( $edit ) . '">' . sprintf(
					/* translators: %s: privacy page title. */
					esc_html__( 'Edit %s', 'nera-credit-page' ),
					esc_html( $title )
				) . '</a>' : '' );
			return $field;
		}

		if ( ! Plugin::is_enabled() ) {
			/* translators: %s: linked privacy page title. */
			$field['message'] = sprintf( esc_html__( 'Turn on Enable credit page first. The section then goes at the bottom of %s.', 'nera-credit-page' ), $page );
		} elseif ( ! PrivacyCredit::is_switched_on() ) {
			/* translators: %s: linked privacy page title. */
			$field['message'] = sprintf( esc_html__( 'Off: nothing is added. Turn this on to add it at the bottom of %s.', 'nera-credit-page' ), $page );
		} else {
			/* translators: %s: linked privacy page title. */
			$field['message'] = sprintf( esc_html__( 'Will be added at the bottom of the privacy page: %s.', 'nera-credit-page' ), $page );
		}
		return $field;
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
		if ( true !== $valid || ! is_string( $value ) || '' === trim( $value ) || Content::is_https_url( $value ) ) {
			return $valid;
		}
		return __( 'The case study URL must be a full https:// address.', 'nera-credit-page' );
	}

	/**
	 * @param mixed $valid Current result.
	 * @param mixed $value Submitted value.
	 * @return mixed
	 */
	public static function validate_credit_url( $valid, $value ) {
		if ( true !== $valid || ! is_string( $value ) || '' === trim( $value ) || Content::is_https_url( $value ) ) {
			return $valid;
		}
		return __( 'The URL must be a full https:// address.', 'nera-credit-page' );
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
