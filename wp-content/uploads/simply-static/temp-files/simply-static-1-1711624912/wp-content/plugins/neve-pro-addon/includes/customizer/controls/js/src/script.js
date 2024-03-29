import initializeRepeater from './repeater.js';

function initEDDArchiveFocus() {
	wp.customize.section( 'neve_edd_archive', ( section ) => {
		section.expanded.bind( ( isExpanded ) => {
			if ( isExpanded ) {
				wp.customize.previewer.previewUrl.set(
					'/index.php?post_type=download'
				);
			}
		} );
	} );

	wp.customize.section( 'neve_edd_typography', ( section ) => {
		section.expanded.bind( ( isExpanded ) => {
			if ( isExpanded ) {
				wp.customize.previewer.previewUrl.set(
					'/index.php?post_type=download'
				);
			}
		} );
	} );
}

/**
 * Find the Scroll to top button within the customizer preview.
 */
function findScrollToTopBtn() {
	const iframeElement = document.querySelector( '#customize-preview iframe' );

	if ( ! iframeElement ) {
		return;
	}

	const scrollToTopBtn = iframeElement.contentWindow.document.querySelector(
		'#scroll-to-top'
	);

	return scrollToTopBtn;
}

/**
 * Show the Scroll to Top button as soon as the user enters the section in Customizer.
 */
function previewScrollToTopChanges() {
	wp.customize.section( 'neve_scroll_to_top', ( section ) => {
		section.expanded.bind( ( isExpanded ) => {
			const scrollToTopBtn = findScrollToTopBtn();

			if ( ! scrollToTopBtn ) {
				return;
			}

			// If Scroll to top customizer section is expanded
			if ( isExpanded ) {
				wp.customize.previewer.bind( 'ready', () => {
					wp.customize.previewer.send( 'nv-opened-stt', true );
				} );
				scrollToTopBtn.style.visibility = 'visible';
				scrollToTopBtn.style.opacity = '1';
			} else {
				// Hide the button when we leave the section
				scrollToTopBtn.style.visibility = 'hidden';
				scrollToTopBtn.style.opacity = '0';
			}
		} );
	} );
}

wp.customize.bind( 'ready', function () {
	initializeRepeater();
	previewScrollToTopChanges();
	initEDDArchiveFocus();
} );
