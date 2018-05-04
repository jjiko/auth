<?php
namespace Jiko\Auth;

class UserMailer extends Mailer
{
  public function verify($email, $data)
  {
    $view = 'auth::emails.activate-link';
    $subject = $data['subject'];
    $fromEmail = 'contact@joejiko.com';

    $this->sendTo($email, $subject, $fromEmail, $view, $data);
  }

  public function passwordReset($email, $data)
  {
    $view = 'auth::emails.password-reset';
    $subject = $data['subject'];
    $fromEmail = 'contact@joejiko.com';

    $this->sendTo($email, $subject, $fromEmail, $view, $data);
  }
}