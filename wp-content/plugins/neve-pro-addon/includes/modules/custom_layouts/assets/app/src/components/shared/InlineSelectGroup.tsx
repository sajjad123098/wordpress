import React from 'react';
import { SelectControl } from '@wordpress/components';
import { Option } from '../../types/types';

/**
 * Type for SelectGroup
 */
type InlineSelectGroupArgs = {
	value: string | string[];
	onChange: ( nextValue: string ) => void;
	options: Option[];
	label: string;
	disabled: boolean;
};

/**
 * Select component that supports OptGroup.
 *
 * @param {InlineSelectGroupArgs} args
 * @class
 */
const InlineSelectGroup = ( {
	value,
	onChange,
	options,
	label,
	disabled = false,
}: InlineSelectGroupArgs ) => {
	return (
		<div className="select-inline font-weight">
			{ label && (
				<span className="customize-control-title">{ label }</span>
			) }
			<SelectControl
				disabled={ disabled }
				aria-label={ label }
				value={ value }
				onChange={ onChange }
			>
				{ options.map( ( option: Option, index ) => {
					if ( Array.isArray( option.value ) ) {
						const optGroupKey =
							option.id || `${ option.label }-${ index }`;
						return (
							<optgroup
								key={ optGroupKey }
								label={ option.label }
								disabled={ option.disabled }
							>
								{ option.value.map(
									( groupedOption, groupedOptionIndex ) => {
										const key =
											groupedOption.id ||
											`${ groupedOption.label }-${ groupedOption.value }-${ groupedOptionIndex }`;
										return (
											<option
												key={ key }
												value={ groupedOption.value }
												disabled={
													groupedOption.disabled
												}
											>
												{ groupedOption.label }
											</option>
										);
									}
								) }
							</optgroup>
						);
					}
					const key =
						option.id ||
						`${ option.label }-${ option.value }-${ index }`;
					return (
						<option
							key={ key }
							value={ option.value }
							disabled={ option.disabled }
						>
							{ option.label }
						</option>
					);
				} ) }
			</SelectControl>
		</div>
	);
};

export default InlineSelectGroup;
