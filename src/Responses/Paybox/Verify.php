<?php

namespace Sf\PayboxGateway\Responses\Paybox;

use Sf\PayboxGateway\ResponseCode;
use Sf\PayboxGateway\ResponseField;
use Sf\PayboxGateway\Responses\Exceptions\InvalidSignature;
use Sf\PayboxGateway\Services\Amount;
use Sf\PayboxGateway\Services\SignatureVerifier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository as Config;

class Verify
{
  /**
   * @var Request
   */
  protected $request;

  /**
   * Default parameters mapping from request.
   *
   * @var array
   */
  protected $parameters = [
    ResponseField::AMOUNT => 'amount',
    ResponseField::AUTHORIZATION_NUMBER => 'authorization_number',
    ResponseField::RESPONSE_CODE => 'response_code',
    ResponseField::SIGNATURE => 'signature',
  ];

  /**
   * @var SignatureVerifier
   */
  protected $signatureVerifier;

  /**
   * @var Amount
   */
  protected $amountService;

  /**
   * @var Config
   */
  protected $config;

  /**
   * Verify constructor.
   *
   * @param Request $request
   * @param SignatureVerifier $signatureVerifier
   * @param Amount $amountService
   * @param Config $config
   */
  public function __construct(
    Request $request,
    SignatureVerifier $signatureVerifier,
    Amount $amountService,
    Config $config
  ) {
    $this->request = $request;
    $this->signatureVerifier = $signatureVerifier;
    $this->amountService = $amountService;
    $this->config = $config;
    $this->initParameters();
  }

  /**
   * Init parameters from config.
   */
  protected function initParameters()
  {
    $parameters =
      (array) $this->config->get('paybox.return_fields');
    foreach($parameters as $key => $value) {
      $this->parameters[$key] = $value;
    }
  }


/**
   * Verify whether payment is valid and accepted.
   *
   * @param float $amount
   *
   * @return bool
   */
  public function isSuccess($amount)
  {
    $this->checkSignature();

    return $this->request->input(
      $this->parameters[ResponseField::AUTHORIZATION_NUMBER]
    ) &&
      $this->request->input($this->parameters[ResponseField::RESPONSE_CODE]) ==
        ResponseCode::SUCCESS &&
      $this->request->input($this->parameters[ResponseField::AMOUNT]) ==
        $this->amountService->get($amount, false);
  }

  /**
   * Get Paybox response code.
   *
   * @return string
   */
  public function getResponseCode()
  {
    return (string) $this->request->input(
      $this->parameters[ResponseField::RESPONSE_CODE]
    );
  }

  /**
   * Set parameters map in order to make it possible to verify request in case custom request
   * parameters names vere used.
   *
   * @param array $parameters
   *
   * @throws Exception
   */
  public function setParametersMap(array $parameters)
  {
    if (!isset($parameters[ResponseField::AMOUNT])) {
      throw new Exception('Amount is missing');
    }

    if (!isset($parameters[ResponseField::AUTHORIZATION_NUMBER])) {
      throw new Exception('Authorization number is missing');
    }

    if (!isset($parameters[ResponseField::RESPONSE_CODE])) {
      throw new Exception('Response code is missing');
    }

    if (!isset($parameters[ResponseField::SIGNATURE])) {
      throw new Exception('Signature is missing');
    }

    $this->parameters = $parameters;
  }

  /**
   * @throws InvalidSignature
   */
  protected function checkSignature()
  {
    $signatureParameter = $this->parameters[ResponseField::SIGNATURE];

    if (
      !$this->signatureVerifier->isCorrect(
        $this->request->input($signatureParameter),
        $this->request->except($signatureParameter)
      )
    ) {
      throw new InvalidSignature();
    }
  }
}
