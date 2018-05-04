<?php

namespace Jiko\Auth\Commands;

use Illuminate\Console\Command;
use Jiko\Auth\OAuthUser;
use Jiko\Auth\OAuthMeta;

class MetaCommand extends Command
{
  protected $signature = 'oauth:meta';

  protected $description = 'create meta from oauth raw';

  public function handle()
  {
    $users = OAuthUser::all();
    foreach ($users as $user) {
      $data = json_decode($user->RAW);
      if (!$data) continue;
      foreach ($data as $k => $v) {
        if ($v instanceof \stdClass) {
          $v = json_encode($v);
        }
        // Check for existing meta
        $meta = OAuthMeta::firstOrCreate(['oauth_users_id' => $user->id, 'key' => "{$user->provider}.{$k}", 'value' => $v]);
        $meta->textValue = $v;
        $meta->save();
      }
    }
  }
}