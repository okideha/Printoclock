<?php

namespace App\Controller;
use App\Entity\User;
use App\Service\SendMailService;
use App\Form\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em, MailerInterface $mailer){
        $this->em =$em;
        $this->mailer =$mailer;
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request, SendMailService $mailService): Response
    {
        $user = new User();
        $form = $this->createForm(NewsletterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = hash('sha256', uniqid());
            $user->setToken($token);
            $this->em->persist($user);
            $this->em->flush();

            //confirm registration
            $this->addFlash('message', 'Merci pour votre inscription, une confirmation vous a été envoyée par mail');

            //send mail confirmation
            $mailService->sendMail($user,'Votre inscription à notre newsletter','email/confirmation.html.twig',['user'=> $user]);

            return $this->redirectToRoute('home');
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
