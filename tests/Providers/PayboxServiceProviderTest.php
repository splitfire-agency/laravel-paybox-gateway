<?php

namespace Tests\Providers;

use Sf\PayboxGateway\Providers\PayboxServiceProvider;
use Illuminate\Foundation\Application;
use Mockery;
use Tests\UnitTestCase;

/**
 * Class PayboxServiceProviderTest
 * @package Tests\Providers
 * @group PayboxServiceProviderTest
 */
class PayboxServiceProviderTest extends UnitTestCase
{
  /**
   *
   */
  public function testDoesAllRequiredActionsWhenRegistering()
  {
    $app = Mockery::mock(Application::class);

    $moduleConfigFile = realpath(__DIR__ . '/../../config/paybox.php');
    $configPath = 'dummy/config/path';
    $basePath = 'dummy/base/path';

    $payboxProvider = Mockery::mock(PayboxServiceProvider::class, [$app])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    // merge config
    $payboxProvider
      ->shouldReceive('mergeConfigFrom')
      ->with($moduleConfigFile, 'paybox')
      ->once();

    // publishing configuration files
    $app
      ->shouldReceive('offsetGet')
      ->with('path.config')
      ->once()
      ->andReturn($configPath);
    $payboxProvider
      ->shouldReceive('publishes')
      ->once()
      ->with(
        [
          $moduleConfigFile => $configPath . DIRECTORY_SEPARATOR . 'paybox.php',
        ],
        'config'
      );

    $app
      ->shouldReceive('offsetGet')
      ->with('path.base')
      ->once()
      ->andReturn($basePath);
    $payboxProvider
      ->shouldReceive('publishes')
      ->once()
      ->with(
        [
          realpath(__DIR__ . '/../../views') =>
            $basePath .
            DIRECTORY_SEPARATOR .
            'resources' .
            DIRECTORY_SEPARATOR .
            'views' .
            DIRECTORY_SEPARATOR .
            'paybox',
        ],
        'views'
      );

    $payboxProvider->shouldReceive('loadMigrationsFrom')->once();

    $payboxProvider->register();
  }
}
