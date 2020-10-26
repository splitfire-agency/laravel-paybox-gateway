<?php

namespace Tests\Services;

use Sf\PayboxGateway\Services\SignatureVerifier;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

/**
 * Class SignatureVerifierTest
 * @package Tests\Services
 * @group SignatureVerifierTest
 */
class SignatureVerifierTest extends UnitTestCase
{
  /**
   * Test return one when signature is correct
   */
  public function testReturnsOneWhenSignatureIsCorrect()
  {
    $config = m::mock(Config::class);
    $files = new Filesystem();

    $parameters = ['a' => 'b', 'c' => 'd', 'e' => 'fg'];
    $data = 'a=b&c=d&e=fg';

    $key = openssl_pkey_get_private(
      file_get_contents(realpath(__DIR__ . '/../keys/prvkey.pem'))
    );
    openssl_sign($data, $signature, $key);
    openssl_free_key($key);

    $signatureVerifier = m::mock(SignatureVerifier::class, [
      $config,
      $files,
    ])->makePartial();

    $config
      ->shouldReceive('get')
      ->with('paybox.public_key')
      ->once()
      ->andReturn(realpath(__DIR__ . '/../keys/pubkey.pem'));

    /** @var SignatureVerifier $signatureVerifier */
    $result = $signatureVerifier->isCorrect(
      base64_encode($signature),
      $parameters
    );

    $this->assertSame(1, $result);
  }

  /**
   * Test return zero when signature is incorrect
   */
  public function testReturnsZeroWhenSignatureIsIncorrect()
  {
    $config = m::mock(Config::class);
    $files = new Filesystem();

    $parameters = ['a' => 'b', 'c' => 'd', 'e' => 'fg'];

    $signature = 'sample invalid signature';

    $signatureVerifier = m::mock(SignatureVerifier::class, [
      $config,
      $files,
    ])->makePartial();

    $config
      ->shouldReceive('get')
      ->with('paybox.public_key')
      ->once()
      ->andReturn(realpath(__DIR__ . '/../keys/pubkey.pem'));

    /** @var SignatureVerifier $signatureVerifier */
    $result = $signatureVerifier->isCorrect(
      base64_encode($signature),
      $parameters
    );

    $this->assertSame(0, $result);
  }
}
