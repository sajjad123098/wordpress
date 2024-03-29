import React, { useEffect } from 'react';

import { __ } from '@wordpress/i18n';
import { Button, Spinner } from '@wordpress/components';
import { Suspense, useState } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { withSelect, withDispatch } from '@wordpress/data';

import { Toggle, maybeParseJson } from '@neve-wp/components';

import { LayoutSelect } from './selects/LayoutSelect';
import { HooksSelect } from './selects/HooksSelect';
import { SidebarSelect } from './selects/SidebarSelect';
import { SidebarActionsSelect } from './selects/SidebarActionsSelect';

import { PostMeta } from '../types/types';
import { InsideSelect } from './selects/InsideSelect';
import NumberInputComponent from './inputs/NumberInputComponent';
import DateTimeComponent from './inputs/DateTimeComponent';
import ConditionalPanel, { Rules } from './conditional/ConditionalPanel';
import { copyTextToClipboard } from '../common/utils';

type Props = {
	postMeta: PostMeta;
	currentPostId: number;
	setPostMeta: ( newValue: Record< string, unknown > | null ) => void;
	setPostTitle: ( newValue: string ) => void;
	removeEditorPanel: < T = void >( panel: string ) => T;
};

const MainPanel: React.FC< Props > = ( {
	postMeta,
	currentPostId,
	setPostMeta,
	setPostTitle,
	removeEditorPanel,
} ) => {
	const {
		layouts,
		hooks,
		sidebarPositions,
		sidebarActions,
		insidePositions,
	} = window.neveCustomLayouts.sidebarOptions;

	const REMOVE_PANELS = [
		'featured-image',
		'post-excerpt',
		'discussion-panel',
		'page-attributes',
	];
	REMOVE_PANELS.forEach( ( panel ) => {
		removeEditorPanel( panel );
	} );

	const META_LAYOUT = 'custom-layout-options-layout';
	const META_HOOK = 'custom-layout-options-hook';
	const META_PRIORITY = 'custom-layout-options-priority-v2';
	const META_SIDEBAR = 'custom-layout-options-sidebar';
	const META_SIDEBAR_ACTIONS = 'custom-layout-options-sidebar-action';
	const META_INSIDE = 'custom-layout-options-inside-display';
	const META_EVENTS_NO = 'custom-layout-options-events-no';
	const META_EXPIRE = 'custom-layout-options-should-expire';
	const META_EXP_DATE = 'custom-layout-expiration-date';
	const META_CONDITIONAL = 'custom-layout-conditional-logic';

	const [ isloaded, setLoaded ] = useState( false );
	const [ conditions, setConditions ] = useState( () => {
		return ( ( maybeParseJson( postMeta[ META_CONDITIONAL ] as string ) ??
			[] ) as unknown ) as Rules[][];
	} );

	const [ date, setDate ] = useState(
		postMeta[ META_EXP_DATE ]
			? new Date( postMeta[ META_EXP_DATE ] )
			: new Date()
	);

	const showConditional = [
		'header',
		'inside',
		'single_post',
		'single_page',
		'archives',
		'footer',
		'global',
		'hooks',
		'sidebar',
	].includes( postMeta[ META_LAYOUT ] as never );

	const showIndividual = postMeta[ META_LAYOUT ] === 'individual';

	const showHooks = postMeta[ META_LAYOUT ] === 'hooks';

	const showSidebar = postMeta[ META_LAYOUT ] === 'sidebar';

	const hasManySidebars = Object.keys( sidebarPositions ).length > 1;

	const showInside = postMeta[ META_LAYOUT ] === 'inside';

	const showExpirationDate = postMeta[ META_EXPIRE ];

	const updateExpirationDate = ( newDate: string | null ) => {
		if ( newDate === null || newDate === '' ) {
			newDate = new Date().toISOString();
		}

		setDate( new Date( newDate ) );
		setPostMeta( {
			[ META_EXP_DATE ]: newDate,
		} );
	};

	const changeLayout = ( nextValue: string ) => {
		// @ts-ignore
		window.tiTrk?.with( 'neve' ).set( 'custom-layouts-sidebar-layout', {
			feature: 'custom-layouts-sidebar',
			featureComponent: 'layout',
			featureValue: nextValue,
		} );
		setPostMeta( { [ META_LAYOUT ]: nextValue } );
	};

	const changeExpire = ( nextValue: Record< string, unknown > ) => {
		// @ts-ignore
		window.tiTrk
			?.with( 'neve' )
			.set( 'custom-layouts-sidebar-expire-date', {
				feature: 'custom-layouts-sidebar',
				featureComponent: 'expire-date',
				featureValue: nextValue,
			} );
		setPostMeta( {
			[ META_EXPIRE ]: ( nextValue as unknown ) as boolean,
		} );
	};

	const changeHook = ( nextValue: string ) => {
		// @ts-ignore
		window.tiTrk?.with( 'neve' ).set( 'custom-layouts-sidebar-hook', {
			feature: 'custom-layouts-sidebar',
			featureComponent: 'hook',
			featureValue: nextValue,
		} );
		setPostMeta( {
			[ META_HOOK ]: nextValue,
		} );
	};

	const changePriority = (
		nextValue: number | Record< string, unknown >
	) => {
		// @ts-ignore
		window.tiTrk?.with( 'neve' ).set( 'custom-layouts-sidebar-priority', {
			feature: 'custom-layouts-sidebar',
			featureComponent: 'priority',
			featureValue: nextValue,
		} );
		setPostMeta( {
			[ META_PRIORITY ]: nextValue,
		} );
	};

	const changeSidebar = ( nextValue: string ) => {
		// @ts-ignore
		window.tiTrk?.with( 'neve' ).set( 'custom-layouts-sidebar-sidebar', {
			feature: 'custom-layouts-sidebar',
			featureComponent: 'sidebar',
			featureValue: nextValue,
		} );
		setPostMeta( {
			[ META_SIDEBAR ]: nextValue,
		} );
	};

	const changeSidebarAction = ( nextValue: string ) => {
		// @ts-ignore
		window.tiTrk
			?.with( 'neve' )
			.set( 'custom-layouts-sidebar-sidebar-action', {
				feature: 'custom-layouts-sidebar',
				featureComponent: 'sidebar-action',
				featureValue: nextValue,
			} );
		setPostMeta( {
			[ META_SIDEBAR_ACTIONS ]: nextValue,
		} );
	};

	const changeInside = ( nextValue: string ) => {
		// @ts-ignore
		window.tiTrk?.with( 'neve' ).set( 'custom-layouts-sidebar-inside', {
			feature: 'custom-layouts-sidebar',
			featureComponent: 'inside',
			featureValue: nextValue,
		} );
		setPostMeta( {
			[ META_INSIDE ]: nextValue,
		} );
	};

	const changeEventNo = ( nextValue: number | Record< string, unknown > ) => {
		// @ts-ignore
		window.tiTrk?.with( 'neve' ).set( 'custom-layouts-sidebar-event-no', {
			feature: 'custom-layouts-sidebar',
			featureComponent: 'event-no',
			featureValue: nextValue,
		} );
		setPostMeta( {
			[ META_EVENTS_NO ]: nextValue,
		} );
	};

	const changeConditionalRules = ( nextValue: Rules[][] ) => {
		// @ts-ignore
		window.tiTrk
			?.with( 'neve' )
			.set( 'custom-layouts-sidebar-conditional-rules', {
				feature: 'custom-layouts-sidebar',
				featureComponent: 'conditional-rules',
				featureValue: JSON.stringify( nextValue ),
			} );
		const filteredValue = Object.values( nextValue )
			.map( function ( group ) {
				let itemToCheck = group as Array< Rules >;
				itemToCheck = itemToCheck.filter( function ( condition ) {
					if (
						Array.isArray( condition.end ) &&
						condition.end.length === 0
					) {
						return false;
					}

					if (
						Array.isArray( condition.root ) &&
						condition.root.length === 0
					) {
						return false;
					}

					if (
						Array.isArray( condition.end ) &&
						condition.end.length === 0
					) {
						return false;
					}

					return condition.end !== '' && condition.root !== '';
				} );

				return itemToCheck;
			} )
			.filter( ( value ) => JSON.stringify( value ) !== '[]' );

		const valueToUpdate =
			Object.keys( filteredValue ).length !== 0
				? JSON.stringify( filteredValue )
				: null;
		setPostMeta( { [ META_CONDITIONAL ]: valueToUpdate } );
	};

	const shortCodeText = `[nv-custom-layout id="${ currentPostId }"]`;

	useEffect( () => {
		if ( ! isloaded ) {
			const queryString = window.location.search;
			const urlParams = new URLSearchParams( queryString );
			if (
				postMeta[ META_LAYOUT ] &&
				postMeta[ META_LAYOUT ] === 'none' &&
				urlParams.get( 'layout' )
			) {
				changeLayout( urlParams.get( 'layout' ) as string );
			}

			if (
				postMeta[ META_HOOK ] &&
				postMeta[ META_HOOK ] === 'none' &&
				urlParams.get( 'hook' )
			) {
				changeHook( urlParams.get( 'hook' ) as string );
			}

			if ( urlParams.get( 'title' ) ) {
				setPostTitle( urlParams.get( 'title' ) as string );
			}

			setLoaded( true );
		}
	}, [] );

	return (
		<>
			<Suspense fallback={ <Spinner /> }>
				<LayoutSelect
					layouts={ layouts }
					selectedValue={ postMeta[ META_LAYOUT ] || '' }
					onChange={ changeLayout }
				/>

				{ showIndividual && (
					<div className="neve-white-background-control">
						<span>{ __( 'Available shortcode:', 'neve' ) }</span>
						<Button
							className="short-code"
							icon="welcome-write-blog"
							label={ __( 'Copy shortcode', 'neve' ) }
							showTooltip={ true }
							isSmall
							onClick={ async () => {
								await copyTextToClipboard( shortCodeText );
							} }
						>
							{ shortCodeText }
						</Button>
					</div>
				) }

				<Toggle
					label={ __( 'Enable expiration date', 'neve' ) }
					checked={ postMeta[ META_EXPIRE ] || false }
					onChange={ changeExpire }
				/>

				{ showExpirationDate && (
					<DateTimeComponent
						currentDate={ date.toISOString() }
						onChange={ updateExpirationDate }
					/>
				) }

				{ showHooks && (
					<>
						<HooksSelect
							hooks={ hooks }
							selectedValue={ postMeta[ META_HOOK ] || '' }
							onChange={ changeHook }
						/>
					</>
				) }

				{ showSidebar && hasManySidebars && (
					<SidebarSelect
						sidebarPositions={ sidebarPositions }
						selectedValue={ postMeta[ META_SIDEBAR ] || '' }
						onChange={ changeSidebar }
					/>
				) }

				{ showSidebar && (
					<SidebarActionsSelect
						sidebarActions={ sidebarActions }
						selectedValue={ postMeta[ META_SIDEBAR_ACTIONS ] || '' }
						onChange={ changeSidebarAction }
					/>
				) }

				{ ( showHooks || showSidebar ) && (
					<NumberInputComponent
						label={ __( 'Priority', 'neve' ) }
						defaultValue={ 10 }
						value={ postMeta[ META_PRIORITY ] || 10 }
						onChange={ changePriority }
						max={ 150 }
					/>
				) }

				{ showInside && (
					<>
						<InsideSelect
							insidePositions={ insidePositions }
							selectedValue={ postMeta[ META_INSIDE ] || '' }
							onChange={ changeInside }
						/>

						<NumberInputComponent
							defaultValue={ 1 }
							value={ postMeta[ META_EVENTS_NO ] || 1 }
							onChange={ changeEventNo }
						/>
					</>
				) }

				{ showConditional && (
					<ConditionalPanel
						description={ __(
							'If no conditional logic is selected, the Custom Layout will be applied site-wide.',
							'neve'
						) }
						selectedRules={ conditions }
						onChange={ setConditions }
						updateDb={ changeConditionalRules }
					/>
				) }
			</Suspense>
		</>
	);
};

export default compose(
	withSelect( ( select ) => {
		return {
			postMeta: select( 'core/editor' ).getEditedPostAttribute(
				'meta'
			) as PostMeta,
			currentPostId: select( 'core/editor' ).getCurrentPostId() as number,
		};
	} ),
	withDispatch( ( dispatch ) => {
		const { removeEditorPanel } = dispatch( 'core/edit-post' );
		return {
			setPostMeta(
				newMeta:
					| null
					| string
					| boolean
					| number
					| Record< string, unknown >
			) {
				dispatch( 'core/editor' ).editPost( { meta: newMeta } );
			},
			setPostTitle( newTitle: string ) {
				dispatch( 'core/editor' ).editPost( { title: newTitle } );
			},
			removeEditorPanel,
		};
	} )
)( MainPanel );
