<?php
/**
 * Credit page template.
 *
 * Section markup and utility classes follow the theme's attribution page so the
 * theme bundle and child overrides scoped on .nera-attribution-page still apply.
 *
 * @package Nera\CreditPage
 */

use Nera\CreditPage\Content;

defined( 'ABSPATH' ) || exit;

$nera_credit_sections = array(
	array( 'about_heading', 'about_body', 'bg-surface' ),
	array( 'built_heading', 'built_body', 'bg-secondary' ),
	array( 'ecommerce_heading', 'ecommerce_body', 'bg-surface' ),
);

$nera_credit_links = array(
	array( 'services_link_label', 'services_link_url' ),
	array( 'case_study_label', 'case_study_url' ),
	array( 'home_link_label', 'home_link_url' ),
);

get_header();
?>

<main id="main" class="nera-attribution-page nera-credit-page" role="main">

	<section class="py-20 lg:py-28 border-b border-gray-200 overflow-hidden relative" data-aos="fade-up" data-aos-duration="600">
		<div class="max-w-7xl mx-auto px-4 lg:px-0">
			<h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight text-text-primary mb-8"><?php echo esc_html( Content::get( 'hero_heading' ) ); ?></h1>
			<?php if ( '' !== Content::get( 'hero_intro' ) ) : ?>
			<p class="text-lg text-text-primary leading-relaxed max-w-2xl font-light"><?php echo esc_html( Content::get( 'hero_intro' ) ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<?php
	foreach ( $nera_credit_sections as $nera_credit_section ) :
		$nera_credit_heading = Content::get( $nera_credit_section[0] );
		$nera_credit_body    = Content::get( $nera_credit_section[1] );
		if ( '' === $nera_credit_body ) {
			continue;
		}
		?>
	<section class="py-16 lg:py-24 border-b border-gray-200 <?php echo esc_attr( $nera_credit_section[2] ); ?>">
		<div class="max-w-7xl mx-auto px-4 lg:px-0">
			<div class="max-w-3xl mx-auto text-center" data-aos="fade-up">
				<?php if ( '' !== $nera_credit_heading ) : ?>
				<h2 class="text-3xl lg:text-4xl font-bold text-text-primary leading-tight mb-4"><?php echo esc_html( $nera_credit_heading ); ?></h2>
				<?php endif; ?>
				<p class="nera-attr-section-lead text-base font-medium text-text-primary leading-relaxed border-t-2 border-primary pt-4 mx-auto max-w-2xl"><?php echo esc_html( $nera_credit_body ); ?></p>
			</div>
		</div>
	</section>
	<?php endforeach; ?>

	<?php
	$nera_credit_faqs = Content::faqs();
	if ( $nera_credit_faqs ) :
		?>
	<section class="py-16 lg:py-24 border-b border-gray-200 bg-secondary">
		<div class="max-w-7xl mx-auto px-4 lg:px-0">

			<?php if ( '' !== Content::get( 'faq_heading' ) ) : ?>
			<div class="mb-12" data-aos="fade-up">
				<h2 class="text-3xl lg:text-4xl font-bold text-text-primary leading-tight"><?php echo esc_html( Content::get( 'faq_heading' ) ); ?></h2>
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
		<div class="max-w-7xl mx-auto px-4 lg:px-0 relative">
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

</main>

<?php
get_footer();