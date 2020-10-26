<?php

namespace Sf\PayboxGateway\Requests\PayboxDirect;

use Sf\PayboxGateway\QuestionTypeCode;
use Sf\PayboxGateway\Responses\PayboxDirect\SubscriberAuthorizationWithCapture as SubscriberAuthorizationWithCaptureResponse;

class SubscriberAuthorizationWithCapture extends SubscriberAuthorization
{
  /**
   * @inheritdoc
   */
  public function getQuestionType()
  {
    return QuestionTypeCode::SUBSCRIBER_AUTHORIZATION_WITH_CAPTURE;
  }

  /**
   * @inheritdoc
   */
  public function getResponseClass()
  {
    return SubscriberAuthorizationWithCaptureResponse::class;
  }
}
