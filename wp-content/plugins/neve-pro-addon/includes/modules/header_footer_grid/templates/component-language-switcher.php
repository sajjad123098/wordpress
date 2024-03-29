<?php
/**
 * Template used for component rendering wrapper.
 *
 * Name:    Header Footer Grid
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Templates;

use Neve_Pro\Modules\Header_Footer_Grid\Components\Language_Switcher;
use function HFG\current_component;

$translation_plugins = array(
	Language_Switcher::WPML           => defined( 'ICL_SITEPRESS_VERSION' ),
	Language_Switcher::TRANSLATEPRESS => defined( 'TRP_PLUGIN_VERSION' ),
	Language_Switcher::POLYLANG       => defined( 'POLYLANG_VERSION' ),
	Language_Switcher::WEGLOT         => defined( 'WEGLOT_VERSION' ),
);

$selected_plugin = null;
foreach ( $translation_plugins as $key => $key_status ) {
	if ( $key_status !== true ) {
		continue;
	}
	$selected_plugin = $key;
	break;
}

?>
<div class="component-wrap">
	<?php
	if ( $selected_plugin === 'polylang' && function_exists( 'pll_the_languages' ) ) {
		$options = [
			'dropdown'               => 0,
			'show_names'             => (int) \HFG\component_setting( Language_Switcher::PLL_NAMES ),
			'show_flags'             => (int) \HFG\component_setting( Language_Switcher::PLL_SHOW_FLAGS ),
			'force_home'             => (int) \HFG\component_setting( Language_Switcher::PLL_FORCE_FP ),
			'hide_current'           => (int) \HFG\component_setting( Language_Switcher::PLL_HIDE_CURRENT ),
			'hide_if_no_translation' => (int) \HFG\component_setting( Language_Switcher::PLL_HIDE_NO_TRANSLATION ),
		];

		echo '<ul class="nv--lang-switcher nv--pll">';
		pll_the_languages( $options );
		echo '</ul>';
	}

	if ( $selected_plugin === 'translatepress' ) {
		echo '<div class="nv--lang-switcher nv--tlp">';
		echo preg_replace( '#<script(.*?)>(.*?)</script>#is', '', do_shortcode( '[language-switcher]' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	if ( $selected_plugin === 'wpml' ) {
		echo '<div class="nv--lang-switcher nv--wpml">';
		do_action(
			'wpml_language_switcher',
			array(
				'flags'      => 1,
				'native'     => 0,
				'translated' => 0,
			)
		);
		echo '</div>';
	}

	if ( $selected_plugin === 'weglot' ) {
		echo '<div class="nv--lang-switcher nv--weglot">';
		echo do_shortcode( '[weglot_switcher]' );
		echo '</div>';
	}
	?>
</div>
