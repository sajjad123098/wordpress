<?php
/**
 * Author:          Uriahs Victor
 * Created on:      27/09/2021 (d/m/y)
 *
 * @package Neve_Pro\Modules\Easy_Digital_Downloads
 */

namespace Neve_Pro\Modules\Easy_Digital_Downloads;

use Neve_Pro\Core\Generic_Style;
use Neve\Core\Styles\Dynamic_Selector;
use Neve\Core\Settings\Config;

/**
 * Class Dynamic_Style
 *
 * @package Neve_Pro\Modules\Easy_Digital_Downloads
 */
class Dynamic_Style extends Generic_Style {

	/**
	 * Typography options
	 */
	const MODS_TYPEFACE_ARCHIVE_DOWNLOAD_TITLE = 'neve_edd_archive_title_typography';   
	const MODS_TYPEFACE_ARCHIVE_DOWNLOAD_META  = 'neve_edd_archive_meta_typography';   
	const MODS_TYPEFACE_SINGLE_DOWNLOAD_TITLE  = 'neve_edd_single_title_typography';   
	const MODS_TYPEFACE_SINGLE_DOWNLOAD_META   = 'neve_edd_single_meta_typography';   

	/**
	 * 
	 * Add our dynamic CSS subscribers.
	 * 
	 * @param array $subscribers 
	 * @return array 
	 */
	public function add_subscribers( $subscribers = [] ) {

		$subscribers[] = [
			Dynamic_Selector::KEY_SELECTOR => '#nv-edd-grid-container',
			Dynamic_Selector::KEY_RULES    => [
				'--grid-cols'         => [
					Dynamic_Selector::META_KEY           => 'neve_edd_grid_columns',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_DEFAULT       => '{"desktop":3,"tablet":2,"mobile":1}',
				],
				'--grid-cols-spacing' => [
					Dynamic_Selector::META_KEY           => 'neve_edd_grid_spacing',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_DEFAULT       => '{"desktop":40,"tablet":30,"mobile":20}',
					Dynamic_Selector::META_SUFFIX        => 'px',
				],
			],
		];

		$edd_typography = [
			self::MODS_TYPEFACE_ARCHIVE_DOWNLOAD_TITLE => '.nv-edd #nv-edd-download-archive-container .nv-edd-download-title, .edd_downloads_list .edd_download_title',
			self::MODS_TYPEFACE_ARCHIVE_DOWNLOAD_META  => '#nv-edd-download-archive-container .nv-edd-download-meta, .edd_downloads_list .edd_download_excerpt p',
			self::MODS_TYPEFACE_SINGLE_DOWNLOAD_TITLE  => '#nv-single-download-container .nv-page-title h1',
			self::MODS_TYPEFACE_SINGLE_DOWNLOAD_META   => '#nv-single-download-container .nv-edd-single-download-meta',
		];

		foreach ( $edd_typography as $mod => $selector ) {
			
			$font = $mod == self::MODS_TYPEFACE_ARCHIVE_DOWNLOAD_TITLE || $mod == self::MODS_TYPEFACE_SINGLE_DOWNLOAD_TITLE ? 'mods_' . Config::MODS_FONT_HEADINGS : 'mods_' . Config::MODS_FONT_GENERAL;

			$subscribers[] = [
				'selectors' => $selector,
				'rules'     => [
					'--texttransform' => [
						Dynamic_Selector::META_KEY => $mod . '.textTransform',
					],
					'--fontweight'    => [
						Dynamic_Selector::META_KEY => $mod . '.fontWeight',
						'font'                     => $font,
					],
					'--fontsize'      => [
						Dynamic_Selector::META_KEY    => $mod . '.fontSize',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => 'px',
					],
					'--lineheight'    => [
						Dynamic_Selector::META_KEY => $mod . '.lineHeight',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
					],
					'--letterspacing' => [
						Dynamic_Selector::META_KEY    => $mod . '.letterSpacing',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => 'px',
					],
				],
			];

		}

		return $subscribers;
	}

}
