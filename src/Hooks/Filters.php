<?php

/**
 * The Filters class, used for defining settings and integrations filter variables.
 *
 * @package EightshiftForms\Hooks
 */

declare(strict_types=1);

namespace EightshiftForms\Hooks;

use EightshiftForms\Cache\SettingsCache;
use EightshiftForms\Integrations\Greenhouse\Greenhouse;
use EightshiftForms\Integrations\Greenhouse\SettingsGreenhouse;
use EightshiftForms\Integrations\Hubspot\SettingsHubspot;
use EightshiftForms\Integrations\Mailchimp\Mailchimp;
use EightshiftForms\Integrations\Mailchimp\SettingsMailchimp;
use EightshiftForms\Mailer\SettingsMailer;
use EightshiftForms\Settings\Settings\SettingsGeneral;
use EightshiftForms\Validation\SettingsValidation;

/**
 * The Filters class, used for defining settings and integrations filter variables.
 */
class Filters
{
	/**
	 * All settings, panels and integrations.
	 * Order of items here will determin the order in the browser sidebar for settings.
	 */
	public const ALL = [
		SettingsGeneral::SETTINGS_TYPE_KEY => [
			'global' => SettingsGeneral::FILTER_SETTINGS_GLOBAL_NAME,
			'settings' => SettingsGeneral::FILTER_SETTINGS_NAME,
			'settingsSidebar' => SettingsGeneral::FILTER_SETTINGS_SIDEBAR_NAME,
		],
		SettingsValidation::SETTINGS_TYPE_KEY => [
			'settings' => SettingsValidation::FILTER_SETTINGS_NAME,
			'settingsSidebar' => SettingsValidation::FILTER_SETTINGS_SIDEBAR_NAME,
		],
		SettingsCache::SETTINGS_TYPE_KEY => [
			'global' => SettingsCache::FILTER_SETTINGS_GLOBAL_NAME,
			'settingsSidebar' => SettingsCache::FILTER_SETTINGS_SIDEBAR_NAME,
		],
		SettingsMailer::SETTINGS_TYPE_KEY => [
			'settings' => SettingsMailer::FILTER_SETTINGS_NAME,
			'settingsSidebar' => SettingsMailer::FILTER_SETTINGS_SIDEBAR_NAME,
		],
		SettingsMailchimp::SETTINGS_TYPE_KEY => [
			'global' => SettingsMailchimp::FILTER_SETTINGS_GLOBAL_NAME,
			'settings' => SettingsMailchimp::FILTER_SETTINGS_NAME,
			'settingsSidebar' => SettingsMailchimp::FILTER_SETTINGS_SIDEBAR_NAME,
			'fields' => Mailchimp::FILTER_FORM_FIELDS_NAME,
		],
		SettingsGreenhouse::SETTINGS_TYPE_KEY => [
			'global' => SettingsGreenhouse::FILTER_SETTINGS_GLOBAL_NAME,
			'settings' => SettingsGreenhouse::FILTER_SETTINGS_NAME,
			'settingsSidebar' => SettingsGreenhouse::FILTER_SETTINGS_SIDEBAR_NAME,
			'fields' => Greenhouse::FILTER_FORM_FIELDS_NAME,
		],
		SettingsHubspot::SETTINGS_TYPE_KEY => [
			'global' => SettingsHubspot::FILTER_SETTINGS_GLOBAL_NAME,
			'settings' => SettingsHubspot::FILTER_SETTINGS_NAME,
			'settingsSidebar' => SettingsHubspot::FILTER_SETTINGS_SIDEBAR_NAME,
			'fields' => '',
		],
	];
}
