<?php

namespace App\Command;

use App\Repository\NewsletterRepository;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use App\Message\SendNewsletterMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;


class SendNewsletterCommand extends Command
{
    private $newsletterRepository;
    private $sendMailService;
    private $messageBus;
    protected static $defaultName = 'app:send-newsletter';

    public function __construct(
        NewsletterRepository $newsletterRepository,
        UserRepository $userRepository,
        SendMailService $sendMailService,
        MessageBusInterface $messageBus
    ) {
        $this->newsletterRepository = $newsletterRepository;
        $this->sendMailService = $sendMailService;
        $this->userRepository = $userRepository;
        $this->messageBus = $messageBus;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newslettersToSend = $this->newsletterRepository->findBy(['sendAt' => null]);

        foreach ($newslettersToSend as $newsletter) {
            $users = $newsletter->getCategory()->getUsers();
            foreach ($users as $user) {
                //send newsletter
                $this->messageBus->dispatch(new SendNewsletterMessage($user->getId(), $newsletter->getId()));
            }
            //set date sendAt
            $this->sendMailService->isSend($newsletter);
        }

        // outputs a success message
        $output->writeln([
            sizeof($newslettersToSend) . ' newsletter(e) envoyée(s)',
            '',
        ]);

        return Command::SUCCESS;
    }
}
