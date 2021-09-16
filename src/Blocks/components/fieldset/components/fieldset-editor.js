import React from 'react';
import classnames from 'classnames';
import { InnerBlocks } from '@wordpress/editor';
import { selector, checkAttr } from '@eightshift/frontend-libs/scripts';
import manifest from '../manifest.json';

export const FieldsetEditor = (attributes) => {
	const {
		componentClass,
	} = manifest;

	const {
		selectorClass = componentClass,
		blockClass,
		additionalClass,
	} = attributes;

	const fieldsetLegend = checkAttr('fieldsetLegend', attributes, manifest);
	const fieldsetId = checkAttr('fieldsetId', attributes, manifest);
	const fieldsetAllowedBlocks = checkAttr('fieldsetAllowedBlocks', attributes, manifest);
	const fieldsetName = checkAttr('fieldsetName', attributes, manifest);

	const fieldsetClass = classnames([
		selector(componentClass, componentClass),
		selector(blockClass, blockClass, selectorClass),
		selector(additionalClass, additionalClass),
	]);

	return (
		<fieldset
			className={fieldsetClass}
			id={fieldsetId}
			name={fieldsetName}
		>
			{fieldsetLegend &&
				<legend>
					{fieldsetLegend}
				</legend>
			}
			<InnerBlocks
				allowedBlocks={(typeof fieldsetAllowedBlocks === 'undefined') || fieldsetAllowedBlocks}
			/>
		</fieldset>
	);
};