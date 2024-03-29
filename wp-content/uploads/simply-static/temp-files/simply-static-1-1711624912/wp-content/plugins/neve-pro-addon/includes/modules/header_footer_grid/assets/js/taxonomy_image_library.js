/*global TaxonomyTermTranslations, jQuery */
( function ( $ ) {
	const taxonomyTermImageTranslations = TaxonomyTermTranslations || {};

	const taxonomyTermImage = {
		file_frame: null,
		settings: {
			title:
				taxonomyTermImageTranslations.modalTitle ||
				'Select or upload a featured image',
			button: {
				text: taxonomyTermImageTranslations.modalButton || 'Use image',
			},
			multiple: false,
			library: {
				type: 'image',
			},
		},

		/**
		 * Initialize script
		 */
		init() {
			const _this = this;
			$( 'body' ).on(
				'click',
				'.taxonomy-term-image-attach',
				function ( event ) {
					event.preventDefault();

					_this.openModal();
				}
			);

			$( '.taxonomy-term-image-remove' ).click( _this.removeImage );
		},

		/**
		 * Remove the current image by emptying the container and field
		 */
		removeImage() {
			$( '#taxonomy-term-image-container' ).html( '' );
			$( '#taxonomy-term-image-id' ).val( '' );
		},

		/**
		 * Open the media modal window
		 */
		openModal() {
			const _this = this;

			// If the media frame already exists, reopen it.
			if ( _this.file_frame ) {
				_this.file_frame.open();
				return;
			}

			// Create the media frame.
			_this.file_frame = wp.media.frames.file_frame = wp.media(
				_this.settings
			);

			// When an image is selected, run a callback.
			_this.file_frame.on( 'select', function () {
				_this.file_frame
					.state()
					.get( 'selection' )

					// handle each attachment
					.map( _this.updateImage );
			} );

			// Finally, open the modal
			_this.file_frame.open();
		},

		/**
		 * Handle a single selected image attachment
		 *
		 * @param {Array} attachment
		 */
		updateImage( attachment ) {
			// the selected image
			const image = attachment.toJSON();

			// retrieve image url.
			let url;
			const sizes = attachment.get( 'sizes' );
			if ( typeof sizes !== 'undefined' )
				url =
					typeof sizes.thumbnail === 'undefined'
						? sizes.full.url
						: sizes.thumbnail.url;
			else url = attachment.get( 'url' );

			//image.id
			$( '#taxonomy-term-image-id' ).val( image.id );

			//sizes.thumbnail.url
			$( '#taxonomy-term-image-container' ).html(
				"<img class='taxonomy-term-image-attach' src='" + url + "' />"
			);
		},
	};

	$( document ).ready( function () {
		taxonomyTermImage.init();
	} );
} )( jQuery );
