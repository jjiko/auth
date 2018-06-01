<?php

return [
  'auth' => [
    'driver' => 'mysql',
    'host' => env('APP_DEBUG_AUTH', false) ? env('LOCAL_DB_HOST') : env('REMOTE_DB_HOST'),
    'database' => 'j5',
    'username' => env('APP_DEBUG_AUTH', false) ? env('LOCAL_DB_USERNAME') : env('REMOTE_DB_USERNAME'),
    'password' => env('APP_DEBUG_AUTH', false) ? env('LOCAL_DB_PASSWORD') : env('REMOTE_DB_PASSWORD'),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'strict' => false,
  ]
];