<?php

namespace Nodrew\Bundle\PhpAirbrakeBundle\Airbrake;

use Airbrake\Client as AirbrakeClient;
use Airbrake\Configuration as AirbrakeConfiguration;
use Airbrake\Notice;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The PhpAirbrakeBundle Client Loader.
 *
 * This class assists in the loading of the php-airbrake Client class.
 *
 * @package		Airbrake
 * @author		Drew Butler <hi@nodrew.com>
 * @copyright	(c) 2011 Drew Butler
 * @license		http://www.opensource.org/licenses/mit-license.php
 */
class Client extends AirbrakeClient
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @param AirbrakeConfiguration $apiKey
     * @param string $envName
     * @param string $rootDir
     * @param RequestStack $requestStack
     * @param mixed $queue
     * @param string $apiEndPoint
     * @param bool $enabled
     * @throws \Exception
     */
    public function __construct($apiKey, $envName, $rootDir, RequestStack $requestStack, $queue = null, $apiEndPoint = null, $enabled = false)
    {
        if (!$apiKey) {
            throw new \Exception("Need API-Key");
        }

        $this->enabled = $enabled;

        $projectRoot    = realpath($rootDir.'/..');

        if ($requestStack->getCurrentRequest() === null) {
            $serverData     = "";
            $getData        = "";
            $postData       = "";
            $sessionData    = null;
            $action         = 'None';
            $component      = 'None';
        } else {
            $request        = $requestStack->getCurrentRequest();
            $controller     = 'None';
            $action         = 'None';

            if ($sa = $request->attributes->get('_controller')) {
                $controllerArray = explode('::', $sa);
                if(sizeof($controllerArray) > 1){
                    list($controller, $action) = $controllerArray;
                }
            }

            $serverData     = $request->server->all();
            $getData        = $request->query->all();
            $postData       = $request->request->all();
            $sessionData    = $request->getSession() ? $request->getSession()->all() : null;
            $component      = $controller;

        }
        $options = array(
            'environmentName' => $envName,
            'queue'           => $queue,
            'serverData'      => $serverData,
            'getData'         => $getData,
            'postData'        => $postData,
            'sessionData'     => $sessionData,
            'component'       => $component,
            'action'          => $action,
            'projectRoot'     => $projectRoot,
        );
        if(!empty($apiEndPoint)){
            $options['apiEndPoint'] = $apiEndPoint;
        }
        parent::__construct(new AirbrakeConfiguration($apiKey, $options));
    }

    /**
     * Notify about the notice.
     *
     * If there is a PHP Resque client given in the configuration, then use that to queue up a job to
     * send this out later. This should help speed up operations.
     *
     * @param Notice $notice
     * @return bool
     */
    public function notify(Notice $notice)
    {
        if(false === $this->enabled){
            return true;
        }
        return parent::notify($notice);
    }
}
