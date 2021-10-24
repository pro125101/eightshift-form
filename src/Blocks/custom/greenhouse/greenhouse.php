<?php

/**
 * Template for the Greenhouse Block view.
 *
 * @package EightshiftForms
 */

use EightshiftForms\Helpers\Components;
use EightshiftForms\Helpers\Helper;
use EightshiftForms\Integrations\Greenhouse\Greenhouse;
use EightshiftForms\Integrations\Greenhouse\SettingsGreenhouse;
use EightshiftForms\Settings\Settings\SettingsAll;
use EightshiftForms\Settings\Settings\SettingsGeneral;

$manifest = Components::getManifest(__DIR__);

$greenhouseServerSideRender = Components::checkAttr('greenhouseServerSideRender', $attributes, $manifest);
$greenhouseFormPostId = Components::checkAttr('greenhouseFormPostId', $attributes, $manifest);

if ($greenhouseServerSideRender) {
	$greenhouseFormPostId = Helper::encryptor('encrypt', $greenhouseFormPostId);
}

$greenhouseFormPostIdDecoded = Helper::encryptor('decode', $greenhouseFormPostId);

// Check if Greenhouse data is set and valid.
$isSettingsValid = \apply_filters(SettingsGreenhouse::FILTER_SETTINGS_IS_VALID_NAME, $greenhouseFormPostIdDecoded);

// Bailout if settings are not ok.
if (!$isSettingsValid) {
	return;
}

echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	Greenhouse::FILTER_MAPPER_NAME,
	[
		'formPostId' => $greenhouseFormPostId,
		'formType' => SettingsGreenhouse::SETTINGS_TYPE_KEY,
		'formTrackingEventName' => \apply_filters(
			SettingsAll::FILTER_BLOCK_SETTING_VALUE_NAME,
			SettingsGeneral::SETTINGS_GENERAL_TRACKING_EVENT_NAME_KEY,
			$greenhouseFormPostIdDecoded
		),
		'formSuccessRedirect' => \apply_filters(
			SettingsAll::FILTER_BLOCK_SETTING_VALUE_NAME,
			SettingsGeneral::SETTINGS_GENERAL_REDIRECTION_SUCCESS_KEY,
			$greenhouseFormPostIdDecoded
		),
	]
);