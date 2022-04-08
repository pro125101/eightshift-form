<?php

/**
 * Template for the Submit Block view.
 *
 * @package EightshiftForms
 */

use EightshiftFormsVendor\EightshiftLibs\Helpers\Components;

$unique = Components::getUnique();

$submitName = $attributes['submitSubmitName'] ?? '';
$submitId = $attributes['submitSubmitId'] ?? '';
$props = [];

echo Components::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'submit',
	Components::props('submit', $attributes, $props)
);
