<?php

return [
  'google' => [
    'client_id' => env('GOOGLE_ID'),
    'client_secret' => env('GOOGLE_SECRET'),
    'redirect' => route('auth_handler', ['provider' => 'google'])
  ],
  'facebook' => [

  ],
  'github' => [

  ],
  'steam' => [

  ],
  'twitch' => [

  ],
  'twitter' => [

  ],
];