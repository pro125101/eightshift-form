<?php namespace EightshiftFormsTests\Routes;

use EightshiftFormsTests\BaseTest;

use Eightshift_Forms\Core\Main;
use Eightshift_Forms\Integrations\Authorization\HMAC;
use Eightshift_Forms\Rest\Test_Route;

abstract class BaseRouteTest extends BaseTest
{

  protected function _inject(Main $main, HMAC $hmac)
  {
    $main->set_test(true);
    $this->di_container = $main->build_di_container();
    $this->hmac = $hmac;
  }

  protected function _before()
  {
    parent::_before();
    $this->route_endpoint = $this->di_container->get($this->get_route_name());
  }

  abstract protected function get_route_name();

  protected function verifyProperlyFormattedResponse($response) {
    $this->assertInstanceOf('WP_REST_Response', $response);
    $this->assertObjectHasAttribute('data', $response);
    $this->assertArrayHasKey('code', $response->data);
    $this->assertArrayHasKey('data', $response->data);
    $this->assertArrayHasKey('message', $response->data['data']);
  }

  protected function verifyProperlyFormattedError($response) {
    $this->assertInstanceOf('WP_REST_Response', $response);
    $this->assertObjectHasAttribute('data', $response);
    $this->assertArrayHasKey('code', $response->data);
    $this->assertArrayHasKey('data', $response->data);
    $this->assertArrayHasKey('message', $response->data);
    $this->assertNotEquals($response->data['code'], 200);
  }
}