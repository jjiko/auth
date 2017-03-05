<?php namespace Jiko\Auth;

use Hash, Carbon\Carbon;

class UserRepository
{
  protected $userMailer;

  public function __construct(UserMailer $userMailer)
  {
    $this->userMailer = $userMailer;
  }

  public function register($data)
  {
    $user = new User;
    $user->email = $data['email'];
    $user->first_name = ucfirst($data['first_name']);
    $user->last_name = ucfirst($data['last_name']);
    $user->password = Hash::make($data['password']);
    $user->save();

    // assign role
    $role = Role::whereName('user')->first();
    $user->assignRole($role);
  }

  public function resetPassword(User $user)
  {
    $token = sha1(mt_rand());
    $password = new Password;
    $password->email = $user->email;
    $password->token = $token;
    $password->created_at = Carbon::now();
    $password->save();

    $data = [
      'first_name' => $user->first_name,
      'token' => $token,
      'subject' => 'JoeJiko.com: Reset your password',
      'email' => $user->email
    ];

    $this->userMailer->passwordReset($user->email, $data);
  }
}