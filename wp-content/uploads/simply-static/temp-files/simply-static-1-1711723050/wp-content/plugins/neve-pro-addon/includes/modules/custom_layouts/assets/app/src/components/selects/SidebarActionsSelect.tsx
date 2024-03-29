import React from 'react';

import { __ } from '@wordpress/i18n';
import { Suspense } from '@wordpress/element';
import { Spinner, SelectControl } from '@wordpress/components';

import { isRenderDebugOn, mapSimpleOptions } from '../../common/utils';
import DebugRender from '../shared/DebugRender';

/**
 * Type for SidebarActionsSelect
 */
type SidebarActionsSelectArgs = {
	sidebarActions: Record< string, string >;
	selectedValue: string;
	onChange: ( nextValue: string ) => void;
};

/**
 * SidebarActionsSelect Component
 *
 * @param {SidebarActionsSelectArgs} args
 * @class
 */
export const SidebarActionsSelect = React.memo(
	( {
		selectedValue,
		onChange,
		sidebarActions,
	}: SidebarActionsSelectArgs ) => {
		const sidebarActionsOptions = mapSimpleOptions( sidebarActions, true );

		const label = __( 'Action', 'neve' );
		return (
			<Suspense fallback={ <Spinner /> }>
				<div className="neve-white-background-control">
					{ isRenderDebugOn && <DebugRender forLabel="Actions" /> }
					<div className="select-inline font-weight">
						{ label && (
							<span className="customize-control-title">
								{ label }
							</span>
						) }
						<SelectControl
							disabled={ false }
							aria-label={ label }
							value={ selectedValue }
							onChange={ onChange }
							options={ sidebarActionsOptions }
							help={ sidebarActions[ selectedValue ] || '' }
						/>
					</div>
				</div>
			</Suspense>
		);
	}
);
