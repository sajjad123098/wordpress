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

##### [Version 2.5.2](https://github.com/Codeinwp/neve-pro-addon/compare/v2.5.1...v2.5.2) (2023-02-15)

- [Feat] Add "Hide My Library" option for the White label module
- [Fix] Remove unused language file
- [Fix] Language switcher over search canvas 
- [Fix] Typekit fonts when loading them locally

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
- [Fix] "Select icon" tooltip in the mega menu admin screen
- Test WooCommerce 7.3
- Improve the testing process

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
- Test WooCommerce and update the "tested up to" version

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

- [Feat] Custom "Thank You Pages" - [Learn More](https://bit.ly/nv-custom-ty)
- [Fix] Fixed the issue with slow dashboard when there are a lot of users registered on a website
- [Fix] Comparison table is not translatable
- [Fix] Product links on images require double-tap to open the product page
- [Fix] Compatibility issue between comparison table sticky bar and top positioned sticky add to cart
- [Fix] My Account drop-downs misaligned on Desktop
- Reduce JS file size for WooCommerce Booster

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
- [Fix] "Show Hooks" button appearing for users that are not admins
- [Fix] Sticky header issue that occurs on the mobile devices with admin bar visible
- [Fix] SiteGround Optimization plugin conflict
- Optimize markup for Compare and Wishlist product card buttons

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
- [Feat] Added WooCommerce products variation swatches
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
- [Copywriter](https://themeisle.com/demo/?theme=Copywriter)
- New Gutenberg and Beaver Builder Starter Sites

##### [Version 1.1.10](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.9...v1.1.10) (2020-04-06)

- [Fix] Force Product Image Height maximum value back to 500px
- [Fix] Infinite scroll on product terms
- [Fix] PHP editor in Custom layouts in WP 5.4
- New Elementor Starter Sites
- New Gutenberg Starter Sites

#### [Version 1.1.9](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.8...v1.1.9) (2020-03-23)

- [Feat] Refactor customizer UI
- [Feat] Add WP-CLI commands for license activation/deactivation
- [Fix] Compatibility issues with PHP 7.4
- [Fix] View Cart position on the Shop page
- [University](https://themeisle.com/demo/?theme=University)
- [Interior Design](https://themeisle.com/demo/?theme=Interior%20Design)
- [Electronics Shop](https://themeisle.com/demo/?theme=Electronics%20Shop)
- [Print Shop](https://themeisle.com/demo/?theme=Print%20Shop)
- [City Tours](https://themeisle.com/demo/?theme=City%20Tours)
- [Insurance](https://themeisle.com/demo/?theme=Insurance)
- [Personal Blog](https://themeisle.com/demo/?theme=Personal%20Blog)
- New Beaver Builder Starter Sites

#### [Version 1.1.8](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.7...v1.1.8) (2020-02-26)

- [Feat] New My Account component in the header builder
- [Feat] Sticky footer
- [Feat] New options for the Cart Icon component: dynamic label, multiple icons and styles
- [Feat] Option for a custom number of columns in the Related Products section
- [Feat] New hooks available in Custom Layouts for the Cart and Checkout pages
- [Fix] Filter products by price when using Infinite Scroll
- [Fix] Sale tag position in the Quick View modal
- [Fix] Warning with Conditional Header when WooCommerce is disabled
- [Scuba Diving](https://themeisle.com/demo/?theme=Scuba%20Diving)
- [Wine Bar](https://themeisle.com/demo/?theme=Wine%20Bar)
- [Mountain Bike Racing](https://themeisle.com/demo/?theme=Mountain%20Bike%20Race)
- [Movie](https://themeisle.com/demo/?theme=Movie%20Showcase)
- [Running Club](https://themeisle.com/demo/?theme=Running%20Club)
- [Jewellery Shop](https://themeisle.com/demo/?theme=Jewellery%20Shop)
- [Car Service](https://themeisle.com/demo/?theme=Car%20Service)
- [Museum](https://themeisle.com/demo/?theme=Museum)
- [Art Exhibition](https://themeisle.com/demo/?theme=Art%20Exhibition)
- [Conference](https://themeisle.com/demo/?theme=Conference)

- New Beaver Builder Starter Sites

#### [Version 1.1.7](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.6...v1.1.7) (2020-02-13)

- [Feat] Extend the transparent header feature for each conditional header
- [Feat] New second menu icon component
- [Fix] Conditional Headers not escaping quotes and slashes for Custom Layouts
- [Fix] Conditional Headers when using a child theme
- [Fix] Conditional Headers buttons when Gutenberg is installed
- [Fix] Product related filters not available in the Custom Layouts

#### [Version 1.1.6](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.5...v1.1.6) (2020-02-03)

- [Feat] New Conditional Headers
- [Feat] New off-canvas sidebar for the Shop page
- [Feat] New image styles for the shop page: Blur, Fade in, Fade out, Glow, Colorize and Grayscale
- [Feat] New payment icons available on the Cart page and in the footer builder
- [Feat] New alignment option for product title and price on the same line on the shop page
- [Feat] New text color and formatting options for the Sale tag on discounted products
- [Feat] New seamless add to cart mechanism for the single product page
- [Fix] Edit Popups in Elementor
- [Fix] Outer Border Top Color applied to all Review Boxes widgets on one page
- [Fix] Buttons alignment in the Flip Card widget
- [Industrial](https://themeisle.com/demo/?theme=Industrial)
- [Adventure](https://themeisle.com/demo/?theme=Adventure)
- [Product Launch](https://themeisle.com/demo/?theme=Product%20Launch)
- [Accounting](https://themeisle.com/demo/?theme=Accounting)
- [Beauty Shop](https://themeisle.com/demo/?theme=Beauty%20Shop)
- [Recruitment Agency](https://themeisle.com/demo/?theme=Recruitment%20Agency)
- [Artist](https://themeisle.com/demo/?theme=Artist)
- [VR Studio](https://themeisle.com/demo/?theme=VR%20Studio)
- [Makeup Artist](https://themeisle.com/demo/?theme=Makeup%20Artist)

- New Beaver Builder Starter Sites
- New Gutenberg Starter Sites

#### [Version 1.1.5](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.4...v1.1.5) (2019-12-19)

- [Fix] JavaScript error with the Flip Card widget

#### [Version 1.1.4](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.3...v1.1.4) (2019-12-19)

- [Feat] Team Member Elementor Widget
- [Feat] Progress Circle Elementor Widget
- [Feat] Banner Elementor Widget
- [Feat] Content Switcher Elementor Widget
- [Feat] Custom Field Elementor Widget
- [Feat] Typography and padding options for the Share Buttons Elementor Widget
- [Feat] Particles for Elementor sections
- [Feat] Animations for Elementor widgets
- [Feat] Content Protection for Elementor widgets
- [Feat] Mobile version for the page header
- [Feat] Color, typography and spacing controls for the Review Box widget
- [Fix] Icons not visible in the Share Buttons widget
- [Fix] Typekit Fonts module not loading fonts
- [Fix] Icons not visible in the Review Box widget when Elementor Pro is activated

- [Fashion Model Agency](https://themeisle.com/demo/?theme=Fashion%20Model%20Agency)
- [IT Technology Agency](https://themeisle.com/demo/?theme=IT%20Technology%20Agency)
- [Christmas Market](https://themeisle.com/demo/?theme=Christmas%20Market)
- [Podcast](https://themeisle.com/demo/?theme=Podcast)
- [Digital Agency](https://themeisle.com/demo/?theme=Digital%20Agency)

- New Beaver Builder Starter Sites

#### [Version 1.1.3](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.2...v1.1.3) (2019-12-05)

- [Feat] New option to hide the Starter Sites Library in the White Label module
- [Feat] Controls for two separate logos in the header builder
- [Feat] New dedicated typography controls for the Contact and Wish List components of the header builder
- [Feat] New dedicated typography controls for the Page header menu and HTML components of the page header builder
- [Blogger](https://themeisle.com/demo/?theme=Blogger)
- [Coupons](https://themeisle.com/demo/?theme=Coupons%20Discounts%20and%20Offers)
- [Escape Room](https://themeisle.com/demo/?theme=Escape%20Room)
- [Wellness Spa](https://themeisle.com/demo/?theme=Wellness%20Spa)
- [Online Courses](https://themeisle.com/demo/?theme=Online%20Courses)
- [Resume](https://themeisle.com/demo/?theme=Resume)

- New Beaver Builder Starter Sites
- New Divi Starter Sites

#### [Version 1.1.2](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.1...v1.1.2) (2019-11-19)

- [Fix] Elementor Pro templates not loading when Related Post was enabled
- [Winery - Wine Shop](https://themeisle.com/demo/?theme=Winery%20-%20Wine%20Shop)
- [News Magazine](https://themeisle.com/demo/?theme=News%20Magazine)
- [Coworking space](https://themeisle.com/demo/?theme=Coworking%20Space)
- [Fishing and Hunting Club](https://themeisle.com/demo/?theme=Fishing%20Hunting%20Club)
- [Car Rental](https://themeisle.com/demo/?theme=Car%20Rental)
- [Ski Resort](https://themeisle.com/demo/?theme=Ski%20Resort%20Winter%20Hotel)
- [Digital Product Funnel](https://themeisle.com/demo/?theme=Digital%20Product%20Funnel)
- [Moving Company Logistics Services](https://themeisle.com/demo/?theme=Moving%20Company%20Logistics%20Services)
- [Translation and Interpreter Services](https://themeisle.com/demo/?theme=Translation%20and%20Interpreter%20Services)

- New Beaver Builder Starter Sites

#### [Version 1.1.1](https://github.com/Codeinwp/neve-pro-addon/compare/v1.1.0...v1.1.1) (2019-11-04)

- [Feat] New color options for the header builder Contact component
- [Feat] New Social icons component for the footer builder
- [Fix] Better explain conditional logic in Custom Layouts
- [Fix] Removed unused page templates in Custom Layouts
- [Fix] Transparent header overriding WP dropdown menus
- [Fix] Missing link on Shop products images
- [Cake Shop](https://themeisle.com/demo/?theme=Cake%20Shop)
- [Transport](https://themeisle.com/demo/?theme=Transport)
- [Speed](https://themeisle.com/demo/?theme=Speed)
- [eBook](https://themeisle.com/demo/?theme=eBook)
- [Food Magazine](https://themeisle.com/demo/?theme=Food%20Magazine)
- [eCourse - Web Design](https://themeisle.com/demo/?theme=eCourse%20Web%20Design)
- [Yoga Studio](https://themeisle.com/demo/?theme=Yoga%20Studio)
- [Sales Funnel](https://themeisle.com/demo/?theme=Crypto%20Sales%20Funnel)
- [Church](https://themeisle.com/demo/?theme=Church)
- [Fashion Magazine](https://themeisle.com/demo/?theme=Fashion%20Magazine)
- New Thrive Architect Starter Sites
- New Beaver Builder Starter Sites
- New Brizy Starter Sites

### v0.0.1 - 2019-01-15 
    **Changes:**
