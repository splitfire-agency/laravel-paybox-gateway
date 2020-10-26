<?php

namespace Tests\Services;

use Bnb\PayboxGateway\Services\ServerSelector;
use DOMDocument;
use Exception;
use Illuminate\Config\Repository as Config;
use stdClass;
use Tests\UnitTestCase;
use Mockery;

/**
 * Class ServerSelectorTest
 * @package Tests\Services
 * @group ServerSelectorTest
 */
class ServerSelectorTest extends UnitTestCase
{
  /**
   * Test find paybox server when test is on and 1st server is fine
   * @throws Exception
   */
  public function testFindValidServerForPayboxWhenTestIsOnAnd1stServerIsFine()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $dom = Mockery::mock(DOMDocument::class)->makePartial();
    $domElement = Mockery::mock(stdClass::class);

    $urls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->once()
      ->andReturn(true);
    $config
      ->shouldReceive('get')
      ->with('paybox.test_urls.paybox')
      ->once()
      ->andReturn($urls);

    $serverSelector
      ->shouldReceive('getDocumentLoader')
      ->once()
      ->andReturn($dom);
    $dom
      ->shouldReceive('loadHTMLFile')
      ->with('https://example.com/load.html')
      ->once();
    $domElement->textContent = 'OK';
    $dom->shouldReceive('getElementById')->andReturn($domElement);

    /** @var ServerSelector $serverSelector */
    $result = $serverSelector->find('paybox');

    $this->assertSame($urls[0], $result);
  }

  /**
   * Test return valid server when test is on and 1st server is fine
   * @throws Exception
   */
  public function testReturnValidServerForPayboxWhenTestIsOffAnd1stServerIsFine()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $dom = Mockery::mock(DOMDocument::class)->makePartial();
    $domElement = Mockery::mock(stdClass::class);

    $urls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->once()
      ->andReturn(false);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox')
      ->once()
      ->andReturn($urls);

    $serverSelector
      ->shouldReceive('getDocumentLoader')
      ->once()
      ->andReturn($dom);
    $dom
      ->shouldReceive('loadHTMLFile')
      ->with('https://example.com/load.html')
      ->once();
    $domElement->textContent = 'OK';
    $dom->shouldReceive('getElementById')->andReturn($domElement);

    /** @var ServerSelector $serverSelector */
    $result = $serverSelector->find('paybox');

    $this->assertSame($urls[0], $result);
  }

  /**
   * Test find valid server when test is off and 1st server is down
   * @throws Exception
   */
  public function testFindValidServerForPayboxWhenTestIsOffAnd1stServerIsDown()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $dom = Mockery::mock(DOMDocument::class)->makePartial();
    $domElement = Mockery::mock(stdClass::class);

    $dom2 = Mockery::mock(DOMDocument::class)->makePartial();
    $domElement2 = Mockery::mock(stdClass::class);

    $urls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->once()
      ->andReturn(false);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox')
      ->once()
      ->andReturn($urls);

    $serverSelector
      ->shouldReceive('getDocumentLoader')
      ->once()
      ->andReturn($dom);
    $dom
      ->shouldReceive('loadHTMLFile')
      ->with('https://example.com/load.html')
      ->once();
    $domElement->textContent = 'ERROR';
    $dom->shouldReceive('getElementById')->andReturn($domElement);

    $serverSelector
      ->shouldReceive('getDocumentLoader')
      ->once()
      ->andReturn($dom2);
    $dom2
      ->shouldReceive('loadHTMLFile')
      ->with('https://example.net/load.html')
      ->once();
    $domElement2->textContent = 'OK';
    $dom2->shouldReceive('getElementById')->andReturn($domElement2);

    /** @var ServerSelector $serverSelector */
    $result = $serverSelector->find('paybox');

    $this->assertSame($urls[1], $result);
  }

  /**
   * Test throw exception when test is off and all servers are down
   */
  public function testThrowsExceptionWhenTestIsOffAndAllServersAreDown()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $dom = Mockery::mock(DOMDocument::class)->makePartial();
    $domElement = Mockery::mock(stdClass::class);

    $dom2 = Mockery::mock(DOMDocument::class)->makePartial();
    $domElement2 = Mockery::mock(stdClass::class);

    $urls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->once()
      ->andReturn(false);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox')
      ->once()
      ->andReturn($urls);

    $serverSelector
      ->shouldReceive('getDocumentLoader')
      ->once()
      ->andReturn($dom);
    $dom
      ->shouldReceive('loadHTMLFile')
      ->with('https://example.com/load.html')
      ->once();
    $domElement->textContent = 'ERROR';
    $dom->shouldReceive('getElementById')->andReturn($domElement);

    $serverSelector
      ->shouldReceive('getDocumentLoader')
      ->once()
      ->andReturn($dom2);
    $dom2
      ->shouldReceive('loadHTMLFile')
      ->with('https://example.net/load.html')
      ->once();
    $domElement2->textContent = 'ERROR';
    $dom2->shouldReceive('getElementById')->andReturn($domElement2);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('No servers set or all servers are down');

    /** @var ServerSelector $serverSelector */
    $serverSelector->find('paybox');
  }

  /**
   * Test throw exception when server contains only protocol
   */
  public function testThrowsExceptionWhenServerContainsOnlyProtocol()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $urls = ['http://'];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->once()
      ->andReturn(true);
    $config
      ->shouldReceive('get')
      ->with('paybox.test_urls.paybox')
      ->once()
      ->andReturn($urls);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Url http:// is invalid');

    /** @var ServerSelector $serverSelector */
    $serverSelector->find('paybox');
  }

  /**
   * Test throw exception when server contains only path without host
   */
  public function testThrowsExceptionWhenServerContainsOnlyPathWithoutHost()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $urls = ['sample/path'];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->once()
      ->andReturn(true);
    $config
      ->shouldReceive('get')
      ->with('paybox.test_urls.paybox')
      ->once()
      ->andReturn($urls);

    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Url sample/path is invalid');

    /** @var ServerSelector $serverSelector */
    $serverSelector->find('paybox');
  }

  /**
   * Test find valid server from given when same
   */
  public function testFindValidServerFromGivenWhenSame()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $payboxUrls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $payboxDirectUrls = [
      'https://example-direct.com/paybox-payment-url',
      'https://example-direct.net/paybox-payment-url-2',
    ];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->times(2)
      ->andReturn(false);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox')
      ->once()
      ->andReturn($payboxUrls);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox_direct')
      ->once()
      ->andReturn($payboxDirectUrls);

    /** @var ServerSelector $serverSelector */
    $url = $serverSelector->findFrom(
      'paybox',
      'paybox_direct',
      $payboxUrls[0],
      false
    );
    $this->assertSame($payboxDirectUrls[0], $url);
  }

  /**
   * Test find valid server from given when other
   */
  public function testFindsValidServerFromGivenWhenOther()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $payboxUrls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $payboxDirectUrls = [
      'https://example-direct.com/paybox-payment-url',
      'https://example-direct.net/paybox-payment-url-2',
    ];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->times(2)
      ->andReturn(false);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox')
      ->once()
      ->andReturn($payboxUrls);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox_direct')
      ->once()
      ->andReturn($payboxDirectUrls);

    /** @var ServerSelector $serverSelector */
    $url = $serverSelector->findFrom(
      'paybox',
      'paybox_direct',
      $payboxUrls[0],
      true
    );
    $this->assertSame($payboxDirectUrls[1], $url);
  }

  /**
   * Test return current url when other url cannot be found
   */
  public function testReturnCurrentUrlWhenOtherUrlCannotBeFound()
  {
    $config = Mockery::mock(Config::class);
    $serverSelector = Mockery::mock(ServerSelector::class, [$config])
      ->makePartial()
      ->shouldAllowMockingProtectedMethods();

    $payboxUrls = [
      'https://example.com/paybox-payment-url',
      'https://example.net/paybox-payment-url-2',
    ];

    $payboxDirectUrls = ['https://example-direct.com/paybox-payment-url'];

    $config
      ->shouldReceive('get')
      ->with('paybox.test')
      ->times(2)
      ->andReturn(false);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox')
      ->once()
      ->andReturn($payboxUrls);
    $config
      ->shouldReceive('get')
      ->with('paybox.production_urls.paybox_direct')
      ->once()
      ->andReturn($payboxDirectUrls);

    /** @var ServerSelector $serverSelector */
    $url = $serverSelector->findFrom(
      'paybox',
      'paybox_direct',
      $payboxUrls[0],
      true
    );
    $this->assertSame($payboxDirectUrls[0], $url);
  }
}
