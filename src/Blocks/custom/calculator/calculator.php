<?php

/**
 * Template for the Calculator Block view.
 *
 * @package EightshiftForms
 */

use EightshiftFormsVendor\EightshiftLibs\Helpers\Components;

echo Components::render(
	'form',
	Components::props('form', $attributes, [
		'formContent' => $innerBlockContent,
	])
);
