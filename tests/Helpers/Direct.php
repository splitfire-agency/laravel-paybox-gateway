<?php

namespace Tests\Helpers;

use Sf\PayboxGateway\Requests\PayboxDirect\DirectRequest;
use Sf\PayboxGateway\Requests\Request;
use Sf\PayboxGateway\Services\Amount;
use Sf\PayboxGateway\Services\HmacHashGenerator;
use Sf\PayboxGateway\Services\ServerSelector;
use Illuminate\Contracts\Config\Repository as Config;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Sf\PayboxGateway\HttpClient\GuzzleHttpClient;

/**
 * Trait Direct
 * @package Tests\Helpers
 */
trait Direct
{
  /**
   * @var ServerSelector|LegacyMockInterface|MockInterface
   */
  protected ServerSelector $serverSelector;

  /**
   * @var Config|LegacyMockInterface|MockInterface
   */
  protected Config $config;

  /**
   * @var HmacHashGenerator|LegacyMockInterface|MockInterface
   */
  protected HmacHashGenerator $hmacHashGenerator;

  /**
   * @var Request|Mockery\Mock
   */
  protected $request;

  /**
   * @var Amount|LegacyMockInterface|MockInterface
   */
  protected Amount $amountService;

  /**
   * @var GuzzleHttpClient|LegacyMockInterface|MockInterface
   */
  protected GuzzleHttpClient $client;

  /**
   * Set up mocks
   * @param string $class Direct Request class
   */
  protected function setUpMocks($class = DirectRequest::class)
  {
    $this->serverSelector = Mockery::mock(ServerSelector::class);
    $this->config = Mockery::mock(Config::class);
    $this->hmacHashGenerator = Mockery::mock(HmacHashGenerator::class);
    $this->amountService = Mockery::mock(Amount::class);
    $this->client = Mockery::mock(GuzzleHttpClient::class);
    $this->request = Mockery::mock($class, [
      $this->serverSelector,
      $this->config,
      $this->hmacHashGenerator,
      $this->amountService,
      $this->client
    ])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();
  }

  /**
   * Should ignore missing methods
   */
  protected function ignoreMissingMethods()
  {
    $this->config->shouldIgnoreMissing();
    $this->client->shouldIgnoreMissing();
    $this->hmacHashGenerator->shouldIgnoreMissing();
    $this->amountService->shouldIgnoreMissing();
  }
}
