/* global neveAccessRestriction */
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import Toggle from '../../components/Toggle';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { useState, useCallback } from '@wordpress/element';
import AsyncSelect from 'react-select/async';
import Select from 'react-select';
import { debounce } from 'lodash';

import { TextControl } from '@wordpress/components';
import { handlePasswordChange, loadUsersCallback } from '../../utils';

const META_KEY_RESTRICTION_TYPES =
	neveAccessRestriction.metaKeys.restrictionTypes;
const META_KEY_ALLOWED_USER_IDS = neveAccessRestriction.metaKeys.allowedUserIds;
const META_KEY_ALLOWED_USER_ROLES =
	neveAccessRestriction.metaKeys.allowedUserRoles;
const META_KEY_RESTRICTION_PASSWORD =
	neveAccessRestriction.metaKeys.restrictionPassword;
const CURRENT_USER_ROLES = neveAccessRestriction.currentValues.allowedUserRoles;

const Component = ( { metaFields, setMetaFields } ) => {
	const restrictionTypes = metaFields[ META_KEY_RESTRICTION_TYPES ];

	const [ userIdOptions, setUserIdOptionsState ] = useState(
		neveAccessRestriction.currentValues.allowedUserIdOptions
	);

	const loadUsers = useCallback( loadUsersCallback, [] );

	const updateRestrictionTypeStatus = ( restrictionType, status ) => {
		const metaValue = {
			...restrictionTypes,
			[ restrictionType ]: status ? 'yes' : 'no',
		};

		setMetaFields( { [ META_KEY_RESTRICTION_TYPES ]: metaValue } );
	};

	const updateAllowedUserIds = ( values ) => {
		const userIds = values.map( ( args ) => args.value );
		setMetaFields( { [ META_KEY_ALLOWED_USER_IDS ]: userIds } );
		setUserIdOptionsState( values );
	};

	const updateAllowedUserRoles = ( values ) => {
		const userRoles = values.map( ( args ) => args.value );
		setMetaFields( { [ META_KEY_ALLOWED_USER_ROLES ]: userRoles } );
	};

	const debouncedUserOptions = debounce( loadUsers, 500 );

	const currentPostType = wp.data
		.select( 'core/editor' )
		.getCurrentPostType();

	return (
		<PluginDocumentSettingPanel
			name="content-restriction"
			title={ __( 'Content Restriction', 'neve' ) }
		>
			<Toggle
				value={ restrictionTypes.user_role }
				save={ ( newValue ) => {
					window.tiTrk
						?.with( 'neve' )
						.set(
							'content-restriction-' +
								currentPostType +
								'-user-role',
							{
								feature: 'content-restriction',
								featureComponent:
									currentPostType + '-user-role',
								featureValue: newValue,
							}
						);
					updateRestrictionTypeStatus( 'user_role', newValue );
				} }
				label={ __( 'Allowed User Roles', 'neve' ) }
			/>
			{ restrictionTypes.user_role === 'yes' && (
				<div className="nv-ac-margin">
					<Select
						placeholder={ __( 'Search user role…', 'neve' ) }
						cacheOptions
						options={
							neveAccessRestriction.availableOptions.userRoles
						}
						isMulti={ true }
						onChange={ updateAllowedUserRoles }
						defaultValue={ CURRENT_USER_ROLES }
					/>
				</div>
			) }
			<Toggle
				value={ restrictionTypes.user_id }
				save={ ( newValue ) => {
					window.tiTrk
						?.with( 'neve' )
						.set(
							'content-restriction-' +
								currentPostType +
								'-allowed-users',
							{
								feature: 'content-restriction',
								featureComponent:
									currentPostType + '-allowed-users',
								featureValue: newValue,
							}
						);
					updateRestrictionTypeStatus( 'user_id', newValue );
				} }
				label={ __( 'Allowed Users', 'neve' ) }
			/>
			{ restrictionTypes.user_id === 'yes' && (
				<div className="nv-ac-margin">
					<AsyncSelect
						placeholder={ __( 'Search by display name…', 'neve' ) }
						cacheOptions
						loadOptions={ debouncedUserOptions }
						isMulti={ true }
						onChange={ updateAllowedUserIds }
						defaultValue={ userIdOptions }
					/>
				</div>
			) }
			<Toggle
				value={ restrictionTypes.password }
				save={ ( newValue ) => {
					window.tiTrk
						?.with( 'neve' )
						.set(
							'content-restriction' +
								currentPostType +
								'-password',
							{
								feature: 'content-restriction',
								featureComponent: currentPostType + '-password',
								featureValue: newValue,
							}
						);
					updateRestrictionTypeStatus( 'password', newValue );
				} }
				label={ __( 'Password', 'neve' ) }
			/>
			{ restrictionTypes.password === 'yes' && (
				<div className="nv-ac-margin">
					<TextControl
						value={ metaFields[ META_KEY_RESTRICTION_PASSWORD ] }
						onChange={ ( value ) => {
							handlePasswordChange(
								value,
								neveAccessRestriction.allowedPassChars,
								( val ) => {
									setMetaFields( {
										[ META_KEY_RESTRICTION_PASSWORD ]: val,
									} );
								}
							);
						} }
					/>
				</div>
			) }
		</PluginDocumentSettingPanel>
	);
};

const applyWithSelect = withSelect( ( select ) => {
	const metaFields = select( 'core/editor' ).getEditedPostAttribute( 'meta' );
	return {
		metaFields,
	};
} );

const applyWithDispatch = withDispatch( ( dispatch ) => {
	return {
		setMetaFields( value ) {
			dispatch( 'core/editor' ).editPost( { meta: value } );
		},
	};
} );

export default compose( [ applyWithSelect, applyWithDispatch ] )( Component );
