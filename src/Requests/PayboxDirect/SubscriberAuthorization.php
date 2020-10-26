<?php

namespace Sf\PayboxGateway\Requests\PayboxDirect;

use Sf\PayboxGateway\ActivityCode;
use Sf\PayboxGateway\DirectQuestionField;
use Sf\PayboxGateway\QuestionTypeCode;
use Sf\PayboxGateway\Responses\PayboxDirect\SubscriberAuthorization as SubscriberAuthorizationResponse;
use Carbon\Carbon;

class SubscriberAuthorization extends SubscriberRequest
{
  /**
   * @var string
   */
  protected $cardExpirationDate = null;

  /**
   * @var string
   */
  protected $cardControlNumber = null;

  /**
   * @var string
   */
  protected $activity = ActivityCode::UNSPECIFIED;

  /**
   * Set card expiration date provided by the customer.
   *
   * @param Carbon $cardExpirationDate
   *
   * @return $this
   */
  public function setCardExpirationDate(Carbon $cardExpirationDate)
  {
    $this->cardExpirationDate = $cardExpirationDate->format('my');

    return $this;
  }

  /**
   * Set card control number (CVV) provided by the customer.
   *
   * @param string $cardControlNumber
   *
   * @return $this
   */
  public function setCardControlNumber($cardControlNumber)
  {
    $this->cardControlNumber = $cardControlNumber;

    return $this;
  }

  /**
   * Set operation activity code
   *
   * @param string $activity
   *
   * @return $this
   *
   * @see ActivityCode
   */
  public function setActivity($activity)
  {
    $this->activity = $activity;

    return $this;
  }

  /**
   * Get parameters that will be send to Paybox Direct.
   *
   * @return array
   */
  public function getBasicParameters()
  {
    return [
      DirectQuestionField::AMOUNT => $this->amount,
      DirectQuestionField::CURRENCY => $this->currencyCode,
      DirectQuestionField::REFERENCE => $this->paymentNumber,
      DirectQuestionField::SUBSCRIBER_NUMBER => $this->subscriberNumber,
      DirectQuestionField::CARD_OR_WALLET_NUMBER => $this->subscriberWallet,
      DirectQuestionField::CARD_EXPIRATION_DATE => $this->cardExpirationDate,
      DirectQuestionField::CARD_CONTROL_NUMBER => $this->cardControlNumber,
      DirectQuestionField::ACTIVITY => $this->activity,
    ];
  }

  /**
   * @inheritdoc
   */
  public function getQuestionType()
  {
    return QuestionTypeCode::SUBSCRIBER_AUTHORIZATION_ONLY;
  }

  /**
   * @inheritdoc
   */
  public function getResponseClass()
  {
    return SubscriberAuthorizationResponse::class;
  }
}
