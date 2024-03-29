import React from 'react';

import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import RuleComponent from './RuleComponent';
import { Rules } from './ConditionalPanel';
import MagicTagsComponent from './MagicTagsComponent';

/**
 * Type for RuleGroup
 */
type RuleGroupArgs = {
	group: Rules[];
	isFirst: boolean;
	isLast: boolean;
	canAddMore: boolean;
	magicTags: string[];
	onChange: (
		val: string | [  ],
		type: keyof Rules,
		ruleSetIndex: number
	) => void;
	addRuleSet: ( ruleIndex: number ) => void;
	addRuleGroup: () => void;
	removeRuleSet: ( ruleIndex: number ) => void;
	removeRuleGroup: () => void;
};

/**
 * Component RuleGroup.
 *
 * @param {RuleGroupArgs} args
 * @class
 */
const RuleGroup = ( {
	group,

	isFirst,
	isLast,
	canAddMore,
	magicTags,
	onChange,
	addRuleSet,
	addRuleGroup,
	removeRuleSet,
	removeRuleGroup,
}: RuleGroupArgs ) => {
	const updateValues = (
		value: string,
		type: keyof Rules,
		ruleSetIndex: number
	) => {
		onChange( value, type, ruleSetIndex );
	};

	if ( group.length < 1 ) {
		return null;
	}

	return (
		<>
			<div className="rule-group">
				<MagicTagsComponent magicTags={ magicTags } />

				{ Object.values( group ).map( ( ruleset, index ) => (
					<RuleComponent
						key={ index }
						ruleset={ ruleset }
						isLast={ index === group.length - 1 }
						isFirst={ index === 0 }
						canAddMore={ group.length < 5 }
						updateRoot={ ( value: string ) => {
							updateValues( value, 'root', index );
						} }
						updateCondition={ ( value: string ) => {
							updateValues( value, 'condition', index );
						} }
						updateEnd={ ( value: string ) => {
							updateValues( value, 'end', index );
						} }
						addRuleSet={ () => {
							addRuleSet( index );
						} }
						removeRuleSet={ () => {
							removeRuleSet( index );
						} }
					/>
				) ) }
				<div className="actions">
					<Button
						isSecondary
						isSmall
						disabled={ ! canAddMore }
						className="add-group"
						onClick={ addRuleGroup }
					>
						{ __( 'Add Rule Group', 'neve' ) }
					</Button>
					{ ! ( isLast && isFirst ) && (
						<Button
							isLink
							isDestructive
							isSmall
							className="remove-group"
							onClick={ removeRuleGroup }
						>
							{ __( 'Remove Rule Group', 'neve' ) }
						</Button>
					) }
				</div>
			</div>
			{ ! isLast && (
				<span className="chainer">{ __( 'or', 'neve' ) }</span>
			) }
		</>
	);
};

export default RuleGroup;
