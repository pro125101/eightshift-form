<?php namespace EightshiftFormsTests;

use \Brain\Monkey;
use \Brain\Monkey\Functions;

class BaseTest extends \Codeception\Test\Unit
{

  const WP_REDIRECT_ACTION = 'eightshift_forms_test/wp_safe_redirect_happened';
  const WP_MAIL_ACTION = 'eightshift_forms_test/wp_mail_happened';

  protected function _before()
  {
    Monkey\setUp();

    // Given functions will return the first argument they will receive,
    // just like `when( $function_name )->justReturnArg()` was used for all of them.
    Functions\stubs(
      [
          'esc_attr',
          'esc_html',
          'esc_textarea',
          '__',
          '_x',
          'esc_html__',
          'esc_html_x',
          'esc_attr_x',
      ]
    );

    // Given functions can have a custom callback.
    Functions\stubs([
      'wp_json_encode' => function($data) {
          return json_encode($data);
      },
      'rest_ensure_response' => function($response) {
        if ( is_wp_error( $response ) ) {
          return $response;
        }

        if ( $response instanceof \WP_REST_Response ) {
            return $response;
        }

        return new \WP_REST_Response( $response );
      },
      'wp_safe_redirect' => function($data) {
        do_action( self::WP_REDIRECT_ACTION, $this);
      },
      'wp_mail' => function($data) {
        do_action( self::WP_MAIL_ACTION, $this);
        return true;
      },
    ]);

  }

  protected function _after()
  {
    Monkey\tearDown();
  }
}