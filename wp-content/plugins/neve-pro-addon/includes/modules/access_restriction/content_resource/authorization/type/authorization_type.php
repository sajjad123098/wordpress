<?php
/**
 * Authorization
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Authorization;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;

/**
 * Abstract class Authorization_Type
 * Provides a base class for all authorization types.
 */
abstract class Authorization_Type implements Authorization {
	/**
	 * Content_Resource settings.
	 *
	 * @var Resource_Settings
	 */
	protected $resource_settings;

	/**
	 * Constructor.
	 *
	 * @param  Resource_Settings $resource_settings provides methods for getting and setting the settings of the resource.
	 * @return void
	 */
	public function __construct( $resource_settings ) {
		$this->resource_settings = $resource_settings;
	}
}
