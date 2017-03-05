<?php namespace Jiko\Auth\Events;
use Jiko\Auth\User;

class UserRegistered {
  public $user;

  function __construct(User $user)
  {
    $this->user = $user;
  }
}