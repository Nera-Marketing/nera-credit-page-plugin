<?php
/**
 * Guide layout: hero, then one centred column with the sections, the Questions and the CTA card,
 * then the credit bar. Uses the variables prepared in templates/credit-page.php.
 *
 * Optional blocks open and close their PHP tags at column 0, so the page prints the same
 * whitespace when they are empty.
 *
 * @package Nera\CreditPage
 */

use Nera\CreditPage\Content;

defined( 'ABSPATH' ) || exit;
?>
	<section class="ncp-hero bg-gray-50 border-b border-gray-200" data-aos="fade-up" data-aos-duration="600">
		<div class="ncp-wrap">
<?php if ( '' !== $nera_credit_hero_badge ) : ?>
			<div class="nera-attr-badge inline-flex items-center gap-2 bg-primary/10 border border-primary/30 text-primary text-xs font-semibold tracking-widest uppercase px-4 py-1.5 rounded-sm"><?php echo esc_html( $nera_credit_hero_badge ); ?></div>
<?php endif; ?>
			<h1 class="ncp-hero__title font-bold text-text-primary"><?php echo Content::heading_html( 'hero_heading' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped line by line in heading_html(). ?></h1>
<?php if ( '' !== $nera_credit_hero_intro ) : ?>
			<p class="ncp-hero__intro text-text-secondary leading-relaxed font-light"><?php echo esc_html( $nera_credit_hero_intro ); ?></p>
<?php endif; ?>
<?php if ( '' !== $nera_credit_address ) : ?>
			<div class="ncp-hero__meta">
				<span class="text-sm font-semibold text-text-primary tracking-wide">Nera Marketing</span>
				<span class="ncp-hero__rule bg-gray-300" aria-hidden="true"></span>
				<span class="text-sm text-text-secondary tracking-wide"><?php echo esc_html( $nera_credit_address ); ?></span>
			</div>
<?php endif; ?>
		</div>
	</section>

	<div class="ncp-body bg-surface">
		<div class="ncp-wrap">
			<div class="ncp-col">
<?php foreach ( $nera_credit_rows as $nera_credit_row ) : ?>
				<section class="ncp-section border-b border-gray-200" data-aos="fade-up">
<?php if ( '' !== $nera_credit_row['eyebrow'] ) : ?>
					<span class="ncp-eyebrow text-primary"><?php echo esc_html( $nera_credit_row['eyebrow'] ); ?></span>
<?php endif; ?>
<?php if ( '' !== $nera_credit_row['heading'] ) : ?>
					<h2 class="ncp-section__title font-bold text-text-primary"><?php echo esc_html( $nera_credit_row['heading'] ); ?></h2>
<?php endif; ?>
<?php foreach ( $nera_credit_row['body'] as $nera_credit_p => $nera_credit_paragraph ) : ?>
					<p class="<?php echo 0 === $nera_credit_p ? 'nera-attr-section-lead ' : ''; ?>text-text-secondary font-light"><?php echo esc_html( $nera_credit_paragraph ); ?></p>
<?php endforeach; ?>
				</section>
<?php endforeach; ?>
<?php if ( $nera_credit_faqs ) : ?>
				<section class="ncp-faq" data-aos="fade-up">
<?php if ( '' !== $nera_credit_faq_eyebrow ) : ?>
					<span class="ncp-eyebrow text-primary"><?php echo esc_html( $nera_credit_faq_eyebrow ); ?></span>
<?php endif; ?>
<?php if ( '' !== $nera_credit_faq_heading ) : ?>
					<h2 class="ncp-section__title font-bold text-text-primary"><?php echo esc_html( $nera_credit_faq_heading ); ?></h2>
<?php endif; ?>
<?php if ( '' !== $nera_credit_faq_intro ) : ?>
					<p class="ncp-faq__intro text-text-secondary font-light"><?php echo esc_html( $nera_credit_faq_intro ); ?></p>
<?php endif; ?>
					<div class="ncp-faq__list">
						<?php foreach ( $nera_credit_faqs as $nera_credit_faq ) : ?>
						<div class="nera-attr-faq-item ncp-faq__item bg-surface border border-gray-200 overflow-hidden"
							x-data="{ open: false }">
							<button
								type="button"
								@click="open = !open"
								class="ncp-faq__q w-full flex justify-between items-center gap-4 cursor-pointer text-sm font-medium text-text-primary text-left hover:text-primary transition-colors"
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
							<div x-show="open" x-collapse x-cloak class="ncp-faq__a text-sm text-text-secondary font-light leading-relaxed">
								<?php echo esc_html( $nera_credit_faq['answer'] ); ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</section>
<?php endif; ?>
<?php if ( $nera_credit_has_cta ) : ?>
				<div class="ncp-cta bg-primary relative overflow-hidden" data-aos="fade-up">
<?php if ( '' !== $nera_credit_watermark ) : ?>
					<div class="absolute right-0 top-1/2 -translate-y-1/2 text-[200px] font-bold leading-none text-white/5 pointer-events-none select-none tracking-wide" aria-hidden="true"><?php echo esc_html( $nera_credit_watermark ); ?></div>
<?php endif; ?>
					<div class="ncp-cta__inner relative">
<?php if ( $nera_credit_cta_lines || '' !== $nera_credit_subtitle ) : ?>
						<div class="ncp-cta__text">
<?php if ( $nera_credit_cta_lines ) : ?>
							<h2 class="ncp-cta__title font-bold text-white leading-tight"><?php echo implode( '<br>', array_map( 'esc_html', $nera_credit_cta_lines ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- each line escaped. ?></h2>
<?php endif; ?>
<?php if ( '' !== $nera_credit_subtitle ) : ?>
							<p class="ncp-cta__subtitle text-white/70 text-sm font-light"><?php echo esc_html( $nera_credit_subtitle ); ?></p>
<?php endif; ?>
						</div>
<?php endif; ?>
<?php if ( $nera_credit_actions ) : ?>
						<div class="ncp-cta__actions">
							<?php foreach ( $nera_credit_actions as $nera_credit_n => $nera_credit_action ) : ?>
							<a href="<?php echo esc_url( $nera_credit_action[0] ); ?>" target="_blank" rel="noopener"
								class="ncp-btn <?php echo 0 === $nera_credit_n ? 'ncp-btn--primary' : 'ncp-btn--ghost'; ?> inline-flex items-center justify-center gap-2 text-xs font-bold tracking-widest uppercase rounded-sm border-2 transition-colors <?php echo 0 === $nera_credit_n ? 'bg-surface text-primary border-white hover:bg-surface/90' : 'bg-transparent text-white border-white/40 hover:border-white'; ?>">
								<?php echo esc_html( $nera_credit_action[1] ); ?>
							</a>
							<?php endforeach; ?>
						</div>
<?php endif; ?>
					</div>
				</div>
<?php endif; ?>
			</div>
		</div>
	</div>

<?php if ( '' !== $nera_credit_text || '' !== $nera_credit_badge ) : ?>
	<div class="ncp-bar border-t border-gray-200 bg-surface">
		<div class="ncp-wrap ncp-bar__inner">
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
