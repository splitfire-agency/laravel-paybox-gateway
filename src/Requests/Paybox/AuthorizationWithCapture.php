<?php

namespace Sf\PayboxGateway\Requests\Paybox;

class AuthorizationWithCapture extends Authorization
{
  /**
   * {@inheritdoc}
   */
  public function getBasicParameters()
  {
    $parameters = parent::getBasicParameters();
    $parameters['PBX_AUTOSEULE'] = 'N';

    return $parameters;
  }
}
