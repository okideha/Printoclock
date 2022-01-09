<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Form\NewsletterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class NewsletterController extends AbstractController
{
    private $em;
    private $encoder;

    public function __construct(EntityManagerInterface $em){
        $this->em =$em;
    }
    /**
     * @Route("/newsletter", name="newsletter")
     */
    public function index(Request $request, MailerInterface $mailer): Response
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

            //confirmation by mail
            $email = (new TemplatedEmail())
            -> from ('newsletter@printoclok.fr')
            -> to(new Address($user->getEmail()))
            -> subject('Votre inscription à notre newsletter')
            -> htmlTemplate('email/confirmation.html.twig')
            -> context(['user'=> $user]);

            $mailer->send($email);

            return $this->redirectToRoute('home');
        }
        return $this->render('home/newsletter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
