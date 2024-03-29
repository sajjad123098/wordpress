/* global neveAccessRestriction */
import apiFetch from '@wordpress/api-fetch';

export const saveOption = ( value ) => {
	return new Promise( ( resolve ) => {
		apiFetch( {
			path: neveAccessRestriction.settingsRoute,
			method: 'POST',
			data: { settings: value },
		} )
			.then( ( responseRaw ) => {
				const response = JSON.parse( responseRaw );
				const status = response.status === 'success';
				resolve( { success: status } );
			} )
			.catch( () => {
				resolve( { success: false } );
			} );
	} );
};
