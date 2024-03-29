jQuery( window ).on( 'elementor/frontend/init', function () {
	var InstaCarouselHandler = elementorModules.frontend.handlers.Base.extend( {
		onInit: function () {
			elementorModules.frontend.handlers.Base.prototype.onInit.apply(
				this,
				arguments
			);
			this.run();
		},
		run: function () {
			var settings = this.getElementSettings();

			if ( settings.display_type !== 'carousel' ) {
				return;
			}

			if ( typeof settings === 'undefined' || settings === null ) {
				console.log(
					'Neve Elementor Booster: Instagram widget could not find settings'
				);
				return;
			}

			/**
			 * We need to find a more specific class name for the widget so the swiper library can with muliple
			 * Carousels on the same page. The element class list created by Elementor would contain an ID for the particular element
			 * So the result of the below operations would leave you with something like:
			 * .elementor-element.elementor-element-db45bd9.elementor-widget.elementor-widget-neve_instagram_feed .swiper-container.neb
			 */
			const parts = this.$element[ 0 ].classList.value.split( ' ' );
			const classname =
				'.' + parts.join( '.' ) + ' .swiper-container.neb';

			const instagramCarouselElement = new Swiper( classname, {
				autoplay: settings[ 'autoplay_carousel' ]
					? {
							delay: settings[ 'autoplay_carousel_timeout' ]
								? parseInt(
										settings[ 'autoplay_carousel_timeout' ]
								  ) * 1000
								: 2000,
							disableOnInteraction: false,
							reverseDirection:
								settings[ 'loop_type' ] === 'reverse'
									? true
									: false,
					  }
					: false,
				breakpoints: {
					640: {
						slidesPerView: 1,
						spaceBetween: settings.space_between_mobile.size,
					},
					768: {
						slidesPerView: 2,
						spaceBetween: settings.space_between_tablet.size,
					},
					1024: {
						slidesPerView: settings[
							'number_of_carousel_images_to_show'
						]
							? settings[ 'number_of_carousel_images_to_show' ]
							: 4,
						spaceBetween: settings.space_between.size,
					},
				},
				slidesPerGroup: settings[ 'slide_by' ]
					? settings[ 'slide_by' ]
					: 1,
				loop: settings[ 'loop_type' ] !== 'disabled' ? true : false,
				// If we need pagination
				pagination: {
					el: '.swiper-pagination',
				},
				// Navigation arrows
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},
				// And if we need scrollbar
				scrollbar: {
					el: '.swiper-scrollbar',
				},
			} );
		},
	} );

	window.elementorFrontend.hooks.addAction(
		'frontend/element_ready/neve_instagram_feed.default',
		function ( $scope ) {
			new InstaCarouselHandler( { $element: $scope } );
		}
	);
} );
