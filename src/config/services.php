<?php

return [
  'google' => [
    'client_id' => env('GOOGLE_ID'),
    'client_secret' => env('GOOGLE_SECRET'),
    'redirect' => input()->server('HTTP_HOST').'/auth/handler/google'
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