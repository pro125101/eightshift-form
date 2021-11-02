<?php

/**
 * Template for the radio item Block view.
 *
 * @package EightshiftForms
 */

use EightshiftForms\Blocks\Blocks;
use EightshiftForms\Helpers\Components;

$unique = Components::getUnique();

$radioLabel = $attributes['radioRadioLabel'] ?? '';
$radioId = $attributes['radioRadioId'] ?? '';
$radioValue = $attributes['radioRadioValue'] ?? '';
$blockClass = $attributes['blockClass'] ?? '';
$props = [];

if (empty($radioValue)) {
	$props['radioValue'] = apply_filters(Blocks::BLOCKS_STRING_TO_VALUE_FILTER_NAME, $radioLabel);
}

$props['blockClass'] = $blockClass;

echo Components::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'radio',
	Components::props('radio', $attributes, $props)
);
