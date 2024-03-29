/* global jQuery */
jQuery( document ).ready( function ( $ ) {
	$( 'body' ).addClass( 'js' );

	/**
	 * EDD cart element in the header
	 */
	const cartTotalAmount = $( '.nv-edd-cart-total' );

	function updateEDDTotalCartAmount( response ) {
		let total = response.total;

		/**
		 * If this class exists on the element then we don't want to drop the currency symbol.
		 * See edd_cart_total_currency_symbol()
		 */
		if ( ! cartTotalAmount.hasClass( 'nv-cart-icon-total-currency' ) ) {
			total = response.total.replace( /[^0-9.,]/g, '' );
		}
		cartTotalAmount.html( total );
	}
	/**
	 * Handle cart total live refresh
	 */
	$( 'body' ).on( 'edd_cart_item_added', function ( event, response ) {
		updateEDDTotalCartAmount( response );
	} );

	$( 'body' ).on( 'edd_cart_item_removed', function ( event, response ) {
		updateEDDTotalCartAmount( response );
	} );
} );
