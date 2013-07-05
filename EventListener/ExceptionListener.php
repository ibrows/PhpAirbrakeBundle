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
class ExceptionListener extends AbstractListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof HttpException) {
            return;
        }
        
        if(!$this->client->notifyOnException($exception)){
            $this->sendEmailOnError((string)$exception);
        }

        error_log($exception->getMessage().' in: '.$exception->getFile().':'.$exception->getLine());
    }
}
