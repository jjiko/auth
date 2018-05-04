<?php

return [
  'google' => [
    'client_id' => env('GOOGLE_ID'),
    'client_secret' => env('GOOGLE_SECRET'),
    'redirect' => env('APP_URL') . '/auth/handler/google',
    'scopes' => [
      'profile',
      'email',
      'https://www.googleapis.com/auth/calendar',
      'https://www.googleapis.com/auth/calendar.readonly',
      'https://www.googleapis.com/auth/youtube',
      'https://www.googleapis.com/auth/youtube.readonly'
    ],
    'options' => [
      'access_type' => 'offline'
    ]
  ],
  'facebook' => [
    'client_id' => env('FACEBOOK_APP_ID'),
    'client_secret' => env('FACEBOOK_APP_SECRET'),
    'redirect' => env('APP_URL') . '/auth/handler/facebook',
    'scopes' => [
      'email',
      'publish_actions',
      'manage_pages',
      'publish_pages'
    ]
  ],
  'instagram' => [
    'client_id' => env('INSTAGRAM_CLIENT_ID'),
    'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/instagram',
    'scopes' => ['scope' => 'basic follower_list likes comments relationships'],
  ],
  'github' => [

  ],
  'spotify' => [
    'client_id' => env('SPOTIFY_KEY'),
    'client_secret' => env('SPOTIFY_SECRET'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/spotify',
  ],
  'steam' => [
    'client_id' => null,
    'client_secret' => env('STEAM_KEY'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/steam'
  ],
  'twitch' => [
    'client_id' => env('TWITCH_CLIENT_ID'),
    'client_secret' => env('TWITCH_CLIENT_SECRET'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/twitch'
  ],
  'twitter' => [
    'client_id' => env('TWITTER_API_KEY'),
    'client_secret' => env('TWITTER_API_SECRET'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/twitter'
  ],
  'blueiris' => [
    'redirect' => env('APP_URL') . '/auth/connection/handler/blueiris'
  ],
  'nest' => [
    'client_id' => env('NEST_KEY'),
    'client_secret' => env('NEST_SECRET'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/nest'
  ],
  'discord' => [
    'client_id' => env('DISCORD_KEY'),
    'client_secret' => env('DISCORD_SECRET'),
    'redirect' => env('APP_URL') . '/auth/connection/handler/discord'
  ]
];