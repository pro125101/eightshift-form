import React from 'react';
import { __ } from '@wordpress/i18n';
import { TextControl, SelectControl } from '@wordpress/components';
import {
	icons,
	getOption,
	checkAttr,
	getAttrKey,
	IconLabel,
	IconToggle
} from '@eightshift/frontend-libs/scripts';
import manifest from './../manifest.json';

export const InputOptions = (attributes) => {
	const {
		setAttributes,
	} = attributes;

	const inputName = checkAttr('inputName', attributes, manifest);
	const inputValue = checkAttr('inputValue', attributes, manifest);
	const inputId = checkAttr('inputId', attributes, manifest);
	const inputPlaceholder = checkAttr('inputPlaceholder', attributes, manifest);
	const inputType = checkAttr('inputType', attributes, manifest);
	const inputIsDisabled = checkAttr('inputIsDisabled', attributes, manifest);
	const inputIsReadOnly = checkAttr('inputIsReadOnly', attributes, manifest);
	const inputIsRequired = checkAttr('inputIsRequired', attributes, manifest);

	return (
		<>
			<TextControl
				label={<IconLabel icon={icons.id} label={__('Name', 'eightshift-forms')} />}
				value={inputName}
				onChange={(value) => setAttributes({ [getAttrKey('inputName', attributes, manifest)]: value })}
			/>

			<TextControl
				label={<IconLabel icon={icons.id} label={__('Value', 'eightshift-forms')} />}
				value={inputValue}
				onChange={(value) => setAttributes({ [getAttrKey('inputValue', attributes, manifest)]: value })}
			/>

			<TextControl
				label={<IconLabel icon={icons.id} label={__('Id', 'eightshift-forms')} />}
				value={inputId}
				onChange={(value) => setAttributes({ [getAttrKey('inputId', attributes, manifest)]: value })}
			/>

			<TextControl
				label={<IconLabel icon={icons.id} label={__('Placeholder', 'eightshift-forms')} />}
				value={inputPlaceholder}
				onChange={(value) => setAttributes({ [getAttrKey('inputPlaceholder', attributes, manifest)]: value })}
			/>

			<SelectControl
				label={<IconLabel icon={icons.id} label={__('Type', 'eightshift-forms')} />}
				value={inputType}
				options={getOption('inputType', attributes, manifest)}
				onChange={(value) => setAttributes({ [getAttrKey('inputType', attributes, manifest)]: value })}
			/>

			<IconToggle
				icon={icons.play}
				label={__('Is Disabled', 'eightshift-forms')}
				checked={inputIsDisabled}
				onChange={(value) => setAttributes({ [getAttrKey('inputIsDisabled', attributes, manifest)]: value })}
			/>

			<IconToggle
				icon={icons.play}
				label={__('Is Read Only', 'eightshift-forms')}
				checked={inputIsReadOnly}
				onChange={(value) => setAttributes({ [getAttrKey('inputIsReadOnly', attributes, manifest)]: value })}
			/>

			<IconToggle
				icon={icons.play}
				label={__('Is Required', 'eightshift-forms')}
				checked={inputIsRequired}
				onChange={(value) => setAttributes({ [getAttrKey('inputIsRequired', attributes, manifest)]: value })}
			/>
		</>
	);
};
