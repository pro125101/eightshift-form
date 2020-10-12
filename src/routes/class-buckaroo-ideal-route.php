<?php
/**
 * Endpoint for handling Buckaroo integration on form submit.
 *
 * Example call:
 * /wp-json/eightshift-forms/v1/buckaroo
 *
 * @package Eightshift_Forms\Rest
 */

declare( strict_types=1 );

namespace Eightshift_Forms\Rest;

use Eightshift_Forms\Core\Filters;
use Eightshift_Libs\Core\Config_Data;
use Eightshift_Forms\Captcha\Basic_Captcha;
use Eightshift_Forms\Integrations\Buckaroo\Buckaroo;
use Eightshift_Forms\Exception\Missing_Filter_Info_Exception;
use Eightshift_Forms\Exception\Unverified_Request_Exception;
use Eightshift_Forms\Integrations\Authorization\Authorization_Interface;
use Eightshift_Forms\Integrations\Authorization\HMAC;

/**
 * Class Buckaroo_Ideal_Route
 */
class Buckaroo_Ideal_Route extends Base_Route {

  /**
   * Route slug
   *
   * @var string
   */
  const ENDPOINT_SLUG = '/buckaroo-ideal';

  /**
   * Issuer, bank code.
   */
  const ISSUER_PARAM = 'issuer';

  /**
   * Name of the required parameter for donation amount.
   *
   * @var string
   */
  const DONATION_AMOUNT_PARAM = 'donation-amount';

  /**
   * Name of the required parameter for redirect url.
   *
   * @var string
   */
  const REDIRECT_URL_PARAM = 'redirect-url';

  /**
   * Name of the required parameter for redirect url cancel.
   *
   * @var string
   */
  const REDIRECT_URL_CANCEL_PARAM = 'redirect-url-cancel';

  /**
   * Name of the required parameter for redirect url error.
   *
   * @var string
   */
  const REDIRECT_URL_ERROR_PARAM = 'redirect-url-error';

  /**
   * Name of the required parameter for redirect url reject.
   *
   * @var string
   */
  const REDIRECT_URL_REJECT_PARAM = 'redirect-url-reject';

  /**
   * Construct object
   *
   * @param Config_Data                     $config                          Config data obj.
   * @param Buckaroo                        $buckaroo                        Buckaroo integration obj.
   * @param Buckaroo_Response_Handler_Route $buckaroo_response_handler_route Response handler route obj.
   * @param Authorization_Interface         $hmac                            Authorization object.
   * @param Basic_Captcha                   $basic_captcha                   Basic_Captcha object.
   */
  public function __construct(
    Config_Data $config,
    Buckaroo $buckaroo,
    Buckaroo_Response_Handler_Route $buckaroo_response_handler_route,
    Authorization_Interface $hmac,
    Basic_Captcha $basic_captcha
  ) {
    $this->config                          = $config;
    $this->buckaroo                        = $buckaroo;
    $this->buckaroo_response_handler_route = $buckaroo_response_handler_route;
    $this->hmac                            = $hmac;
    $this->basic_captcha                   = $basic_captcha;
  }

  /**
   * Method that returns rest response
   *
   * @param  \WP_REST_Request $request Data got from endpoint url.
   *
   * @return WP_REST_Response|mixed If response generated an error, WP_Error, if response
   *                                is already an instance, WP_HTTP_Response, otherwise
   *                                returns a new WP_REST_Response instance.
   */
  public function route_callback( \WP_REST_Request $request ) {

    try {
      $params = $this->verify_request( $request, Filters::BUCKAROO );
    } catch ( Unverified_Request_Exception $e ) {
      return rest_ensure_response( $e->get_data() );
    }

    try {
      $params = $this->set_redirect_urls( $params );

      $this->buckaroo->set_test();
      $response = $this->buckaroo->send_payment(
        $params[ self::DONATION_AMOUNT_PARAM ],
        'test invoice 123',
        $params[ self::ISSUER_PARAM ] ?? 'ABNANL2A'
      );
    } catch ( Missing_Filter_Info_Exception $e ) {
      return $this->rest_response_handler( 'buckaroo-missing-keys', [ 'message' => $e->getMessage() ] );
    } catch ( \Exception $e ) {
      return $this->rest_response_handler_unknown_error( [ 'error' => $e->getResponse()->getBody()->getContents() ] );
    }

    return \rest_ensure_response(
      [
        'code' => 200,
        'data' => $response,
      ]
    );
  }

  /**
   * We need to define redirect URLs so that Buckaroo redirects the user to our buckaroo-response-handler route
   * which might run some custom logic and then redirect the user to the actual redirect URL as defined in the form's
   * options.
   *
   * @param array $params Array of WP_REST_Request params.
   * @return array
   */
  protected function set_redirect_urls( array $params ): array {

    // Now let's define all Buckaroo-recognized statuses for which we need to provide redirect URLs.
    $statuses = [
      Buckaroo_Response_Handler_Route::STATUS_SUCCESS,
      Buckaroo_Response_Handler_Route::STATUS_CANCELED,
      Buckaroo_Response_Handler_Route::STATUS_ERROR,
      Buckaroo_Response_Handler_Route::STATUS_REJECT,
    ];

    // Now let's build redirect URLs (to buckaroo-response-handler middleware route) for each status.
    $redirect_urls = [];
    $base_url      = \home_url( $this->buckaroo_response_handler_route->get_route_uri() );
    foreach ( $statuses as $status_value ) {
      $url_params = $params;
      $url_params[ Buckaroo_Response_Handler_Route::STATUS_PARAM ] = $status_value;
      $url = \add_query_arg( array_merge(
        $url_params,
        [ HMAC::AUTHORIZATION_KEY => rawurlencode( $this->hmac->generate_hash( $url_params, $this->generate_authorization_salt_for_response_handler() ) ) ]
      ), $base_url );

      $redirect_urls[] = $url;
    }

    $this->buckaroo->set_redirect_urls( ...$redirect_urls );

    return $params;
  }

  /**
   * Define authorization salt used for request to response handler.
   *
   * @return string
   */
  protected function generate_authorization_salt_for_response_handler(): string {
    return \apply_filters( Filters::BUCKAROO, 'secret_key' ) ?? 'invalid-salt-for-response-handler';
  }

  /**
   * Defines a list of required parameters which must be present in the request or it will error out.
   *
   * @return array
   */
  protected function get_required_params(): array {
    return [
      self::DONATION_AMOUNT_PARAM,
    ];
  }
}
