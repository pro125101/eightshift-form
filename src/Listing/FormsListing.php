<?php

/**
 * Class that holds data for admin forms listing.
 *
 * @package EightshiftForms\Listing
 */

declare(strict_types=1);

namespace EightshiftForms\Listing;

use EightshiftForms\CustomPostType\Forms;
use EightshiftForms\CustomPostType\Result;
use EightshiftForms\General\SettingsGeneral;
use EightshiftFormsVendor\EightshiftFormsUtils\Config\UtilsConfig;
use EightshiftFormsVendor\EightshiftFormsUtils\Helpers\UtilsGeneralHelper;
use EightshiftFormsVendor\EightshiftFormsUtils\Helpers\UtilsIntegrationsHelper;
use WP_Query;

/**
 * FormsListing class.
 */
class FormsListing implements FormListingInterface
{
	/**
	 * Get Forms List.
	 *
	 * @param string $type Type of listing to output.
	 * @param string $parent Parent type for listing to output.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getFormsList(string $type = '', string $parent = ''): array
	{
		$postType = Forms::POST_TYPE_SLUG;
		$showTrash = false;

		switch ($type) {
			case UtilsConfig::SLUG_ADMIN_LISTING_TRASH:
				$postType = ($parent === UtilsConfig::SLUG_ADMIN_LISTING_RESULTS) ? Result::POST_TYPE_SLUG : Forms::POST_TYPE_SLUG;
				$showTrash = true;
				break;
			case UtilsConfig::SLUG_ADMIN_LISTING_RESULTS:
				$postType = Result::POST_TYPE_SLUG;
				break;
			default:
				$postType = Forms::POST_TYPE_SLUG;
				break;
		}

		// Prepare query args.
		$args = [
			'post_type' => $postType,
			'posts_per_page' => 5000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
			'post_status' => $showTrash ? 'trash' : '',
		];

		$theQuery = new WP_Query($args);

		$output = [];

		if (!$theQuery->have_posts()) {
			\wp_reset_postdata();
			return [];
		}

		foreach ($theQuery->get_posts() as $post) {
			$id = (int) $post->ID;

			// Output predefined array of data.
			$output[] = [
				'id' => $id,
				'title' => \get_the_title($id),
				'status' => \get_post_status($id),
				'settingsLink' => UtilsGeneralHelper::getSettingsPageUrl((string) $id, SettingsGeneral::SETTINGS_TYPE_KEY),
				'editLink' => !$showTrash ? UtilsGeneralHelper::getFormEditPageUrl((string) $id) : '',
				'trashLink' => UtilsGeneralHelper::getFormTrashActionUrl((string) $id, $showTrash),
				'entriesLink' => UtilsGeneralHelper::getListingPageUrl(UtilsConfig::SLUG_ADMIN_LISTING_ENTRIES, (string) $id),
				'trashRestoreLink' => UtilsGeneralHelper::getFormTrashRestoreActionUrl((string) $id),
				'activeIntegration' => UtilsIntegrationsHelper::getIntegrationDetailsById((string) $id),
				'useSync' => true,
			];
		}

		\wp_reset_postdata();

		return $output;
	}
}
