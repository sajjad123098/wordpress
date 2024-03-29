import React from 'react';

import { __ } from '@wordpress/i18n';
import { Suspense } from '@wordpress/element';
import { Spinner } from '@wordpress/components';

import { InlineSelect } from '@neve-wp/components';
import { isRenderDebugOn, mapSimpleOptions } from '../../common/utils';
import DebugRender from '../shared/DebugRender';

/**
 * Type for SidebarSelect
 */
type SidebarSelectArgs = {
	sidebarPositions: Record< string, string >;
	selectedValue: string;
	onChange: ( nextValue: string ) => void;
};

/**
 * SidebarSelect Component
 *
 * @param {SidebarSelectArgs} args
 * @class
 */
export const SidebarSelect = React.memo(
	( { selectedValue, onChange, sidebarPositions }: SidebarSelectArgs ) => {
		const sidebarOptions = mapSimpleOptions( sidebarPositions );

		return (
			<Suspense fallback={ <Spinner /> }>
				<div className="neve-white-background-control">
					{ isRenderDebugOn && <DebugRender forLabel="Sidebar" /> }
					<InlineSelect
						disabled={ false }
						label={ __( 'Sidebar', 'neve' ) }
						value={ selectedValue }
						onChange={ onChange }
						options={ sidebarOptions }
					/>
				</div>
			</Suspense>
		);
	}
);
