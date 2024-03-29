<?php
/**
 * Module Class for Debug module.
 *
 * Name:    Debug fonts module.
 *
 * @version 1.0.0
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Debug;

use Neve_Pro\Core\Abstract_Module;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Debug
 */
class Module extends Abstract_Module {

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Debug'; // @phpstan-ignore-line - not used in this context. Can be removed if not used in the future.

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug        = 'debug';
		$this->name        = 'Debug';
		$this->description = 'Debug description.';

		$this->documentation = [
			'url'   => 'https://docs.themeisle.com/',
			'label' => 'Learn more',
		];

		$this->theme_min_version = '2.6';
		$this->order             = 0;
		$this->options           = [
			[
				'label'   => 'Settings group 1',
				'options' => [
					'debug_module_toggle_1' => [
						'label'   => 'Toggle 1',
						'type'    => 'toggle',
						'default' => true,
					],
					'debug_module_toggle_2' => [
						'label'   => 'Toggle 2',
						'type'    => 'toggle',
						'default' => false,
					],
					'debug_module_input_1'  => [
						'label'   => 'Input 1',
						'type'    => 'text',
						'default' => '',
					],
				],
			],
			[
				'label'   => 'Settings group 2',
				'options' => [
					'debug_module_toggle_3' => [
						'label'   => 'Toggle 1',
						'type'    => 'toggle',
						'default' => false,
					],
					'debug_module_toggle_4' => [
						'label'   => 'Toggle 2',
						'type'    => 'toggle',
						'default' => true,
					],
					'debug_module_input_2'  => [
						'label'       => 'Input 1',
						'type'        => 'text',
						'default'     => 'text',
						'placeholder' => 'Placeholder',
					],
				],
			],
		];
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	public function should_load() {
		return $this->is_active();
	}

	/**
	 * Run Header Footer Grid Module
	 */
	public function run_module() {

	}
}
