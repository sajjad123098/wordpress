import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

const WP_LIST_USERS_ENDPOINT = '/wp/v2/users';

export const handlePasswordChange = (
	value,
	allowedChars,
	setStateCallback
) => {
	const lastChar = value.charAt( value.length - 1 );

	if ( allowedChars.includes( lastChar ) ) {
		setStateCallback( value );
	}
};

export const loadUsersCallback = ( userName, callback ) => {
	if ( ! userName ) {
		callback( [] );
	}

	const queryParams = { search: userName };

	apiFetch( {
		path: addQueryArgs( WP_LIST_USERS_ENDPOINT, queryParams ),
	} ).then( ( users ) => {
		const usersFormatted = users.map( ( user ) => {
			return {
				value: user.id,
				label: user.name,
			};
		} );

		callback( usersFormatted );
	} );
};
