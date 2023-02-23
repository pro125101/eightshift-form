import React from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { PanelBody } from '@wordpress/components';
import { checkAttr } from '@eightshift/frontend-libs/scripts';
import { IntegrationsOptions } from './../../../components/integrations/components/integrations-options';
import manifest from './../manifest.json';

export const AirtableOptions = ({
	attributes,
	setAttributes,
	clientId,
	itemIdKey,
	innerIdKey,
}) => {

	const {
		title,
		blockName,
	} = manifest;

	return (
		<PanelBody title={sprintf(__('%s form', 'eightshift-forms'), title)}>
			<IntegrationsOptions
				block={blockName}
				setAttributes={setAttributes}
				clientId={clientId}
				itemId={checkAttr(itemIdKey, attributes, manifest)}
				itemIdKey={itemIdKey}
				innerId={checkAttr(innerIdKey, attributes, manifest)}
				innerIdKey={innerIdKey}
			/>
		</PanelBody>
	);
};