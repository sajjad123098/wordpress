import React, { Component, KeyboardEventHandler } from 'react';

import removeAccents from 'remove-accents';
import classnames from 'classnames';

import {
	withFocusOutside,
	BaseControl,
	Flex,
	FlexItem,
} from '@wordpress/components';

import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { speak } from '@wordpress/a11y';

// @ts-ignore
import { FixedSizeList as List } from 'react-window';

import { chevronDown, chevronUp, Icon } from '@wordpress/icons';
import { useInstanceId } from '@wordpress/compose';

export type Option = {
	label: string;
	value: string | number;
	isGroupLabel?: boolean;
	isSubGroup?: boolean;
};

/**
 * Extend the base Component to hook the `onFocusOutside` function.
 */
const DetectOutside = withFocusOutside(
	// @ts-ignore
	class extends Component {
		handleFocusOutside( event: Event ) {
			// @ts-ignore
			this.props.onFocusOutside( event );
		}

		render() {
			return this.props.children;
		}
	}
);

/**
 * RowProps Type
 */
type RowProps = {
	index: number;
	style: React.CSSProperties;
	data: {
		onChange: ( nextValue: string | number ) => void;
		options: Option[];
		focusedItemIndex: number;
		query: string;
		id: string;
	};
};

/**
 * The Row component for the list items,
 *
 * @param {RowProps} args
 * @class
 */
const Row = ( { index, style, data }: RowProps ) => {
	const { id, options, onChange, focusedItemIndex, query } = data;
	const [ isFocused, setIsFocus ] = useState( false );

	let label = options[ index ].label;
	const isGroupLabel = options[ index ].isGroupLabel || false;
	if ( isGroupLabel ) {
		return (
			<button
				id={ `${ id }_ls_${ index }` }
				className="components-form-token-field__suggestion"
				type="button"
				style={ {
					...style,
					background: 'none',
					color: '#757575',
					border: 'none',
					padding: '0 0 0 8px',
					textAlign: 'left',
					cursor: 'default',
				} }
			>
				<strong>{ label }</strong>
			</button>
		);
	}

	if ( query.length > 3 ) {
		label = label.replace(
			new RegExp( `(${ query })`, 'gi' ),
			'<strong>$1</strong>'
		);
	}
	const padding = options[ index ]?.isSubGroup ? 24 : 8;

	return (
		<button
			id={ `${ id }_ls_${ index }` }
			className={ classnames( 'components-form-token-field__suggestion', {
				'is-selected': isFocused,
			} ) }
			type="button"
			style={ {
				...style,
				background:
					index === focusedItemIndex || isFocused
						? 'var(--wp-admin-theme-color)'
						: 'none',
				color:
					index === focusedItemIndex || isFocused
						? '#fff'
						: '#757575',
				border: 'none',
				padding: `0 0 0 ${ padding }px`,
				textAlign: 'left',
				textOverflow: 'ellipsis',
				overflowX: 'hidden',
				whiteSpace: 'nowrap',
			} }
			onClick={ () => {
				onChange( options[ index ].value );
			} }
			onFocus={ () => setIsFocus( true ) }
			onBlur={ () => setIsFocus( false ) }
			onMouseOver={ () => setIsFocus( true ) }
			onMouseOut={ () => setIsFocus( false ) }
			dangerouslySetInnerHTML={ { __html: label } }
		/>
	);
};

/**
 * VirtualizedComboBoxProps Type
 */
type VirtualizedComboBoxProps = {
	value: string | number;
	onChange: ( nextValue: string ) => void;
	options: Option[];
	label?: string;
	messages?: { selected: string };
	setQueryOpt?: ( value: string ) => void;
	queryOpt?: string;
	dynamicQuery?: boolean;
};

/**
 * VirtualizedComboBox Component
 *
 * @param {VirtualizedComboBoxProps} args
 * @class
 */
const VirtualizedComboBox = React.memo(
	( {
		value,
		onChange,
		options,
		label = '',
		messages = {
			selected: __( 'Item selected.', 'neve' ),
		},
		queryOpt = '',
		setQueryOpt,
		dynamicQuery = false,
	}: VirtualizedComboBoxProps ) => {
		const [ isExpanded, setIsExpanded ] = useState( false );
		const [ inputHasFocus, setInputHasFocus ] = useState( false );
		const [ query, setQuery ] = useState( dynamicQuery ? queryOpt : '' );
		const [ focusedItemIndex, setFocusedItemIndex ] = useState( -1 );

		const currentOption = options.find(
			( option: Option ) => option.value === value
		);
		const [ selectedSuggestion, setSelectedSuggestion ] = useState(
			currentOption || null
		);
		const currentLabel = currentOption?.label ?? '';
		const listRef: React.RefObject< {
			scrollToItem: ( index: number, align: string ) => void;
		} > = React.createRef();
		const instanceId = useInstanceId(
			VirtualizedComboBox,
			'virtualized-combobox-control'
		);

		const togglePopover = () => {
			setIsExpanded( ! isExpanded );
		};

		const onFocus = () => {
			setInputHasFocus( true );
			setIsExpanded( true );
			setQuery( '' );
		};

		const onBlur = () => {
			setInputHasFocus( false );
		};

		const onFocusOutside = () => {
			setIsExpanded( false );
		};

		const onSuggestionSelected = ( newSelectedSuggestion: Option ) => {
			onChange( newSelectedSuggestion.value as string );
			speak( messages.selected, 'assertive' );
			setSelectedSuggestion( newSelectedSuggestion );
			setQuery( '' );
			setIsExpanded( false );
		};

		const getNextIndex = ( offset = 1 ) => {
			let nextIndex = focusedItemIndex + offset;
			if ( nextIndex < 0 ) {
				nextIndex = options.length - 1;
			} else if ( nextIndex >= options.length ) {
				nextIndex = 0;
			}
			return nextIndex;
		};

		const handleArrowNavigation = ( offset = 1 ) => {
			let nextIndex = getNextIndex( offset );
			let offsetDirection = offset;
			while ( options[ nextIndex ]?.isGroupLabel ) {
				offsetDirection =
					offset < 0 ? offsetDirection - 1 : offsetDirection + 1;
				nextIndex = getNextIndex( offsetDirection );
				if ( nextIndex === 0 || nextIndex === options.length - 1 ) {
					break;
				}
			}

			setSelectedSuggestion( options[ nextIndex ] );
			setFocusedItemIndex( nextIndex );
			setIsExpanded( true );
		};

		const onKeyDown = ( event: KeyboardEvent ) => {
			let preventDefault = false;

			if ( event.defaultPrevented ) {
				return;
			}

			switch ( event.code ) {
				case 'Enter':
					if ( selectedSuggestion ) {
						onSuggestionSelected( selectedSuggestion );
						preventDefault = true;
					}
					break;
				case 'ArrowUp':
					handleArrowNavigation( -1 );
					preventDefault = true;
					break;
				case 'ArrowDown':
					handleArrowNavigation( 1 );
					preventDefault = true;
					break;
				case 'Escape':
					setIsExpanded( false );
					setSelectedSuggestion( null );
					preventDefault = true;
					break;
				default:
					break;
			}

			if ( preventDefault ) {
				event.preventDefault();
			}
		};

		const matchingSuggestionsToFirstIndex = () => {
			const match = removeAccents( query.toLocaleLowerCase() );
			let foundKey = 0;

			for ( let i = 0; i < options.length; i++ ) {
				const index = removeAccents( options[ i ].label )
					.toLocaleLowerCase()
					.indexOf( match );
				if ( index !== -1 ) {
					foundKey = i;
					break;
				}
			}

			return foundKey;
		};

		const onInputChange = ( event: { target: { value: string } } ) => {
			if ( dynamicQuery && setQueryOpt ) {
				setQueryOpt( event.target.value );
			}
			setQuery( event.target.value );
			const matchIndex = matchingSuggestionsToFirstIndex();

			if ( inputHasFocus ) {
				setIsExpanded( true );
			}

			if ( listRef.current ) {
				listRef.current.scrollToItem( matchIndex, 'center' );
				setFocusedItemIndex( matchIndex );
				setSelectedSuggestion( options[ matchIndex ] );
			}
		};

		const selectChange = ( nextValue: string ) => {
			onChange( nextValue );
			setInputHasFocus( false );
			setIsExpanded( false );
			setQuery( '' );
		};

		return (
			<DetectOutside onFocusOutside={ onFocusOutside }>
				<BaseControl id={ instanceId as string } label={ label }>
					<div
						className="components-combobox-control__suggestions-container"
						tabIndex={ -1 }
						onKeyDown={
							( onKeyDown as unknown ) as KeyboardEventHandler< HTMLDivElement >
						}
						aria-hidden="true"
					>
						<Flex
							gap={ 2 }
							align="center"
							justify="space-between"
							style={ { height: '100%' } }
						>
							<FlexItem style={ { width: '100%' } }>
								<input
									type="text"
									className="components-combobox-control__input components-form-token-field__input"
									value={ isExpanded ? query : currentLabel }
									aria-label={
										currentLabel
											? `${ currentLabel }, ${ label }`
											: undefined
									}
									placeholder="Search..."
									onBlur={ onBlur }
									onFocus={ onFocus }
									onChange={ onInputChange }
								/>
							</FlexItem>
							<FlexItem style={ { height: '20px' } }>
								<button
									type="button"
									className="components-select-control__input components-form-token-field__input"
									style={ {
										border: '0',
										height: '20px',
										margin: '0',
										padding: '0',
									} }
									onClick={ togglePopover }
								>
									<Icon
										size={ 18 }
										icon={
											isExpanded ? chevronUp : chevronDown
										}
									/>
								</button>
							</FlexItem>
						</Flex>

						{ isExpanded && (
							<List
								id={ instanceId }
								className="components-form-token-field__suggestions-list"
								style={ { padding: '0', position: 'relative' } }
								height={
									options.length >= 5
										? 120
										: options.length * 24
								}
								itemCount={ options.length }
								itemData={ {
									options,
									focusedItemIndex,
									query,
									id: instanceId,
									onChange: selectChange,
								} }
								itemSize={ 24 }
								width={ 248 }
								ref={ listRef }
							>
								{ Row }
							</List>
						) }
					</div>
				</BaseControl>
			</DetectOutside>
		);
	}
);

export default VirtualizedComboBox;
