<?php

namespace App\MessageHandler;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Message\SendNewsletterMessage;
use App\Service\SendMailService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class SendNewsletterMessageHandler implements MessageHandlerInterface
{
    private $em;
    private $sendMailService;

    public function __construct(EntityManagerInterface $em, SendMailService $sendMailService){
        $this->em =$em;
        $this->sendMailService =$sendMailService;
    }

    public function __invoke(SendNewsletterMessage $message)
    {
        $user = $this->em->find(User::class, $message->getUserId());
        $newsletter = $this->em->find(Newsletter::class, $message->getNewsletterId());

        if($newsletter!==null && $user!==null){
            $this->sendMailService->sendMail($user,'Votre newsletter '.$newsletter->getCategory()->getName().' mensuelle',
            'email/newsletter.html.twig',[
                'newsletter' => $newsletter,
                'user'=> $user
            ]); 
        }
    }
}
