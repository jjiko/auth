<?php

return [
  'google' => [
    'client_id' => env('GOOGLE_ID'),
    'client_secret' => env('GOOGLE_SECRET'),
    'redirect' => $_SERVER['HTTP_HOST'].'/auth/handler/google'
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