import React from 'react';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Button, Icon } from '@wordpress/components';

type MagicTagsComponentArgs = {
	magicTags: string[];
};

const MagicTagsComponent = ( { magicTags }: MagicTagsComponentArgs ) => {
	const copyToClipboard = ( text: string ) => {
		const dummy = document.createElement( 'textarea' );
		document.body.appendChild( dummy );
		dummy.value = text;
		dummy.select();
		document.execCommand( 'copy' );
		document.body.removeChild( dummy );
		window.wp.data
			.dispatch( 'core/notices' )
			.createNotice(
				'info',
				window.neveCustomLayouts.strings.copiedToClipboard,
				{
					type: 'snackbar',
					isDismissible: true,
				}
			);
	};

	const [ showMore, setShowMore ] = useState( false );

	if ( ! magicTags || ! ( magicTags.length > 0 ) ) {
		return null;
	}

	const SHOW_MORE_LIMIT = 6;
	const showMoreNeeded = magicTags.length > SHOW_MORE_LIMIT;

	const getVisibleTags = ( allMagicTags: string[] ) => {
		if ( ! showMoreNeeded ) {
			return allMagicTags;
		}

		const baseMagicTags = allMagicTags.slice( 0, SHOW_MORE_LIMIT );

		return showMore ? allMagicTags : baseMagicTags;
	};

	// show short length magic tags first (to able to show more tag side by side)
	magicTags.sort( ( a, b ) => {
		return a.length - b.length;
	} );

	const showMoreLabel = showMore
		? __( 'Show less', 'neve' )
		: __( 'Show more', 'neve' );

	return (
		<div className="magic-tags">
			<div className="heading">
				<Icon icon="clipboard" />
				<h3>{ __( 'Available Magic Tags', 'neve' ) }</h3>
			</div>

			{ getVisibleTags( magicTags ).map( ( tag, index ) => (
				<Button
					type="button"
					className="tag"
					key={ index }
					data-tag={ tag }
					isTertiary
					isSmall
					onClick={ ( e: React.MouseEvent ) => {
						if ( ! ( e.target instanceof HTMLButtonElement ) ) {
							return;
						}

						const tagEl = e.target.getAttribute( 'data-tag' );

						if ( ! tagEl ) {
							return;
						}

						copyToClipboard( tagEl );
					} }
				>
					{ tag }
				</Button>
			) ) }

			{ showMoreNeeded && (
				<>
					<hr></hr>
					<Button
						className="components-button is-link"
						onClick={ () => setShowMore( ! showMore ) }
					>
						<span>{ showMoreLabel }</span>
					</Button>
				</>
			) }
		</div>
	);
};

export default MagicTagsComponent;
