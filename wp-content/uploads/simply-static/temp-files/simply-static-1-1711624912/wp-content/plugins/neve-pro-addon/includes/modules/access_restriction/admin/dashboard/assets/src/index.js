import './style.scss';
import App from './Components/App';
import { render } from '@wordpress/element';
window.addEventListener( 'neve-dashboard-react-placeholder', ( event ) => {
	if ( event.detail.slug ) {
		const target = document.getElementById( event.detail.slug );
		if ( target ) {
			render( <App />, target );
		}
	}
} );
