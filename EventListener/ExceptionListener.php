<?php
namespace Nodrew\Bundle\PhpAirbrakeBundle\EventListener;

use Nodrew\Bundle\PhpAirbrakeBundle\Airbrake\Client;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * The PhpAirbrakeBundle ExceptionListener.
 *
 * Handles exceptions that occur in the code base.
 *
 * @package		Airbrake
 * @author		Drew Butler <hi@nodrew.com>
 * @copyright	(c) 2011 Drew Butler
 * @license		http://www.opensource.org/licenses/mit-license.php
 */
class ExceptionListener
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof HttpException) {
            return;
        }
        
        $this->client->notifyOnException($exception);

        error_log($exception->getMessage().' in: '.$exception->getFile().':'.$exception->getLine());
    }
}
