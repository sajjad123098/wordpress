<?php
/**
 * Template used for component rendering wrapper.
 *
 * Name:    Header Footer Grid
 *
 * @version 1.0.0
 * @package HFG
 */
namespace HFG;

use Neve_Pro\Modules\Header_Footer_Grid\Components\Advanced_Search_Core;

/**
 * Filters advanced search component field styles.
 *
 * @since 2.2.0
 *
 * @param array  $styles_array The styles array.
 */
$component_styles_array = apply_filters(
	'neve_advanced_search_component_filed_styles',
	[
		'padding' => '0',
		'margin'  => '8px 2px',
	] 
);

$component_styles = '';
if ( ! empty( $component_styles_array ) ) {
	$component_styles = ' style="';
	foreach ( $component_styles_array as $key => $value ) {
		$component_styles .= sprintf( '%1$s: %2$s;', $key, $value );
	}
	$component_styles .= '" ';
}

$args = [ 'context' => 'hfg' ];

if ( Advanced_Search_Core::supports_customizable_iconbutton() ) {
	if ( component_setting( \HFG\Core\Components\Utility\SearchIconButton::BUTTON_APPEARANCE ) === 'text_button' ) {
		$args['button_text'] = component_setting( \HFG\Core\Components\Utility\SearchIconButton::BUTTON_TEXT, \HFG\Core\Components\Utility\SearchIconButton::get_default_button_text() );
	}
}

?>
<div class="component-wrap search-field">
	<div class="widget widget-search" <?php echo wp_kses_post( $component_styles ); ?> >
		<?php get_search_form( $args ); ?>
	</div>
</div>

<?php
