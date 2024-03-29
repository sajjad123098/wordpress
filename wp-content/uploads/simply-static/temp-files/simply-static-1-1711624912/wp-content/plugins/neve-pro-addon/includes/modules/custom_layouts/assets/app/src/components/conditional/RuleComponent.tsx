import React, { useCallback, useEffect } from 'react';

import VirtualizedComboBox, { Option } from '../shared/VirtualizedComboBox';
import { __ } from '@wordpress/i18n';
import InlineSelectGroup from '../shared/InlineSelectGroup';
import { mapOptGroupRoot, mapSimpleOptions } from '../../common/utils';
import { Button, ButtonGroup, SelectControl } from '@wordpress/components';
import { plus } from '@wordpress/icons';
import { Rules } from './ConditionalPanel';
import { useState } from '@wordpress/element';
import { debounce } from 'lodash';
import { maybeParseJson } from '@neve-wp/components';

/**
 * Type for RuleComponent
 */
type RuleComponentArgs = {
	ruleset: Rules;
	isFirst: boolean;
	isLast: boolean;
	canAddMore: boolean;
	updateRoot: ( value: string ) => void;
	updateCondition: ( value: string ) => void;
	updateEnd: ( value: string ) => void;
	addRuleSet: () => void;
	removeRuleSet: () => void;
};

/**
 * Component RuleComponent.
 *
 * @param {RuleComponentArgs} args
 * @class
 */
const RuleComponent = ( {
	ruleset,
	isLast,
	isFirst,
	canAddMore,

	updateRoot,
	updateCondition,
	updateEnd,

	addRuleSet,
	removeRuleSet,
}: RuleComponentArgs ) => {
	const {
		root,
		map,
		end,
	} = window.neveCustomLayouts.sidebarOptions.conditions;
	const {
		root: selectedRoot,
		condition: selectedCondition,
		end: selectedEnd,
	} = ruleset as {
		root: string;
		condition: string;
		end: string | [  ];
	};

	const conditionMap =
		window.neveCustomLayouts.conditionMap[ selectedRoot ] ||
		window.neveCustomLayouts.conditionMap.default;
	const conditionOptions = mapSimpleOptions( conditionMap );

	type TaxonomyOptions = {
		[ key: string ]: {
			nicename: string;
			name: string;
			// eslint-disable-next-line camelcase
			terms: { term_id: number; name: string; slug: string }[];
		}[];
	};

	const mapOptGroupOptionsFlatten = (
		options: TaxonomyOptions,
		isArchiveTaxonomy = false
	) => {
		const processed = Object.keys( options ).map( ( groupKey ) => {
			return options[ groupKey ]
				.map( ( group ) => {
					const { nicename, name: groupName, terms } = group;

					let actualTerms = terms;

					if ( ! actualTerms || actualTerms.length < 1 ) {
						return false;
					}

					if ( typeof actualTerms === 'object' ) {
						actualTerms = Object.values( actualTerms );
					}

					if ( isArchiveTaxonomy ) {
						return {
							label: `${ nicename } (${ groupKey } - ${ groupName })`,
							value: groupName,
						};
					}
					const flatten = [];
					flatten.push( {
						label: `${ nicename } (${ groupKey } - ${ groupName })`,
						value: '',
						isGroupLabel: true,
					} );

					actualTerms.forEach( ( term ) => {
						flatten.push( {
							label: term.name,
							value: `${ groupName }|${ term.slug }`,
							isSubGroup: true,
						} );
					} );

					return flatten;
				} )
				.filter( ( item ) => item !== false );
		} );
		if ( isArchiveTaxonomy ) {
			return Object.values( processed ).flat() as Option[];
		}
		return Object.values( processed ).flat().flat() as Option[];
	};

	const endRoot = Object.keys(
		map as Record< string, string[] | string >
	).find( ( key ) => {
		return map[ key ].includes( selectedRoot as string );
	} ) as string;

	const endConditions = end[ endRoot ];
	let endOptions = [] as Option[];
	if ( endConditions ) {
		let parsedOptions: Option[];
		switch ( endRoot ) {
			case 'terms':
				parsedOptions = mapOptGroupOptionsFlatten(
					( endConditions as unknown ) as TaxonomyOptions
				);
				break;
			case 'taxonomies':
				parsedOptions = mapOptGroupOptionsFlatten(
					( endConditions as unknown ) as TaxonomyOptions,
					true
				);
				break;
			default:
				parsedOptions = mapSimpleOptions( endConditions );
		}
		endOptions = [
			{ value: '', label: __( 'Select', 'neve' ) },
			...parsedOptions,
		];
	}
	const [ endOptionsState, setEndOptionsState ] = useState( endOptions );

	const rootOptions = ( mapOptGroupRoot(
		( root as unknown ) as Record<
			string,
			{ label: string; choices: Record< string, string > }
		>
	) as unknown ) as Option[];
	const rootWithSelect = [
		{ value: '', label: __( 'Select', 'neve' ) },
		...rootOptions,
	];

	const onRootChange = React.useCallback( ( value ) => updateRoot( value ), [
		updateRoot,
	] );

	const onConditionChange = React.useCallback(
		( value ) => updateCondition( value ),
		[ updateCondition ]
	);
	const onEndChange = React.useCallback( ( value ) => updateEnd( value ), [
		updateEnd,
	] );

	const [ query, setQuery ] = useState( '' );
	const [ prevOption, setPrevOption ] = useState( {} );
	const restUrl = window.neveCustomLayouts.ajaxOptions;
	const nonce = window.neveCustomLayouts.nonce;
	const options: RequestInit = {
		headers: {
			Accept: 'application/json',
			'Content-Type': 'application/json',
			'x-wp-nonce': nonce,
		},
		method: 'GET',
	};

	useEffect( () => {
		if ( selectedEnd && [ 'pages', 'posts' ].includes( endRoot ) ) {
			options.method = 'GET';
			const fetchAbortController = new AbortController();

			try {
				fetch( `${ restUrl }/${ selectedEnd }`, {
					...options,
					signal: fetchAbortController.signal,
				} ).then( async ( response ) => {
					const responseJson = await response.json();
					const data = maybeParseJson( responseJson.data );

					setPrevOption( {
						value: `${ data.value }`,
						label: data.label,
						type: endRoot,
					} );

					const found = Object.keys( endOptions ).filter( ( k ) => {
						// @ts-ignore
						return endOptions[ k ].value === selectedEnd;
					} );
					if ( found.length === 0 ) {
						endOptions = [
							...endOptions,
							{
								value: `${ data.value }`,
								label: data.label as string,
							},
						];
					}
					setEndOptionsState( endOptions );

					return responseJson;
				} );
			} catch ( e ) {
				if ( ! fetchAbortController.signal.aborted ) {
					// eslint-disable-next-line no-console
					console.error( e );
				}
			}

			return () => {
				fetchAbortController.abort();
			};
		}
	}, [] );

	const fetchResults = useCallback(
		debounce( ( searchQuery, searchType, alreadyDefinedOption ) => {
			const postOptions = {
				...options,
				method: 'POST',
				body: JSON.stringify( {
					type: searchType === 'pages' ? 'page' : 'post',
					query: searchQuery,
				} ),
			};
			fetch( `${ restUrl }`, postOptions ).then( async ( response ) => {
				const responseJson = await response.json();
				const parsedOptions = mapSimpleOptions( responseJson.data );
				endOptions = [
					{ value: '', label: __( 'Select', 'neve' ) },
					...parsedOptions,
				];
				const found = Object.keys( endOptions ).filter( ( k ) => {
					// @ts-ignore
					return endOptions[ k ].value === selectedEnd;
				} );
				if (
					found.length === 0 &&
					searchType === alreadyDefinedOption.type
				) {
					endOptions = [
						...endOptions,
						{
							value: `${ alreadyDefinedOption.value }`,
							label: alreadyDefinedOption.label,
						},
					];
				}

				setEndOptionsState( endOptions );
				return responseJson;
			} );
		}, 250 ),
		[]
	);

	const handleQueryChange = ( nextValue: string ) => {
		setQuery( nextValue );
		fetchResults( nextValue, endRoot, prevOption );
	};

	useEffect( () => {
		if ( ! [ 'pages', 'posts' ].includes( endRoot ) ) {
			setEndOptionsState( endOptions );
			return;
		}

		fetchResults( query, endRoot, prevOption );
	}, [ prevOption, endRoot ] );

	return (
		<>
			<div className="rule-set">
				<InlineSelectGroup
					disabled={ false }
					label={ '' }
					value={ selectedRoot }
					onChange={ onRootChange }
					options={ rootWithSelect }
				/>
				<SelectControl
					disabled={ false }
					aria-label={ '' }
					value={ selectedCondition }
					onChange={ onConditionChange }
					options={ conditionOptions }
				/>
				{ endConditions && (
					<VirtualizedComboBox
						options={ endOptionsState }
						value={ selectedEnd as string }
						onChange={ onEndChange }
						dynamicQuery={ [ 'pages', 'posts' ].includes(
							endRoot
						) }
						setQueryOpt={ handleQueryChange }
						queryOpt={ query }
					/>
				) }
				<ButtonGroup className="rule-actions">
					{ ! isLast && (
						<span className="chainer">{ __( 'and', 'neve' ) }</span>
					) }
					{ ! ( isLast && isFirst ) && (
						<Button
							label={ __( 'Remove Rule', 'neve' ) }
							icon="minus"
							iconSize={ 20 }
							isSecondary
							isDestructive
							className="remove-rule is-small"
							disabled={ isLast && isFirst }
							onClick={ removeRuleSet }
						/>
					) }
					{ isLast && (
						<Button
							label={ __( 'Add Rule', 'neve' ) }
							icon={ plus }
							iconSize={ 20 }
							isSecondary
							className="add-rule is-small"
							disabled={ ! canAddMore }
							onClick={ addRuleSet }
						/>
					) }
				</ButtonGroup>
			</div>
		</>
	);
};

export default RuleComponent;
