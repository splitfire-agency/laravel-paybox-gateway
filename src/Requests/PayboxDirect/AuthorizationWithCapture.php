<?php

namespace Sf\PayboxGateway\Requests\PayboxDirect;

use Sf\PayboxGateway\QuestionTypeCode;
use Sf\PayboxGateway\Responses\PayboxDirect\AuthorizationWithCapture as AuthorizationWithCaptureResponse;

class AuthorizationWithCapture extends Authorization
{
  /**
   * @inheritdoc
   */
  public function getQuestionType()
  {
    return QuestionTypeCode::AUTHORIZATION_WITH_CAPTURE;
  }

  /**
   * @inheritdoc
   */
  public function getResponseClass()
  {
    return AuthorizationWithCaptureResponse::class;
  }
}
