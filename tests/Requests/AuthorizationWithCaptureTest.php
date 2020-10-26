<?php

namespace Tests\Requests;

use Tests\UnitTestCase;
use Tests\Helpers\Authorization as AuthorizationHelper;

/**
 * Class AuthorizationWithCaptureTest
 * @package Tests\Requests
 * @group AuthorizationWithCaptureRequestTest
 */
class AuthorizationWithCaptureTest extends UnitTestCase
{
  use AuthorizationHelper;

  /**
   * Setup mocks
   */
  public function setUp(): void
  {
    parent::setUp();
    $this->setUpMocks();
  }

  /**
   * Test getParameters return valid capture parameters
   */
  public function testGetParametersItReturnsValidCaptureParameters()
  {
    $this->ignoreMissingMethods();
    $parameters = $this->request->getParameters();
    $this->assertSame('N', $parameters['PBX_AUTOSEULE']);
  }
}
