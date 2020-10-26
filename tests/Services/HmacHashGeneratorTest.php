<?php

namespace Tests\Services;

use Bnb\PayboxGateway\Services\HmacHashGenerator;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Foundation\Application;
use Tests\UnitTestCase;
use Mockery;

/**
 * Class HmacHashGeneratorTest
 * @package Tests\Services
 * @group HmacHashGeneratorTest
 */
class HmacHashGeneratorTest extends UnitTestCase
{
  /**
   * Test valid hmac hash for multiple params
   */
  public function testValidHmacHashForMultipleParams()
  {
    $app = Mockery::mock(Application::class)->makePartial();
    $config = Mockery::mock(Config::class);

    $app
      ->shouldReceive('make')
      ->with('config')
      ->once()
      ->andReturn($config);

    /** @var Application $app */
    $generator = new HmacHashGenerator($app);

    $params = [
      'param1' => 'value',
      'param2' => 'value % 2',
    ];

    $secret = 'secret';

    $key = unpack('H*', $secret);
    $key = $key[1];

    $config
      ->shouldReceive('get')
      ->with('paybox.hmac_key')
      ->once()
      ->andReturn($key);

    $result = $generator->get($params);

    $hmac = mb_strtoupper(
      hash_hmac(
        'sha512',
        'param1=' . $params['param1'] . '&param2=' . $params['param2'],
        $secret
      )
    );
    $this->assertSame($hmac, $result);
  }
}
