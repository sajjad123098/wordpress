import React from 'react';

import { NumberControl } from '@neve-wp/components';

/**
 * Type for NumberInput
 */
export type NumberInputArgs = {
	label?: string;
	defaultValue: number;
	value: number;
	onChange: ( nextValue: Record< string, unknown > | number ) => void;
	className?: string;
	step?: number;
	min?: number;
	max?: number;
};

/**
 * Input base component for Number Inputs.
 *
 * @param {NumberInputArgs} args
 * @class
 */
const NumberInput = ( {
	label = '',
	defaultValue,
	value,
	onChange,
	className = '',
	step = 1,
	min = 1,
	max = 100,
}: NumberInputArgs ) => {
	return (
		<NumberControl
			label={ label }
			step={ step }
			units={ [] }
			value={ value }
			onChange={ onChange }
			onReset={ () => {
				onChange( defaultValue );
			} }
			default={ defaultValue }
			max={ max }
			min={ min }
			hasResponsive={ false }
			onChangedDevice={ onChange }
			className={ className }
		/>
	);
};

export default NumberInput;
