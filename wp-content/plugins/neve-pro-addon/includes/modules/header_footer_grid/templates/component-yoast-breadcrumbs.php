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

use Neve_Pro\Modules\Header_Footer_Grid\Components\Yoast_Breadcrumbs as Breadcrumbs;

$html_tag = \HFG\component_setting( Breadcrumbs::HTML_TAG );

?>
<div class="component-wrap">
	<?php do_action( 'neve_pro_hfg_breadcrumb', $html_tag, 'hfg' ); ?>
</div>
