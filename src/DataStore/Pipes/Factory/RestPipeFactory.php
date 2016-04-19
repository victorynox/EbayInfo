<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 28.03.16
 * Time: 15:18
 */

namespace DataStore\Pipes\Factory;

use Interop\Container\ContainerInterface;
use zaboy\rest\Pipes\Factory\RestPipeFactory as PipeFactory;

class RestPipeFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $pipeFactory =  new PipeFactory();
        return $pipeFactory($container, $requestedName, []);
    }
}