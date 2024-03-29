import '../../style.scss';

import { registerPlugin } from '@wordpress/plugins';
import Component from './Component';

registerPlugin( 'nv-access-restriction-post-settings', {
	render: Component,
} );
