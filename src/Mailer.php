<?php namespace Jiko\Auth;

abstract class Mailer
{
  public function sendTo($email, $subject, $fromEmail, $view, $data = [])
  {
    \Mail::queue($view, $data, function ($message) use ($email, $subject, $fromEmail) {
      $message->from($fromEmail, 'contact@joejiko.com');
      $message->to($email)
        ->subject($subject);
    });
  }
}