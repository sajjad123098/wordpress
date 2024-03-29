<?php
/**
 * Neve_Pro_Main class
 *
 * @package Neve_Pro\Customizer\Sections
 */
namespace Neve_Pro\Customizer\Sections;

/**
 * Class Neve_Pro_Main
 * Represents neve_pro_main customizer section.
 */
class Neve_Pro_Main extends \WP_Customize_Section {
	/**
	 * Section type
	 *
	 * @var string
	 */
	public $type = 'neve_pro_main';

	/**
	 * Render template.
	 */
	protected function render_template() {
		?>
		<li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }}">
			<div class="customize-control-notifications-container"></div>
		</li>
		<?php
	}
}
