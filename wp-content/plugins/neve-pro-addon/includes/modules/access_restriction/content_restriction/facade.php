<?php
/**
 * Facade
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Restriction
 */
namespace  Neve_Pro\Modules\Access_Restriction\Content_Restriction;

use Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer\Post;
use  Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer\Main_Query;

/**
 * Facade
 * That class is responsible for the initialization of the content restriction on the site.
 *
 * Two layers are supported:
 * - Restriction on the main query (Restrict the current route)
 * - Restriction on the post (e.g: a post is listed in a category, and the category is restricted, then the post is restricted too)
 */
class Facade {
	/**
	 * Run the content restriction layers.
	 *
	 * @return void
	 */
	public function run() {
		if ( is_admin() ) {
			return;
		}

		( new Main_Query() )->init();
		( new Post() )->init();
	}
}
