<?php

/**
 * Helpers for components
 *
 * @package EightshiftForms\Helpers
 */

declare(strict_types=1);

namespace EightshiftForms\Helpers;

use EightshiftForms\Config\Config;
use EightshiftFormsVendor\EightshiftLibs\Helpers\Components as LibsComponents;

/**
 * Helpers for components
 */
class Components extends LibsComponents
{

  /**
   * Wrapper for libs components so we don't have to pass the path each time.
   *
   * @param  string $component   Component's name or full path (ending with .php).
   * @param  array  $attributes  Array of attributes that's implicitly passed to component.
   * @param  string $parent_path If parent path is provides it will be appended to the file location, if not get_template_directory_uri() will be used as a default parent path.
   * @return string
   *
   * @throws \Exception When we're unable to find the component by $component.
   */
	public static function render(string $component, array $attributes = [], string $parent_path = '')
	{
		$parent_path = Config::getProjectPath();
		return parent::render($component, $attributes, $parent_path);
	}
}
