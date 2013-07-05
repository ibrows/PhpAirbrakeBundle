<?php

namespace Nodrew\Bundle\PhpAirbrakeBundle\Airbrake;

use Airbrake\Client as AirbrakeClient;
use Airbrake\Configuration as AirbrakeConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @param AirbrakeConfiguration $apiKey
     * @param string $envName
     * @param ContainerInterface $container
     * @param mixed $queue
     * @param string $apiEndPoint
     * @throws \Exception
     */
    public function __construct($apiKey, $envName, ContainerInterface $container, $queue = null, $apiEndPoint = null)
    {
        if (!$apiKey) {
            throw new \Exception("Need API-Key");
        }

        $request       = $container->get('request');
        $controller    = 'None';
        $action        = 'None';

        if ($sa = $request->attributes->get('_controller')) {
            $controllerArray = explode('::', $sa);
            if(sizeof($controllerArray) > 1){
                list($controller, $action) = $controllerArray;
            }
        }

        $options = array(
            'environmentName' => $envName,
            'queue'           => $queue,
            'serverData'      => $request->server->all(),
            'getData'         => $request->query->all(),
            'postData'        => $request->request->all(),
            'sessionData'     => $request->getSession() ? $request->getSession()->all() : null,
            'component'       => $controller,
            'action'          => $action,
            'projectRoot'     => realpath($container->getParameter('kernel.root_dir').'/..'),
        );

        if(!empty($apiEndPoint)){
            $options['apiEndPoint'] = $apiEndPoint;
        }

        parent::__construct(new AirbrakeConfiguration($apiKey, $options));
    }
}
