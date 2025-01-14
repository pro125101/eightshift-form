/* global esFormsLocalization */

import React, { useEffect } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { select, dispatch } from "@wordpress/data";
import apiFetch from '@wordpress/api-fetch';
import { Tooltip, Button, TextControl } from '@wordpress/components';
import { createBlock, createBlocksFromInnerBlocksTemplate } from '@wordpress/blocks';
import {
	AnimatedContentVisibility,
	camelize,
	classnames,
	IconLabel,
	icons,
	STORE_NAME,
	Notification,
	lockPostEditing,
	unlockPostEditing,
	unescapeHTML,
	getUnique,
} from '@eightshift/frontend-libs/scripts';
import { FORMS_STORE_NAME } from './../../assets/scripts/store';
import { getRestUrl, getRestUrlByType, getUtilsIcons } from '../form/assets/state-init';

/**
 * check if block options is disabled by integration or other component.
 *
 * @param {string} key Key to check.
 * @param {array} options Options array to check.
 *
 * @returns {boolean}
 */
export const isDeveloperMode = () => {
	return Boolean(esFormsLocalization?.isDeveloperMode);
};

/**
 * check if block options is disabled by integration or other component.
 *
 * @param {string} key Key to check.
 * @param {array} options Options array to check.
 *
 * @returns {boolean}
 */
export const isOptionDisabled = (key, options) => {
	return options.includes(key);
};

/**
 * Update/create new integration blocks by getting the update from rest api.
 *
 * @param {int} clientId Client ID from block editor.
 * @param {string} postId Post ID to get data from.
 * @param {string} type Integration type.
 * @param {string} itemId Integration internal ID.
 * @param {string} innerId Integration internal alternative ID.
 *
 * @returns {void}
 */
export const updateIntegrationBlocks = (clientId, postId, type, itemId, innerId = '') => {
	apiFetch({
		path: `${getRestUrlByType('prefixIntegrationEditor', 'integrationsEditorCreate', true, true)}?id=${postId}&type=${type}&itemId=${itemId}&innerId=${innerId}`,
	}).then((response) => {
		resetInnerBlocks(clientId);

		if (response.code === 200) {
			const builtBlocks = createBlocksFromInnerBlocksTemplate(response?.data?.data?.output);

			updateInnerBlocks(clientId, builtBlocks);

			dispatch('core/editor').savePost();
		}
	});
};

/**
 * Sync integration blocks by getting the update from rest api.
 *
 * @param {int} clientId Client ID from block editor.
 * @param {string} postId Post ID to get data from.
 *
 * @returns {void}
 */
export const syncIntegrationBlocks = (clientId, postId) => {
	return apiFetch({
		path: `${getRestUrlByType('prefixIntegrationEditor', 'integrationsEditorSync', true, true)}?id=${postId}`,
	}).then((response) => {
		if (isDeveloperMode()) {
			console.log(response);
		}

		dispatch(FORMS_STORE_NAME).setSyncDialog({});

		if (response.code === 200) {
			const parentId = select('core/block-editor').getBlockParents(clientId)?.[0];

			if (parentId) {
				resetInnerBlocks(parentId);
				updateInnerBlocks(parentId, createBlocksFromInnerBlocksTemplate(response?.data?.data?.output));

				const blocks = select('core/block-editor').getBlocks(parentId);

				if (blocks) {
					dispatch('core/block-editor').selectBlock(blocks?.[0].clientId);
				}
			}
		}

		if (!response?.data?.data?.update) {
			dispatch(FORMS_STORE_NAME).setSyncDialog({});
		} else {
			dispatch(FORMS_STORE_NAME).setSyncDialog({
				update: response?.data?.data?.update,
				removed: response?.data?.data?.removed,
				added: response?.data?.data?.added,
				replaced: response?.data?.data?.replaced,
				changed: response?.data?.data?.changed,
			});
		}

		return {
			message: response?.message,
			debugType: response?.data?.debugType,
			status: response?.status,
			update: response?.data?.data?.update,
		};
	});
};

/**
 * Clear transient cache.
 *
 * @param {string} type Cache integration type name.
 *
 * @returns {string}
 */
export const clearTransientCache = (type) => {
	return apiFetch({
		path: getRestUrl('cacheClear', true),
		method: 'POST',
		data: { type },
	}).then((response) => {
		return response.message;
	});
};

/**
 * Create new inner blocks from provided template.
 *
 * @param {int} clientId Client ID from block editor.
 * @param {string} name Block name template is set to.
 * @param {array} templates Templates of blocks to build.
 *
 * @returns {void}
 */
export const createBlockFromTemplate = (clientId, name, templates) => {
	const {
		blockName,
		attributes = {},
		innerBlocks = [],
	} = templates.filter((form) => form.slug === name)[0];

	// Build all inner blocks.
	const inner = innerBlocks.map((item) => createBlock(item[0], item[1] ?? {}, item[2] ?? []));

	// Build top level block.
	const block = createBlock(blockName, attributes, inner);

	// Insert built block in DOM.
	dispatch('core/block-editor').insertBlock(block, 0, clientId);
};

/**
 * Update inner blocks state by id.
 *
 * @param {int} clientId Client ID from block editor.
 * @param {array} blocks Blocks to append.
 *
 * @returns {void}
 */
export const updateInnerBlocks = (clientId, blocks) => {
	dispatch('core/block-editor').replaceInnerBlocks(clientId, blocks);
};

/**
 * Reset current inner blocks by id.
 *
 * @param {int} clientId Client ID from block editor
 *
 * @returns {void}
 */
export const resetInnerBlocks = (clientId, useParent = false) => {
	if (useParent) {
		const parentId = select('core/block-editor').getBlockParents(clientId)?.[0];

		if (parentId) {
			updateInnerBlocks(parentId, []);
		}
	} else {
		updateInnerBlocks(clientId, []);
	}
};

/**
 * Get forms fields blocks by checking the current block editor state.
 *
 * @returns {object}
 */
export const getFormFields = () => {
	const blocks = select('core/block-editor').getBlocks();

	const fields = blocks?.[0]?.innerBlocks?.[0]?.innerBlocks ?? [];

	if (!fields) {
		return [];
	}

	return [
		{
			value: '',
			label: '',
		},
		...fields.map((item) => {
			const {
				attributes,
				attributes: {
					blockName,
				}
			} = item;

			const value = attributes[camelize(`${blockName}-${blockName}-name`)];
			let label = attributes[camelize(`${blockName}-${blockName}-field-label`)];

			if (value === 'submit') {
				return null;
			}

			if (label === 'Label') {
				label = value;
			}

			return {
				'label': label,
				'value': value,
			};
		}).filter((elm) => elm),
	];
};

/**
 * Filter attributes by array of keys. Used to provide alternative attributes to server side render component to prevent unnecessary rerender.
 *
 * @param {object} attributes Attributes data source.
 * @param {array} filterAttributes Array of attributes to filter.
 * @param {object} appendItems Append additional attributes.
 *
 * @returns {object}
 */
export const getFilteredAttributes = (attributes, filterAttributes, appendItems = {}) => {
	const output = {};

	for (const [key, value] of Object.entries(attributes)) {
		if (filterAttributes.includes(key)) {
			output[key] = value;
		}
	}

	return {
		...output,
		...appendItems,
	};
};

/**
 * Output select options from array.
 *
 * @param {object} options
 * @param {*} useEmpty
 * @returns
 */
export const getConstantsOptions = (options, useEmpty = false) => {
	const empty = {
		value: '',
		label: '',
	};

	const items = [];
	if (options) {
		for (const [key, value] of Object.entries(options)) {
			items.push({
				'value': key,
				'label': value
			});
		}
	}

	return useEmpty ? [empty, ...items] : items;
};

/**
 * Output select options from array.
 *
 * @param {object} options
 * @param {bool} useEmpty
 * @returns
 */
export const getSettingsJsonOptions = (options, useEmpty = false) => {
	const empty = {
		value: '',
		label: '',
	};

	const items = [];
	if (options) {
		options.map((item) => {
			items.push({
				'value': item[0],
				'label': item[1],
			});
		});
	}

	return useEmpty ? [empty, ...items] : items;
};

/**
 * Outputs notification if name is missing.
 *
 * @param {string} value Field name value.
 * @param {bool} asPlaceholder If this is a placeholder.
 * @param {string} className Additional class name to add.
 *
 * @returns Component
 */
export const MissingName = ({
	value,
	asPlaceholder,
	className = '',
	isOptional = false,
}) => {
	if (value || asPlaceholder) {
		return null;
	}

	const style = classnames(
		'es-position-absolute es-right-0 es-top-0 es-nested-color-pure-white es-nested-w-5 es-nested-h-5 es-w-8 es-h-8 es-rounded-full es-has-enhanced-contrast-icon es-display-flex es-items-center es-content-center',
		isOptional ? 'es-bg-yellow-500' : 'es-bg-red-500',
		className,
	);

	return (
		<div className={style}>
			<Tooltip text={!isOptional ? __('Name not set!', 'eightshift-forms') : __('If you are using conditional tags you must set name on this field.', 'eightshift-forms')}>
				{React.cloneElement(icons.warning, {className: 'es-mb-0.5'})}
			</Tooltip>
		</div>
	);
};

/**
 * Outputs notification if visibility is hidden.
 *
 * @param {bool} value Field value.
 * @param {string} label Field label.
 *
 * @returns Component
 */
export const VisibilityHidden = ({ value, label }) => {
	if (!value) {
		return null;
	}

	return (
		<div className='es-position-absolute es-right-7 es-top-0 es-nested-color-pure-white es-bg-cool-gray-650 es-nested-w-6 es-nested-h-6 es-w-8 es-h-8 es-rounded-full es-has-enhanced-contrast-icon es-display-flex es-items-center es-content-center'>
			<Tooltip text={__(`${label} is hidden`, 'eightshift-forms')}>
				{icons.hide}
			</Tooltip>
		</div>
	);
};

/**
 * "Name" option with optional "Required" notification.
 *
 * @param {string} value Field name value.
 * @param {string} attribute Field name attribute.
 * @param {string} help Field help text.
 * @param {array} disabledOptions Array of disabled options.
 * @param {string} label Field label.
 * @param {function} setAttributes Set attributes function.
 * @param {bool} show Show this field.
 * @param {string} type Type of this field.
 *
 * @returns Component
 */
export const NameField = ({
	value,
	attribute,
	help = '',
	disabledOptions = [],
	label,
	setAttributes,
	show = true,
	type,
	isChanged = false,
	isOptional = false,
	setIsChanged,
}) => {

	const isDisabled = isOptionDisabled(attribute, disabledOptions);

	const NameFieldLabel = () => {
		return (
			<div className='es-h-between es-w-full'>
				<IconLabel icon={icons.idCard} label={label ? label : __('Name', 'eightshift-forms')} additionalClasses={classnames(!value && 'es-nested-color-red-500!')} standalone />

				<AnimatedContentVisibility showIf={!value}>
					<Tooltip text={!isOptional ? __('The form may not work correctly.', 'eightshift-forms') : __('Name field is required only if you are using conditional tags on this field.', 'eightshift-forms')}>
						{
							!isOptional ? 
							<span className='es-color-pure-white es-bg-red-500 es-px-1.5 es-py-1 es-rounded-1 es-text-3 es-font-weight-500'>{__('Required', 'eightshift-forms')}</span> :
							<span className='es-color-pure-white es-bg-yellow-500 es-px-1.5 es-py-1 es-rounded-1 es-text-3 es-font-weight-500'>{__('Optional', 'eightshift-forms')}</span>
						}
					</Tooltip>
				</AnimatedContentVisibility>

				{(!value && !isDisabled) &&
					<Button
						className='es-rounded-1.5 es-border-cool-gray-300 es-hover-border-cool-gray-400 es-transition'
						onClick={() => {
							setIsChanged(true);

							const valueName = `${type}-${getUnique()}`;
							setAttributes({ [attribute]: valueName });
						}}
					>
						{__('Generate name', 'eightshift-forms')}
					</Button>
				}
			</div>
		);
	};

	return (
		<>
			{show &&
				<>
					<TextControl
						label={<NameFieldLabel />}
						help={sprintf(__('Identifies the %s within form submission data. Must be unique. %s', 'eightshift-forms'), type, help)}
						value={value}
						onChange={(value) => {
							setIsChanged(true);
							setAttributes({ [attribute]: value });
						}}
						disabled={isDisabled}
					/>

					<NameChangeWarning isChanged={isChanged} />
				</>
			}
		</>
	);
};

/**
 * Toggle save on missing prop.
 *
 * @param {string} blockClientId Block client Id.
 * @param {string} key Manifest key to check.
 * @param {string} value Value to check.
 *
 * @returns void
 */
export const preventSaveOnMissingProps = (blockClientId, key, value) => {
	useEffect(() => {
		// Allows triggering this action only when the block is inserted in the editor.
		if (select('core/block-editor').getBlock(blockClientId)) {
			// Lock/unlock depending on the value.
			(value === '') ? lockPostEditing(blockClientId, key) : unlockPostEditing(blockClientId, key);
		}

		// Use this method to detect if the block has been deleted from the block editor.
		return () => {
			unlockPostEditing(blockClientId, key);
		};
	}, [key, value, blockClientId]);
};

/**
 * Show warning if name value is changed.
 *
 * @param {bool} isChanged Is name changed.
 * @param {string} type Is this value.
 *
 * @returns Component
 */
export const NameChangeWarning = ({
		isChanged = false,
		type = 'default'
	}) => {
	let text = '';

	if (!isChanged) {
		return null;
	}

	switch (type) {
		case 'value':
			text = __('After changing the field value, ensure that you review all conditional tags and form multi-flow configurations to avoid any errors.', 'eightshift-forms');
			break;
		case 'step':
			text = __('After changing the step name, ensure that you review forms multi-flow configurations to avoid any errors.', 'eightshift-forms');
			break;
		default:
			text = __('After changing the field name, ensure that you review all conditional tags and form multi-flow configurations to avoid any errors.', 'eightshift-forms');
			break;
	}

	return (
		<Notification
			text={text}
			type={'warning'}
		/>
	);
};

/**
 * Returns setting button component.
 *
 * @returns Component
 */
export const FormEditButton = ({formId}) => {
	const wpAdminUrl = esFormsLocalization.wpAdminUrl;

	const {
		editFormUrl,
	} = select(STORE_NAME).getSettings();

	return (
		<Button
			href={`${wpAdminUrl}${editFormUrl}&post=${formId}`}
			icon={icons.edit}
			className='es-rounded-1.5 es-border-cool-gray-300 es-hover-border-cool-gray-400 es-transition'
		>
			{__('Edit fields', 'eightshift-forms')}
		</Button>
	);
};

/**
 * Returns setting button component.
 *
 * @returns Component
 */
export const SettingsButton = ({formId}) => {
	const wpAdminUrl = esFormsLocalization.wpAdminUrl;
	const postId = select('core/editor').getCurrentPostId();

	const id = formId ?? postId;

	const {
		settingsPageUrl,
	} = select(STORE_NAME).getSettings();

	return (
		<Button
			href={`${wpAdminUrl}${settingsPageUrl}&formId=${id}`}
			icon={icons.options}
			className='es-rounded-1 es-border-cool-gray-300 es-hover-border-cool-gray-400 es-transition'
		>
			{__('Edit settings', 'eightshift-forms')}
		</Button>
	);
};

/**
 * Returns global setting button component.
 *
 * @returns Component
 */
export const GlobalSettingsButton = () => {
	const wpAdminUrl = esFormsLocalization.wpAdminUrl;

	const {
		globalSettingsPageUrl,
	} = select(STORE_NAME).getSettings();

	return (
		<Button
			href={`${wpAdminUrl}${globalSettingsPageUrl}`}
			icon={icons.globe}
			className='es-rounded-1 es-border-cool-gray-300 es-hover-border-cool-gray-400 es-transition'
		>
			{__('Edit global settings', 'eightshift-forms')}
		</Button>
	);
};

/**
 * Returns location button component.
 *
 * @returns Component
 */
export const LocationsButton = ({formId}) => {
	const wpAdminUrl = esFormsLocalization.wpAdminUrl;
	const postId = select('core/editor').getCurrentPostId();

	const id = formId ?? postId;

	const {
		locationsPageUrl,
	} = select(STORE_NAME).getSettings();

	return (
		<Button
			href={`${wpAdminUrl}${locationsPageUrl}&formId=${id}`}
			icon={icons.notebook}
			className='es-rounded-1 es-border-cool-gray-300 es-hover-border-cool-gray-400 es-transition'
		>
			{__('Locations', 'eightshift-forms')}
		</Button>
	);
};

/**
 * Returns dashboard button component.
 *
 * @returns Component
 */
export const DashboardButton = () => {
	const wpAdminUrl = esFormsLocalization.wpAdminUrl;

	const {
		dashboardPageUrl,
	} = select(STORE_NAME).getSettings();

	return (
		<Button
			href={`${wpAdminUrl}${dashboardPageUrl}`}
			icon={icons.layoutAlt}
			className='es-rounded-1 es-border-cool-gray-300 es-hover-border-cool-gray-400 es-transition'
		>
			{__('Visit dashboard settings', 'eightshift-forms')}
		</Button>
	);
};

/**
 * Returns output select item with icon.
 *
 * @returns object
 */
export const outputFormSelectItemWithIcon = (props) => {
	const {
		label,
		id,
		metadata,
	} = props;

	if (!id) {
		return '';
	}

	let outputLabel = unescapeHTML(label);
	let icon = getUtilsIcons('post');

	if (!outputLabel) {
		outputLabel = __(`Form ${id}`, 'eightshift-forms');
	}

	if (getUtilsIcons(metadata)) {
		icon = getUtilsIcons(metadata);
	}

	if (isDeveloperMode()) {
		outputLabel = `${outputLabel} (${id})`;
	}

	return {
		id,
		label: <span dangerouslySetInnerHTML={{ __html: `<span class="es-display-inline-flex es-vertical-align-middle es-mr-2">${icon}</span>${outputLabel}`}} />,
		value: id,
		metadata,
	};
};
