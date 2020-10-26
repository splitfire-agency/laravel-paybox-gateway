<?php

namespace Tests\Requests;

use Bnb\PayboxGateway\Requests\Paybox\AuthorizationWithoutCapture;
use Tests\Helpers\Authorization as AuthorizationHelper;
use Tests\UnitTestCase;

/**
 * Class AuthorizationWithoutCaptureTest
 * @package Tests\Requests
 * @group AuthorizationWithoutCaptureRequestTest
 */
class AuthorizationWithoutCaptureTest extends UnitTestCase
{
  use AuthorizationHelper;

  /**
   * Setup mocks
   */
  public function setUp(): void
  {
    parent::setUp();
    $this->setUpMocks(AuthorizationWithoutCapture::class);
  }

  /**
   * Test getParameters return valid capture parameters
   */
  public function testGetParametersItReturnsValidCaptureParameters()
  {
    $this->ignoreMissingMethods();
    $parameters = $this->request->getParameters();
    $this->assertSame('O', $parameters['PBX_AUTOSEULE']);
  }
}
