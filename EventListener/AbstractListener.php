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
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $emailTo;

    /**
     * @var string
     */
    protected $emailFrom;

    /**
     * @param Client $client
     * @param Swift_Mailer $mailer
     * @param string $emailTo
     * @param string $emailFrom
     */
    public function __construct(Client $client, Swift_Mailer $mailer, $emailTo = null, $emailFrom = null)
    {
        $this->client = $client;
        $this->mailer = $mailer;
        $this->emailTo = $emailTo;
        $this->emailFrom = $emailFrom ?: $emailTo;
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
            ->setFrom($this->emailFrom)
            ->setBody($text)
        ;

        $this->mailer->send($message);
    }
}