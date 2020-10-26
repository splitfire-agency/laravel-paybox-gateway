<?php

namespace Sf\PayboxGateway\Requests\PayboxDirect;

use Sf\PayboxGateway\DirectQuestionField;
use Sf\PayboxGateway\QuestionTypeCode;
use Sf\PayboxGateway\Responses\PayboxDirect\SubscriberDelete as SubscriberDeleteResponse;

class SubscriberDelete extends SubscriberRequest
{
  /**
   * Get parameters that will be send to Paybox Direct.
   *
   * @return array
   */
  public function getBasicParameters()
  {
    return [
      DirectQuestionField::SUBSCRIBER_NUMBER => $this->subscriberNumber,
      DirectQuestionField::CARD_OR_WALLET_NUMBER => $this->subscriberWallet,
    ];
  }

  /**
   * @inheritdoc
   */
  public function getQuestionType()
  {
    return QuestionTypeCode::SUBSCRIBER_DELETE;
  }

  /**
   * @inheritdoc
   */
  public function getResponseClass()
  {
    return SubscriberDeleteResponse::class;
  }
}
