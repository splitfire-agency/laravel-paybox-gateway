<?php

namespace Tests\Responses;

use Sf\PayboxGateway\DirectResponseCode;
use Sf\PayboxGateway\Responses\PayboxDirect\Capture;
use Mockery;
use Tests\UnitTestCase;

/**
 * Class CaptureTest
 * @package Tests\Responses
 * @group CaptureResponseTest
 */
class CaptureTest extends UnitTestCase
{
  /**
   * Test get fields it gets valid fields
   */
  public function testGetFieldsItGetsValidFields()
  {
    $frenchMessage = 'Transaction non trouvé';

    $responseBody =
      'foo=bar&a=b&c=d&message=' . iconv('UTF-8', 'ISO-8859-1', $frenchMessage);

    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();

    /** @var Capture $response */
    $fields = $response->getFields();
    $this->assertEquals(
      [
        'foo' => 'bar',
        'a' => 'b',
        'c' => 'd',
        'message' => $frenchMessage,
      ],
      $fields
    );
  }

  /**
   * Test isSuccess it returns true when success
   */
  public function testIsSuccessItReturnsTrueWhenSuccess()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::SUCCESS;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertTrue($response->isSuccess());
  }

  /**
   * Test isSuccess it returns false when fail
   */
  public function testIsSuccessItReturnsFalseWhenFail()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::CONNECTION_FAILED;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertFalse($response->isSuccess());
  }

  /**
   * Test shouldBeRepeated return false when success
   */
  public function testShouldBeRepeatedItReturnsFalseWhenSuccess()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::SUCCESS;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertFalse($response->shouldBeRepeated());
  }

  /**
   * Test shouldBeRepeated return false when other error
   */
  public function testShouldBeRepeatedItReturnsFalseWhenOtherError()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::INCOHERENCE_ERROR;
    $response = Mockery::mock(
      Capture::class,
      [$responseBody]
    )->makePartial();
    /** @var Capture $response */
    $this->assertFalse($response->shouldBeRepeated());
  }

  /**
   * Test shouldBeRepeated return false when connection failed
   */
  public function testShouldBeRepeatedItReturnsTrueWhenConnectionFailed()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::CONNECTION_FAILED;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertTrue($response->shouldBeRepeated());
  }

  /**
   * Test shouldBeRepeated return true when connection timeout
   */
  public function shouldBeRepeatedItReturnsTrueWhenTimeout()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::TIMEOUT;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertTrue($response->shouldBeRepeated());
  }

  /**
   * Test shouldBeRepeated return true when internal timeout
   */
  public function shouldBeRepeatedItReturnsTrueWhenInternalTimeout()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::INTERNAL_TIMEOUT;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertTrue($response->shouldBeRepeated());
  }

  /**
   * Test getResponseCode return valid response code
   */
  public function testGetResponseCodeItReturnsValidResponseCode()
  {
    $responseBody =
      'foo=bar&a=b&c=d&CODEREPONSE=' . DirectResponseCode::INCOHERENCE_ERROR;
    $response = Mockery::mock(Capture::class, [$responseBody])->makePartial();
    /** @var Capture $response */
    $this->assertSame(
      DirectResponseCode::INCOHERENCE_ERROR,
      $response->getResponseCode()
    );
  }
}
