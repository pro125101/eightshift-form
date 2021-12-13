<?php

/**
 * Template for admin listing page.
 *
 * @package EightshiftForms\Blocks.
 */

use EightshiftForms\Helpers\Components;

$globalManifest = Components::getManifest(dirname(__DIR__, 2));
$manifest = Components::getManifest(__DIR__);
$manifestSection = Components::getManifest(dirname(__DIR__, 1) . '/admin-settings-section');

echo Components::outputCssVariablesGlobal($globalManifest); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

$componentClass = $manifest['componentClass'] ?? '';
$sectionClass = $manifestSection['componentClass'] ?? '';

$adminListingPageTitle = Components::checkAttr('adminListingPageTitle', $attributes, $manifest);
$adminListingSubTitle = Components::checkAttr('adminListingSubTitle', $attributes, $manifest);
$adminListingNewFormLink = Components::checkAttr('adminListingNewFormLink', $attributes, $manifest);
$adminListingTrashLink = Components::checkAttr('adminListingTrashLink', $attributes, $manifest);
$adminListingForms = Components::checkAttr('adminListingForms', $attributes, $manifest);
$adminListingType = Components::checkAttr('adminListingType', $attributes, $manifest);

$layoutClass = Components::classnames([
	Components::selector($componentClass, $componentClass),
	Components::selector($sectionClass, $sectionClass),
]);

?>

<div class="<?php echo \esc_attr($layoutClass); ?>">
	<div class="<?php echo \esc_attr("{$sectionClass}__section"); ?>">
		<?php if ($adminListingPageTitle || $adminListingSubTitle) { ?>
			<div class="<?php echo \esc_attr("{$sectionClass}__heading {$sectionClass}__heading--no-spacing"); ?>">
				<div class="<?php echo \esc_attr("{$sectionClass}__heading-wrap"); ?>">
					<div class="<?php echo \esc_attr("{$sectionClass}__heading-title"); ?>">
						<?php echo \esc_html($adminListingPageTitle); ?>
					</div>

					<div class="<?php echo \esc_attr("{$sectionClass}__actions"); ?>">
						<?php if ($adminListingType !== 'trash' && $adminListingTrashLink) { ?>
							<a href="<?php echo esc_url($adminListingTrashLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
								<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-trash"); ?> "></span>
								<?php echo \esc_html__('View Trash', 'eightshift-forms'); ?>
							</a>
						<?php } ?>

						<?php if ($adminListingNewFormLink) { ?>
							<a href="<?php echo esc_url($adminListingNewFormLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
								<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-plus-alt"); ?> "></span>
								<?php echo \esc_html__('Add new form', 'eightshift-forms'); ?>
							</a>
						<?php } ?>
					</div>
				</div>

				<?php if ($adminListingSubTitle) { ?>
					<div class="<?php echo \esc_attr("{$sectionClass}__description"); ?>">
						<?php echo esc_html($adminListingSubTitle); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="<?php echo \esc_attr("{$sectionClass}__content"); ?>">
			<?php if ($adminListingForms) { ?>
				<ul class="<?php echo \esc_attr("{$componentClass}__list"); ?>">
					<?php foreach ($adminListingForms as $form) { ?>
						<?php
						$id = $form['id'] ?? ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$editLink = $form['editLink'] ?? '';
						$postType = $form['postType'] ?? '';
						$viewLink = $form['viewLink'] ?? '';
						$trashLink = $form['trashLink'] ?? '';
						$trashRestoreLink = $form['trashRestoreLink'] ?? '';
						$settingsLink = $form['settingsLink'] ?? '';
						$title = $form['title'] ?? ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						$status = $form['status'] ?? ''; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

						$slug = $editLink;
						if (!$editLink) {
							$slug = '#';
						}
						?>
						<li class="<?php echo \esc_attr("{$componentClass}__list-item"); ?>">
							<div class="<?php echo esc_attr("{$componentClass}__item-intro"); ?>">
								<a href="<?php echo esc_url($slug); ?>" class="<?php echo \esc_attr("{$componentClass}__label"); ?>">
									<span class="dashicons dashicons-feedback <?php echo \esc_attr("{$componentClass}__label-icon"); ?>"></span>
									<?php echo $title ? esc_html($title) : esc_html($id); ?>
								</a>

								<?php if ($status !== 'publish') { ?>
									<span class="<?php echo esc_attr("{$componentClass}__item-status"); ?>">
										<?php echo esc_html($status); ?>
									</span>
								<?php } ?>

								<?php if ($postType) { ?>
									<span class="<?php echo esc_attr("{$componentClass}__item-post-type"); ?>">
										<?php echo esc_html($postType); ?>
									</span>
								<?php } ?>
							</div>
							<div class="<?php echo \esc_attr("{$sectionClass}__actions"); ?>">
								<?php if ($editLink) { ?>
									<a href="<?php echo esc_url($editLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
										<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-edit"); ?> "></span>
										<?php echo esc_html__('Edit', 'eightshift-forms'); ?>
									</a>
								<?php } ?>

								<?php if ($trashLink) { ?>
									<a href="<?php echo esc_url($trashLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
										<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-trash"); ?> "></span>
										<?php
										if ($adminListingType === 'trash') {
											echo esc_html__('Delete permanently', 'eightshift-forms');
										} else {
											echo esc_html__('Delete', 'eightshift-forms');
										}
										?>
									</a>
								<?php } ?>

								<?php if ($adminListingType === 'trash') { ?>
									<a href="<?php echo esc_url($trashRestoreLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
										<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-image-rotate"); ?> "></span>
										<?php echo esc_html__('Restore', 'eightshift-forms'); ?>
									</a>
								<?php } ?>

								<?php if ($settingsLink) { ?>
									<a href="<?php echo esc_url($settingsLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
										<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-admin-settings"); ?> "></span>
										<?php echo esc_html__('Settings', 'eightshift-forms'); ?>
									</a>
								<?php } ?>

								<?php if ($viewLink) { ?>
									<a href="<?php echo esc_url($viewLink); ?>" class="<?php echo \esc_attr("{$sectionClass}__link"); ?>">
										<span class="<?php echo \esc_attr("{$sectionClass}__link-icon dashicons dashicons-welcome-view-site"); ?> "></span>
										<?php echo esc_html__('View', 'eightshift-forms'); ?>
									</a>
								<?php } ?>
							</div>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
	</div>
</div>
