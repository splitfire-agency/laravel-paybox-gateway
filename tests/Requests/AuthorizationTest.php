<?php

namespace Tests\Requests;

use Carbon\Carbon;
use Bnb\PayboxGateway\Currency;
use Bnb\PayboxGateway\Language;
use Tests\Helpers\Authorization as AuthorizationHelper;
use Tests\UnitTestCase;

/**
 * Class AuthorizationTest
 * @package Tests\Requests
 * @group AuthorizationRequestTest
 */
class AuthorizationTest extends UnitTestCase
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
   * Test getParameters return all parameters
   */
  public function testGetParametersItReturnsAllParameters()
  {
    $sampleParameters = ['a' => 'b', 'c' => 'd', 'e' => 'f'];
    $sampleHmac = 'sampleHmacHash';

    $this->request
      ->shouldReceive('getBasicParameters')
      ->withNoArgs()
      ->once()
      ->andReturn($sampleParameters);

    $this->hmacHashGenerator
      ->shouldReceive('get')
      ->with($sampleParameters)
      ->once()
      ->andReturn($sampleHmac);

    $parameters = $this->request->getParameters();

    $this->assertEquals(
      $sampleParameters + ['PBX_HMAC' => $sampleHmac],
      $parameters
    );
  }

  /**
   * Test getParameters return valid parameters
   */
  public function testGetParametersItReturnsValidParameters()
  {
    $sampleHmac = 'sampleHmacHash';

    $this->request
      ->shouldReceive('getBasicParameters')
      ->withNoArgs()
      ->once()
      ->passthru();

    $sampleSite = 'SITE-NR';
    $sampleRank = 'SITE-RANK';
    $sampleId = 'SITE-ID';
    $defaultParameters = ['a' => 'b', 'c' => 'd', 'e' => 'fg'];
    $acceptedRoute = 'paybox.accepted';
    $acceptedUrl = 'http://example.com/accepted-url';
    $refusedRoute = 'paybox.refused';
    $refusedUrl = 'http://example.com/refused-url';
    $abortedRoute = 'paybox.aborted';
    $abortedUrl = 'http://example.com/aborted-url';
    $waitingRoute = 'paybox.waiting';
    $waitingUrl = 'http://example.com/waiting-url';
    $transactionRoute = 'paybox.transaction';
    $transactionUrl = 'http://example.com/transaction-url';

    $this->config
      ->shouldReceive('get')
      ->with('paybox.site')
      ->once()
      ->andReturn($sampleSite);
    $this->config
      ->shouldReceive('get')
      ->with('paybox.rank')
      ->once()
      ->andReturn($sampleRank);
    $this->config
      ->shouldReceive('get')
      ->with('paybox.id')
      ->once()
      ->andReturn($sampleId);

    $this->request
      ->shouldReceive('getFormattedReturnFields')
      ->withNoArgs()
      ->once()
      ->passthru();
    $this->config
      ->shouldReceive('get')
      ->with('paybox.return_fields')
      ->once()
      ->andReturn($defaultParameters);

    $this->config
      ->shouldReceive('get')
      ->with('paybox.customer_return_routes_names.accepted')
      ->once()
      ->andReturn($acceptedRoute);
    $this->urlGenerator
      ->shouldReceive('route')
      ->with($acceptedRoute)
      ->once()
      ->andReturn($acceptedUrl);
    $this->config
      ->shouldReceive('get')
      ->with('paybox.customer_return_routes_names.refused')
      ->once()
      ->andReturn($refusedRoute);
    $this->urlGenerator
      ->shouldReceive('route')
      ->with($refusedRoute)
      ->once()
      ->andReturn($refusedUrl);
    $this->config
      ->shouldReceive('get')
      ->with('paybox.customer_return_routes_names.aborted')
      ->once()
      ->andReturn($abortedRoute);
    $this->urlGenerator
      ->shouldReceive('route')
      ->with($abortedRoute)
      ->once()
      ->andReturn($abortedUrl);
    $this->config
      ->shouldReceive('get')
      ->with('paybox.customer_return_routes_names.waiting')
      ->once()
      ->andReturn($waitingRoute);
    $this->urlGenerator
      ->shouldReceive('route')
      ->with($waitingRoute)
      ->once()
      ->andReturn($waitingUrl);

    $this->config
      ->shouldReceive('get')
      ->with('paybox.transaction_verify_route_name')
      ->once()
      ->andReturn($transactionRoute);
    $this->urlGenerator
      ->shouldReceive('route')
      ->with($transactionRoute)
      ->once()
      ->andReturn($transactionUrl);

    $this->hmacHashGenerator
      ->shouldReceive('get')
      ->once()
      ->andReturn($sampleHmac);

    $parameters = $this->request->getParameters();

    $this->assertSame($sampleSite, $parameters['PBX_SITE']);
    $this->assertSame($sampleRank, $parameters['PBX_RANG']);
    $this->assertSame($sampleId, $parameters['PBX_IDENTIFIANT']);
    $this->assertSame(null, $parameters['PBX_TOTAL']);
    $this->assertSame(Language::FRENCH, $parameters['PBX_LANGUE']);
    $this->assertSame(null, $parameters['PBX_CMD']);
    $this->assertSame('SHA512', $parameters['PBX_HASH']);
    $this->assertSame(null, $parameters['PBX_PORTEUR']);
    $this->assertSame('a:b;c:d;e:fg', $parameters['PBX_RETOUR']);
    $this->assertArrayHasKey('PBX_TIME', $parameters);
    $this->assertSame($acceptedUrl, $parameters['PBX_EFFECTUE']);
    $this->assertSame($refusedUrl, $parameters['PBX_REFUSE']);
    $this->assertSame($abortedUrl, $parameters['PBX_ANNULE']);
    $this->assertSame($waitingUrl, $parameters['PBX_ATTENTE']);
    $this->assertSame($transactionUrl, $parameters['PBX_REPONDRE_A']);
    $this->assertSame($sampleHmac, $parameters['PBX_HMAC']);
  }

  /**
   * Test setAmount get valid amount and currency when both given
   */
  public function getSetAmountItGetsValidAmountAndCurrencyWhenBothGiven()
  {
    $this->ignoreMissingMethods();
    $this->amountService
      ->shouldReceive('get')
      ->with(100.22, false)
      ->once()
      ->andReturn('sample');
    $this->request->setAmount(100.22, Currency::CHF);
    $parameters = $this->request->getParameters();
    $this->assertSame('sample', $parameters['PBX_TOTAL']);
    $this->assertSame(Currency::CHF, $parameters['PBX_DEVISE']);
  }

  /**
   * Test setAmount get valid amount and currency when no currency given
   */
  public function testSetAmountItGetsValidAmountAndCurrencyWhenNoCurrency()
  {
    $this->ignoreMissingMethods();
    $this->amountService
      ->shouldReceive('get')
      ->with('100,4567', false)
      ->once()
      ->andReturn('sample2');
    $this->request->setAmount('100,4567');
    $parameters = $this->request->getParameters();
    $this->assertSame('sample2', $parameters['PBX_TOTAL']);
    $this->assertSame(Currency::EUR, $parameters['PBX_DEVISE']);
  }

  /**
   * Test setLanguage get valid language when language set
   */
  public function testSetLanguageItGetsValidLanguageWhenLanguageWasSet()
  {
    $this->ignoreMissingMethods();
    $this->request->setLanguage(Language::DUTCH);
    $parameters = $this->request->getParameters();
    $this->assertSame(Language::DUTCH, $parameters['PBX_LANGUE']);
  }

  /**
   * Test setLanguage get valid language when language is not set
   */
  public function testSetLanguageItGetsValidLanguageWhenLanguageWasNotSet()
  {
    $this->ignoreMissingMethods();
    $parameters = $this->request->getParameters();
    $this->assertSame(Language::FRENCH, $parameters['PBX_LANGUE']);
  }

  /**
   * Test setCustomerEmail get valid customer email when email is set
   */
  public function testSetCustomerEmailItGetsValidCustomerEmailWhenSet()
  {
    $this->ignoreMissingMethods();
    $this->request->setCustomerEmail('foo-bar@example.com');
    $parameters = $this->request->getParameters();
    $this->assertSame('foo-bar@example.com', $parameters['PBX_PORTEUR']);
  }

  /**
   * Test setTime get valid datetime when set
   */
  public function testSetTimeItGetsValidDateTimeWhenSet()
  {
    $this->ignoreMissingMethods();
    $date = Carbon::now()->addDays(10);
    $this->request->setTime($date);
    $parameters = $this->request->getParameters();
    $this->assertSame($date->format('c'), $parameters['PBX_TIME']);
  }

  /**
   * Test setTime get valid datetime when set
   */
  public function testSetTimeItGetsValidDateTimeWhenNotSet()
  {
    $this->ignoreMissingMethods();
    $parameters = $this->request->getParameters();
    $now = Carbon::now();
    Carbon::setTestNow($now);
    $this->assertSame($now->format('c'), $parameters['PBX_TIME']);
  }

  /**
   * Test setPaymentNumber get valid payment number
   */
  public function testSetPaymentNumberItGetsValidPaymentNumber()
  {
    $this->ignoreMissingMethods();
    $this->request->setPaymentNumber(123);
    $parameters = $this->request->getParameters();
    $this->assertSame(123, $parameters['PBX_CMD']);
  }

  /**
   * Test setReturnFields get valid fields
   */
  public function testSetReturnFieldsItGetsValidReturnFields()
  {
    $this->ignoreMissingMethods();
    $fields = ['a' => 'b', 'c' => 'de', 'f' => 'g'];
    $this->request->setReturnFields($fields);
    $parameters = $this->request->getParameters();
    $this->assertSame('a:b;c:de;f:g', $parameters['PBX_RETOUR']);
  }

  /**
   * Test setCustomerPaymentAcceptedUrl get valid url
   */
  public function testSetCustomerPaymentAcceptedUrlItGetsValidAcceptedUrl()
  {
    $this->ignoreMissingMethods();
    $sampleUrl = 'https://example.com/accepted-url';
    $this->request->setCustomerPaymentAcceptedUrl($sampleUrl);
    $parameters = $this->request->getParameters();
    $this->assertSame($sampleUrl, $parameters['PBX_EFFECTUE']);
  }

  /**
   * Test setCustomerPaymentRefusedUrl get valid url
   */
  public function setCustomerPaymentRefusedUrl_it_gets_valid_refused_url()
  {
    $this->ignoreMissingMethods();
    $sampleUrl = 'https://example.com/refused-url';
    $this->request->setCustomerPaymentRefusedUrl($sampleUrl);
    $parameters = $this->request->getParameters();
    $this->assertSame($sampleUrl, $parameters['PBX_REFUSE']);
  }

  /**
   * Test setCustomerPaymentAbortedUrl get valid url
   */
  public function testSetCustomerPaymentAbortedUrlItGetsValidAbortedUrl()
  {
    $this->ignoreMissingMethods();
    $sampleUrl = 'https://example.com/aborted-url';
    $this->request->setCustomerPaymentAbortedUrl($sampleUrl);
    $parameters = $this->request->getParameters();
    $this->assertSame($sampleUrl, $parameters['PBX_ANNULE']);
  }

  /**
   * Test setCustomerPaymentWaitingUrl get valid url
   */
  public function testSetCustomerPaymentWaitingUrlItGetsValidWaitingUrl()
  {
    $this->ignoreMissingMethods();
    $sampleUrl = 'https://example.com/waiting-url';
    $this->request->setCustomerPaymentWaitingUrl($sampleUrl);
    $parameters = $this->request->getParameters();
    $this->assertSame($sampleUrl, $parameters['PBX_ATTENTE']);
  }

  /**
   * Test setTransactionVerifyUrl get valid transaction url
   */
  public function testSetTransactionVerifyUrlItgetsValidTransactionUrlWhenSet()
  {
    $this->ignoreMissingMethods();
    $sampleUrl = 'https://example.com/transaction-url';
    $this->request->setTransactionVerifyUrl($sampleUrl);
    $parameters = $this->request->getParameters();
    $this->assertSame($sampleUrl, $parameters['PBX_REPONDRE_A']);
  }

  /**
   * Test getUrl with server selection
   */
  public function testGetUrlItFiresServerSelectorOnce()
  {
    $validUrl = 'https://sample.com/valid/server/url';

    $this->serverSelector
      ->shouldReceive('find')
      ->once()
      ->with('paybox')
      ->andReturn($validUrl);

    $url = $this->request->getUrl();
    $this->assertSame($validUrl, $url);

    // now launch again - server should not be searched one more time but result should be same
    $url = $this->request->getUrl();
    $this->assertSame($validUrl, $url);
  }
}
