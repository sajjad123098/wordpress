/**
 * Exported function that calls the initialization
 */
/*global jQuery, _,Event*/
export default function repeater() {
	init();
}

/**
 * Initialize the repeaters.
 */
function init() {
	const repeaters = document.querySelectorAll( '.nv-repeater--wrap' );
	_.each( repeaters, function ( repeaterElement ) {
		const reorderButton = repeaterElement.querySelector(
				'.nv-repeater--reorder'
			),
			addButton = repeaterElement.querySelector(
				'.nv-repeater--add-new'
			),
			items = repeaterElement.querySelectorAll(
				'.nv-repeater--items-wrap .nv-repeater--item'
			),
			itemsWrap = repeaterElement.querySelector(
				'.nv-repeater--items-wrap'
			),
			itemTemplate = repeaterElement.querySelector(
				'.nv-repeater--hidden-item .nv-repeater--item'
			);

		// Initialize all repeater items.
		_.each( items, function ( item ) {
			initializeRepeaterItem( item, repeaterElement );
		} );

		// Initialize add button.
		addButton.addEventListener( 'click', function () {
			const newItem = itemTemplate.cloneNode( true );
			initializeRepeaterItem( newItem, repeaterElement );
			itemsWrap.appendChild( newItem );
			updateValues( repeaterElement );
		} );

		// Initialize the reordering.
		reorderButton.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			repeaterElement.classList.toggle( 'reordering' );
			updateValues( repeaterElement );
		} );
	} );
}

function initializeRepeaterItem( item, repeaterElement ) {
	const visibilityToggle = item.querySelector( '.nv-repeater--toggle' ),
		expander = item.querySelector( '.nv-repeater--item-title' ),
		orderButtonDown = item.querySelector( '.reorder-btn.down' ),
		orderButtonUp = item.querySelector( '.reorder-btn.up' ),
		removeButton = item.querySelector( '.nv-repeater--remove-item' ),
		titleInput = item.querySelector( 'input[data-key="title"]' ),
		titleTag = item.querySelector( '.nv-repeater--title-text' ),
		colorInputs = item.querySelectorAll( '.color-picker-hex' ),
		valueInputs = item.querySelectorAll( '.has-value' ),
		iconPickers = item.querySelectorAll( '.nv--icon-field-wrap' );

	// Toggle Visibility.
	visibilityToggle.addEventListener( 'click', function () {
		this.parentNode.classList.toggle( 'visibility-hidden' );
		this.setAttribute(
			'data-value',
			this.getAttribute( 'data-value' ) === 'yes' ? 'no' : 'yes'
		);
		updateValues( repeaterElement );
	} );

	// Expand Item.
	expander.addEventListener( 'click', function () {
		this.parentNode.parentNode.classList.toggle( 'expanded' );
	} );

	// Remove Item.
	removeButton.addEventListener( 'click', function () {
		item.parentNode.removeChild( item );
		updateValues( repeaterElement );
	} );

	// Move Item Down.
	orderButtonDown.addEventListener( 'click', function ( e ) {
		e.stopPropagation();
		const nextItem = item.nextSibling;
		if ( ! nextItem ) {
			return false;
		}
		nextItem.parentNode.insertBefore( item, nextItem.nextSibling );
		updateValues( repeaterElement );
	} );

	// Move Item Up.
	orderButtonUp.addEventListener( 'click', function ( e ) {
		e.stopPropagation();
		const previousItem = item.previousSibling;
		if ( ! previousItem ) {
			return false;
		}
		item.parentNode.insertBefore( item, item.previousSibling );
		updateValues( repeaterElement );
	} );

	// Sync Title.
	titleInput.addEventListener( 'input', function () {
		titleTag.innerHTML =
			this.value !== '' ? this.value : titleTag.dataset.default;
		updateValues( repeaterElement );
	} );

	// Initialize Color Pickers.
	_.each( colorInputs, function ( input ) {
		jQuery( input ).wpColorPicker( {
			change() {
				// Timeout as iris color picker is a bit weird.
				setTimeout( function () {
					updateValues( repeaterElement );
				}, 1 );
			},
			clear() {
				updateValues( repeaterElement );
			},
		} );
	} );

	// Initialize Icon Pickers
	_.each( iconPickers, function ( iconPicker ) {
		const selectButton = iconPicker.querySelector( '.nv--icon-selector' ),
			input = iconPicker.querySelector( 'input' ),
			clear = iconPicker.querySelector( '.nv--remove-icon' ),
			icons = iconPicker.querySelectorAll( '.nv--icons-container > a' ),
			search = iconPicker.querySelector( '.nv--icons-search > input' );

		// Toggle picker
		selectButton.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			iconPicker.classList.toggle( 'nv--iconpicker-expanded' );
			search.value = '';
			search.dispatchEvent( new Event( 'input' ) );
			search.focus();
		} );

		// Icon clear
		clear.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			input.value = '';
			selectButton.innerHTML =
				'<span class="dashicons dashicons-plus"></span>';
			const selected = iconPicker.querySelector( 'a.selected' );
			if ( selected !== null ) {
				selected.classList.remove( 'selected' );
			}
			updateValues( repeaterElement );
		} );

		// Icon selection
		_.each( icons, function ( icon ) {
			icon.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				selectButton.innerHTML = icon.innerHTML;
				input.value = icon.dataset.icon;
				const selected = iconPicker.querySelector( 'a.selected' );
				if ( selected !== null ) {
					selected.classList.remove( 'selected' );
				}
				icon.classList.add( 'selected' );
				iconPicker.classList.remove( 'nv--iconpicker-expanded' );
				updateValues( repeaterElement );
			} );
		} );

		// Search functionality
		search.addEventListener( 'input', function ( e ) {
			const filter = e.target.value.toLowerCase().replace( /\s+/g, '' );
			_.each( icons, function ( icon ) {
				if ( icon.dataset.icon.toLowerCase().indexOf( filter ) > -1 ) {
					icon.style.display = '';
				} else {
					icon.style.display = 'none';
				}
			} );
		} );
	} );

	// Initialize inputs that have a value.
	_.each( valueInputs, function ( input ) {
		input.addEventListener( 'change', function () {
			updateValues( repeaterElement );
		} );
	} );
}

/**
 * Update the repeater values.
 *
 * @param {Node} repeaterElement
 */
function updateValues( repeaterElement ) {
	const collectorInput = repeaterElement.querySelector(
			'.nv-repeater--collector'
		),
		items = repeaterElement.querySelectorAll(
			'.nv-repeater--items-wrap .nv-repeater--item'
		),
		newValue = [];

	_.each( items, function ( item ) {
		const inputs = item.querySelectorAll( '.has-value' ),
			itemValue = {};
		_.each( inputs, function ( input ) {
			const key = input.dataset.key;
			let value;
			if ( input.getAttribute( 'type' ) === 'checkbox' ) {
				value = input.checked;
			} else {
				value = input.dataset.value ? input.dataset.value : input.value;
			}
			itemValue[ key ] = value;
		} );
		newValue.push( itemValue );
	} );

	collectorInput.value = JSON.stringify( newValue );
	jQuery( collectorInput ).trigger( 'change' );
}
