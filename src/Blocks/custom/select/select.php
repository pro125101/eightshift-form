<?php

/**
 * Template for the Select Block view.
 *
 * @package EightshiftForms
 */

use EightshiftForms\Helpers\Components;

$unique = Components::getUnique();

$selectName = $attributes['selectSelectName'] ?? '';
$selectId = $attributes['selectSelectId'] ?? '';
$blockClass = $attributes['blockClass'] ?? '';
$props = [];

if (empty($selectName)) {
	$props['selectName'] = $unique;
}

if (empty($selectId)) {
	$props['selectId'] = $unique;
}

$props['selectOptions'] = $innerBlockContent;
$props['blockClass'] = $blockClass;

echo Components::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'select',
	Components::props('select', $attributes, $props)
);
