import React from 'react';
import {
	Button,
	DatePicker,
	DateTimePicker,
	TimePicker,
	Flex,
	FlexItem,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { isRenderDebugOn } from '../../common/utils';
import DebugRender from '../shared/DebugRender';

/**
 * DateTime custom Component
 *
 * @param {DateTimePicker.Props} args
 * @class
 */
const DateTimeComponent = React.memo(
	( {
		currentDate,
		onChange,
	}: {
		currentDate: string;
		onChange: ( value: string ) => void;
	} ) => {
		const [ isVisible, setIsVisible ] = useState( false );

		const toggleVisible = () => {
			setIsVisible( ! isVisible );
			if ( currentDate ) {
				onChange( currentDate );
			}
		};

		return (
			<div className="neve-white-background-control">
				{ isRenderDebugOn && <DebugRender forLabel="Date" /> }
				<TimePicker
					currentTime={ currentDate }
					onChange={ onChange }
					is12Hour={ false }
				/>
				{ isVisible && (
					<DatePicker
						currentDate={ currentDate }
						onChange={ onChange }
					/>
				) }

				<Flex gap={ 2 } align="center" justify="space-between">
					<FlexItem>
						<Button
							type="button"
							className="components-button components-datetime__date-show-hide-calendar-button is-link"
							onClick={ toggleVisible }
						>
							{ isVisible
								? __( 'Hide calendar', 'neve' )
								: __( 'Show calendar', 'neve' ) }
						</Button>
					</FlexItem>
					<FlexItem>
						<Button
							className="components-button components-datetime__date-reset-button is-link"
							onClick={ () => onChange( '' ) }
						>
							{ __( 'Reset', 'neve' ) }
						</Button>
					</FlexItem>
				</Flex>
			</div>
		);
	}
);

export default DateTimeComponent;
