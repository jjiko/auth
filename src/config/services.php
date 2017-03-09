<?php

return [
  'google' => [
    'client_id' => env('GOOGLE_ID'),
    'client_secret' => env('GOOGLE_SECRET'),
    'redirect' => route('auth.handler', ['provider' => 'google'])
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