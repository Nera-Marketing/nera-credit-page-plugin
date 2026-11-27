<?php
/**
 * Credit page template.
 *
 * Prepares the page content once, then prints the chosen layout from templates/layouts/.
 * Section markup and utility classes follow the theme's attribution page so the theme
 * bundle and child overrides scoped on .nera-attribution-page still apply.
 *
 * @package Nera\CreditPage
 */

use Nera\CreditPage\Content;

defined( 'ABSPATH' ) || exit;

$nera_credit_sections = array(
	array( 'about_eyebrow', 'about_heading', 'about_body' ),
	array( 'built_eyebrow', 'built_heading', 'built_body' ),
	array( 'ecommerce_eyebrow', 'ecommerce_heading', 'ecommerce_body' ),
);

$nera_credit_links = array(
	array( 'services_link_label', 'services_link_url' ),
	array( 'case_study_label', 'case_study_url' ),
	array( 'home_link_label', 'home_link_url' ),
);

$nera_credit_hero_badge = Content::get( 'hero_badge' );
$nera_credit_hero_intro = Content::get( 'hero_intro' );
$nera_credit_address    = Content::get( 'address' );

$nera_credit_rows = array();
foreach ( $nera_credit_sections as $nera_credit_section ) {
	$nera_credit_body = Content::paragraphs( $nera_credit_section[2] );
	if ( $nera_credit_body ) {
		$nera_credit_rows[] = array(
			'eyebrow' => Content::get( $nera_credit_section[0] ),
			'heading' => Content::get( $nera_credit_section[1] ),
			'body'    => $nera_credit_body,
		);
	}
}

$nera_credit_faqs        = Content::faqs();
$nera_credit_faq_eyebrow = Content::get( 'faq_eyebrow' );
$nera_credit_faq_heading = Content::get( 'faq_heading' );
$nera_credit_faq_intro   = Content::get( 'faq_intro' );

$nera_credit_actions = array();
foreach ( $nera_credit_links as $nera_credit_link ) {
	$nera_credit_url   = Content::get( $nera_credit_link[1] );
	$nera_credit_label = Content::get( $nera_credit_link[0] );
	if ( '' !== $nera_credit_url && '' !== $nera_credit_label ) {
		$nera_credit_actions[] = array( $nera_credit_url, $nera_credit_label );
	}
}

$nera_credit_watermark   = Content::get( 'cta_watermark' );
$nera_credit_cta_lines   = Content::lines( 'cta_heading' );
$nera_credit_subtitle    = Content::get( 'cta_subtitle' );
$nera_credit_has_cta     = '' !== $nera_credit_watermark || $nera_credit_cta_lines || '' !== $nera_credit_subtitle || $nera_credit_actions;
$nera_credit_text        = Content::get( 'credit_text' );
$nera_credit_badge       = Content::get( 'credit_badge_label' );
$nera_credit_href        = Content::https_url( 'credit_url' );
$nera_credit_badge_class = "inline-flex items-center gap-2 bg-gray-50 border border-gray-200 px-4 py-2 rounded-sm text-xs font-bold tracking-widest uppercase text-text-secondary before:content-[''] before:w-2 before:h-2 before:bg-primary before:rounded-[1px] before:shrink-0";

$nera_credit_layout      = Content::layout();
$nera_credit_layout_file = NERA_CREDIT_PAGE_DIR . 'templates/layouts/' . $nera_credit_layout . '.php';
if ( ! is_readable( $nera_credit_layout_file ) ) {
	$nera_credit_layout      = Content::DEFAULT_LAYOUT;
	$nera_credit_layout_file = NERA_CREDIT_PAGE_DIR . 'templates/layouts/' . Content::DEFAULT_LAYOUT . '.php';
}

// Only values that passed sanitize_hex_color() reach the style attribute.
$nera_credit_brand    = Content::color( 'primary' );
$nera_credit_cta_text = Content::color( 'cta_text' );
$nera_credit_style    = ( '' !== $nera_credit_brand ? '--color-primary:' . $nera_credit_brand . ';' : '' )
	. ( '' !== $nera_credit_cta_text ? '--ncp-cta-text:' . $nera_credit_cta_text . ';' : '' );

get_header();
?>

<main id="main" class="nera-attribution-page nera-credit-page ncp-layout-<?php echo esc_attr( $nera_credit_layout ); ?><?php echo '' !== $nera_credit_cta_text ? ' ncp-has-cta-text' : ''; ?>"<?php echo '' !== $nera_credit_style ? ' style="' . esc_attr( $nera_credit_style ) . '"' : ''; ?> role="main">
<?php require $nera_credit_layout_file; ?>
</main>

<?php
get_footer();
