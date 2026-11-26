<?php
/**
 * Credit page template.
 *
 * Section markup and utility classes follow the theme's attribution page so the
 * theme bundle and child overrides scoped on .nera-attribution-page still apply.
 * Optional blocks open and close their PHP tags at column 0, so the page prints
 * the same whitespace when they are empty.
 *
 * @package Nera\CreditPage
 */

use Nera\CreditPage\Content;

defined( 'ABSPATH' ) || exit;

$nera_credit_sections = array(
	array( 'about_eyebrow', 'about_heading', 'about_body', 'bg-surface' ),
	array( 'built_eyebrow', 'built_heading', 'built_body', 'bg-secondary' ),
	array( 'ecommerce_eyebrow', 'ecommerce_heading', 'ecommerce_body', 'bg-surface' ),
);

$nera_credit_links = array(
	array( 'services_link_label', 'services_link_url' ),
	array( 'case_study_label', 'case_study_url' ),
	array( 'home_link_label', 'home_link_url' ),
);

$nera_credit_watermark   = Content::get( 'cta_watermark' );
$nera_credit_cta_lines   = Content::lines( 'cta_heading' );
$nera_credit_subtitle    = Content::get( 'cta_subtitle' );
$nera_credit_text        = Content::get( 'credit_text' );
$nera_credit_badge       = Content::get( 'credit_badge_label' );
$nera_credit_href        = Content::https_url( 'credit_url' );
$nera_credit_badge_class = "inline-flex items-center gap-2 bg-gray-50 border border-gray-200 px-4 py-2 rounded-sm text-xs font-bold tracking-widest uppercase text-text-secondary before:content-[''] before:w-2 before:h-2 before:bg-primary before:rounded-[1px] before:shrink-0";

get_header();
?>

<main id="main" class="nera-attribution-page nera-credit-page" role="main">

	<section class="py-20 lg:py-28 border-b border-gray-200 overflow-hidden relative" data-aos="fade-up" data-aos-duration="600">
		<div class="max-w-7xl mx-auto px-4 lg:px-0">
			<?php if ( '' !== Content::get( 'hero_badge' ) ) : ?>
			<div class="nera-attr-badge inline-flex items-center gap-2 bg-primary/10 border border-primary/30 text-primary text-xs font-semibold tracking-widest uppercase px-4 py-1.5 rounded-sm mb-8"><?php echo esc_html( Content::get( 'hero_badge' ) ); ?></div>
			<?php endif; ?>
			<h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight text-text-primary mb-8"><?php echo Content::heading_html( 'hero_heading' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped line by line in heading_html(). ?></h1>
			<?php if ( '' !== Content::get( 'hero_intro' ) ) : ?>
			<p class="text-lg text-text-secondary leading-relaxed max-w-2xl mb-10 font-light"><strong class="text-text-primary font-semibold"><?php echo esc_html( Content::get( 'hero_intro' ) ); ?></strong></p>
			<?php endif; ?>
			<?php if ( '' !== Content::get( 'address' ) ) : ?>
			<div class="flex flex-wrap items-center gap-x-6 gap-y-3">
				<span class="text-sm font-semibold text-text-primary tracking-wide">Nera Marketing</span>
				<span class="hidden sm:block w-px h-4 bg-gray-300"></span>
				<span class="text-sm text-text-secondary tracking-wide"><?php echo esc_html( Content::get( 'address' ) ); ?></span>
			</div>
			<?php endif; ?>
		</div>
	</section>

	<?php
	foreach ( $nera_credit_sections as $nera_credit_section ) :
		$nera_credit_eyebrow = Content::get( $nera_credit_section[0] );
		$nera_credit_heading = Content::get( $nera_credit_section[1] );
		$nera_credit_body    = Content::paragraphs( $nera_credit_section[2] );
		$nera_credit_last    = count( $nera_credit_body ) - 1;
		if ( ! $nera_credit_body ) {
			continue;
		}
		?>
	<section class="py-16 lg:py-24 border-b border-gray-200 <?php echo esc_attr( $nera_credit_section[3] ); ?>">
		<div class="max-w-7xl mx-auto px-4 lg:px-0">
			<div class="max-w-3xl mx-auto text-center" data-aos="fade-up">
				<?php if ( '' !== $nera_credit_eyebrow ) : ?>
				<span class="text-xs font-semibold tracking-widest uppercase text-primary mb-4 block"><?php echo esc_html( $nera_credit_eyebrow ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $nera_credit_heading ) : ?>
				<h2 class="text-3xl lg:text-4xl font-bold text-text-primary leading-tight mb-4"><?php echo esc_html( $nera_credit_heading ); ?></h2>
				<?php endif; ?>
				<?php foreach ( $nera_credit_body as $nera_credit_p => $nera_credit_paragraph ) : ?>
				<?php
				$nera_credit_class = 0 === $nera_credit_p
					? 'nera-attr-section-lead text-base font-medium text-text-primary leading-relaxed border-t-2 border-primary pt-4 mx-auto max-w-2xl'
					: 'text-text-secondary leading-relaxed text-sm font-light max-w-2xl mx-auto';
				if ( $nera_credit_p < $nera_credit_last ) {
					$nera_credit_class .= ' mb-4';
				}
				?>
				<p class="<?php echo esc_attr( $nera_credit_class ); ?>"><?php echo esc_html( $nera_credit_paragraph ); ?></p>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endforeach; ?>

	<?php
	$nera_credit_faqs      = Content::faqs();
	$nera_credit_faq_intro = Content::get( 'faq_intro' );
	if ( $nera_credit_faqs ) :
		?>
	<section class="py-16 lg:py-24 border-b border-gray-200 bg-secondary">
		<div class="max-w-7xl mx-auto px-4 lg:px-0">

			<?php if ( '' !== Content::get( 'faq_eyebrow' ) || '' !== Content::get( 'faq_heading' ) || '' !== $nera_credit_faq_intro ) : ?>
			<div class="mb-12" data-aos="fade-up">
				<?php if ( '' !== Content::get( 'faq_eyebrow' ) ) : ?>
				<span class="text-xs font-semibold tracking-widest uppercase text-primary mb-3 block"><?php echo esc_html( Content::get( 'faq_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== Content::get( 'faq_heading' ) ) : ?>
				<h2 class="text-3xl lg:text-4xl font-bold text-text-primary leading-tight<?php echo '' !== $nera_credit_faq_intro ? ' mb-3' : ''; ?>"><?php echo esc_html( Content::get( 'faq_heading' ) ); ?></h2>
				<?php endif; ?>
<?php if ( '' !== $nera_credit_faq_intro ) : ?>
				<p class="text-text-secondary text-sm font-light max-w-lg"><?php echo esc_html( $nera_credit_faq_intro ); ?></p>
<?php endif; ?>
			</div>
			<?php endif; ?>

			<div class="flex flex-col gap-0.5">
				<?php foreach ( $nera_credit_faqs as $nera_credit_i => $nera_credit_faq ) : ?>
				<div class="nera-attr-faq-item bg-surface border border-gray-200 overflow-hidden"
					x-data="{ open: false }"
					data-aos="fade-up"
					data-aos-delay="<?php echo esc_attr( $nera_credit_i * 60 ); ?>">
					<button
						type="button"
						@click="open = !open"
						class="w-full flex justify-between items-center gap-4 px-7 py-5 cursor-pointer text-sm font-medium text-text-primary text-left hover:text-primary transition-colors"
						:aria-expanded="open"
						aria-expanded="false">
						<?php echo esc_html( $nera_credit_faq['question'] ); ?>
						<span
							class="nera-attr-faq-icon shrink-0 w-5 h-5 border border-gray-300 rounded-full flex items-center justify-center transition-colors duration-200"
							:class="{ 'bg-primary border-primary': open }">
							<svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke-width="1.5"
								:stroke="open ? 'white' : 'currentColor'"
								:class="{ 'rotate-45': open }"
								class="transition-transform duration-200"
								aria-hidden="true">
								<line x1="5" y1="1" x2="5" y2="9"/>
								<line x1="1" y1="5" x2="9" y2="5"/>
							</svg>
						</span>
					</button>
					<div x-show="open" x-collapse x-cloak class="px-7 pb-5 text-sm text-text-secondary font-light leading-relaxed">
						<?php echo esc_html( $nera_credit_faq['answer'] ); ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php endif; ?>

	<section class="py-16 lg:py-24 bg-primary relative overflow-hidden" data-aos="fade-up">
<?php if ( '' !== $nera_credit_watermark ) : ?>
		<div class="absolute right-0 top-1/2 -translate-y-1/2 text-[200px] font-bold leading-none text-white/5 pointer-events-none select-none tracking-wide" aria-hidden="true"><?php echo esc_html( $nera_credit_watermark ); ?></div>
<?php endif; ?>
		<div class="max-w-7xl mx-auto px-4 lg:px-0 relative">
<?php if ( $nera_credit_cta_lines || '' !== $nera_credit_subtitle ) : ?>
			<div class="mb-10">
<?php if ( $nera_credit_cta_lines ) : ?>
				<h2 class="text-4xl lg:text-6xl font-bold text-white leading-tight mb-4"><?php echo implode( '<br>', array_map( 'esc_html', $nera_credit_cta_lines ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- each line escaped. ?></h2>
<?php endif; ?>
<?php if ( '' !== $nera_credit_subtitle ) : ?>
				<p class="text-white/70 text-sm font-light max-w-md"><?php echo esc_html( $nera_credit_subtitle ); ?></p>
<?php endif; ?>
			</div>
<?php endif; ?>
			<div class="flex flex-col sm:flex-row flex-wrap justify-center gap-3">
				<?php
				$nera_credit_first = true;
				foreach ( $nera_credit_links as $nera_credit_link ) :
					$nera_credit_url   = Content::get( $nera_credit_link[1] );
					$nera_credit_label = Content::get( $nera_credit_link[0] );
					if ( '' === $nera_credit_url || '' === $nera_credit_label ) {
						continue;
					}
					$nera_credit_class = $nera_credit_first
						? 'bg-surface text-primary border-white hover:bg-surface/90'
						: 'bg-transparent text-white border-white/40 hover:border-white';
					$nera_credit_first = false;
					?>
				<a href="<?php echo esc_url( $nera_credit_url ); ?>" target="_blank" rel="noopener"
					class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-xs font-bold tracking-widest uppercase rounded-sm border-2 transition-colors <?php echo esc_attr( $nera_credit_class ); ?>">
					<?php echo esc_html( $nera_credit_label ); ?>
				</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

<?php if ( '' !== $nera_credit_text || '' !== $nera_credit_badge ) : ?>
	<div class="border-t border-gray-200 bg-surface py-7">
		<div class="max-w-7xl mx-auto px-4 lg:px-0 flex flex-wrap items-center justify-between gap-4">
			<?php if ( '' !== $nera_credit_text ) : ?>
			<p class="text-xs text-text-secondary font-light"><?php echo esc_html( $nera_credit_text ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $nera_credit_badge && '' !== $nera_credit_href ) : ?>
			<a href="<?php echo esc_url( $nera_credit_href ); ?>" target="_blank" rel="noopener" class="<?php echo esc_attr( $nera_credit_badge_class ); ?> hover:border-primary hover:text-primary transition-colors"><?php echo esc_html( $nera_credit_badge ); ?></a>
			<?php elseif ( '' !== $nera_credit_badge ) : ?>
			<span class="<?php echo esc_attr( $nera_credit_badge_class ); ?>"><?php echo esc_html( $nera_credit_badge ); ?></span>
			<?php endif; ?>
		</div>
	</div>

<?php endif; ?>
</main>

<?php
get_footer();