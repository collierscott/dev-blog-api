<?php

namespace App\EMail;

use App\Entity\User;

/**
 * Class Mailer
 */
class Mailer
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        \Swift_Mailer $mailer,
        \Twig_Environment $twig
    )
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user){

        try {
            $body = $this->twig->render(
                'email/confirmation.html.twig',
                [
                    'user' => $user,
                ]
            );
        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        }

        // Send email here
        $message = (new \Swift_Message('Please confirm your account'))
            ->setFrom('api-platform@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html');
        $this->mailer->send($message);
    }
}