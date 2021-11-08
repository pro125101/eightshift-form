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

$manifest = Components::getManifest(__DIR__);

$blockClass = $attributes['blockClass'] ?? '';

$greenhouseServerSideRender = Components::checkAttr('greenhouseServerSideRender', $attributes, $manifest);
$greenhouseFormPostId = Components::checkAttr('greenhouseFormPostId', $attributes, $manifest);

if ($greenhouseServerSideRender) {
	$greenhouseFormPostId = Helper::encryptor('encrypt', $greenhouseFormPostId);
}

$greenhouseFormPostIdDecoded = Helper::encryptor('decode', $greenhouseFormPostId);

// Check if Greenhouse data is set and valid.
$isSettingsValid = \apply_filters(SettingsGreenhouse::FILTER_SETTINGS_IS_VALID_NAME, $greenhouseFormPostIdDecoded);

$greenhouseClass = Components::classnames([
	Components::selector($blockClass, $blockClass),
	Components::selector(!$isSettingsValid, $blockClass, '', 'invalid')
]);

// Bailout if settings are not ok.
if ($isSettingsValid) {
	echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		Greenhouse::FILTER_MAPPER_NAME,
		$greenhouseFormPostId
	);
} else { ?>
	<div class="<?php echo esc_attr($greenhouseClass); ?>">
		<?php esc_html_e('Sorry, it looks like your Greenhose settings are not configured correctly. Please contact your admin.', 'eightshift-forms'); ?>
	</div>
		<?php
}
