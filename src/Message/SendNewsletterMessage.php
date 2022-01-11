<?php

namespace App\Message;

final class SendNewsletterMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    private $userId;
    private $newsletterId;

    public function __construct(int $userId, int $newsletterId)
    {
        $this->userId = $userId;
        $this->newsletterId = $newsletterId;
    }

   public function getUserId(): int
   {
       return $this->userId;
   }

   public function getNewsletterId(): int
   {
       return $this->newsletterId;
   }

   
}
