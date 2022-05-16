<?php

namespace App\Notification;
use App\Entity\User;
use Twig\Environment;

class RegisterNotification
{
 /**
 * @var \Swift_Mailer
 */
 private $mailer;
 /**
 * @var Environment
 */
 private $renderer;
 public function __construct(\Swift_Mailer $mailer, Environment $renderer)
 {
 $this->mailer = $mailer;
 $this->renderer = $renderer;
 }

 public function notify(User $user)
 {
 $message = (new \Swift_Message('Message : Votre compte a bien été crée'))
 ->setFrom('Challenge@support.com')
 ->setTo($user->getEmail())
 ->setReplyTo('Challenge@support.com')
 ->setBody($this->renderer->render('email/register.html.twig', [
 'user' => $user
 ]), 'text/html');
 $this->mailer->send($message);
 }
}