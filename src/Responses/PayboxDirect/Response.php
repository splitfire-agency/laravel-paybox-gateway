<?php

namespace Sf\PayboxGateway\Responses\PayboxDirect;

use Sf\PayboxGateway\DirectResponseCode;
use Sf\PayboxGateway\DirectResponseField;

abstract class Response
{
  /**
   * Response body.
   *
   * @var string
   */
  protected $body;

  /**
   * Response fields.
   *
   * @var array
   */
  protected $fields = [];

  /***
   * @var \Sf\PayboxGateway\Models\Response
   */
  protected $model;

  /**
   * Capture constructor.
   *
   * @param $body
   */
  public function __construct($body)
  {
    $this->body = $body;
    $this->setFields();
  }

  /**
   * Get response body.
   *
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Get fields from body.
   *
   * @return array
   */
  public function getFields()
  {
    return $this->fields;
  }

  /**
   * Get a field from body.
   *
   * @return mixed|null
   *
   * @see DirectResponseField
   */
  public function getField($key)
  {
    return isset($this->fields[$key]) ? $this->fields[$key] : null;
  }

  /**
   * Verify whether request was successful.
   *
   * @return bool
   */
  public function isSuccess()
  {
    return $this->fields[DirectResponseField::RESPONSE_CODE] ==
      DirectResponseCode::SUCCESS;
  }

  /**
   * Get Paybox response code.
   *
   * @return string
   */
  public function getResponseCode()
  {
    return $this->fields[DirectResponseField::RESPONSE_CODE];
  }

  /**
   * Get Paybox response comment.
   *
   * @return string
   */
  public function getComment()
  {
    return $this->fields[DirectResponseField::COMMENT];
  }

  /**
   * @return \Sf\PayboxGateway\Models\Response
   */
  public function getModel()
  {
    return $this->model;
  }

  /**
   * @param \Sf\PayboxGateway\Models\Response $model
   *
   * @return $this
   */
  public function setModel(\Sf\PayboxGateway\Models\Response $model)
  {
    $this->model = $model;

    return $this;
  }

  /**
   * Verify whether request should be repeated to secondary server.
   *
   * @return bool
   */
  public function shouldBeRepeated()
  {
    return collect([
      DirectResponseCode::CONNECTION_FAILED,
      DirectResponseCode::TIMEOUT,
      DirectResponseCode::INTERNAL_TIMEOUT,
    ])->contains($this->fields[DirectResponseField::RESPONSE_CODE]);
  }

  /**
   * Set fields from response body.
   */
  protected function setFields()
  {
    $fields = explode('&', $this->body);

    array_walk($fields, function (&$item, &$key) {
      list($key, $item) = explode('=', $item);
      $this->fields[$key] = urldecode(iconv('ISO-8859-1', 'UTF-8', $item));
    });
  }
}
