import React from 'react';

import { __ } from '@wordpress/i18n';
import { Suspense } from '@wordpress/element';
import { Spinner } from '@wordpress/components';

import { InlineSelect } from '@neve-wp/components';
import { mapSimpleOptions, isRenderDebugOn } from '../../common/utils';
import DebugRender from '../shared/DebugRender';

/**
 * Type for LayoutSelect
 */
type LayoutSelectArgs = {
	selectedValue: string;
	onChange: ( nextValue: string ) => void;
	layouts: Record< string, string >;
};

/**
 * LayoutSelect Component
 *
 * @param {LayoutSelectArgs} args
 * @class
 */
export const LayoutSelect = React.memo(
	( { selectedValue, onChange, layouts }: LayoutSelectArgs ) => {
		const layoutOptions = mapSimpleOptions( layouts );

		return (
			<Suspense fallback={ <Spinner /> }>
				<div className="neve-white-background-control">
					{ isRenderDebugOn && <DebugRender forLabel="Layout" /> }
					<InlineSelect
						disabled={ false }
						label={ __( 'Layout', 'neve' ) }
						value={ selectedValue }
						onChange={ onChange }
						options={ layoutOptions }
					/>
				</div>
			</Suspense>
		);
	}
);
