<?php

/**
 * The Variables class, used for defining available variables.
 *
 * @package EightshiftForms\Hooks
 */

declare(strict_types=1);

namespace EightshiftForms\Hooks;

/**
 * The Variables class, used for defining available Variables.
 */
class Variables
{
	/**
	 * Get forms develop mode, this will output new global settings for testing inputs.
	 *
	 * @return bool
	 */
	public static function isDevelopMode(): bool
	{
		return \defined('ES_DEVELOP_MODE') ? true : false;
	}

	/**
	 * Get API Key for HubSpot.
	 *
	 * @return string
	 */
	public static function getApiKeyHubspot(): string
	{
		return \defined('ES_API_KEY_HUBSPOT') ? \ES_API_KEY_HUBSPOT : '';
	}

	/**
	 * Get API Key for Greenhouse.
	 *
	 * @return string
	 */
	public static function getApiKeyGreenhouse(): string
	{
		return \defined('ES_API_KEY_GREENHOUSE') ? \ES_API_KEY_GREENHOUSE : '';
	}

	/**
	 * Get Board token Key for Greenhouse.
	 *
	 * @return string
	 */
	public static function getBoardTokenGreenhouse(): string
	{
		return \defined('ES_BOARD_TOKEN_GREENHOUSE') ? \ES_BOARD_TOKEN_GREENHOUSE : '';
	}

	/**
	 * Get API Key for Workable.
	 *
	 * @return string
	 */
	public static function getApiKeyWorkable(): string
	{
		return \defined('ES_API_KEY_WORKABLE') ? \ES_API_KEY_WORKABLE : '';
	}

	/**
	 * Get subdomain Key for Workable.
	 *
	 * @return string
	 */
	public static function getSubdomainWorkable(): string
	{
		return \defined('ES_SUBDOMAIN_WORKABLE') ? \ES_SUBDOMAIN_WORKABLE : '';
	}

	/**
	 * Get API Key for Mailchimp.
	 *
	 * @return string
	 */
	public static function getApiKeyMailchimp(): string
	{
		return \defined('ES_API_KEY_MAILCHIMP') ? \ES_API_KEY_MAILCHIMP : '';
	}

	/**
	 * Get API Key for Mailerlite.
	 *
	 * @return string
	 */
	public static function getApiKeyMailerlite(): string
	{
		return \defined('ES_API_KEY_MAILERLITE') ? \ES_API_KEY_MAILERLITE : '';
	}

	/**
	 * Get API Key for Goodbits.
	 *
	 * @return string|array<string, mixed>
	 */
	public static function getApiKeyGoodbits()
	{
		return \defined('ES_API_KEY_GOODBITS') ? \ES_API_KEY_GOODBITS : '';
	}

	/**
	 * Get Google reCaptcha site key.
	 *
	 * @return string
	 */
	public static function getGoogleReCaptchaSiteKey()
	{
		return \defined('ES_GOOGLE_RECAPTCHA_SITE_KEY') ? \ES_GOOGLE_RECAPTCHA_SITE_KEY : '';
	}

	/**
	 * Get Google reCaptcha secret key.
	 *
	 * @return string
	 */
	public static function getGoogleReCaptchaSecretKey()
	{
		return \defined('ES_GOOGLE_RECAPTCHA_SECRET_KEY') ? \ES_GOOGLE_RECAPTCHA_SECRET_KEY : '';
	}

	/**
	 * Get forms geolocation ip.
	 *
	 * @return string
	 */
	public static function getGeolocationIp(): string
	{
		return \defined('ES_GEOLOCAITON_IP') ? \ES_GEOLOCAITON_IP : '';
	}

	/**
	 * Get API Key for Clearbit.
	 *
	 * @return string|array<string, mixed>
	 */
	public static function getApiKeyClearbit()
	{
		return \defined('ES_API_KEY_CLEARBIT') ? \ES_API_KEY_CLEARBIT : '';
	}

	/**
	 * Get API key for ActiveCampaign.
	 *
	 * @return string
	 */
	public static function getApiKeyActiveCampaign(): string
	{
		return \defined('ES_API_KEY_ACTIVE_CAMPAIGN') ? \ES_API_KEY_ACTIVE_CAMPAIGN : '';
	}

	/**
	 * Get API URL for ActiveCampaign.
	 *
	 * @return string
	 */
	public static function getApiUrlActiveCampaign(): string
	{
		return \defined('ES_API_URL_ACTIVE_CAMPAIGN') ? \ES_API_URL_ACTIVE_CAMPAIGN : '';
	}
}
