<?php

/**
 * Template for the Submit Component.
 *
 * @package EightshiftForms
 */

use EightshiftForms\Helpers\Components;

$manifest = Components::getManifest(__DIR__);

$componentClass = $manifest['componentClass'] ?? '';
$additionalClass = $attributes['additionalClass'] ?? '';
$blockClass = $attributes['blockClass'] ?? '';
$selectorClass = $attributes['selectorClass'] ?? $componentClass;

$submitName = Components::checkAttr('submitName', $attributes, $manifest);
$submitValue = Components::checkAttr('submitValue', $attributes, $manifest);
$submitId = Components::checkAttr('submitId', $attributes, $manifest);
$submitType = Components::checkAttr('submitType', $attributes, $manifest);
$submitIsDisabled = Components::checkAttr('submitIsDisabled', $attributes, $manifest);
$submitTracking = Components::checkAttr('submitTracking', $attributes, $manifest);

$submitClass = Components::classnames([
	Components::selector($componentClass, $componentClass),
	Components::selector($blockClass, $blockClass, $selectorClass),
	Components::selector($additionalClass, $additionalClass),
]);

$submitIsDisabled = $submitIsDisabled ? 'disabled' : '';

if (empty($submitId)) {
	$submitId = $submitName;
}

$submit = '
	<input
		type="submit"
		value="' . esc_attr($submitValue) . '"
		class="' . esc_attr($submitClass) . '"
		name="' . esc_attr($submitName) . '"
		id="' . esc_attr($submitId) . '"
		data-tracking="' . $submitTracking . '"
		' . $submitIsDisabled . '
	/>
';

$button = '
	<button
		class="' . esc_attr($submitClass) . '"
		name="' . esc_attr($submitName) . '"
		id="' . esc_attr($submitId) . '"
		data-tracking="' . $submitTracking . '"
		' . $submitIsDisabled . '
	>
		' . esc_attr($submitValue) . '
	</button>
';

echo Components::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'field',
	Components::props('field', $attributes, [
		'fieldContent' => $submitType === 'button' ? $button : $submit,
		'fieldId' => $submitId,
	])
);
