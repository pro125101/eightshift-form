<?php

/**
 * The class to provide integration items from cache.
 *
 * @package EightshiftForms\Rest\Routes\Integrations\Moments
 */

declare(strict_types=1);

namespace EightshiftForms\Rest\Routes\Integrations\Moments;

use EightshiftForms\Integrations\ClientInterface;
use EightshiftForms\Integrations\Moments\SettingsMoments;
use EightshiftFormsVendor\EightshiftFormsUtils\Helpers\UtilsApiHelper;
use EightshiftFormsVendor\EightshiftFormsUtils\Config\UtilsConfig;
use EightshiftFormsVendor\EightshiftFormsUtils\Rest\Routes\AbstractUtilsBaseRoute;
use WP_REST_Request;

/**
 * Class IntegrationItemsMomentsRoute
 */
class IntegrationItemsMomentsRoute extends AbstractUtilsBaseRoute
{
	/**
	 * Instance variable for Moments data.
	 *
	 * @var ClientInterface
	 */
	protected $momentsClient;

	/**
	 * Route slug.
	 */
	public const ROUTE_SLUG = SettingsMoments::SETTINGS_TYPE_KEY;

	/**
	 * Get the base url of the route
	 *
	 * @return string The base URL for route you are adding.
	 */
	protected function getRouteName(): string
	{
		return '/' . UtilsConfig::ROUTE_PREFIX_INTEGRATION_ITEMS . '/' . self::ROUTE_SLUG;
	}

	/**
	 * Create a new instance that injects classes
	 *
	 * @param ClientInterface $momentsClient Inject HubSpot which holds HubSpot connect data.
	 */
	public function __construct(ClientInterface $momentsClient)
	{
		$this->momentsClient = $momentsClient;
	}

	/**
	 * Returns allowed methods for this route.
	 *
	 * @return string
	 */
	protected function getMethods(): string
	{
		return static::READABLE;
	}

	/**
	 * Method that returns rest response
	 *
	 * @param WP_REST_Request $request Data got from endpoint url.
	 *
	 * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
	 *                                is already an instance, WP_HTTP_Response, otherwise
	 *                                returns a new WP_REST_Response instance.
	 */
	public function routeCallback(WP_REST_Request $request)
	{
		$premission = $this->checkUserPermission();
		if ($premission) {
			return \rest_ensure_response($premission);
		}

		$debug = [
			'request' => $request,
		];

		// Check if Moments global settings is valid.
		$isGlobalSettingsValid = \apply_filters(SettingsMoments::FILTER_SETTINGS_GLOBAL_NAME, false);

		if (!$isGlobalSettingsValid) {
			return \rest_ensure_response(
				UtilsApiHelper::getApiErrorPublicOutput(
					\esc_html__('Global not configured', 'eightshift-forms'),
					[],
					$debug
				)
			);
		}

		$items = $this->momentsClient->getItems();

		if (!$items) {
			return \rest_ensure_response(
				UtilsApiHelper::getApiErrorPublicOutput(
					\esc_html__('Items missing', 'eightshift-forms'),
					[],
					$debug
				)
			);
		}

		$items = \array_filter(\array_values(\array_map(
			static function ($item) {
				$id = $item['id'] ?? '';

				if ($id) {
					return [
						'label' => $item['title'] ?? \__('No title', 'eightshift-forms'),
						'value' => $id,
					];
				}
			},
			$items
		)));

		// Finish.
		return \rest_ensure_response(
			UtilsApiHelper::getApiSuccessPublicOutput(
				\esc_html__('Success', 'eightshift-forms'),
				$items,
				$debug
			)
		);
	}
}
