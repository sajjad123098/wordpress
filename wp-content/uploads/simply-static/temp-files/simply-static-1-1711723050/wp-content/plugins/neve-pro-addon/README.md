# Neve Pro Addon Plugin #
**Contributors:** [themeisle](https://profiles.wordpress.org/themeisle)  
**Tags:** neve, pro, addon  
**Requires at least:** 5.5  
**Tested up to:** 6.4  
**Requires PHP:** 7.0  
**License:** GPLv3  
**License URI:** https://www.gnu.org/licenses/gpl-3.0.en.html  

## Description ##
The pro addon plugin for [Neve theme](https://wordpress.org/themes/neve).

## License ##
The code is licensed under GPLv3.

## Changelog ##

##### [Version 2.8.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.8.3...v2.8.4) (2024-03-12)

### Bug Fixes
- **Fix Conditional Header** not allowing a different primary menu on some pages.
- **Fix border radius** fallback for 0 values when previewing in the Customizer.

### Improvements
- **Updated internal dependencies**




##### [Version 2.8.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.8.2...v2.8.3) (2024-02-22)

### Bug Fixes:
- **Fix ACF interaction when used with Custom Layout**, it will now use the values of the original post.
- **Improved compatibility** between Neve Pro and Toolset plugin

### Improvements
- **Updated internal dependencies**




##### [Version 2.8.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.8.1...v2.8.2) (2024-02-01)

### Bug Fixes
- Fixed an issue on Elementor pages that prevented proper displaying of some elements.




##### [Version 2.8.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.8.0...v2.8.1) (2024-01-30)

### Improvements
- **Updated internal dependencies**




#### [Version 2.8.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.7.3...v2.8.0) (2024-01-30)

### New Features
- **Allow filtering the heading tag of the related posts title.**
- **Custom payment icons**: You can now add your own custom payment icon.
- **SVG custom menu icon**: Allows user to change the menu icon with a custom SVG.
- **Add X (former Twitter) and Apple Music logo to Social Icons**: Added new icons to the social icons list and replace old Twitter icon in some components

### BUG Fixes
- **Mega menu submenu hover background color on column**: Previously the color was not applied correctly in Mega menu context.

### Improvements
- **Add feature usage observer**
- **Updated dependencies**




##### [Version 2.7.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.7.2...v2.7.3) (2023-12-22)

### New Features
- **WooCommerce booster** exclude hidden products from search

### Bug Fixes
- **Social icons component** display correct target attribute
- **WooCommerce 8.3** improved compatibility and support

### Improvements
- **Updated dependencies** using the latest dependencies.




##### [Version 2.7.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.7.1...v2.7.2) (2023-11-29)

### Bug Fixes
- **Loader for infinite scroll:** Added the loader for the infinite scroll on the shop page, previously it was missing.
- **Author images alt tag:** Improved the author images alt tags to now show the author of the image.
- **Custom Layouts:** Solved a regression with the Custom Layouts menu not hiding when the module is disabled.
- **Header Presets:** Adjusted cart position in header presets.
- **Custom Code:** Solved the display of the custom code editor for Custom Layouts

### Improvements
- **Tested compatibility:** Tested and improved compatibility with WooCommerce, Elementor, and WordPress latest versions.
- **E2E testing:** Improved end-to-end testing.




##### [Version 2.7.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.7.0...v2.7.1) (2023-10-05)

### Improvements
- **WooCommerce Compatibility Check**: Verified the compatibility with WooCommerce 8.1 to ensure there are no issues.
- **Elementor Compatibility Check**: Verified the compatibility with Elementor 3.17 to ensure there are no issues.

### Bug Fixes
- **Installation of plugins from module card**: Fixed the issue where the module card notifications were lacking links for installing necessary plugins.
- **EDD Module issue**: Fixed the issue where the EDD Module was not compatible with the EDD Pro plugin
- **Content Restriction**: Addressed the problem within the Content Restriction module that caused the editor to cease loading when activated on WordPress 5.5.




#### [Version 2.7.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.7...v2.7.0) (2023-09-25)

- [Feat] Add support for different numbers of related posts per row based on the device
- [Feat] Add support for custom taxonomies inside post meta
- [Feat] Add Mastodon icon
- [Feat] Add Web Developer starter site - [Gutenberg](https://demosites.io/web-developer-gb/) | [Elementor](https://demosites.io/web-developer/)
- [Fix] Access in the dashboard for admin users on multisite environments
- Compatibility check with Elementor 3.16




##### [Version 2.6.7](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.6...v2.6.7) (2023-08-22)

- [Fix] PHP error when running Neve Pro with PHP 7
- Test compatibility with Elementor 3.15
- Test compatibility with WooCommerce 8.0
- Adjusted the tested version of WordPress to 6.3
- Increased minimum required version of WordPress




##### [Version 2.6.6](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.5...v2.6.6) (2023-08-03)

- WooCommerce 7.9 compatibility check
- Improve compatibility with WPML




##### [Version 2.6.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.4...v2.6.5) (2023-07-17)

- [Fix] Duplicate Custom Layout with Polylang
- [Fix] Skip Restricted post from infinite scroll
- [Fix] Enabled compatibility with COT (Custom Orders Tabel)
- [Fix] Support link encoding
- [Fix] Mega Menu sub-menu open when using more than 4 col
- [Fix] Custom Layouts role access
- [Fix] Mega Menu full-width issue
- E2E test fix
- Updated SDK




##### [Version 2.6.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.3...v2.6.4) (2023-06-16)

- [Fix] reported error for non-admin




##### [Version 2.6.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.2...v2.6.3) (2023-06-15)

- [Feat] Supporting changes for the dedicated Neve settings page
- [Fix] Author avatar image display in custom layouts
- Test compatibility with WooCommerce 7.8
- Improved E2E test




##### [Version 2.6.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.1...v2.6.2) (2023-06-09)

- [Fix] Restrict Addon compatibility with WordPress 5.8
- [Fix] Restrict Addon compatibility with RankMath




##### [Version 2.6.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.6.0...v2.6.1) (2023-05-30)

- [Fix] Solve compatibility check
- Update SDK




#### [Version 2.6.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.8...v2.6.0) (2023-05-29)

- [Feat] Add relative CSS unit support for font size controls in the customizer
- [Feat] Add price templates for products
- [Feat] Extend Custom Layouts to WooCommerce templates
- [Feat] Access Restriction Module
- [Fix] Update the label on Otter Pro install button
- [Fix] Price element disappears from Card content elements order in specific cases
- [Fix] Hover Skin Mode, active color and hover color not working for the Page Header Menu
- [Fix] Breadcrumbs component
- [Fix] Allow independent toggle for TPC inside white label
- [Fix] Register translation for Polylang copyright
- [Fix] Scroll to the top link not crawlable
- Improve E2E test coverage
- Update the documentation link for Enabled featured image for taxonomies




##### [Version 2.5.8](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.7...v2.5.8) (2023-04-21)

- [Fix] HFG components not available if the page header is disabled




##### [Version 2.5.7](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.6...v2.5.7) (2023-04-19)

- [Feat] Add a direct link to the support form inside the Neve dashboard
- [Fix] Reading time meta not working for individual post settings
- [Fix] Author Avatar and Reading Time not available for cover mode in individual post settings by default
- [Fix] The Reading Time from the individual post setting not reflecting the value from the customizer
- [Fix] Sale tag text and percentage do not apply in the Quick View modal
- [Fix] Properly disable the page header when it is disabled from module settings
- Test compatibility with WooCommerce 7.6




##### [Version 2.5.6](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.5...v2.5.6) (2023-04-06)

- [Feat] Dynamic Icon Selection and Optional Button for HFG Advanced Search Components
- [Fix] The color for the copyright component is not updating in the customizer
- [Fix] Search form inherits the post card min-height on the search-no-result page
- [Fix] Primary menu Submenu Icon size is different in customizer vs. frontend
- [Fix] The customizer is not opening with older Neve versions (Neve v3.1 or Neve v3.2 etc.)
- [Fix] Sticky header remains on top of the lightbox of image
- [Fix] Social icons in footer
- Improve code readability




##### [Version 2.5.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.4...v2.5.5) (2023-03-22)

- [Fix] Infinite scroll on the shop page
- [Fix] Card image hover effects do not work on category archive pages
- [Fix] Sidebar patterns display issue
- Improve the development process
- Remove Neve v2.x support
- Update WooCommerce tested version to 7.5




##### [Version 2.5.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.3...v2.5.4) (2023-03-13)

- Removed legacy skin




##### [Version 2.5.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.2...v2.5.3) (2023-03-01)

- [Feat] Allow the show/hide mechanism on posts with closed comments
- [Feat] Allow custom colors to be selected inside the mega-menu module
- [Fix] Thumbnails slider section is not syncing with main image in the slider of product page
- [Fix] Fade animation issue on mobile menu
- [Fix] cart-link magic tag not correctly replaced
- [Fix] Prevent Layout toggle from displaying when the shop page displays just categories
- [Fix] Mega menu column display
- Improve the shop page when it shows categories and products
- A new notification in the Neve dashboard indicating that a license is almost expired
- Check compatibility with WooCommerce 7.4
- [Feat] Allow the show/hide mechanism on posts with closed comments
- [Feat] Allow custom colors to be selected inside the mega-menu module
- [Fix] Thumbnails slider section is not syncing with the main image in the slider of the product gallery
- [Fix] Fade animation issue on mobile menu
- [Fix] cart-link magic tag not correctly replaced
- [Fix] Prevent Layout toggle from displaying when the shop page displays just categories
- [Fix] Mega menu column display
- Improve the shop page when it shows categories and products
- A new notification in the Neve dashboard indicating that a license is almost expired
- Check compatibility with WooCommerce 7.4




##### [Version 2.5.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.1...v2.5.2) (2023-02-15)

- [Feat] Add Hide

##### [Version 2.5.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.0...v2.5.1) (2023-01-30)

- Update the tested up to version for Neve




#### [Version 2.5.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.9...v2.5.0) (2023-01-30)

- [Feat] Blog hover effects - [Learn more](https://docs.themeisle.com/article/1306-neve-blog-archive-options#image-style)
- [Feat] Custom Layout Patterns - [Learn more](https://docs.themeisle.com/article/1725-templates-in-neves-custom-layouts)
- [Feat] Custom sidebars module - [Learn more](https://docs.themeisle.com/article/1770-custom-sidebars-module-documentation)
- [Fix] Show missing license notification in the customizer
- [Fix] Activate License label from the plugin screen was replaced with Activate
- [Fix] Inside content display values
- [Fix] After Shop Loop hook is moved before pagination when Sparks is activated
- [Fix] Compatibility support added for Elementor v3.5+
- [Fix] Select

##### [Version 2.4.9](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.8...v2.4.9) (2023-01-09)

- [Feat] Add Bandcamp icon to the social component in HFG
- [Feat] Allow the installation of Otter directly from Neve's dashboard
- [Fix] Conditional header added as custom layout also appears on the search page template
- [Fix] Popup for color controls in mega menu not appearing
- [Fix] Sticky Add to Cart does not work if the Single Product Elementor template is applied
- Adjusted position of the Custom Layouts modal close button
- Test integration with WooCommerce 7.2 and update tested up to




##### [Version 2.4.8](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.7...v2.4.8) (2022-12-15)

- [Fix] Blocks module not displaying the required plugins
- [Fix] Revert menu changes to fix regressions




##### [Version 2.4.7](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.6...v2.4.7) (2022-12-14)

- [Feat] Add advanced search component support for post types created with ACF Extended
- [Fix] Notice for Otter Pro for new users
- [Fix] Social icons component throwing an error when the link is missing
- [Fix] Posts per page control not working
- [Fix] Show Available Magic Tags on Custom Layout Editor
- [Fix] Theme's header disappears when the footer is built in Elementor Pro




##### [Version 2.4.6](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.5...v2.4.6) (2022-11-29)

- [Feat] Add an option for displaying the MyAccount icon for non-logged users
- [Fix] Text color not configurable with menu skin mode 2 for sub-menus
- [Fix] Post meta compatibility with ACF
- [Fix] Woo actions not executing when a template exists
- Test WooCommerce and update the tested

##### [Version 2.4.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.4...v2.4.5) (2022-11-16)

- [Feat] Add compatibility between Custom meta and Pods fields
- [Fix] Cross-sells image width
- [Fix] Products image width when shop shortcode is used
- [Fix] Submenu border width not working
- Remove unnecessary functions for product video
- Test Neve with WooCommerce 7.0




##### [Version 2.4.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.3...v2.4.4) (2022-11-03)

- [Feat] Add Line icon to social icons component
- [Fix] Typekit font locally loading
- [Fix] Page Parent and Page Ancestor properties for custom layout
- [Fix] Compatibility between Spectra and Neve's scroll to top button
- [Fix] Tab title when installing Otter Pro
- [Fix] Template not applying without WooCommerce installed
- [Fix] Fixed the PHP editor render when switching between Code / Visual editor
- [Fix] Deprecation warnings in PHP 8.1
- [Fix] Post and Pages conditions in Custom Layouts are capped at 100 entries




##### [Version 2.4.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.2...v2.4.3) (2022-10-12)

- [Fix] Added styles for RankMath breadcrumbs.
- [Fix] Empty line between border and element when the row is sticky
- [Fix] Notice for new sharing icons until a title is set for it
- Prepare Neve Pro for WordPress 6.1




##### [Version 2.4.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.1...v2.4.2) (2022-09-27)

- [Fix] Upside down menu caret in Safari
- [Fix] Page header not appearing when expanding its section
- [Fix[ Submenu looks different in customizer by default
- [Fix] Performace issue caused by Advanced search components




##### [Version 2.4.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.4.0...v2.4.1) (2022-09-19)

- [Fix] Missing PHP editor in custom layouts
- [Fix] Added an additional check for custom layouts role capability
- [Fix] Custom layout sidebar not working
- [Fix] Missing conditions when setting a custom layout




#### [Version 2.4.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.6...v2.4.0) (2022-09-19)

- [Feat] Advanced style controls for sub menus - [Learn more](https://docs.themeisle.com/article/1226-header-primary-menu)
- [Feat] Improve Custom Layouts experience - [Learn more](https://docs.themeisle.com/article/1062-custom-layouts-module)
- [Feat] Responsive controls for Social Icons - [Learn more](https://docs.themeisle.com/article/1288-the-social-icons-component)
- [Feat] Add Advanced search icon header component - [Learn more](https://docs.themeisle.com/article/1715-the-advanced-search-icon-component-neve-header-footer-builder)
- [Feat] Add Advanced search form header component - [Learn more](https://docs.themeisle.com/article/1714-the-advanced-search-form-component-neve-header-footer-builder)
- [Feat] Add Finance Blog starter site - [Gutenberg](https://demosites.io/finance-blog-gb/)
- [Fix] Prevent other users than the Administrator and Editor from creating a Custom layout
- [Fix] Social icons not appearing without setting a background color
- [Fix] Issues related to Sparks installation from Neve dashboard




##### [Version 2.3.6](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.5...v2.3.6) (2022-08-31)

- [Fix] Compatibility issue between Sparks Variation Swatches & Neve Pro infinite products mechanism
- [Fix] Regression-related compatibility to Sparks Comparison Table Sticky Bar & Neve Pro Sticky Add to Cart
- [Fix] Admin notice related to Woo Booster




##### [Version 2.3.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.4...v2.3.5) (2022-08-18)

- [Feat] Introduce two new conditions for Custom Layouts: page parent and page ancestor
- [Fix] Custom Layout Sidebar not working on page




##### [Version 2.3.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.3...v2.3.4) (2022-07-26)

- [Feat] Add support for hiding archive titles for custom post types
- [Fix] Product gallery style
- [Fix] Sharing icons not appearing without background and color




##### [Version 2.3.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.2...v2.3.3) (2022-07-20)

- [Feat] Enhanced controls for related posts
- [Feat] Add settings for Polylang inside the language switcher component
- [Feat] Add support for discovering late-registered custom post types in the Post type enhancements module
- [Fix] Meta on custom post types not displaying anything until the control in the customizer is changed
- [Fix] Shop and product pages if elementor template is installed
- [Fix] Hide doc section when Whitelabel is active
- [Fix] Broken install link for sparks in dashboard
- [Fix] Post type enhancements header layout does not inherit post header layout
- [Fix] Product header affected by the post type enhancements module
- [Fix] PHP notice on payment icons if Sparks is not activated




##### [Version 2.3.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.1...v2.3.2) (2022-07-11)

- [Fix] Notice behavior in the dashboard
- [Fix] Possible error with Neve Pro and Otter Pro




##### [Version 2.3.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.3.0...v2.3.1) (2022-07-11)

- [Fix] ACF field value not displayed in Meta
- [Fix] Related posts read more button is trimmed in some cases
- Improve the development process
- Move the WooCommerce Booster module to Sparks for the WooCommerce plugin




#### [Version 2.3.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.2.5...v2.3.0) (2022-06-23)

- [Feat] Custom Sidebars using custom layout
- [Feat] Enhance sharing icons section both on single posts and on custom post types
- [Feat] New Global custom layout option
- [Feat] Custom meta integration with ACF, Toolset, and Metabox
- [Feat] Adds support for saving local fonts to the server for any plugin which enqueues fonts
- [Feat] Card Style for the Blog List Layout
- [Feat] Add a way to translate the logo with Polylang
- [Feat] Add Architecture Studio starter site - [Gutenberg](https://demosites.io/architecture-studio-gb/)
- [Feat] Add Recipe Blog starter site - [Gutenberg](https://demosites.io/recipes-blog-gb/) | [Elementor](https://demosites.io/recipes-blog/)
- [Feat] Add Real Estate Agency starter site - [Gutenberg](https://demosites.io/real-estate-agency-gb/)
- [Feat] Add NFT Illustrator starter site - [Gutenberg](https://demosites.io/nft-illustrator-gb/)
- [Feat] Add Medicare starter site - [Gutenberg](https://demosites.io/medicare-gb/) | [Elementor](https://demosites.io/medicare/)
- [Fix] Control out of the accordion
- [Fix] Add a check for Custom Layouts when using the custom editor
- [Fix] Post items order controls affect shop items order
- [Fix] Console error when the transparent header and sticky are turned on
- [FIx] Related post's colors are overwritten by card style colors from blog
- We have started to add a new generation of starter sites called Expert Sites, which are more complex and cover a more variety of sections and integrations for each niche. Furthermore, we have started to review and convert some of the older ones to Expert Sites and you will see them available soon. To support this change, you will see a lighter starter site collection than we had in the past as we want to focus on a smaller but more powerful collection of ready-to-use sites which we can constantly update. You will still be able to access the legacy starter sites by [following this doc](https://docs.themeisle.com/article/1613-where-do-i-find-the-legacy-the-starter-sites), but keep in mind that those will no longer receive updates or be tested with the latest versions.




##### [Version 2.2.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.2.4...v2.2.5) (2022-05-25)

- [Fix] Multiple inside layouts not displaying
- [Fix] Compatibility issue with Ninja Forms
- [Fix] Products Quick View not working with plain permalinks
- [Fix] Mega menu colors
- [Fix] Compatibility between Pods - Custom Content Types and Fields and Post type enhancements module
- [Fix] Variation swatches image upload issue in WP 6.0
- [Fix] Conflict with BuddyPress post update button and WooCommerce Booster module




##### [Version 2.2.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.2.3...v2.2.4) (2022-05-12)

- [Feat] Added responsive control for the author content alignment
- [Fix] Fixed the alignment and styles for the author box
- [Fix] Improve the WooBooster module performance by not having unnecessary polyfills as part of the woocommerce_booster script file
- [Fix] Non-Latin character usage on custom product tabs
- [Fix] Rollback button does not appear in some cases
- Move the Block Editor Booster module in Otter Pro




##### [Version 2.2.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.2.2...v2.2.3) (2022-05-05)

- [Feat] Gradient support for color controls
- [Feat] Add TikTok icons in the social media icons component
- [Fix] HTML/CSS Validation Issues reported by Nu Html Checker
- Refactor the way we mark blocks as top-level blocks to improve TTFB when using the performance module




##### [Version 2.2.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.2.1...v2.2.2) (2022-04-20)

- [Feat] Add Xing to the icon list
- [Feat] More conditionals for custom layouts and headers
- [Fix] Content padding for grid elements the same no matter the unit
- [Fix] Switching product card layout in the customizer
- [Fix] Handle edge cases for the Instagram Elementir widget
- [Fix] Seamless add to cart adds two products in the WooCommerce Product Bundles plugin
- [Fix] Spacing on comparison button
- [Fix] Handle sale percentage for variable products
- [Fix] When using Learndash LMS, context would default to post for the page
- [Fix] Performance module display for related products




##### [Version 2.2.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.2.0...v2.2.1) (2022-04-05)

- [Feat] Add a new filter to allow users to enable quantity input in the quick view modal.
- [Fix] After rollback the update button doesn't appear in some situations.
- [Fix] Auto-update link not appearing after installing the latest version.
- [Fix] PHP warning due to React controls.
- [Fix] Column layout display on mega-menu.
- [Fix] Some Typekit fonts are not working.
- Trigger a JS event after loading a new post in the infinite scroll on the single page.




#### [Version 2.2.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.6...v2.2.0) (2022-03-23)

- [Feat] Allow direct SVG input on icons components. [Learn more](https://docs.themeisle.com/article/1233-cart-icon#general)
- [Feat] Post Type enhancements module. [Learn more](https://docs.themeisle.com/article/1505-neve-post-type-enhancements-module)
- [Feat] Page header individual display. [Learn more](https://docs.themeisle.com/article/1262-neve-page-header)
- [Feat] Easy Digital Downloads module. [Learn more](https://docs.themeisle.com/article/1480-neve-easy-digital-downloads-booster)
- [Feat] Enhanced options for blog featured post. [Learn more](https://docs.themeisle.com/article/1306-neve-blog-archive-options#latestposts)
- [Fix] Contact component when adding quotes.
- [Fix] Solve sticky header displaying on top of WooCommerce lightbox.
- [Fix] Masonry Layout breaks Blog Layout.
- [Fix] Stepped checkout at order review step when changing the shipping method.
- [Fix] Compatibility between otter 2 and product tab manager.




##### [Version 2.1.6](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.5...v2.1.6) (2022-02-23)

- [Feat] Product-based restriction added to a custom thank you page
- [Fix] Updated method visibility to improve php8.1 compatibility
- [Fix] Priority field of the custom layout wasn't working with other functions hooked there
- [Fix] Add a smooth transition for the non-sticky header
- [Fix] Console error in Customizer environment
- [Fix] Product sliders of Essential Addons for Elementor




##### [Version 2.1.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.4...v2.1.5) (2022-02-07)

- [Feat] Added more icons to scroll to top
- [Feat] Allow different navigation menu on each additional Primary Menu component
- [Fix] Quick view button in mobile view on product card image automatically gets triggered on touch
- [Fix] Comparison table compatibility with Otter Blocks
- [Fix] Admin bar drop-downs getting overlapped by sticky navigation
- Add action links to plugin card on plugins dashboard page




##### [Version 2.1.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.3...v2.1.4) (2022-01-20)

- [Fix] Range control's design broken in customizer on WordPress 5.9
- Supporting changes for logo based on palette switcher




##### [Version 2.1.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.2...v2.1.3) (2022-01-06)

- [Fix] Error on the search page with no results when comparison table is active
- [Fix] Show hide comments button on single post infinite scroll
- [Fix] Trying to edit a post that doesn't exist produce a notice in the backend when announcement bars are enabled




##### [Version 2.1.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.1...v2.1.2) (2021-12-23)

- [Fix] Missing icons in the dashboard for Elementor widgets.
- [Fix] Product navigation works through all products, not just between products in the same category.
- [Fix] Prevent Comparison table scripts to load on pages where it's not required.
- [Fix] Compare button on the cart page.
- [Fix] Off-canvas cart not opening in some cases
- [Fix] The caret for the mega menu is not doing anything in the mobile sidebar
- Improve UX for users when they configure the scroll to the top section.




##### [Version 2.1.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.1.0...v2.1.1) (2021-12-09)

- [Feat] Custom Thank

#### [Version 2.1.0](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.7...v2.1.0) (2021-11-24)

- [Feat] - WooCommerce Booster advanced product review - [Learn more](https://docs.themeisle.com/article/1479-advanced-product-review-in-neve)
- [Feat] - Elementor Booster Instagram widget - [Learn more](https://docs.themeisle.com/article/1474-instagram-widget-elementor-booster)
- [Feat] - New divider component for header/footer builder - [Learn more](https://docs.themeisle.com/article/1488-header-divider-pro)
- [Feat] - New widget component for header/footer builder - [Learn more](https://docs.themeisle.com/article/1287-the-widget-component)
- [Feat] - Header Booster mega menu options
- [Feat] - WooCommerce tabs manager




##### [Version 2.0.7](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.6...v2.0.7) (2021-11-10)

- [Fix] Performance module features continue to take action when the main module is disabled
- [Fix] Custom layouts not working inside content
- [Fix] Sticky header/footer script loading when not needed in edge case
- [Fix] Elementor custom field widget is not displaying the meta fields
- [Fix] Unicode characters displayed as encoded on conditional headers
- [Fix] Conditional headers JSON data getting corrupted in some cases
- [Fix] Conditional headers not saving after first publishing in the customizer




##### [Version 2.0.6](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.5...v2.0.6) (2021-10-26)

- [Feat] Product Tabs Support on Sticky Add to Cart - [Learn more](https://docs.themeisle.com/article/1058-woocommerce-booster-documentation#single-product-sticky-add-to-cart)
- [Feat] Archive backgrounds support, select featured images backgrounds for each term for the selected taxonomies -  [Learn more](https://docs.themeisle.com/article/1245-header-rows-settings#archives)
- [Feat] New custom layout option as inside content - [Learn more](https://docs.themeisle.com/article/1062-custom-layouts-module#custom-hook)
- [Feat] Ability to add a custom hook - [Learn more](https://docs.themeisle.com/article/1062-custom-layouts-module#inside-content)
- [Feat] Add more hooks for custom layouts
- [Feat] Add Nonprofit starter site - [Gutenberg](https://demosites.io/nonprofit-gb/ ) | [Elementor](https://demosites.io/nonprofit/)
- [Feat] Add Audio Books starter site - [Gutenberg](https://demosites.io/audio-books-gb/) | [Elementor](https://demosites.io/audio-books/)
- [Feat] Add Business starter site - [Gutenberg](https://demosites.io/business-gb/) | [Elementor](https://demosites.io/business/)
- [Fix] Missing icons for widgets in Elementor booster module
- [Fix] Add conditional checks before trying to load the content from files generated by custom layout's PHP Editor
- [Fix] Variation Swatches JS error on WooCommerce category page
- Change labels in stepped checkout for better translations




##### [Version 2.0.5](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.4...v2.0.5) (2021-10-11)

- [Feat] Adds variations swatches support on shop catalog
- [Feat] Add Block Editor (Gutenberg) Module - [Learn More](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module)
- [Feat] [Reviews comparison block](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#comparereview)
- [Feat] [Product Comparison block](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#compareproducts)
- [Feat] [Popup block](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#popup)
- [Feat] [Add to Cart block](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#addtocart)
- [Feat] [Business Hours block](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#businesshours)
- [Feat] [Display conditions for blocks](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#conditionalloading)
- [Feat] [Sync Product Review block data with WooCommerce Products](https://docs.themeisle.com/article/1473-neve-block-editor-booster-module#importandsync)
- [Feat] WooCommerce variable products quick view support
- [Feat] Add expiration date options for custom layouts
- [Feat] Adds granular control for Page Header inside Neve Options page
- [Fix] WooCommerce tabs (reviews, description) on Single Product page disappears with Polylang plugin enabled and WooCommerce Booster enabled
- [Fix] My account links dropdown isn't working on mobile
- [Fix] Remove CSS code for drop form fields & labels legacy spacing controls
- [Fix] Font-family inheritance on my account
- [Fix] Variation swatches were not clickable in some edge cases




##### [Version 2.0.4](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.3...v2.0.4) (2021-09-22)

- [Feat] Add infinite scroll for single posts - [Learn more](https://bit.ly/nv-sp-inf)
- [Feat] Connect comparison page setup to Woocommerce core setup pages functionality
- [Fix] Compatibility with Woocommerce composite product
- [Fix] On the custom post layout to support infinite scroll hooks
- [Fix] Products image width in the list layout
- [Fix] Compare button not working in the exclusive products
- [Fix] Don't load comparison table scripts and views if AMP is enabled
- [Fix] Tooltip under sale tag
- [Fix] Comparison table compatibility with WC products shortcodes




##### [Version 2.0.3](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.2...v2.0.3) (2021-09-06)

- [Fix] Cart Total Box and Fixed Total Box overlapping the footer when sticky
- [Fix] Show placeholder image in quick view modal if the product has no images
- [Fix] Show

##### [Version 2.0.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.1...v2.0.2) (2021-08-23)

- [Fix] Sticky order total box on the side-by-side cart
- [Fix] Related posts have featured images without width and height
- [Fix] Notice when the comparison page is being deleted
- [Fix] Product breadcrumbs not showing up
- [Fix] Checkout duplicated title
- [Fix] Re-design for comparison table max product notice
- [Fix] Disable compare button from quick view
- [Fix] Disable the variation swatches options according to the stock availability or attribute dependencies
- [Feat] Enhanced repeater control for Customizer settings




##### [Version 2.0.1](https://github.com/Codeinwp/neve-pro-addon/compare/v2.0.0...v2.0.1) (2021-08-06)

- [Fix] transparent/sticky header
- [Fix] notice that amp check was called too soon
- [Fix] image swipe effect goes over product card buttons




#### [Version 2.0.0](https://github.com/Codeinwp/neve-pro-addon/compare/v1.5.5...v2.0.0) (2021-08-04)

- [Feat] Add compatibility with Neve 3.0 new default skin - [Learn more](https://docs.themeisle.com/article/1388-neve-3-0-core-design)
- [Feat] Add product comparison table on new skin - [Learn more](https://docs.themeisle.com/article/1365-comparison-table-in-neve)
- [Feat] Additional customizer options for the single post on new skin
- [Feat] New performance module - [Learn more](https://docs.themeisle.com/article/1366-performance-module-documentation)
- [Fix] Image sizes of related products were updated as full-width
- [Fix] Deprecation notice for allowed_block_types filter in 5.8
- [Fix] Single post navigation not showing posts in proper order
- [Fix] Fix variation swatches color-picker in WooCommerce versions lower than 5.0
- [Fix] Products filtering customizer option not working
- [Fix] Disable outside sale tag for products in the related products slider
- [Fix] Announcement bars style inside the editor
- [Fix] Compatibility between YITH Badge Management and Neve Pro swipe next product image style
- [Fix] Compatibility with YITH Brands Add-on Premium
- [Fix] Conflict with Happy Addons inside Elementor
- Refactor conditional headers controls




##### [Version 1.5.5](https://github.com/Codeinwp/neve-pro-addon/compare/v1.5.4...v1.5.5) (2021-05-31)

- [Feat] Add wishlist on the single product
- [Fix] Buttons in the stepped checkout were behaving like a secondary button, thus it caused a font-weight overriding problem
- [Fix] When setting padding values in Blog/Archive layout - only PX can be used but not EM
- [Fix] Background on quantity field on sticky add to cart should inherit the right global color
- [Fix] Smooth scroll instead of snapping to the top on Safari
- [Fix] Individual custom layout Elementor duplicating footer
- [Fix] Do not load dynamic styles of Neve Pro modules when the module is disabled




##### [Version 1.5.4](https://github.com/Codeinwp/neve-pro-addon/compare/v1.5.3...v1.5.4) (2021-05-14)

- [Fix] Fatal error that related to WooCommerce Rest API




##### [Version 1.5.3](https://github.com/Codeinwp/neve-pro-addon/compare/v1.5.2...v1.5.3) (2021-05-12)

- [Fix] Warning that occurs when the quick view opens
- [Fix] WooCommerce Wishlist plugin was causing an error in Quick View
- [Fix] Elementor deprecated classes and repeater controls errors
- [Fix] Sticky header overlapping admin bar dropdowns
- [Fix] Activate Related Product Slider according to the column number
- [Fix] Load WooCommerce dynamic styles conditionally if they are needed




##### [Version 1.5.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.5.1...v1.5.2) (2021-04-28)

- [Fix] HTML component does not render shortcodes in Page Header
- [Fix] Color picker inputs from Overlay Color (Blog options) are not bound to the slider
- [Fix] WooBooster - Page scrolls to top when swiping images in Quick view
- [Fix] Sticky add to cart scroll freezing
- [Fix] WooCommerce Booster breaks the Woocommerce category pages and single product pages if PHP 8.0 is used (Fatal error)
- [Feat] implement next page links for infinite scroll
- Improve sticky cart behavior by scrolling to form when there are not variations selected




##### [Version 1.5.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.5.0...v1.5.1) (2021-04-13)

- [Fix] Regression over-escaping for Elementor widgets




#### [Version 1.5.0](https://github.com/Codeinwp/neve-pro-addon/compare/v1.4.3...v1.5.0) (2021-04-12)

- [Fix] Font weight for WooCommerce typography
- [Fix] Blog posts separator toggle not working
- [Fix] Custom Layouts conditional form not appearing
- [Fix] Mobile menu blocked by admin bar when logged in
- [Fix] Show hook names in the Custom Layouts hook selector
- [Fix] Show hook names in the hooks preview mode
- [Fix] Cart side-by-side layout breaking cart notices
- [Feat] Added support for shortcodes in cart notice text
- [Feat] Allow hosting Adobe TypeKit fonts locally
- [Feat] Add WeGlot support in the language switcher header component
- Cart notices UX improvements
- WordPress config for license support




##### [Version 1.4.3](https://github.com/Codeinwp/neve-pro-addon/compare/v1.4.2...v1.4.3) (2021-03-16)

- [Fix] product details duplicated when products shortcode is used in Elementor
- [Fix] custom layouts conditionals not showing in the editor




##### [Version 1.4.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.4.1...v1.4.2) (2021-03-01)

- [Feat] Adds Elementor widget that displays a custom layout of type individual
- [Feat] Adds the nv-custom-layout shortcode that displays a custom layout of type individual
- [Fix] WooCommerce products results from count when the infinite scroll is enabled not updating
- [Fix] Remove spaces from phone link in the header contact component
- [Fix] Remove product description padding if there is no box shadow for the product




##### [Version 1.4.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.4.0...v1.4.1) (2021-02-10)

- [Fix] Shop columns CSS gets overridden with Autoptimize on
- [Fix] Elementor editor conflict with Cart Notices
- [Fix] Notices when there are no memberships with LifterMLS
- [Fix] Image coming from {author_avatar} magic tag in Custom Layouts
- [Fix] 404 page created with Custom Layout not translatable with WPML
- [Fix] Custom Layouts not editable with Beaver Builder
- [Feat] New Gutenberg starter sites
- [Fix] Accessibility for scroll to top button
- [Fix] Off-canvas cart inherits site background-color
- [Fix] Recently viewed products not working with Elementor Pro single product templates




#### [Version 1.4.0](https://github.com/Codeinwp/neve-pro-addon/compare/v1.3.2...v1.4.0) (2021-01-18)

- [Feat] Cart notices
- [Fix] PHP editor not working in the Custom Layouts
- [Fix] Whitelabel global color names




##### [Version 1.3.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.3.1...v1.3.2) (2020-12-21)

- [Fix] Added rel=noopener to Social Sharing links on single post pages
- [Fix] Sticky header scripts not loading in the Customizer
- [Fix] Breadcrumbs for SEOPress and RankMath plugins
- [Fix] Composer requiring PHP greater than 7.1.0
- [Fix] Recently viewed products not working
- [Fix] Sale tag alignment
- [Fix] Add to cart button alignment in the Quick View popup




##### [Version 1.3.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.3.0...v1.3.1) (2020-12-07)

- [Fix] Compatibility with WP 5.6
- [Fix] Add to cart button for variable products in the Quick View popup




#### [Version 1.3.0](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.12...v1.3.0) (2020-11-24)

- [Feat] New layouts for the Checkout page ( Standard, Vertical, and Stepped )
- [Feat] New CSS and JS methods of loading the Typekit fonts
- [Feat] New option to enable/disable Scroll to top from Customizer
- [Fix] Custom Layouts conditional logic for the Shop page
- New Gutenberg Starter Sites




##### [Version 1.2.12](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.11...v1.2.12) (2020-11-02)

- [Fix] Related products layout
- [Fix] Registered callbacks priorities order for the woocommerce_single_product_summary hook




##### [Version 1.2.11](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.10...v1.2.11) (2020-10-19)

- [Fix] Layout options not applying for the [lifterlms_courses] shortcode
- [Fix] Add to cart button behavior on mobile
- [Fix] Quick view popup overlapping fixed Cart Total box on the Cart page
- [Fix] Overlapping underline style for Elementor links
- New Gutenberg Starter Sites




##### [Version 1.2.10](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.9...v1.2.10) (2020-10-01)

- [Feat] New Blog layout options
- [Feat] Sticky add to cart
- [Feat] New Header Builder presets in the WooBooster
- [Fix] Custom Layouts compatibility with the Gutenberg plugin
- [Fix] Page Header builder interfering with conditional headers
- [Fix] Color options for the My Account and Wishlist components
- [Fix] Wrong number for WooCommerce orders count when using conditional headers




##### [Version 1.2.9](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.8...v1.2.9) (2020-09-17)

- [Fix] [Elementor Booster] Content Switcher HTML Tag
- [Fix] Icon spacing & border-radius default values in the Social Icons component
- [Fix] Seamless add to cart checked icon not properly aligned
- [Fix] Duplicated CSS rules from the pro modules
- New Gutenberg Starter Sites




##### [Version 1.2.8](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.7...v1.2.8) (2020-09-04)

- [Feat] New Custom Layout component for the Header/Footer Builder ( for Individual Custom Layouts )
- [Fix] Compatibility with WooCoomerce Extra Product Options plugin
- New Gutenberg Starter Sites




##### [Version 1.2.7](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.6...v1.2.7) (2020-08-24)

- [Fix] Compatibility between Custom Layouts and the latest version of Brizy
- [Fix] Make Related Posts images clickable
- [Fix] Hover Opacity control of the Banner Elementor widget
- New Gutenberg Starter Sites




##### [Version 1.2.6](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.5...v1.2.6) (2020-08-04)

- [Feat] Allow HTML tags in the header Contact component
- [Feat] New magic tags for single post pages in Custom Layouts
- [Fix] Compatibility between Elementor Booster and WPML
- [Fix] Layout for the Off canvas cart
- [Fix] Payment Icons component behaviour when WooCommerce Booster is deactivated
- [Fix] Images not visible on the Shop page when Layout is list and Force same image height is checked




##### [Version 1.2.5](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.4...v1.2.5) (2020-07-08)

- [Feat] New options for the Scroll to top module ( image, label, padding, AMP compatibility, accessibility )
- [Feat] Improved layout for the White Label module options
- New Starter Sites
- [Sports Academy](https://themeisle.com/demo/?theme=Sports%20Academy)
- New Gutenberg Starter Sites




##### [Version 1.2.4](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.3...v1.2.4) (2020-06-23)

- [Feat] New typography controls for the Shop and Single Product pages
- [Feat] New layout styles for Categories on the Shop page
- [Feat] New multi-select control to chose the position of the Page Header
- [Feat] Keep infinite scroll state on the Shop page
- [Feat] Refactor LifterLMS customizer controls
- [Feat] Improved licensing mechanism
- [Fix] Buttons not working in the Off-canvas Cart mode
- New Brizy Starter Sites




##### [Version 1.2.3](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.2...v1.2.3) (2020-06-09)

- [Fix] Sticky header on Desktop
- [Fix] Transparent header




##### [Version 1.2.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.1...v1.2.2) (2020-06-04)

- [Feat] Responsive sticky header
- [Feat] Color control for the footer Payment Icons component
- [Fix] Transparent header layout while the page is loading
- [Fix] Better handle license validation mechanism
- [Fix] Custom Layouts translatable using Polylang
- [Fix] My Account component layout when using the Gutenberg plugin
- [Fix] Archive Taxonomy and Archive Term conditionals in the Custom Layouts
- [Fix] Icon size control for the Cart icon component
- [Fix] Broken Speed and Video Agency Starter Sites
- [Fix] Turning OFF breadcrumbs was hiding the products filtering and layout toggle on the Shop page
- [Fix] Infinite scroll on categories pages containing subcategories
- [Fix] Page title not updating on the My Account page based on the active subpage
- [Fix] Scroll to top not showing on mobile devices
- New Starter Sites
- [Leather Shop](https://themeisle.com/demo/?theme=Leather%20Shop)
- [Public Notary](https://themeisle.com/demo/?theme=Public%20Notary)
- [Pharmacy](https://themeisle.com/demo/?theme=Pharmacy)
- [Investment Consulting](https://themeisle.com/demo/?theme=Investment%20Consulting)
- New Beaver Builder and Gutenberg Starter Sites




##### [Version 1.2.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.2.0...v1.2.1) (2020-05-21)

- [Fix] Wrong font-size unit for the My Account component
- [Fix] Icon color control for the Contact component
- [Fix] Mobile alignment on ordering area on the Shop page
- [Fix] Improved Yoast breadcrumbs appearance mechanism
- New Starter Sites
- [Laundry Services](https://themeisle.com/demo/?theme=Laundry%20Services)
- [Craft Beer](https://themeisle.com/demo/?theme=Craft%20Beer)
- [Gardening](https://themeisle.com/demo/?theme=Gardening)
- [Resume 2](https://themeisle.com/demo/?theme=Resume%202)
- New Beaver Builder and Gutenberg Starter Sites




#### [Version 1.2.0](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.10...v1.2.0) (2020-05-13)

- [Feat] Improved Neve Options page and onboarding experience
- [Fix] Forcing product thumbnails height affects related products
- [Fix] Custom Layouts using the wrong content filter
- [Fix] Certain Typekit fonts not loading
- [Fix] Missing .pot file
- [Fix] Author avatar size on mobile
- [Fix] View Cart button on mobile
- New starter site
- Copywriter - https://themeisle.com/demo/?theme=Copywriter
- New Gutenberg and Beaver Builder Starter Sites




##### [Version 1.1.10](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.9...v1.1.10) (2020-04-06)

- [Fix] Force Product Image Height maximum value back to 500px
- [Fix] Infinite scroll on product terms
- [Fix] PHP editor in Custom layouts in WP 5.4
- New Elementor Starter Sites
- New Gutenberg Starter Sites




#### [Version 1.1.9](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.8...v1.1.9) (2020-03-23)

* **Bug Fixes**

   * php7.4 warnings [closes [#584](https://github.com/Codeinwp/neve-pro-addon/issues/584)] ([468b201](https://github.com/Codeinwp/neve-pro-addon/commit/468b201))
   * view cart button inline content on product card ([320d58f](https://github.com/Codeinwp/neve-pro-addon/commit/320d58f)), closes		[#588](https://github.com/Codeinwp/neve-pro-addon/issues/588)

* **Features**

   * adds compatibility with newest sdk ([e743cb7](https://github.com/Codeinwp/neve-pro-addon/commit/e743cb7))
   * Customizer Controls UI refactoring ([127631e](https://github.com/Codeinwp/neve-pro-addon/commit/127631e))
   * Customizer Controls UI refactoring ([dcf575e](https://github.com/Codeinwp/neve-pro-addon/commit/dcf575e))
   * new Starter Sites ([64a2b34](https://github.com/Codeinwp/neve-pro-addon/commit/64a2b34))



#### [Version 1.1.8](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.7...v1.1.8) (2020-02-26)

* **Bug Fixes**

   * change priority of contextual enqueue and de-enqueue on hfg booster script [closes [#537](https://github.com/Codeinwp/neve-pro-addon/issues/537)] ([0f6607b](https://github.com/Codeinwp/neve-pro-addon/commit/0f6607b))
   * changing plugin folder were blocking module activation, fix [#461](https://github.com/Codeinwp/neve-pro-addon/issues/461) ([4f43957](https://github.com/Codeinwp/neve-pro-addon/commit/4f43957))
   * infinite scroll on shop with plain permalinks [[#344](https://github.com/Codeinwp/neve-pro-addon/issues/344)] ([1963ead](https://github.com/Codeinwp/neve-pro-addon/commit/1963ead))
   * issue with number of related products and columns [[#548](https://github.com/Codeinwp/neve-pro-addon/issues/548)] ([9e9854a](https://github.com/Codeinwp/neve-pro-addon/commit/9e9854a))
   * my account component dropdown alignment [closes [#449](https://github.com/Codeinwp/neve-pro-addon/issues/449)] ([87734ab](https://github.com/Codeinwp/neve-pro-addon/commit/87734ab))
   * my account component dropdown alignment [closes [#449](https://github.com/Codeinwp/neve-pro-addon/issues/449)] ([b804c42](https://github.com/Codeinwp/neve-pro-addon/commit/b804c42))
   * notice when woocommerce is disabled after header is set to products archive ([01375ed](https://github.com/Codeinwp/neve-pro-addon/commit/01375ed)), closes		[#539](https://github.com/Codeinwp/neve-pro-addon/issues/539)
   * product query with price filters on infinite scroll [closes [#344](https://github.com/Codeinwp/neve-pro-addon/issues/344)] ([63553ec](https://github.com/Codeinwp/neve-pro-addon/commit/63553ec))
   * single product classes leaking into quick view modal [closes [#554](https://github.com/Codeinwp/neve-pro-addon/issues/554)] ([039ebdc](https://github.com/Codeinwp/neve-pro-addon/commit/039ebdc))

* **Features**

   * create my account component [[#449](https://github.com/Codeinwp/neve-pro-addon/issues/449)] ([63ecf6d](https://github.com/Codeinwp/neve-pro-addon/commit/63ecf6d))
   * new starter sites: Scuba Diving, Wine Bar, Bike Race, Movie, Running Club, Jewellery Shop, Car Service, Museum, Exhibition, Conference ([23aa9c8](https://github.com/Codeinwp/neve-pro-addon/commit/23aa9c8))
   * sticky footer [wip][Codeinwp/neve-pro-addon[#537](https://github.com/Codeinwp/neve-pro-addon/issues/537)] ([07aca4e](https://github.com/Codeinwp/neve-pro-addon/commit/07aca4e))



#### [Version 1.1.7](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.6...v1.1.7) (2020-02-13)

* **Bug Fixes**

   * [custom layouts] ([74fe937](https://github.com/Codeinwp/neve-pro-addon/commit/74fe937)), closes		[#547](https://github.com/Codeinwp/neve-pro-addon/issues/547)
   * [custom layouts] ([0bc149f](https://github.com/Codeinwp/neve-pro-addon/commit/0bc149f)), closes		[#547](https://github.com/Codeinwp/neve-pro-addon/issues/547)
   * [custom layouts] ([7bc58af](https://github.com/Codeinwp/neve-pro-addon/commit/7bc58af)), closes		[#547](https://github.com/Codeinwp/neve-pro-addon/issues/547)
   * [custom layouts] ([194277a](https://github.com/Codeinwp/neve-pro-addon/commit/194277a)), closes		[#547](https://github.com/Codeinwp/neve-pro-addon/issues/547)
   * [custom layouts] Custom Layouts decision list was built too early ([b267f90](https://github.com/Codeinwp/neve-pro-addon/commit/b267f90)), closes		[#547](https://github.com/Codeinwp/neve-pro-addon/issues/547)
   * conditional header buttons with gutenberg installed ([e329f77](https://github.com/Codeinwp/neve-pro-addon/commit/e329f77)), closes		[#525](https://github.com/Codeinwp/neve-pro-addon/issues/525)
   * conditional headers not escaping quotes and slashes for custom layouts ([69937c6](https://github.com/Codeinwp/neve-pro-addon/commit/69937c6)), closes		[#533](https://github.com/Codeinwp/neve-pro-addon/issues/533)
   * conditional headers not working with child themes [closes [#527](https://github.com/Codeinwp/neve-pro-addon/issues/527)] ([bb74b00](https://github.com/Codeinwp/neve-pro-addon/commit/bb74b00))

* **Features**

   * add second menu icon component [closes [#531](https://github.com/Codeinwp/neve-pro-addon/issues/531)] ([9a7f7ae](https://github.com/Codeinwp/neve-pro-addon/commit/9a7f7ae))
   * apply transparent header for conditional headers ([0418dac](https://github.com/Codeinwp/neve-pro-addon/commit/0418dac)), closes		[#530](https://github.com/Codeinwp/neve-pro-addon/issues/530)



#### [Version 1.1.6](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.5...v1.1.6) (2020-02-03)

* **Bug Fixes**

   * adds compatibility with new otter version ([a984c15](https://github.com/Codeinwp/neve-pro-addon/commit/a984c15))
   * custom layouts archive taxonomy rule evaluation ([528c45f](https://github.com/Codeinwp/neve-pro-addon/commit/528c45f))
   * disable autoload on Otter class check for custom layouts ([882e077](https://github.com/Codeinwp/neve-pro-addon/commit/882e077))
   * elementor popup not working because of an animation script [[#480](https://github.com/Codeinwp/neve-pro-addon/issues/480)] ([da0d28a](https://github.com/Codeinwp/neve-pro-addon/commit/da0d28a))
   * review box border color [[#471](https://github.com/Codeinwp/neve-pro-addon/issues/471)] ([7070733](https://github.com/Codeinwp/neve-pro-addon/commit/7070733))
   * section without woocommerce and icons style [[#448](https://github.com/Codeinwp/neve-pro-addon/issues/448)] ([719d176](https://github.com/Codeinwp/neve-pro-addon/commit/719d176))
   * widget js not keeping the side and buttons margin [[#505](https://github.com/Codeinwp/neve-pro-addon/issues/505)] ([d8d27d3](https://github.com/Codeinwp/neve-pro-addon/commit/d8d27d3))

* **Features**

   * add alignment option for title and price for product cart [[#445](https://github.com/Codeinwp/neve-pro-addon/issues/445)] ([46b7d8f](https://github.com/Codeinwp/neve-pro-addon/commit/46b7d8f))
   * add more effects on product image [[#444](https://github.com/Codeinwp/neve-pro-addon/issues/444)] ([a8b00b0](https://github.com/Codeinwp/neve-pro-addon/commit/a8b00b0))
   * add navigation between products ([a6834dc](https://github.com/Codeinwp/neve-pro-addon/commit/a6834dc)), closes		[#441](https://github.com/Codeinwp/neve-pro-addon/issues/441)
   * add off canvas filtering support ([c8c2ae4](https://github.com/Codeinwp/neve-pro-addon/commit/c8c2ae4)), closes		[#447](https://github.com/Codeinwp/neve-pro-addon/issues/447)
   * add payment icons [[#448](https://github.com/Codeinwp/neve-pro-addon/issues/448)] ([76a4abb](https://github.com/Codeinwp/neve-pro-addon/commit/76a4abb))
   * add sale bubble improvements [[#443](https://github.com/Codeinwp/neve-pro-addon/issues/443)] ([254d030](https://github.com/Codeinwp/neve-pro-addon/commit/254d030))
   * add seamless add to cart ([405e164](https://github.com/Codeinwp/neve-pro-addon/commit/405e164)), closes		[#442](https://github.com/Codeinwp/neve-pro-addon/issues/442)
   * new Industrial, Adventure, Product Launch, Accounting, Beauty Shop, Recruitment Agency and Artist Starter Sites ([fc75828](https://github.com/Codeinwp/neve-pro-addon/commit/fc75828))



#### [Version 1.1.5](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.4...v1.1.5) (2019-12-19)

* **Bug Fixes**

   * flip card js error ([0e5c6e5](https://github.com/Codeinwp/neve-pro-addon/commit/0e5c6e5)), closes		[#409](https://github.com/Codeinwp/neve-pro-addon/issues/409)
   * scroll to top error on elementor canvas template ([05ebfcf](https://github.com/Codeinwp/neve-pro-addon/commit/05ebfcf))



#### [Version 1.1.4](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.3...v1.1.4) (2019-12-19)

* **Bug Fixes**

   * font awesome 5 icons [[#367](https://github.com/Codeinwp/neve-pro-addon/issues/367)] ([37df389](https://github.com/Codeinwp/neve-pro-addon/commit/37df389))
   * remove default fa4 icons form flipcard widget [[#394](https://github.com/Codeinwp/neve-pro-addon/issues/394)] ([386e8c0](https://github.com/Codeinwp/neve-pro-addon/commit/386e8c0))
   * remove review box widget pros and cons italic and bold [[#625](https://github.com/Codeinwp/neve-pro-addon/issues/625)] ([59add13](https://github.com/Codeinwp/neve-pro-addon/commit/59add13))
   * review icons [[#307](https://github.com/Codeinwp/neve-pro-addon/issues/307)] ([bfe6bd9](https://github.com/Codeinwp/neve-pro-addon/commit/bfe6bd9))

* **Features**

   * add acf elementor widget ([cbda4ba](https://github.com/Codeinwp/neve-pro-addon/commit/cbda4ba)), closes		[#405](https://github.com/Codeinwp/neve-pro-addon/issues/405)
   * add animations as global feature for any widget ([f23c852](https://github.com/Codeinwp/neve-pro-addon/commit/f23c852)), closes		[#408](https://github.com/Codeinwp/neve-pro-addon/issues/408)
   * add banner widget ([fbda6c2](https://github.com/Codeinwp/neve-pro-addon/commit/fbda6c2)), closes		[#404](https://github.com/Codeinwp/neve-pro-addon/issues/404)
   * add colors for reviews ([ec9728d](https://github.com/Codeinwp/neve-pro-addon/commit/ec9728d))
   * add content protection feature ([859d92d](https://github.com/Codeinwp/neve-pro-addon/commit/859d92d)), closes		[#410](https://github.com/Codeinwp/neve-pro-addon/issues/410)
   * add content switcher widget ([626c749](https://github.com/Codeinwp/neve-pro-addon/commit/626c749)), closes		[#403](https://github.com/Codeinwp/neve-pro-addon/issues/403)
   * add mobile version for the page header ([6e911b3](https://github.com/Codeinwp/neve-pro-addon/commit/6e911b3)), closes		[Codeinwp/neve-pro-addon#425](https://github.com/Codeinwp/neve-pro-addon/issues/425)
   * add option to disable Particles on reduced motion devices ([015fdc0](https://github.com/Codeinwp/neve-pro-addon/commit/015fdc0)), closes		[#409](https://github.com/Codeinwp/neve-pro-addon/issues/409)
   * add padding and typography controls for share buttons widgets ([ea928c1](https://github.com/Codeinwp/neve-pro-addon/commit/ea928c1)), closes		[#411](https://github.com/Codeinwp/neve-pro-addon/issues/411)
   * add Particles for sections in Elementor ([9d51a3d](https://github.com/Codeinwp/neve-pro-addon/commit/9d51a3d)), closes		[#409](https://github.com/Codeinwp/neve-pro-addon/issues/409)
   * Add progress circle widget ([d2200ab](https://github.com/Codeinwp/neve-pro-addon/commit/d2200ab)), closes		[#407](https://github.com/Codeinwp/neve-pro-addon/issues/407)
   * Add styling options for review box [[#307](https://github.com/Codeinwp/neve-pro-addon/issues/307)] ([cd9cff1](https://github.com/Codeinwp/neve-pro-addon/commit/cd9cff1))
   * added new team member elementor widget ([628705c](https://github.com/Codeinwp/neve-pro-addon/commit/628705c)), closes		[#402](https://github.com/Codeinwp/neve-pro-addon/issues/402)
   * New Beaver Starter Sites for News Magazine, Car Rental, Translation, Coupons, Blogger and Escape Room ([8e5bb5b](https://github.com/Codeinwp/neve-pro-addon/commit/8e5bb5b))
   * New Starter Sites for Fashion Agency, Christmas Market, Podcasts, IT Company and Digital Agency ([6730079](https://github.com/Codeinwp/neve-pro-addon/commit/6730079))

* **Performance Improvements**

   * Add date time settings for content protection ([03ad0bb](https://github.com/Codeinwp/neve-pro-addon/commit/03ad0bb)), closes		[#410](https://github.com/Codeinwp/neve-pro-addon/issues/410)



#### [Version 1.1.3](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.2...v1.1.3) (2019-12-05)

* **Bug Fixes**

   * adds dependency locks ([2a1207d](https://github.com/Codeinwp/neve-pro-addon/commit/2a1207d))
   * not loading the control classes for wish list [[#360](https://github.com/Codeinwp/neve-pro-addon/issues/360)] ([494fd91](https://github.com/Codeinwp/neve-pro-addon/commit/494fd91))

* **Features**

   * adds typography controls to HFG components ([a4a8e23](https://github.com/Codeinwp/neve-pro-addon/commit/a4a8e23)), closes		[#360](https://github.com/Codeinwp/neve-pro-addon/issues/360)
   * allow second logo [closes [#359](https://github.com/Codeinwp/neve-pro-addon/issues/359)] ([c14844e](https://github.com/Codeinwp/neve-pro-addon/commit/c14844e))
   * new Beaver Builder Starter Sites for Church, Winery and Fashion Magazine ([abefe20](https://github.com/Codeinwp/neve-pro-addon/commit/abefe20))
   * New Divi Starter Sites [#423](https://github.com/Codeinwp/neve-pro-addon/issues/423) ([7181dd4](https://github.com/Codeinwp/neve-pro-addon/commit/7181dd4))
   * new Starter Sites for Blogger, Coupons and Escape Room ([3bcd683](https://github.com/Codeinwp/neve-pro-addon/commit/3bcd683))
   * new Starter Sites for Wellness Spa, Online Courses and Resume ([0e6c8f0](https://github.com/Codeinwp/neve-pro-addon/commit/0e6c8f0))



#### [Version 1.1.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.1...v1.1.2) (2019-11-19)

* **Bug Fixes**

   * Remove search form index in the component name ([03131a3](https://github.com/Codeinwp/neve-pro-addon/commit/03131a3))
   * reset query for related posts [[#365](https://github.com/Codeinwp/neve-pro-addon/issues/365)] ([8abb105](https://github.com/Codeinwp/neve-pro-addon/commit/8abb105))

* **Features**

   * Beaver Builder starter sites: Cake Shop, Yoga Studio and Sales Funnel ([f6f990a](https://github.com/Codeinwp/neve-pro-addon/commit/f6f990a))
   * new Moving Company and Translation Services Starter Sites ([0c2a265](https://github.com/Codeinwp/neve-pro-addon/commit/0c2a265))
   * new Starter Sites for Car Rental, Ski Resort and Digital Product Funnel ([e83ebeb](https://github.com/Codeinwp/neve-pro-addon/commit/e83ebeb))
   * new Starter Sites: Winery, News Magazine, Coworking space and Fishing Hunting Club ([eb2b7b1](https://github.com/Codeinwp/neve-pro-addon/commit/eb2b7b1))



#### [Version 1.1.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.0...v1.1.1) (2019-11-04)

* **Bug Fixes**

   * add defaults for page header builder [bug] ([c577f5d](https://github.com/Codeinwp/neve-pro-addon/commit/c577f5d))
   * adds product card image link when there are no buttons ([e3e7e53](https://github.com/Codeinwp/neve-pro-addon/commit/e3e7e53)), closes		[#341](https://github.com/Codeinwp/neve-pro-addon/issues/341)
   * author archive title in magic tags html decode [closes [#333](https://github.com/Codeinwp/neve-pro-addon/issues/333)] ([a6be4b5](https://github.com/Codeinwp/neve-pro-addon/commit/a6be4b5))
   * display name instead of nicename in page header {author} [enh] ([41aa196](https://github.com/Codeinwp/neve-pro-addon/commit/41aa196))
   * html tags not interpreted inside author magic tag for archive page ([8e37f56](https://github.com/Codeinwp/neve-pro-addon/commit/8e37f56)), closes		[#333](https://github.com/Codeinwp/neve-pro-addon/issues/333)
   * script tag is striped after update [[#330](https://github.com/Codeinwp/neve-pro-addon/issues/330)] ([c79de99](https://github.com/Codeinwp/neve-pro-addon/commit/c79de99))
   * transparent header z-index [closes [#323](https://github.com/Codeinwp/neve-pro-addon/issues/323)] ([05c4f55](https://github.com/Codeinwp/neve-pro-addon/commit/05c4f55))

* **Features**

   * add color options for Contact header component [closes [#326](https://github.com/Codeinwp/neve-pro-addon/issues/326)] ([e724c2d](https://github.com/Codeinwp/neve-pro-addon/commit/e724c2d))
   * Add magic tags in custom layouts [[#334](https://github.com/Codeinwp/neve-pro-addon/issues/334)] ([5ea2414](https://github.com/Codeinwp/neve-pro-addon/commit/5ea2414))
   * add social icons component to footer grid [closes [#327](https://github.com/Codeinwp/neve-pro-addon/issues/327)] ([582fbd2](https://github.com/Codeinwp/neve-pro-addon/commit/582fbd2))
   * new Beaver Builder and Brizy Starter Sites ([7b7a52b](https://github.com/Codeinwp/neve-pro-addon/commit/7b7a52b))
   * new Beaver Builder starter sites eBook and Florist ([203470a](https://github.com/Codeinwp/neve-pro-addon/commit/203470a))
   * new Church and Fashion Magazine Starter Sites ([4a66fe3](https://github.com/Codeinwp/neve-pro-addon/commit/4a66fe3))
   * new starter sites: Cake Shop, Transport, eBook, Speed and BB starter sites ([82877b8](https://github.com/Codeinwp/neve-pro-addon/commit/82877b8))
   * new Starter Sites: Food Magazine, eCourse - Web Design, Yoga Studio and Sales Funnel ([89904f4](https://github.com/Codeinwp/neve-pro-addon/commit/89904f4))
   * Thrive Architect starter sites ([0027422](https://github.com/Codeinwp/neve-pro-addon/commit/0027422))



### [Version 1.1.0](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.6...v1.1.0) (2019-10-10)

* #### Bug Fixes

   * [#259](https://github.com/Codeinwp/neve-pro-addon/issues/259) improve custom layouts module db requests ([591e9ce](https://github.com/Codeinwp/neve-pro-addon/commit/591e9ce))
   * condition on archive term which was not preserving state ([c010fde](https://github.com/Codeinwp/neve-pro-addon/commit/c010fde))
   * Custom layouts with Brizy builder ([73e2ae0](https://github.com/Codeinwp/neve-pro-addon/commit/73e2ae0))
   * fatal error when beaver is active ([53f8180](https://github.com/Codeinwp/neve-pro-addon/commit/53f8180))
   * hide white label module settings if the module is disabled ([579470b](https://github.com/Codeinwp/neve-pro-addon/commit/579470b))
   * isolate hook content render. ([a74cd87](https://github.com/Codeinwp/neve-pro-addon/commit/a74cd87))
   * issue with $ undefined in customizer [closes [#304](https://github.com/Codeinwp/neve-pro-addon/issues/304)] ([d6947ac](https://github.com/Codeinwp/neve-pro-addon/commit/d6947ac))
   * issue with multiple layouts on same hook ([0ef78e2](https://github.com/Codeinwp/neve-pro-addon/commit/0ef78e2))
   * loading order on custom hooks ([2aa588b](https://github.com/Codeinwp/neve-pro-addon/commit/2aa588b))
   * only on scroll rows show at load [closes [#293](https://github.com/Codeinwp/neve-pro-addon/issues/293)] ([780891c](https://github.com/Codeinwp/neve-pro-addon/commit/780891c))
   * sticky navigation row overflow hidden [fixes [#294](https://github.com/Codeinwp/neve-pro-addon/issues/294)] ([3878acf](https://github.com/Codeinwp/neve-pro-addon/commit/3878acf))

* #### Features

   * add 404 custom layout ([018a6b6](https://github.com/Codeinwp/neve-pro-addon/commit/018a6b6))
   * add templates for 500 and offline page for pwa [[#272](https://github.com/Codeinwp/neve-pro-addon/issues/272)] ([bd900c7](https://github.com/Codeinwp/neve-pro-addon/commit/bd900c7))
   * new Florist Starter Site + Beaver Builder Starter Sites ([e1d8fdd](https://github.com/Codeinwp/neve-pro-addon/commit/e1d8fdd))
   * new Kindergarten and Photographer starter sites ([b772c75](https://github.com/Codeinwp/neve-pro-addon/commit/b772c75))
   * new Personal Trainer and Farm Beaver Builder Starter Sites ([145ebdb](https://github.com/Codeinwp/neve-pro-addon/commit/145ebdb))



#### [Version 1.0.6](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.5...v1.0.6) (2019-09-23)

* **Bug Fixes**

   * disable related products slider for less than 4 products ([1c575b5](https://github.com/Codeinwp/neve-pro-addon/commit/1c575b5)), closes		[#283](https://github.com/Codeinwp/neve-pro-addon/issues/283)
   * product thumbnail gets small on second nav-cart layout ([a6326f7](https://github.com/Codeinwp/neve-pro-addon/commit/a6326f7)), closes		[#289](https://github.com/Codeinwp/neve-pro-addon/issues/289)
   * quick view modal overlapped by sticky nav [fixes [#288](https://github.com/Codeinwp/neve-pro-addon/issues/288)] ([1eff79e](https://github.com/Codeinwp/neve-pro-addon/commit/1eff79e))
   * quick view not working on front page and other pages than woo ([45842ae](https://github.com/Codeinwp/neve-pro-addon/commit/45842ae))
   * remove page header on page templates [fixes [#286](https://github.com/Codeinwp/neve-pro-addon/issues/286)] ([7391c4f](https://github.com/Codeinwp/neve-pro-addon/commit/7391c4f))
   * spacing on cart and checkout with page header enabled [fixes [#285](https://github.com/Codeinwp/neve-pro-addon/issues/285)] ([dff6036](https://github.com/Codeinwp/neve-pro-addon/commit/dff6036))
   * WooCommerce floating boxes overlapping sticky header [[#284](https://github.com/Codeinwp/neve-pro-addon/issues/284)] ([a26da7d](https://github.com/Codeinwp/neve-pro-addon/commit/a26da7d))

* **Features**

   * 5 new starter sites ([503304b](https://github.com/Codeinwp/neve-pro-addon/commit/503304b))



#### [Version 1.0.5](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.4...v1.0.5) (2019-09-13)

* **Bug Fixes**

   * [#270](https://github.com/Codeinwp/neve-pro-addon/issues/270) amp validation ([f185a99](https://github.com/Codeinwp/neve-pro-addon/commit/f185a99))
   * change components default width ([5112902](https://github.com/Codeinwp/neve-pro-addon/commit/5112902))
   * elementor widgets style and add wpml translation compatibility ([610d95d](https://github.com/Codeinwp/neve-pro-addon/commit/610d95d))
   * enqueue style but not script on amp ([522af23](https://github.com/Codeinwp/neve-pro-addon/commit/522af23))
   * remove default on when pro is active for logger, fix breadcrumb name ([c7312f9](https://github.com/Codeinwp/neve-pro-addon/commit/c7312f9))
   * remove leftover char ([250aed3](https://github.com/Codeinwp/neve-pro-addon/commit/250aed3))
   * remove leftover code ([6afbe55](https://github.com/Codeinwp/neve-pro-addon/commit/6afbe55))
   * reworked the way the sticky header acts [[#254](https://github.com/Codeinwp/neve-pro-addon/issues/254)] ([7376b34](https://github.com/Codeinwp/neve-pro-addon/commit/7376b34))
   * simplify description logic ([852721e](https://github.com/Codeinwp/neve-pro-addon/commit/852721e))
   * transparent header with new background settings in theme ([ee00b07](https://github.com/Codeinwp/neve-pro-addon/commit/ee00b07))

* **Features**

   * Add compatibility with polylang ([44df5f4](https://github.com/Codeinwp/neve-pro-addon/commit/44df5f4))
   * add compatibility with WPML for contact and socials repeaters ([1aae292](https://github.com/Codeinwp/neve-pro-addon/commit/1aae292))
   * add description for page header builder ([4632575](https://github.com/Codeinwp/neve-pro-addon/commit/4632575))
   * add outline and shortcuts to customizer for header builder rows ([a50bd6b](https://github.com/Codeinwp/neve-pro-addon/commit/a50bd6b))
   * New Beaver Builder Starter Sites ([ab4cf8f](https://github.com/Codeinwp/neve-pro-addon/commit/ab4cf8f))
   * New Starter Sites ([e61071e](https://github.com/Codeinwp/neve-pro-addon/commit/e61071e))



#### [Version 1.0.4](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.3...v1.0.4) (2019-08-20)

* **Bug Fixes**

   * add to cart quick view button [[#244](https://github.com/Codeinwp/neve-pro-addon/issues/244)] ([7662a86](https://github.com/Codeinwp/neve-pro-addon/commit/7662a86))
   * featured product video not working ([e9b2479](https://github.com/Codeinwp/neve-pro-addon/commit/e9b2479))
   * features not working with async js ([4a90603](https://github.com/Codeinwp/neve-pro-addon/commit/4a90603))
   * new WPCS errors ([4514033](https://github.com/Codeinwp/neve-pro-addon/commit/4514033))

* **Features**

   * adds two new pro starter sites ([c68df10](https://github.com/Codeinwp/neve-pro-addon/commit/c68df10))

* **Performance Improvements**

   * drop sessions from wish list [[#248](https://github.com/Codeinwp/neve-pro-addon/issues/248)] ([1994463](https://github.com/Codeinwp/neve-pro-addon/commit/1994463))



#### [Version 1.0.3](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.2...v1.0.3) (2019-07-30)

* **Bug Fixes**

   * Otter font awesome not loading in custom layouts ([5c362a8](https://github.com/Codeinwp/neve-pro-addon/commit/5c362a8))
   * Otter font awesome not loading in custom layouts ([b3b77bd](https://github.com/Codeinwp/neve-pro-addon/commit/b3b77bd))
   * transparent nav with dropdown sidebar ([9069dc7](https://github.com/Codeinwp/neve-pro-addon/commit/9069dc7))

* **Features**

   * New starter sites ([58fbfb1](https://github.com/Codeinwp/neve-pro-addon/commit/58fbfb1))



#### [Version 1.0.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.1...v1.0.2) (2019-07-15)

* **Bug Fixes**

   * header overriding admin menu dropdowns [closes [#176](https://github.com/Codeinwp/neve-pro-addon/issues/176)] ([e4805a3](https://github.com/Codeinwp/neve-pro-addon/commit/e4805a3))

* **Performance Improvements**

   * move author avatar toggle control to theme [Codeinwp/neve[#630](https://github.com/Codeinwp/neve-pro-addon/issues/630)] ([9da4e10](https://github.com/Codeinwp/neve-pro-addon/commit/9da4e10))
   * move author avatar toggle control to theme [Codeinwp/neve[#630](https://github.com/Codeinwp/neve-pro-addon/issues/630)] ([76b6915](https://github.com/Codeinwp/neve-pro-addon/commit/76b6915))



#### [Version 1.0.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.0.0...v1.0.1) (2019-07-04)

* **Bug Fixes**

   * image effect not working on infinite scroll ([dcdf54a](https://github.com/Codeinwp/neve-pro-addon/commit/dcdf54a))
   * image effect not working on infinite scroll ([4268c46](https://github.com/Codeinwp/neve-pro-addon/commit/4268c46))
   * overflow for product image card with zoom effect ([b9fd8a3](https://github.com/Codeinwp/neve-pro-addon/commit/b9fd8a3))



### [Version 1.0.0](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.26...v1.0.0) (2019-07-04)

* #### Bug Fixes

   * [#174](https://github.com/Codeinwp/neve-pro-addon/issues/174) - widgets not working in custom layouts hooks ([5e69f08](https://github.com/Codeinwp/neve-pro-addon/commit/5e69f08))



#### [Version 0.0.26](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.25...v0.0.26) (2019-07-04)

* **Bug Fixes**

   * Accessibility fixes [[#55](https://github.com/Codeinwp/neve-pro-addon/issues/55)] ([8dc3aaa](https://github.com/Codeinwp/neve-pro-addon/commit/8dc3aaa))
   * add more share icons + ability to show on desktop/mobile ([a4b1cf6](https://github.com/Codeinwp/neve-pro-addon/commit/a4b1cf6))
   * add more share icons + ability to show on desktop/mobile ([f42bb4f](https://github.com/Codeinwp/neve-pro-addon/commit/f42bb4f))
   * added missing pot file ([27514b2](https://github.com/Codeinwp/neve-pro-addon/commit/27514b2))
   * Adds RTL CSS files and proper enqueues for them [[#55](https://github.com/Codeinwp/neve-pro-addon/issues/55)] ([5e94acb](https://github.com/Codeinwp/neve-pro-addon/commit/5e94acb))
   * author bio image size and alignment [[#185](https://github.com/Codeinwp/neve-pro-addon/issues/185)] ([64b01de](https://github.com/Codeinwp/neve-pro-addon/commit/64b01de))
   * comments hidden on reply ([3cbc5e1](https://github.com/Codeinwp/neve-pro-addon/commit/3cbc5e1))
   * drop-downs inside only on scroll rows [[#206](https://github.com/Codeinwp/neve-pro-addon/issues/206)] ([9cb71d6](https://github.com/Codeinwp/neve-pro-addon/commit/9cb71d6))
   * lifterLms plugin install link ([97766ba](https://github.com/Codeinwp/neve-pro-addon/commit/97766ba))
   * product sale tag text for locales [[#208](https://github.com/Codeinwp/neve-pro-addon/issues/208)] ([968fa30](https://github.com/Codeinwp/neve-pro-addon/commit/968fa30))
   * quick view slider optimole compatibility ([4072d2a](https://github.com/Codeinwp/neve-pro-addon/commit/4072d2a))
   * wish list responsive style [[#587](https://github.com/Codeinwp/neve-pro-addon/issues/587)] ([f8b31a3](https://github.com/Codeinwp/neve-pro-addon/commit/f8b31a3))

* **Features**

   * Add typekit module ([67b1567](https://github.com/Codeinwp/neve-pro-addon/commit/67b1567))



#### [Version 0.0.25](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.24...v0.0.25) (2019-06-13)

* **Bug Fixes**

   * repeater control not working on some servers ([40dc1c7](https://github.com/Codeinwp/neve-pro-addon/commit/40dc1c7))



#### [Version 0.0.24](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.23...v0.0.24) (2019-06-12)

* **Bug Fixes**

   * update control for social share icons [[#177](https://github.com/Codeinwp/neve-pro-addon/issues/177)] ([d0f74a4](https://github.com/Codeinwp/neve-pro-addon/commit/d0f74a4))



#### [Version 0.0.23](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.22...v0.0.23) (2019-06-12)

* **Bug Fixes**

   * grid display in safari ([3db5d0e](https://github.com/Codeinwp/neve-pro-addon/commit/3db5d0e))

* **Features**

   * LifterLMS Booster Module ([5e897e4](https://github.com/Codeinwp/neve-pro-addon/commit/5e897e4))



#### [Version 0.0.22](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.21...v0.0.22) (2019-06-11)

* **Bug Fixes**

   * [#175](https://github.com/Codeinwp/neve-pro-addon/issues/175) - infinite scroll on woo archive page. ([da318d5](https://github.com/Codeinwp/neve-pro-addon/commit/da318d5))
   * improve quick view modal UX [[#87](https://github.com/Codeinwp/neve-pro-addon/issues/87)] ([4f953f0](https://github.com/Codeinwp/neve-pro-addon/commit/4f953f0))
   * move wishlist notification causing padding [Codeinwp/neve[#582](https://github.com/Codeinwp/neve-pro-addon/issues/582)] ([49e476a](https://github.com/Codeinwp/neve-pro-addon/commit/49e476a))
   * wish list and quick view not loading when infinite scroll ([d34c30a](https://github.com/Codeinwp/neve-pro-addon/commit/d34c30a))



#### [Version 0.0.21](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.20...v0.0.21) (2019-06-10)

* **Bug Fixes**

   * hfg sticky header issues ([23fa189](https://github.com/Codeinwp/neve-pro-addon/commit/23fa189))
   * improve wish list UX flow [[#88](https://github.com/Codeinwp/neve-pro-addon/issues/88)] ([09dadc1](https://github.com/Codeinwp/neve-pro-addon/commit/09dadc1))
   * strong tag inside product description [[#171](https://github.com/Codeinwp/neve-pro-addon/issues/171)] ([c0b5180](https://github.com/Codeinwp/neve-pro-addon/commit/c0b5180))



#### [Version 0.0.20](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.19...v0.0.20) (2019-06-05)



#### [Version 0.0.19](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.18...v0.0.19) (2019-06-05)

* **Bug Fixes**

   * solves secondary menu target [#115](https://github.com/Codeinwp/neve-pro-addon/issues/115) ([26a6488](https://github.com/Codeinwp/neve-pro-addon/commit/26a6488))
   * solves secondary menu target [#115](https://github.com/Codeinwp/neve-pro-addon/issues/115) ([8629721](https://github.com/Codeinwp/neve-pro-addon/commit/8629721))



#### [Version 0.0.18](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.17...v0.0.18) (2019-06-05)



#### [Version 0.0.17](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.16...v0.0.17) (2019-06-03)

* **Features**

   * [#118](https://github.com/Codeinwp/neve-pro-addon/issues/118) - add user roles for custom layouts ([5ccd697](https://github.com/Codeinwp/neve-pro-addon/commit/5ccd697))



#### [Version 0.0.16](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.15...v0.0.16) (2019-05-29)

* **Bug Fixes**

   * scroll to top module ([8136182](https://github.com/Codeinwp/neve-pro-addon/commit/8136182))
   * transparent header check ([ca45018](https://github.com/Codeinwp/neve-pro-addon/commit/ca45018))



#### [Version 0.0.15](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.14...v0.0.15) (2019-05-28)

* **Bug Fixes**

   * accessibility fixes for social icons component [Codeinwp/neve[#519](https://github.com/Codeinwp/neve-pro-addon/issues/519)] ([bc78cae](https://github.com/Codeinwp/neve-pro-addon/commit/bc78cae))
   * accessibility fixes for social icons component [Codeinwp/neve[#519](https://github.com/Codeinwp/neve-pro-addon/issues/519)] ([ca355ea](https://github.com/Codeinwp/neve-pro-addon/commit/ca355ea))
   * remove text from language switchers [[#66](https://github.com/Codeinwp/neve-pro-addon/issues/66)] ([606623a](https://github.com/Codeinwp/neve-pro-addon/commit/606623a))
   * single product hover affects products inside page [woo booster] ([703fc15](https://github.com/Codeinwp/neve-pro-addon/commit/703fc15))
   * z-index for translatepress switcher [[#66](https://github.com/Codeinwp/neve-pro-addon/issues/66)] ([6c31bd3](https://github.com/Codeinwp/neve-pro-addon/commit/6c31bd3))

* **Features**

   * [#68](https://github.com/Codeinwp/neve-pro-addon/issues/68) Add Wish list component in header footer grid ([2d7144a](https://github.com/Codeinwp/neve-pro-addon/commit/2d7144a))



#### [Version 0.0.14](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.13...v0.0.14) (2019-05-27)

* **Bug Fixes**

   * properly format request to store ([d91454a](https://github.com/Codeinwp/neve-pro-addon/commit/d91454a))



#### [Version 0.0.13](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.12...v0.0.13) (2019-05-27)

* **Bug Fixes**

   * properly format request to store ([39d81dc](https://github.com/Codeinwp/neve-pro-addon/commit/39d81dc))



#### [Version 0.0.12](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.11...v0.0.12) (2019-05-27)

* **Bug Fixes**

   * deployment & store update ([46fcbca](https://github.com/Codeinwp/neve-pro-addon/commit/46fcbca))



#### [Version 0.0.11](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.10...v0.0.11) (2019-05-27)

* **Bug Fixes**

   * deployment & store update ([a954445](https://github.com/Codeinwp/neve-pro-addon/commit/a954445))



#### [Version 0.0.10](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.9...v0.0.10) (2019-05-27)

* **Bug Fixes**

   * deployment & store update ([eff7e89](https://github.com/Codeinwp/neve-pro-addon/commit/eff7e89))



#### [Version 0.0.9](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.8...v0.0.9) (2019-05-27)

* **Bug Fixes**

   * deployment & store update ([806bf7c](https://github.com/Codeinwp/neve-pro-addon/commit/806bf7c))
   * deployment & store update ([391fd6a](https://github.com/Codeinwp/neve-pro-addon/commit/391fd6a))



#### [Version 0.0.8](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.7...v0.0.8) (2019-05-27)

* **Bug Fixes**

   * deployment & store update ([cfa865e](https://github.com/Codeinwp/neve-pro-addon/commit/cfa865e))



#### [Version 0.0.7](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.6...v0.0.7) (2019-05-27)

* **Bug Fixes**

   * deployment & store update ([e32982a](https://github.com/Codeinwp/neve-pro-addon/commit/e32982a))



#### [Version 0.0.6](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.5...v0.0.6) (2019-05-15)

* **Bug Fixes**

   * Codeinwp/neve-pro-addon[#73](https://github.com/Codeinwp/neve-pro-addon/issues/73) ([eafaf84](https://github.com/Codeinwp/neve-pro-addon/commit/eafaf84))
   * Codeinwp/neve-pro-addon[#73](https://github.com/Codeinwp/neve-pro-addon/issues/73) ([2ad8732](https://github.com/Codeinwp/neve-pro-addon/commit/2ad8732))



#### [Version 0.0.5](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.4...v0.0.5) (2019-05-13)

* **Bug Fixes**

   * [#121](https://github.com/Codeinwp/neve-pro-addon/issues/121) - fatal error if product that is in wishlist is removed ([dd55822](https://github.com/Codeinwp/neve-pro-addon/commit/dd55822))
   * [#43](https://github.com/Codeinwp/neve-pro-addon/issues/43) change the way php-html editor load files ([992b1bc](https://github.com/Codeinwp/neve-pro-addon/commit/992b1bc))
   * [#90](https://github.com/Codeinwp/neve-pro-addon/issues/90) add translation function for default argument ([f40d38a](https://github.com/Codeinwp/neve-pro-addon/commit/f40d38a))
   * background color specificity for rows Codeinwp/neve[#535](https://github.com/Codeinwp/neve-pro-addon/issues/535) ([0e76abc](https://github.com/Codeinwp/neve-pro-addon/commit/0e76abc))
   * background color specificity for rows Codeinwp/neve[#535](https://github.com/Codeinwp/neve-pro-addon/issues/535) ([1860892](https://github.com/Codeinwp/neve-pro-addon/commit/1860892))
   * issues with social icons component [HFG][[#67](https://github.com/Codeinwp/neve-pro-addon/issues/67)] ([78cc89c](https://github.com/Codeinwp/neve-pro-addon/commit/78cc89c))
   * label and box shadow on exclusive products and same image height on ([3531021](https://github.com/Codeinwp/neve-pro-addon/commit/3531021))
   * properly load customizer classes (only when modules are active) ([ed0736f](https://github.com/Codeinwp/neve-pro-addon/commit/ed0736f))
   * quick view on single ( related and exclusive products ) ([dec0a58](https://github.com/Codeinwp/neve-pro-addon/commit/dec0a58))
   * quick view on single ( related and exclusive products ) ([93ede48](https://github.com/Codeinwp/neve-pro-addon/commit/93ede48))
   * redo shop grid logic ([d184e62](https://github.com/Codeinwp/neve-pro-addon/commit/d184e62))
   * Reorder checks for custom layouts module [[#113](https://github.com/Codeinwp/neve-pro-addon/issues/113)] ([8357eb3](https://github.com/Codeinwp/neve-pro-addon/commit/8357eb3))
   * select options button on product not aligned correctly ([213e264](https://github.com/Codeinwp/neve-pro-addon/commit/213e264))
   * select options button on product not aligned correctly ([ba99652](https://github.com/Codeinwp/neve-pro-addon/commit/ba99652))

* **Features**

   * adds expandable cart in woo booster [Codeinwp/neve[#502](https://github.com/Codeinwp/neve-pro-addon/issues/502)] ([2eeda08](https://github.com/Codeinwp/neve-pro-addon/commit/2eeda08))
   * language switcher component [hfg][[#66](https://github.com/Codeinwp/neve-pro-addon/issues/66)] ([6a7d4cf](https://github.com/Codeinwp/neve-pro-addon/commit/6a7d4cf))
   * Sticky menu & transparent header [[#71](https://github.com/Codeinwp/neve-pro-addon/issues/71)][[#72](https://github.com/Codeinwp/neve-pro-addon/issues/72)] ([677bbf2](https://github.com/Codeinwp/neve-pro-addon/commit/677bbf2))
   * Yoast Breadcrumbs Component [hfg][[#69](https://github.com/Codeinwp/neve-pro-addon/issues/69)] ([daff2ce](https://github.com/Codeinwp/neve-pro-addon/commit/daff2ce))



#### [Version 0.0.4](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.3...v0.0.4) (2019-05-03)

* **Bug Fixes**

   *  remove unnecessary file ([96b7f73](https://github.com/Codeinwp/neve-pro-addon/commit/96b7f73))
   * [#43](https://github.com/Codeinwp/neve-pro-addon/issues/43) - check if  exist ([7e0abce](https://github.com/Codeinwp/neve-pro-addon/commit/7e0abce))
   * [#43](https://github.com/Codeinwp/neve-pro-addon/issues/43) - check if  exist ([0ca58bc](https://github.com/Codeinwp/neve-pro-addon/commit/0ca58bc))
   * [#43](https://github.com/Codeinwp/neve-pro-addon/issues/43) - Load scripts only on custom layouts post type ([5e5a1af](https://github.com/Codeinwp/neve-pro-addon/commit/5e5a1af))
   * [#43](https://github.com/Codeinwp/neve-pro-addon/issues/43) - Make the main function for custom layouts public ([1affb0b](https://github.com/Codeinwp/neve-pro-addon/commit/1affb0b))
   * [#43](https://github.com/Codeinwp/neve-pro-addon/issues/43) update custom layouts module ([2574206](https://github.com/Codeinwp/neve-pro-addon/commit/2574206))
   * [#57](https://github.com/Codeinwp/neve-pro-addon/issues/57) add interface and abstract class for modules to implement ([3191ec0](https://github.com/Codeinwp/neve-pro-addon/commit/3191ec0))
   * [#57](https://github.com/Codeinwp/neve-pro-addon/issues/57) Validate requests for module enable disable ([c69f8f1](https://github.com/Codeinwp/neve-pro-addon/commit/c69f8f1))
   * [#57](https://github.com/Codeinwp/neve-pro-addon/issues/57) Validate requests quick view ([fc43d32](https://github.com/Codeinwp/neve-pro-addon/commit/fc43d32))
   * [#78](https://github.com/Codeinwp/neve-pro-addon/issues/78) check if comments open ([a646cf0](https://github.com/Codeinwp/neve-pro-addon/commit/a646cf0))
   * add featured vid icon ([b56d2f9](https://github.com/Codeinwp/neve-pro-addon/commit/b56d2f9))
   * add featured vid icon & small js fixes ([1cb37ce](https://github.com/Codeinwp/neve-pro-addon/commit/1cb37ce))
   * add missing textdomain ([3a54c66](https://github.com/Codeinwp/neve-pro-addon/commit/3a54c66))
   * add missing textdomain ([9137f53](https://github.com/Codeinwp/neve-pro-addon/commit/9137f53))
   * add video thumbnail when there's no featured but we have video ([4e317be](https://github.com/Codeinwp/neve-pro-addon/commit/4e317be))
   * adds repeater [wip][[#67](https://github.com/Codeinwp/neve-pro-addon/issues/67)] ([6925e2c](https://github.com/Codeinwp/neve-pro-addon/commit/6925e2c))
   * better & safer module status option model ([66ba26a](https://github.com/Codeinwp/neve-pro-addon/commit/66ba26a))
   * button display on list mode when image isn't that tall ([c8a1baf](https://github.com/Codeinwp/neve-pro-addon/commit/c8a1baf))
   * change hook name ([8a0d585](https://github.com/Codeinwp/neve-pro-addon/commit/8a0d585))
   * Changes from feedback ([fa05b30](https://github.com/Codeinwp/neve-pro-addon/commit/fa05b30))
   * console error on other post types ([4ff6e6c](https://github.com/Codeinwp/neve-pro-addon/commit/4ff6e6c))
   * disable top bar on checkout without distraction ([cfc98db](https://github.com/Codeinwp/neve-pro-addon/commit/cfc98db))
   * display related products when number isn't set ([568fe67](https://github.com/Codeinwp/neve-pro-addon/commit/568fe67))
   * errors in elementor ([74d1723](https://github.com/Codeinwp/neve-pro-addon/commit/74d1723))
   * featured video enhancements ([152f1b4](https://github.com/Codeinwp/neve-pro-addon/commit/152f1b4))
   * fix add to cart button when there is no add to cart ([5a384d3](https://github.com/Codeinwp/neve-pro-addon/commit/5a384d3))
   * fix error when woocommerce is disabled ([c57ff13](https://github.com/Codeinwp/neve-pro-addon/commit/c57ff13))
   * fix for product reviews ([2ad372e](https://github.com/Codeinwp/neve-pro-addon/commit/2ad372e))
   * fix scroll to top icon as we removed font awesome and fix its js ([66c8c77](https://github.com/Codeinwp/neve-pro-addon/commit/66c8c77))
   * fix wish list problems ([4228a41](https://github.com/Codeinwp/neve-pro-addon/commit/4228a41))
   * infinite scroll for woocommerce module ([a10a39e](https://github.com/Codeinwp/neve-pro-addon/commit/a10a39e))
   * issue with non youtube video single product photoswipe ([1f3a693](https://github.com/Codeinwp/neve-pro-addon/commit/1f3a693))
   * issue with stopping loding on error ([ac35bf3](https://github.com/Codeinwp/neve-pro-addon/commit/ac35bf3))
   * issues for rodica\'s feedback ([18f5db2](https://github.com/Codeinwp/neve-pro-addon/commit/18f5db2))
   * js errors ([a885076](https://github.com/Codeinwp/neve-pro-addon/commit/a885076))
   * lint file ([152a70f](https://github.com/Codeinwp/neve-pro-addon/commit/152a70f))
   * lint php ([f6cc0d0](https://github.com/Codeinwp/neve-pro-addon/commit/f6cc0d0))
   * lint php ([e59fdaa](https://github.com/Codeinwp/neve-pro-addon/commit/e59fdaa))
   * persistent shop layout after clicking on list/grid toggle ([1ad2cc7](https://github.com/Codeinwp/neve-pro-addon/commit/1ad2cc7))
   * photoswipe issue with zoomed image index [[#30](https://github.com/Codeinwp/neve-pro-addon/issues/30)] ([bbaea3b](https://github.com/Codeinwp/neve-pro-addon/commit/bbaea3b))
   * photoswipe items index with featured video ([9eb1b10](https://github.com/Codeinwp/neve-pro-addon/commit/9eb1b10))
   * position for label in preview ([896806b](https://github.com/Codeinwp/neve-pro-addon/commit/896806b))
   * product rating position ([e7d6a83](https://github.com/Codeinwp/neve-pro-addon/commit/e7d6a83))
   * related products display ([1f654bd](https://github.com/Codeinwp/neve-pro-addon/commit/1f654bd))
   * remove character causing fatal error ([ebf4e1b](https://github.com/Codeinwp/neve-pro-addon/commit/ebf4e1b))
   * remove inexistent method call ([f693f6e](https://github.com/Codeinwp/neve-pro-addon/commit/f693f6e))
   * remove php linter due to errors ([639477b](https://github.com/Codeinwp/neve-pro-addon/commit/639477b))
   * remove placeholder when there's a product video ([32b077d](https://github.com/Codeinwp/neve-pro-addon/commit/32b077d))
   * remove unused files and scripts ([2e5b1cf](https://github.com/Codeinwp/neve-pro-addon/commit/2e5b1cf))
   * responsive number control min and max ([3f55509](https://github.com/Codeinwp/neve-pro-addon/commit/3f55509))
   * run grunt local ([b3cf8cd](https://github.com/Codeinwp/neve-pro-addon/commit/b3cf8cd))
   * run grunt local and fix php and jshint errors ([6d423aa](https://github.com/Codeinwp/neve-pro-addon/commit/6d423aa))
   * smaller featured video thumbnail in product gallery ([e6415cd](https://github.com/Codeinwp/neve-pro-addon/commit/e6415cd))
   * solve hook Codeinwp/neve-pro-addon[#71](https://github.com/Codeinwp/neve-pro-addon/issues/71) ([6ab1936](https://github.com/Codeinwp/neve-pro-addon/commit/6ab1936))
   * sticky cart total box issue when page content is not 100% ([72a1ecd](https://github.com/Codeinwp/neve-pro-addon/commit/72a1ecd))
   * submenu page causing neve about not to load anymore ([5c93067](https://github.com/Codeinwp/neve-pro-addon/commit/5c93067))
   * Syntax error in WooCommerce Booster module ([ae2bb4e](https://github.com/Codeinwp/neve-pro-addon/commit/ae2bb4e))
   * unit test ([5d89753](https://github.com/Codeinwp/neve-pro-addon/commit/5d89753))
   * unit test ([412a1b0](https://github.com/Codeinwp/neve-pro-addon/commit/412a1b0))
   * unit test ([05fa505](https://github.com/Codeinwp/neve-pro-addon/commit/05fa505))
   * unit test ([9045d60](https://github.com/Codeinwp/neve-pro-addon/commit/9045d60))
   * unit test php 5.4 ([cfa0987](https://github.com/Codeinwp/neve-pro-addon/commit/cfa0987))
   * unit test php5.4 ([2e237eb](https://github.com/Codeinwp/neve-pro-addon/commit/2e237eb))
   * update scss file ([ce515ff](https://github.com/Codeinwp/neve-pro-addon/commit/ce515ff))
   * verify slider selectors before adding sliders ([739f830](https://github.com/Codeinwp/neve-pro-addon/commit/739f830))
   * video size on load ([6819589](https://github.com/Codeinwp/neve-pro-addon/commit/6819589))
   * way the recently viewed widget is called ([087b416](https://github.com/Codeinwp/neve-pro-addon/commit/087b416))
   * wish list icon position ([6ed3075](https://github.com/Codeinwp/neve-pro-addon/commit/6ed3075))
   * wish list icon position ([f5d8908](https://github.com/Codeinwp/neve-pro-addon/commit/f5d8908))
   * wish-list page in account ([29f0882](https://github.com/Codeinwp/neve-pro-addon/commit/29f0882))

* **Features**

   * add customizer settings for single product [woo booster] ([dbdd34b](https://github.com/Codeinwp/neve-pro-addon/commit/dbdd34b))
   * add public check function for module status ([ed6af08](https://github.com/Codeinwp/neve-pro-addon/commit/ed6af08))
   * added control for transparent header Codeinwp/neve-pro-addon[#72](https://github.com/Codeinwp/neve-pro-addon/issues/72) ([4df9642](https://github.com/Codeinwp/neve-pro-addon/commit/4df9642))
   * adds contact component [hfg][[#70](https://github.com/Codeinwp/neve-pro-addon/issues/70)] ([4dfd856](https://github.com/Codeinwp/neve-pro-addon/commit/4dfd856))
   * adds social icons component [hfg][[#67](https://github.com/Codeinwp/neve-pro-addon/issues/67)] ([0e3b7cf](https://github.com/Codeinwp/neve-pro-addon/commit/0e3b7cf))
   * Blog Pro - new module ([132c565](https://github.com/Codeinwp/neve-pro-addon/commit/132c565))
   * finished repeater control ([426572e](https://github.com/Codeinwp/neve-pro-addon/commit/426572e))
   * finished work on woocommerce booster module ([f7c45b9](https://github.com/Codeinwp/neve-pro-addon/commit/f7c45b9))
   * icon field for repeater control [wip][[#67](https://github.com/Codeinwp/neve-pro-addon/issues/67)] ([6f939b8](https://github.com/Codeinwp/neve-pro-addon/commit/6f939b8))
   * mvp product video ([5b926f0](https://github.com/Codeinwp/neve-pro-addon/commit/5b926f0))
   * product content ordering [done] ([60bacd0](https://github.com/Codeinwp/neve-pro-addon/commit/60bacd0))
   * product feature video [done] ([591d3ed](https://github.com/Codeinwp/neve-pro-addon/commit/591d3ed))
   * Scroll to top - customizer controls ([6e1dd4f](https://github.com/Codeinwp/neve-pro-addon/commit/6e1dd4f))
   * woocommerce booster ([5d968dd](https://github.com/Codeinwp/neve-pro-addon/commit/5d968dd))
   * woocommerce booster [wip] ([29b6816](https://github.com/Codeinwp/neve-pro-addon/commit/29b6816))
   * woocommerce booster [wip] ([4c54869](https://github.com/Codeinwp/neve-pro-addon/commit/4c54869))
   * woocommerce booster [wip] ([b3d0f98](https://github.com/Codeinwp/neve-pro-addon/commit/b3d0f98))
   * woocommerce booster module [wip] ([50617d1](https://github.com/Codeinwp/neve-pro-addon/commit/50617d1))
   * woocommerce booster module [wip] ([4c2cfc4](https://github.com/Codeinwp/neve-pro-addon/commit/4c2cfc4))
   * woocommerce booster module [wip] ([e5b2f93](https://github.com/Codeinwp/neve-pro-addon/commit/e5b2f93))



#### [Version 0.0.3](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.2...v0.0.3) (2019-02-05)

* **Bug Fixes**
   * remove redundant distignore ([22b8671](https://github.com/Codeinwp/neve-pro-addon/commit/22b8671))



#### [Version 0.0.2](https://github.com/Codeinwp/neve-pro-addon/compare/v0.0.1...v0.0.2) (2019-02-05)

* **Bug Fixes**
   * build command ([66fab1f](https://github.com/Codeinwp/neve-pro-addon/commit/66fab1f))
   * proper responses to rest requests ([49900a5](https://github.com/Codeinwp/neve-pro-addon/commit/49900a5))

* **Features**
   * add most of dashboard ([d79ae4c](https://github.com/Codeinwp/neve-pro-addon/commit/d79ae4c))
   * add notice if plugin is used as a theme ([30293b7](https://github.com/Codeinwp/neve-pro-addon/commit/30293b7))
   * dashboard [wip] ([94e9261](https://github.com/Codeinwp/neve-pro-addon/commit/94e9261))



## Installation ##
The following are the steps to install the Neve Pro Addon plugin

1. In your WordPress Administration Panels, click on Add New option under Plugins from the menu.
Click on upload at the top.
2. Browse the location and select the Neve Pro Addon Plugin and click install now.
3. Go to Themes -> Neve Pro Options under the Neve Pro tab and configure your options.

## Resources ##
* FontAwesome, http://fontawesome.io/
* Copyright 2015, Dave Gandy,
* Font Awesome licensed under SIL OFL 1.1, http://scripts.sil.org/OFL
* Code licensed under MIT License, http://www.opensource.org/licenses/MIT
* Documentation licensed under CC BY 3.0, http://creativecommons.org/licenses/by/3.0/
