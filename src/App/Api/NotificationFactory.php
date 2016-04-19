<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.04.16
 * Time: 14:06
 */

namespace App\Api;


use Interop\Container\ContainerInterface;

class NotificationFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        $store = $container->has('ebay_notification') ? $container->get('ebay_notification') : null;
        return new NotificationAction($config['ebay'], $store);
    }
}