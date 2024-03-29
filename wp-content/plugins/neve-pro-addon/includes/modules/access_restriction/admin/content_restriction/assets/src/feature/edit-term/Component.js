/* global neveAccessRestriction */
import { __ } from '@wordpress/i18n';
import Toggle from '../../components/Toggle';
import { useState, useCallback } from '@wordpress/element';
import AsyncSelect from 'react-select/async';
import Select from 'react-select';
import { TextControl } from '@wordpress/components';
import { debounce } from 'lodash';
import { handlePasswordChange, loadUsersCallback } from '../../utils';

const TermSettings = () => {
	const [ restrictionTypes, setRestrictionTypes ] = useState(
		neveAccessRestriction.currentValues.restrictionTypes
	);
	const [ allowedPassword, setAllowedPassword ] = useState(
		neveAccessRestriction.currentValues.allowedPassword
	);
	const [ userRoles, setAllowedUserRoles ] = useState(
		neveAccessRestriction.currentValues.allowedUserRoles
	);

	const [ userIdOptions, setUserIdOptionsState ] = useState(
		neveAccessRestriction.currentValues.allowedUserIdOptions
	);

	const loadUsers = useCallback( loadUsersCallback, [] );

	const updateRestrictionTypeStatus = ( restrictionType, status ) => {
		const metaValue = {
			...restrictionTypes,
			[ restrictionType ]: status ? 'yes' : 'no',
		};

		setRestrictionTypes( metaValue );
	};

	const debouncedUserOptions = debounce( loadUsers, 500 );

	return (
		<>
			<h2>{ __( 'Content restriction', 'neve' ) }</h2>
			<input
				type="hidden"
				name="nv_ac_restriction_types"
				value={ JSON.stringify( restrictionTypes ) }
			/>
			<Toggle
				value={ restrictionTypes.user_role }
				save={ ( newValue ) => {
					window.tiTrk?.with( 'neve' ).set( 'term-user-role', {
						feature: 'content-restriction',
						featureComponent: 'term-user-role',
						featureValue: newValue,
					} );
					updateRestrictionTypeStatus( 'user_role', newValue );
				} }
				label={ __( 'Allowed User Roles', 'neve' ) }
			/>
			{ restrictionTypes.user_role === 'yes' && (
				<div className="nv-ac-margin">
					<Select
						name="nv_ac_user_roles"
						delimiter=","
						placeholder={ __( 'Search user role…', 'neve' ) }
						cacheOptions
						options={
							neveAccessRestriction.availableOptions.userRoles
						}
						isMulti={ true }
						onChange={ setAllowedUserRoles }
						defaultValue={ userRoles }
					/>
				</div>
			) }
			<Toggle
				value={ restrictionTypes.user_id }
				save={ ( newValue ) => {
					window.tiTrk?.with( 'neve' ).set( 'term-allowed-users', {
						feature: 'content-restriction',
						featureComponent: 'term-allowed-users',
						featureValue: newValue,
					} );
					updateRestrictionTypeStatus( 'user_id', newValue );
				} }
				label={ __( 'Allowed Users', 'neve' ) }
			/>
			{ restrictionTypes.user_id === 'yes' && (
				<div className="nv-ac-margin">
					<AsyncSelect
						name="nv_ac_users"
						delimiter=","
						placeholder={ __( 'Search by display name…', 'neve' ) }
						cacheOptions
						loadOptions={ debouncedUserOptions }
						isMulti={ true }
						onChange={ setUserIdOptionsState }
						defaultValue={ userIdOptions }
					/>
				</div>
			) }
			<Toggle
				value={ restrictionTypes.password }
				save={ ( newValue ) => {
					window.tiTrk?.with( 'neve' ).set( 'term-password', {
						feature: 'content-restriction',
						featureComponent: 'term-password',
						featureValue: newValue,
					} );
					updateRestrictionTypeStatus( 'password', newValue );
				} }
				label={ __( 'Password', 'neve' ) }
				disabledText={ __(
					'Password restriction is currently disabled due to the "Hide restricted content from the frontend" option being enabled from the dashboard.',
					'neve'
				) }
			/>
			{ restrictionTypes.password === 'yes' && (
				<div className="nv-ac-margin">
					<TextControl
						name="nv_ac_password"
						value={ allowedPassword }
						onChange={ ( value ) => {
							handlePasswordChange(
								value,
								neveAccessRestriction.allowedPassChars,
								setAllowedPassword
							);
						} }
					/>
				</div>
			) }
		</>
	);
};

export default TermSettings;
