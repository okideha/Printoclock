<?php

namespace App\Service;

use App\Entity\Newsletter;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;

class SendMailService
{
    private $mailer;
    private $em;

    public function __construct(MailerInterface $mailer,EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function sendMail(User $user, String $subject, string $htmlTemplate, $context): void
    {
        $email = (new TemplatedEmail())
            ->from('newsletter@site.fr')
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        $this->mailer->send($email);
    }

    public function isSend(Newsletter $newsletter)
    {
        $newsletter->setSendAt(new \Datetime());
        $this->em->persist($newsletter);
        $this->em->flush();
    }
}
