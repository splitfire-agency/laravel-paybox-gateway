<?php

namespace Tests\HttpClient;

use Sf\PayboxGateway\HttpClient\GuzzleHttpClient;
use GuzzleHttp\Client;
use stdClass;
use Tests\UnitTestCase;
use Mockery;

/**
 * Class GuzzleHttpClientTest
 * @package Tests\HttpClient
 * @group GuzzleHttpClientTest
 */
class GuzzleHttpClientTest extends UnitTestCase
{
  /**
   * Check if guzzle make valid requests
   */
  public function testRunsValidRequest()
  {
    $client = Mockery::mock(Client::class);
    $response = Mockery::mock(stdClass::class);
    $url = 'http://example.com';
    $parameters = ['a' => 'b', 'c' => 'd'];
    $responseBody = 'foo=bar&baz=foo';

    $guzzleClient = Mockery::mock(GuzzleHttpClient::class, [$client])->makePartial();

    $client
      ->shouldReceive('request')
      ->with('POST', $url, ['form_params' => $parameters])
      ->once()
      ->andReturn($response);

    $response
      ->shouldReceive('getBody')
      ->once()
      ->andReturn($responseBody);

    $response = $guzzleClient->request($url, $parameters);

    $this->assertSame($responseBody, $response);
  }
}
