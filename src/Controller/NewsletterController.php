<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Newsletter;
use App\Entity\User;
use App\Message\SendNewsletterMessage;
use App\Repository\CategoryRepository;
use App\Repository\NewsletterRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;

class NewsletterController extends AbstractController
{
    private $em;
    private $mailer;
    private $newsletterRepository;
    private $categoryRepository;

    public function __construct(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        CategoryRepository $categoryRepository,
        NewsletterRepository $newsletterRepository
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->newsletterRepository = $newsletterRepository;
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * @Route("/newsletter", name="newsletter")
     */
    public function index(): Response
    {
        $newsletters = $this->newsletterRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        return $this->render('newsletter/index.html.twig', [
            'newsletters' => $newsletters,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/newsletter/category/{id}", name="newsletter_by_category")
     */
    public function newsletterByCategory(?Category $category): Response
    {
        if ($category) {
            $newsletters = $category->getNewsletters()->getValues();
            $categories = $this->categoryRepository->findAll();
            return $this->render("newsletter/index.html.twig", [
                'newsletters'  => $newsletters,
                'categories' => $categories
            ]);
        } else {
            $newsletters = null;
            return $this->redirectToRoute("newsletters");
        }
    }

    // /**
    //  * @Route("/newsletter/send/{id}", name="send_newsletter")
    //  */
    // public function sendNewsletter(Newsletter $newsletter, MessageBusInterface $messageBus): Response
    // {
    //     if($newsletter->getSendAt()==null){

    //         $users = $newsletter->getCategory()->getUsers();

    //         foreach ($users as $user) {
    //             //send newsletter
    //             $messageBus->dispatch(new SendNewsletterMessage($user->getId(), $newsletter->getId()));
    //         }
    //     }
        

    //     return $this->redirectToRoute('home');
    // }

    /**
     * @Route("/newsletter/unsubscribe/{id}/{newsletter}/{token}", name="unsubscribe")
     */
    public function unsubscribe(User $user, Newsletter $newsletter, $token): Response
    {
        if ($user->getToken() != $token) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        if (count($user->getCategories()) > 0) {
            $user->removeCategory($newsletter->getCategory());
            $this->em->persist($user);
        }
        $this->em->flush();

        $this->addFlash('message', 'Votre demande de désinscription a été prise en compte.');

        return $this->redirectToRoute('home');
    }
}
