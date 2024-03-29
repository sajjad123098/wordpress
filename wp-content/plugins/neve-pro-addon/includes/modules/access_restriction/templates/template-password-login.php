<?php
/**
 * The template for password login form for restricted posts or terms.
 *
 * @package Neve_Pro\Modules\Access_Restriction\Templates
 * @since   3.6.0
 */
$container_class = apply_filters( 'neve_container_class_filter', 'container', 'single-page' );

get_header();
$current_queried_object = get_queried_object();
$context                = 'single-page';

$show_query_title = ( $current_queried_object instanceof WP_Post ) ? '' : $current_queried_object->name;
add_filter(
	'neve_filter_view_data_page-header',
	function ( $vars ) use ( $show_query_title ) {
		if ( ! empty( $show_query_title ) ) {
			$vars['string'] = $show_query_title;
		}
		return $vars;
	} 
);

?>
<div class="<?php echo esc_attr( $container_class ); ?> single-page-container">
	<div class="row">
		<?php do_action( 'neve_do_sidebar', $context, 'left' ); ?>
		<div class="nv-single-page-wrap col">
			<?php
			/**
			 * Executes actions before the page header.
			 *
			 * @since 2.4.0
			 */
			do_action( 'neve_before_page_header' );

			/**
			 * Executes the rendering function for the page header.
			 *
			 * @param string $context The displaying location context.
			 *
			 * @since 1.0.7
			 */
			do_action( 'neve_page_header', $context );

			/**
			 * Executes actions before the page content.
			 *
			 * @param string $context The displaying location context.
			 *
			 * @since 1.0.7
			 */
			do_action( 'neve_before_content', $context );

			do_action( 'neve_ar_password_login_content' );

			/**
			 * Executes actions after the page content.
			 *
			 * @param string $context The displaying location context.
			 *
			 * @since 1.0.7
			 */
			do_action( 'neve_after_content', $context );
			?>
		</div>
		<?php do_action( 'neve_do_sidebar', $context, 'right' ); ?>
	</div>
</div>
<?php get_footer(); ?>
