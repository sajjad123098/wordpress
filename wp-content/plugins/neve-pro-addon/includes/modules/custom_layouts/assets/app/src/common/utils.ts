import { Option } from '../types/types';

/**
 * Check if Render debug is ON.
 */
const isRenderDebugOn =
	window.neveCustomLayouts.renderDebug === 'true' || false;

/**
 * Uppercase all words from string.
 *
 * @param {string} str
 */
const ucwords = ( str: string ) => {
	return ( str + '' ).replace( /^([a-z])|\s+([a-z])/g, function ( $1 ) {
		return $1.toUpperCase();
	} );
};

/**
 * Transform hook name into label.
 *
 * @param {string} str
 */
const beautifyHookName = ( str: string ) => {
	str = str
		.replace( /_/g, ' ' )
		.replace( 'neve', ' ' )
		.replace( 'nv', ' ' )
		.replace( 'woocommerce', ' ' );
	return ucwords( str );
};

/**
 * Map values for a simple select choices.
 *
 * @param {{string, string}[]} options
 * @param {boolean} useKeyAsLabel Will set the label from key value.
 */
const mapSimpleOptions = (
	options: Record< string, string >,
	useKeyAsLabel = false
) => {
	return Object.keys( options ).map( ( key ) => {
		return {
			value: key,
			label: useKeyAsLabel ? ucwords( key ) : options[ key ],
		};
	} );
};

const mapOptGroupRoot = (
	roots: Record<
		string,
		{ label: string; choices: Record< string, string > }
	>
) => {
	return Object.keys( roots ).map( ( root ) => {
		const choices = roots[ root ].choices;
		const processedChoices = Object.keys( choices ).map( ( choice ) => {
			return {
				label: choices[ choice ],
				value: choice,
			};
		} );

		return {
			label: roots[ root ].label ? roots[ root ].label : ucwords( root ),
			value: processedChoices,
		};
	} );
};

/**
 * Map values for a OptGroup Select choices.
 *
 * @param {{string, string}[]} options
 * @param {boolean} beautifyLabel Will use the child values create labels.
 */
const mapOptGroupOptions = ( options: Option[], beautifyLabel = false ) => {
	return Object.keys( options ).map( ( option ) => {
		// @ts-ignore
		const values = options[ option ];
		let mappedValues = values;
		const valueKeys = Object.keys( values );
		const hasChildValues =
			Array.isArray( values ) ||
			( typeof values === 'object' && values !== null );
		if ( hasChildValues ) {
			mappedValues = valueKeys.map( ( name, index ) => {
				// @ts-ignore
				const element = ( values[ name ] || values[ index ] ) as string;
				// For items where the numeric index is the same with the name value use the element instead.
				// `hooks` will use element value, `inside_content` will use name as value.
				const itemValue =
					parseInt( name, 10 ) === index ? element : name;
				return {
					value: itemValue,
					label: beautifyLabel
						? beautifyHookName( element )
						: element,
				};
			} );
		}
		return {
			value: mappedValues,
			label: ucwords( option ),
		};
	} );
};

const copyTextToClipboard = async ( text: string ) => {
	if ( navigator.clipboard ) {
		return await navigator.clipboard.writeText( text );
	}
	return document.execCommand( 'copy', true, text );
};

export {
	ucwords,
	beautifyHookName,
	mapSimpleOptions,
	mapOptGroupOptions,
	mapOptGroupRoot,
	isRenderDebugOn,
	copyTextToClipboard,
};
