<?php

namespace Nodrew\Bundle\PhpAirbrakeBundle\EventListener;

use Nodrew\Bundle\PhpAirbrakeBundle\Airbrake\Client;
use Swift_Mailer;

abstract class AbstractListener
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $emailTo;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param Client $client
     * @param \Swift_Mailer $mailer
     * @param string $emailToOnError
     */
    public function __construct(Client $client, Swift_Mailer $mailer, $emailTo = null)
    {
        $this->client = $client;
        $this->emailTo = $emailTo;
        $this->mailer = $mailer;
    }

    /**
     * @param string $text
     */
    protected function sendEmailOnError($text)
    {
        if(!$this->emailTo){
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('PhpAirbrake / Exception')
            ->setTo($this->emailTo)
            ->setBody($text)
        ;

        $this->mailer->send($message);
    }
}