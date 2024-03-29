/* global neveAccessRestriction */
import Fields from './Fields';
import { saveOption } from '../utils/rest';
import classnames from 'classnames';

import { useState } from '@wordpress/element';
import { Dashicon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const App = () => {
	const [ settings, setSettings ] = useState( neveAccessRestriction.options );
	const [ saving, setSaving ] = useState( false );
	const [ error, setError ] = useState( '' );

	const moreSettingsSlug = 'restrict-content-addon-more-settings';
	const seenMore = window.localStorage.getItem( moreSettingsSlug ) === 'seen';
	const [ openMore, setOpenMore ] = useState( seenMore || true );
	const classesAccordion = classnames( [
		'accordion',
		{ open: openMore, closed: ! openMore },
	] );

	const updateContentTypeStatus = ( slug, status ) => {
		const newSettings = { ...settings };

		newSettings.content_types[ slug ].enabled = status;

		setSettings( newSettings );
		saveAsync( newSettings );
	};

	const updateSetting = ( slug, value ) => {
		const newSettings = {
			...settings,
			[ slug ]: value,
		};

		setSettings( newSettings );
		saveAsync( newSettings );
	};

	const saveAsync = ( newSettings = null ) => {
		const settingsToSave = newSettings || settings;
		setSaving( true );
		setError( '' );
		saveOption( JSON.stringify( settingsToSave ) ).then( ( r ) => {
			if ( ! r.success ) {
				setError(
					__( 'An error occurred. Please try again.', 'neve' )
				);
				setSaving( false );
				return false;
			}

			setError( '' );
			setSaving( false );
		} );
	};

	return (
		<>
			<Fields
				type="content_types"
				updateSetting={ updateContentTypeStatus }
				valueCallback={ ( callbackSettings, callbackKey ) => {
					return callbackSettings[ callbackKey ].enabled;
				} }
				settings={ settings.content_types }
			/>
			<div className={ classesAccordion }>
				<button
					aria-expanded={ openMore }
					className="accordion-header"
					onClick={ () => {
						if (
							openMore &&
							! seenMore &&
							moreSettingsSlug !== undefined
						) {
							window.localStorage.setItem(
								moreSettingsSlug,
								'seen'
							);
						}
						setOpenMore( ! openMore );
					} }
				>
					<div className="accordion-title">
						{ __( 'More settings', 'neve' ) }
					</div>
					<Dashicon
						icon={ openMore ? 'arrow-up-alt2' : 'arrow-down-alt2' }
					/>
				</button>
				<div
					className="accordion-body"
					style={ { height: openMore ? 'auto' : '0px' } }
				>
					<Fields
						type="restriction_behavior"
						updateSetting={ updateSetting }
						settings={ settings }
					/>
				</div>
			</div>
			{ saving && (
				<>
					<Dashicon
						size={ 18 }
						icon="update"
						className="is-loading"
					/>
					<span>{ __( 'Updating', 'neve' ) + '...' }</span>
				</>
			) }
			{ '' !== error && (
				<span
					className="notice notice-error"
					style={ { width: '100%', margin: 0, padding: '8px' } }
				>
					{ error }
				</span>
			) }
		</>
	);
};

export default App;
