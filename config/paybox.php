<?php

return [
  /*
   * Whether test environment is enabled
   */
  'test' => env('PAYBOX_TEST', false),

  /*
   * 00103: Paybox Direct
   * 00104: Paybox Direct Plus
   */
  'direct_version' => env('PAYBOX_DIRECT_VERSION', '00104'),

  /*
   * Site number (provided by Paybox)
   */
  'site' => env('PAYBOX_SITE', ''),

  /*
   * Rank number (provided by Paybox)
   */
  'rank' => env('PAYBOX_RANK', ''),

  /*
   * Internal identifier (provided by Paybox)
   */
  'id' => env('PAYBOX_ID', ''),

  /*
   * HMAC authentication key - it should be generated in Paybox merchant panel
   */
  'back_office_password' => env('PAYBOX_BACK_OFFICE_PASSWORD', ''),

  /*
   * HMAC authentication key - it should be generated in Paybox merchant panel
   */
  'hmac_key' => env('PAYBOX_HMAC_KEY', ''),

  /*
   * Paybox public key location - you can get it from
   * http://www1.paybox.com/wp-content/uploads/2014/03/pubkey.pem
   */
  'public_key' => storage_path('paybox/pubkey.pem'),

  /*
   * Application Prefix for REFABONNE field.
   * Prepended to wallet_id
   */
  'wallet_prefix' => env('PAYBOX_WALLET_PREFIX', 'WALLET_'),

  /*
   * Default return fields when going back from Paybox. You can change here keys as you want,
   * you can add also more values from ResponseField class
   */
  'return_fields' => [
    'amount' => \Sf\PayboxGateway\ResponseField::AMOUNT,
    'authorization_number' =>
      \Sf\PayboxGateway\ResponseField::AUTHORIZATION_NUMBER,
    'order_number' => \Sf\PayboxGateway\ResponseField::ORDER_NUMBER,
    'response_code' => \Sf\PayboxGateway\ResponseField::RESPONSE_CODE,
    'payment_type' => \Sf\PayboxGateway\ResponseField::PAYMENT_TYPE,
    'call_number' => \Sf\PayboxGateway\ResponseField::PAYBOX_CALL_NUMBER,
    'transaction_number' =>
      \Sf\PayboxGateway\ResponseField::TRANSACTION_NUMBER,
    // signature should be always last return field
    'signature' => \Sf\PayboxGateway\ResponseField::SIGNATURE,
  ],

  /*
   * Notifications settings for server-to-server communication about Paybox Direct payments status
   */
  'notifications' => [
    'enabled' => filter_var(
      env('PAYBOX_NOTIFICATIONS_ENABLED', true),
      FILTER_VALIDATE_BOOLEAN
    ),
    'queue' => [
      'connection' => env('PAYBOX_NOTIFICATIONS_QUEUE_CONNECTION'),
      'queue' => env('PAYBOX_NOTIFICATIONS_QUEUE_NAME'),
    ],
    'retry_after' => (int) env('PAYBOX_NOTIFICATIONS_RETRY_AFTER', 60),
    'url' => env('PAYBOX_NOTIFICATIONS_URL'),
    'notify_to' => env('PAYBOX_NOTIFICATIONS_NOTIFY_TO'),
    'notify_from' => [
      'address' => env('PAYBOX_NOTIFICATIONS_NOTIFY_FROM_ADDRESS'),
      'name' => env('PAYBOX_NOTIFICATIONS_NOTIFY_FROM_NAME'),
    ],
  ],

  /*
   * Those are routes names (not urls) where customer will be redirected after payment. If you
   * want to use custom route with params in url you should set them dynamically when creating
   * authorization data. You shouldn't change keys here. Those urls will be later launched using
   * GET HTTP request
   */
  'customer_return_routes_names' => [
    'accepted' => 'paybox.accepted',
    'refused' => 'paybox.refused',
    'aborted' => 'paybox.aborted',
    'waiting' => 'paybox.waiting',
  ],

  /*
   * This is route name (not url) where Paybox will send transaction status. This url is
   * independent from customer urls and it's the only url that should be used to track current
   * payment status for real. If you want to use custom route with params in url you should set it
   * dynamically when creating authorization data. This url will be later launched using GET HTTP
   * request
   */
  'transaction_verify_route_name' => 'paybox.process',

  /*
   * Access urls for Paybox for production environment
   */
  'production_urls' => [
    /*
     * Paybox System urls
     */
    'paybox' => [
      'https://tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
      'https://tpeweb1.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
    ],

    /*
     * Paybox Direct urls
     */
    'paybox_direct' => [
      'https://ppps.e-transactions.fr/PPPS.php',
      'https://ppps1.e-transactions.fr/PPPS.php',
    ],
  ],

  /*
   * Access urls for Paybox for test environment
   */
  'test_urls' => [
    /*
     * Paybox System urls
     */
    'paybox' => [
      'https://preprod-tpeweb.e-transactions.fr/cgi/MYchoix_pagepaiement.cgi',
    ],

    /*
     * Paybox Direct urls
     */
    'paybox_direct' => ['https://preprod-ppps.e-transactions.fr/PPPS.php'],
  ],
];
