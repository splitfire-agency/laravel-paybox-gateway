<?php

namespace Tests\Helpers;

use Sf\PayboxGateway\Requests\Paybox\AuthorizationWithCapture;
use Sf\PayboxGateway\Requests\Request;
use Sf\PayboxGateway\Services\Amount;
use Sf\PayboxGateway\Services\HmacHashGenerator;
use Sf\PayboxGateway\Services\ServerSelector;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

/**
 * Trait Authorization
 * @package Tests\Helpers
 */
trait Authorization
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
   * @var UrlGenerator|LegacyMockInterface|MockInterface
   */
  protected UrlGenerator $urlGenerator;

  /**
   * @var Request|Mockery\Mock
   */
  protected $request;

  /**
   * @var Amount|LegacyMockInterface|MockInterface
   */
  protected Amount $amountService;

  /**
   * Set up mocks
   * @param string $class Request class
   */
  protected function setUpMocks($class = AuthorizationWithCapture::class)
  {
    $this->serverSelector = Mockery::mock(ServerSelector::class);
    $this->config = Mockery::mock(Config::class);
    $this->hmacHashGenerator = Mockery::mock(HmacHashGenerator::class);
    $this->urlGenerator = Mockery::mock(UrlGenerator::class);
    $this->amountService = Mockery::mock(Amount::class);
    $this->request = Mockery::mock($class, [
      $this->serverSelector,
      $this->config,
      $this->hmacHashGenerator,
      $this->urlGenerator,
      $this->amountService,
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
    $this->urlGenerator->shouldIgnoreMissing();
    $this->hmacHashGenerator->shouldIgnoreMissing();
    $this->amountService->shouldIgnoreMissing();
  }
}
