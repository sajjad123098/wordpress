import { ToggleControl } from '@wordpress/components';

const Toggle = ( { value, save, label } ) => (
	<ToggleControl
		checked={ value === 'yes' || false }
		label={ label }
		onChange={ save }
	/>
);

export default Toggle;
