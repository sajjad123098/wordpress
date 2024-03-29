/* global neveAccessRestriction */
import classnames from 'classnames';

import { ToggleControl, SelectControl } from '@wordpress/components';

const defaultValueCallback = ( settings, key ) => settings[ key ];
const Fields = ( {
	type,
	updateSetting,
	settings,
	valueCallback = defaultValueCallback,
} ) => {
	const { fields } = neveAccessRestriction.fields[ type ];

	return (
		<>
			{ Object.keys( fields ).map( ( key, index ) => {
				const { type: fieldType, label, description } = fields[ key ];

				if ( fields[ key ].parent ) {
					const parent = fields[ key ].parent;

					if ( settings[ parent.fieldKey ] !== parent.fieldValue ) {
						return null;
					}
				}

				const value = valueCallback( settings, key );

				return (
					<div
						key={ index }
						className={ classnames( [ 'field-wrap', key ] ) }
					>
						{ 'toggle' === fieldType && (
							<>
								<ToggleControl
									checked={ value === 'yes' || false }
									label={ label }
									onChange={ ( newValue ) => {
										const status = newValue ? 'yes' : 'no';
										updateSetting( key, status );
									} }
								/>
								{ value && description && (
									<p className="description">
										{ description }
									</p>
								) }
							</>
						) }
						{ 'select' === fieldType && (
							<>
								<SelectControl
									value={ value }
									label={ label }
									options={ fields[ key ].options }
									onChange={ ( newValue ) => {
										updateSetting( key, newValue );
									} }
								/>
								{ value && description && (
									<p className="description">
										{ description }
									</p>
								) }
							</>
						) }
					</div>
				);
			} ) }
		</>
	);
};

export default Fields;
