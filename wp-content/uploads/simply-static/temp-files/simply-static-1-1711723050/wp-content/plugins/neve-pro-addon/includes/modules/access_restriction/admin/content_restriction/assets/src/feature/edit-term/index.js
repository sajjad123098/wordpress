import '../../style.scss';

import { render } from '@wordpress/element';
import Component from './Component';

const target = document.getElementById( 'neve-access-restriction-te' );
if ( target ) {
	render( <Component />, target );
}
