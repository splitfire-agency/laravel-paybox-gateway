<?php

namespace Tests\Responses;

use Bnb\PayboxGateway\ResponseCode;
use Bnb\PayboxGateway\ResponseField;
use Bnb\PayboxGateway\Responses\Exceptions\InvalidSignature;
use Bnb\PayboxGateway\Responses\Paybox\Verify;
use Bnb\PayboxGateway\Services\Amount;
use Bnb\PayboxGateway\Services\SignatureVerifier;
use Exception;
use Illuminate\Http\Request;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Tests\UnitTestCase;

/**
 * Class VerifyTest
 * @package Tests\Responses
 * @group VerifyResponseTest
 */
class VerifyTest extends UnitTestCase
{
  /**
   * @var Request|LegacyMockInterface|MockInterface
   */
  protected Request $request;
  /**
   * @var SignatureVerifier|LegacyMockInterface|MockInterface
   */
  protected SignatureVerifier $signatureVerifier;
  /**
   * @var Amount|LegacyMockInterface|MockInterface
   */
  protected Amount $amountService;
  /**
   * @var Verify|Mockery\Mock
   */
  protected $verify;

  /**
   * Setup mocks
   */
  public function setUp(): void
  {
    parent::setUp();
    $this->request = Mockery::mock(Request::class);
    $this->signatureVerifier = Mockery::mock(SignatureVerifier::class);
    $this->amountService = Mockery::mock(Amount::class);
    $this->verify = Mockery::mock(Verify::class, [
      $this->request,
      $this->signatureVerifier,
      $this->amountService,
    ])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();
  }

  /**
   * Test isSuccess throws exception when signature is invalid
   */
  public function testIsSuccessItThrowsExceptionWhenSignatureIsInvalid()
  {
    $amount = 23.32;
    $parameters = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
    $signature = 'sampleSignature';

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once()
      ->passthru();
    $this->request
      ->shouldReceive('input')
      ->with('signature')
      ->once()
      ->andReturn($signature);
    $this->request
      ->shouldReceive('except')
      ->with('signature')
      ->once()
      ->andReturn($parameters);
    $this->signatureVerifier
      ->shouldReceive('isCorrect')
      ->with($signature, $parameters)
      ->once()
      ->andReturn(false);

    $this->expectException(InvalidSignature::class);
    $this->verify->isSuccess($amount);
  }

  /**
   * Test isSuccess it returns true when all conditions are met
   */
  public function testIsSuccessItReturnsTrueWhenAllConditionsAreMet()
  {
    $amount = 23.32;
    $expectedAmount = 2332;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->once()
      ->andReturn(ResponseCode::SUCCESS);

    $this->request
      ->shouldReceive('input')
      ->with('amount')
      ->once()
      ->andReturn($expectedAmount);
    $this->amountService
      ->shouldReceive('get')
      ->with($amount, false)
      ->once()
      ->andReturn($expectedAmount);

    $result = $this->verify->isSuccess($amount);
    $this->assertTrue($result);
  }

  /**
   * Test isSuccess return false when not authorization number
   */
  public function testIsSuccessItReturnsFalseWhenNoAuthorizationNumber()
  {
    $amount = 23.32;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn(null);

    $result = $this->verify->isSuccess($amount);
    $this->assertFalse($result);
  }

  /**
   * Test isSuccess return false when response code is different
   */
  public function testIsSuccessItReturnsFalseWhenResponseCodeIsDifferent()
  {
    $amount = 23.32;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->once()
      ->andReturn(ResponseCode::INVALID_EXPIRATION_DATE);

    $result = $this->verify->isSuccess($amount);
    $this->assertFalse($result);
  }

  /**
   * Test isSuccess return false when invalid amount given
   */
  public function testIsSuccessItReturnsFalseWhenInvalidAmountGiven()
  {
    $amount = 23.32;
    $expectedAmount = 2332;

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once();
    $this->request
      ->shouldReceive('input')
      ->with('authorization_number')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->once()
      ->andReturn(ResponseCode::SUCCESS);

    $this->request
      ->shouldReceive('input')
      ->with('amount')
      ->once()
      ->andReturn($expectedAmount - 1);
    $this->amountService
      ->shouldReceive('get')
      ->with($amount, false)
      ->once()
      ->andReturn($expectedAmount);

    $result = $this->verify->isSuccess($amount);
    $this->assertFalse($result);
  }

  /**
   * Test setParametersMap uses valid parameters when set
   * @throws Exception
   */
  public function testSetParametersMapItUsesValidParametersWhenSet()
  {
    $amount = 23.32;
    $expectedAmount = 2332;
    $parameters = ['foo' => 'bar'];
    $signature = 'sampleSignature';

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::RESPONSE_CODE => 'code',
      ResponseField::SIGNATURE => 'sig',
    ]);

    $this->verify
      ->shouldReceive('checkSignature')
      ->withNoArgs()
      ->once()
      ->passthru();
    $this->request
      ->shouldReceive('input')
      ->with('sig')
      ->once()
      ->andReturn($signature);
    $this->request
      ->shouldReceive('except')
      ->with('sig')
      ->once()
      ->andReturn($parameters);
    $this->signatureVerifier
      ->shouldReceive('isCorrect')
      ->with($signature, $parameters)
      ->once()
      ->andReturn(true);
    $this->request
      ->shouldReceive('input')
      ->with('nr')
      ->once()
      ->andReturn('Sample number');
    $this->request
      ->shouldReceive('input')
      ->with('code')
      ->once()
      ->andReturn(ResponseCode::SUCCESS);

    $this->request
      ->shouldReceive('input')
      ->with('money')
      ->once()
      ->andReturn($expectedAmount);
    $this->amountService
      ->shouldReceive('get')
      ->with($amount, false)
      ->once()
      ->andReturn($expectedAmount);

    $result = $this->verify->isSuccess($amount);
    $this->assertTrue($result);
  }

  /**
   * Test setParametersMap throw exception when no amount field given
   * @throws Exception
   */
  public function testSetParametersMapItThrowsExceptionWhenNoAmountFieldGiven()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Amount is missing');

    $this->verify->setParametersMap([
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::RESPONSE_CODE => 'code',
      ResponseField::SIGNATURE => 'sig',
    ]);
  }

  /**
   * Test setParametersMap throw exception when no authorization number given
   * @throws Exception
   */
  public function testSetParametersMapItThrowsExceptionWhenNoAuthorizationNumberGiven()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Authorization number is missing');

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::RESPONSE_CODE => 'code',
      ResponseField::SIGNATURE => 'sig',
    ]);
  }

  /**
   * Test setParametersMap throw exception when no response code given
   * @throws Exception
   */
  public function testSetParametersMapItThrowsExceptionWhenNoResponseCodeGiven()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Response code is missing');

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::SIGNATURE => 'sig',
    ]);
  }

  /**
   * Test setParametersMap throw exception when no signature given
   * @throws Exception
   */
  public function testSetParametersMapItThrowsExceptionWhenNoSignatureGiven()
  {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Signature is missing');

    $this->verify->setParametersMap([
      ResponseField::AMOUNT => 'money',
      ResponseField::AUTHORIZATION_NUMBER => 'nr',
      ResponseField::RESPONSE_CODE => 'code',
    ]);
  }

  /**
   * Test getResponseCode get valid response code
   */
  public function testGetResponseCodeItGetValidResponseCode()
  {
    $responseCode = 123123;
    $this->request
      ->shouldReceive('input')
      ->with('response_code')
      ->andReturn($responseCode);
    $result = $this->verify->getResponseCode();
    $this->assertSame('123123', $result);
  }
}
