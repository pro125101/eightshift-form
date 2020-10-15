<?php
/**
 * Template for the Label Component.
 * @package Eightshift_Forms\Blocks.
 */

namespace Eightshift_Forms\Blocks;

use Eightshift_Libs\Helpers\Components;

$block_class = $attributes['blockClass'] ?? '';

$component_class = 'spinner';

$component_classes = Components::classnames([
  $component_class,
  "js-{$component_class}",
  'hide-spinner',
  ! empty( $block_class ) ? "{$block_class}__{$component_class}" : '',
]);

?>

<div
  class="<?php echo esc_attr( $component_classes ); ?>"
  role="alert"
  aria-live="assertive"
>
</div>