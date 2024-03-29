import React from 'react';

import { __ } from '@wordpress/i18n';

import NumberInput, { NumberInputArgs } from '../shared/NumberInput';
import { isRenderDebugOn } from '../../common/utils';
import DebugRender from '../shared/DebugRender';

/**
 * InsideNumberInput Component
 *
 * @param {NumberInputArgs} args
 * @class
 */
const NumberInputComponent = React.memo(
	( { label, defaultValue, value, onChange, max }: NumberInputArgs ) => {
		return (
			<div className="neve-white-background-control">
				{ isRenderDebugOn && (
					<DebugRender forLabel={ label || __( 'Number', 'neve' ) } />
				) }
				<NumberInput
					label={ label || __( 'Number', 'neve' ) }
					defaultValue={ defaultValue }
					value={ value }
					onChange={ onChange }
					max={ max || 100 }
				/>
			</div>
		);
	}
);

export default NumberInputComponent;
