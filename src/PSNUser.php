<?php namespace Jiko\Auth;

class PSNUser
{
  protected $id;
  protected $refreshToken;
  protected $npsso;

  public $user;
  public $token;

  function __construct(OAuthUser $user)
  {
    $this->id = $user->oauth_id;
    $this->token = $user->token;
    $this->refreshToken = $user->refreshToken;
    $this->npsso = $user->tokenSecret;
    $this->user = $user;
  }

  public function getTokens()
  {
    return [
      "oauth" => $this->token,
      "refresh" => $this->refreshToken,
      "npsso" => $this->npsso
    ];
  }

  public function updateToken($token)
  {
    $this->token = $token;
    $this->user->token = $token;
    $this->user->save();

    return $this->getTokens();
  }

  public function getId()
  {
    return $this->id;
  }
}