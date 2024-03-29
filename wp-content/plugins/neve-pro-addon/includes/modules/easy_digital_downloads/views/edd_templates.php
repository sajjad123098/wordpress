<?php
/**
 * Author:          Uriahs Victor
 * Created on:      27/09/2021 (d/m/y)
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Easy_Digital_Downloads\Views;

use Neve\Views\Base_View;

/**
 * Class Cart_Page
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Views
 */
class EDD_Templates extends Base_View {
	
	/**
	 * Where the template is being called from (archive or single download)
	 *
	 * @var string
	 */
	private $context = '';

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'neve_do_download_archive', array( $this, 'render_download_archive' ) );
		add_action( 'neve_do_single_download', array( $this, 'render_download_single' ) );
		add_filter( 'edd_purchase_link_defaults', array( $this, 'change_purchase_button_defaults' ) );
	}

	/**
	 * Register submodule hooks
	 */
	public function register_hooks() {
		$this->init();
	}

	/**
	 * Change Easy Digital Downloads purchase link defaults.
	 *
	 * @return array $defaults Altered defaults.
	 */
	public function change_purchase_button_defaults( $defaults ) {
		/*
		 * If Ajax add to cart is enabled, and this is an archive page, get the price setting from customizer.
		 */
		if ( get_theme_mod( 'neve_edd_archive_buy_button_type', 'go-to-download' ) === 'ajax-add-to-cart' && $this->context === 'archive-download' ) {
			$defaults['price'] = get_theme_mod( 'neve_edd_ajax_buy_button_show_price', true );
		}

		$defaults['color'] = '';
		$classes           = $defaults['class'];
		$defaults['class'] = $classes . ' nv-edd-buy-btn';
		$defaults['text']  = esc_html__( 'Buy Now', 'neve' );

		$defaults = apply_filters( 'neve_edd_purchase_link_defaults', $defaults, $this->context );

		return $defaults;

	}
	
	/**
	 * Output the price to the page.
	 * 
	 * @param mixed $id The ID of the current download in the loop.
	 * @return mixed
	 */
	private function output_price( $id ) {
		/*
		* If Ajax add to cart is enabled, and the user chose to show the price inside the button then don't show it on the page.
		*/
		if ( get_theme_mod( 'neve_edd_archive_buy_button_type', 'go-to-download' ) === 'ajax-add-to-cart' && get_theme_mod( 'neve_edd_ajax_buy_button_show_price', true ) === true ) {
			return '';
		}

		/*
		* If Ajax add to cart is enabled and this download has variable pricing, don't show the price on the page.
		*/
		if ( get_theme_mod( 'neve_edd_archive_buy_button_type', 'go-to-download' ) === 'ajax-add-to-cart' && edd_has_variable_prices( $id ) ) {
			return;
		}

		$markup  = '<div class="nv-edd-download-meta">';
		$markup .= '<p class="nv-edd-download-price">';
		if ( edd_has_variable_prices( $id ) ) {
			$markup .= edd_price_range( $id );
		} else {
			$markup .= (string) edd_price( $id, false );
		}
		$markup .= '</p>';
		$markup .= '</div>';

		return $markup;
	}

	/**
	 * Output the buy button to the page.
	 * 
	 * This button is filtered in Neve Pro.
	 * 
	 * @return string
	 */
	private function output_buy_button_type() {

		if ( get_theme_mod( 'neve_edd_archive_buy_button_type', 'go-to-download' ) === 'go-to-download' ) {
			$markup  = '<a class="button nv-edd-buy-btn" href="' . esc_url( get_permalink() ) . '">';
			$markup .= esc_html__( 'Buy Now', 'neve' );
			$markup .= '</a>';
			return $markup;
		} 
		
		// return ajax buy button
		return edd_get_purchase_link();

	}
	
	/**
	 * Render the archive content.
	 *
	 * @param string $context the context provided in do_action.
	 */
	public function render_download_archive( $context ) {
		if ( $context !== 'archive-download' ) {
			return;
		}
		$this->context = $context;
		$id            = get_the_ID();
		global $edd_download_shortcode_item_atts, $edd_download_shortcode_item_i;
		?>

		<div class="<?php echo esc_attr( apply_filters( 'edd_download_class', 'edd_download', get_the_ID(), $edd_download_shortcode_item_atts, $edd_download_shortcode_item_i ) ); ?> nv-edd-download-item" id="edd_download_<?php echo esc_attr( (string) $id ); ?>">
			<div class="<?php echo esc_attr( apply_filters( 'edd_download_inner_class', 'edd_download_inner', get_the_ID(), $edd_download_shortcode_item_atts, $edd_download_shortcode_item_i ) ); ?>">
				<?php do_action( 'edd_download_before' ); ?>
					<div class="nv-edd-download-thumbnail">
						<a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_post_thumbnail( 'large' ); ?></a>
					</div>
					<?php do_action( 'edd_download_after_thumbnail' ); ?>
					<?php do_action( 'edd_download_before_title' ); ?>
					<div class="nv-edd-download-title">
						<p>
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php the_title(); ?>
							</a>
						</p>
					</div>
					<div>
					<?php 
						do_action( 'edd_download_after_title' );
						apply_filters( 'neve_edd_archive_excerpt', '', $id ); 
						do_action( 'edd_download_after_content' );
					?>
					</div>

					<?php 
					echo wp_kses_post( $this->output_price( $id ) );
					do_action( 'edd_download_after_price', $id ); 
					?>
					<div class="nv-edd-buy-btn-wrap">
						<?php 
						// Output already escaped in function 
						echo $this->output_buy_button_type(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
						?>
					</div>
				<?php do_action( 'edd_download_after' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the single download content.
	 *
	 * @param string $context the context provided in do_action.
	 */
	public function render_download_single( $context ) {
		if ( $context !== 'single-download' ) {
			return;
		}
		$this->context    = $context;
		$id               = get_the_ID();
		$categories       = get_the_terms( $id, 'download_category' );
		$categories_count = ! empty( $categories ) ? count( $categories ) : 0;
		$tags             = get_the_terms( $id, 'download_tag' );
		?>

		<section>

			<div class="nv-page-title">

				<?php 
				/**
				 * Executes actions before the single Easy Digital Download title.
				 *
				 * @since 3.0.0
				 */
				do_action( 'neve_before_single_download_title' ); 
				?>
					<h1><?php echo wp_kses_post( get_the_title( $id ) ); ?></h1>
					<?php 
						/**
						 * Executes actions after the single Easy Digital Download title.
						 *
						 * @since 3.0.0
						 */
						do_action( 'neve_after_single_download_title' );
					?>
			</div>

			<div class="nv-title-meta-wrap mobile-left tablet-left desktop-left">
					<?php
					/**
					 * Executes actions before the single Easy Digital Download meta.
					 *
					 * @since 3.0.0
					 */
						do_action( 'neve_before_download_meta' ); 
					?>
					<div class="nv-edd-single-download-meta">
						<?php if ( ! empty( $categories ) ) { ?>
							<p><span class="nv-edd-single-download-categories"><?php esc_html_e( 'Category', 'neve' ); ?>:</span>
								<?php foreach ( $categories as $key => $category ) { ?>
									<span class="nv-edd-single-download-category">
										<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
											<?php 
												echo esc_html( $category->name );
												echo esc_html( ( $key !== ( $categories_count - 1 ) ) ? ',' : '' );
											?>
										</a>
									</span>
								<?php } ?>
							</p>
						<?php } ?>
					</div>
					<?php 
					/**
					 * Executes actions after the single Easy Digital Download meta.
					 *
					 * @since 3.0.0
					 */
					do_action( 'neve_after_download_meta' ); 
					?>
				</div>

			<!-- </div> -->
			<?php 
			/**
			 * Executes actions before the single Easy Digital Download thumbnail.
			 *
			 * @since 3.0.0
			 */
			do_action( 'neve_before_download_thumbnail' ); 
			?>
			<div class="nv-edd-featured-image nv-thumb-wrap">
				<p><?php the_post_thumbnail( 'neve-blog' ); ?></p>
			</div>
			<?php 
				/**
				 * Executes actions after the single Easy Digital Download thumbnail.
				 *
				 * @since 3.0.0
				 */
				do_action( 'neve_after_download_thumbnail' );
				/**
				 * Executes actions before the single Easy Digital Download content.
				 *
				 * @since 3.0.0
				 */ 
				do_action( 'neve_before_download_content' );
			?>
			<div class="nv-edd-content nv-content-wrap entry-content">
				<?php wp_kses_post( the_content() ); ?>
			</div>
			<?php 
			/**
			 * Executes actions after the single Easy Digital Download content.
			 *
			 * @since 3.0.0
			 */
			do_action( 'neve_after_download_content' ); 
			?>
			<?php 
			if ( ! empty( $tags ) ) {
				echo '<hr />';
			}
			?>
			<?php do_action( 'neve_before_download_meta' ); ?>
			<div class="nv-edd-single-download-meta nv-tags-list">
				<?php if ( ! empty( $tags ) ) : ?>
					<p><span class="nv-edd-single-download-tags"><?php esc_html_e( 'Download tags', 'neve' ); ?>:</span> 
						<?php foreach ( $tags as $key => $tag ) : ?>
							<span class="nv-edd-single-download-tag">
								<a href="<?php echo esc_url( get_category_link( $tag->term_id ) ); ?>">
									<?php 
										echo esc_html( $tag->name );
									?>
								</a>
							</span>
						<?php endforeach ?>
					</p>
				<?php endif ?>
			</div>
			<?php do_action( 'neve_after_download_meta' ); ?>
		</section>

		<?php

	}
}
