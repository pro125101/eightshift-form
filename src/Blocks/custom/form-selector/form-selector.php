<?php

/**
 * Template for the Form Selector Block view.
 *
 * @package EightshiftForms
 */

use EightshiftForms\Helpers\Helper;

// Add custom additional content filter.
echo Helper::getBlockAdditionalContentViaFilter('formSelector', $attributes);

echo $innerBlockContent; // phpcs:ignore Eightshift.Security.ComponentsEscape.OutputNotEscaped
