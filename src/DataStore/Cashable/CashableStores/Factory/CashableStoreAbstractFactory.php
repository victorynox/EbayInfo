<?php

namespace DataStore\Cashable\CashableStores\Factory;

use Interop\Container\ContainerInterface;
use zaboy\res\DataStores\DataStoresException;
use zaboy\res\DataStores\Factory\DataStoresAbstractFactoryAbstract;
use zaboy\res\DataStores\Factory\Interop;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 31.03.16
 * Time: 18:00
 */
class CashableStoreAbstractFactory extends DataStoresAbstractFactoryAbstract
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        $serviceConfig = $config['dataStore'][$requestedName];
        $requestedClassName = $serviceConfig['class'];
        if (isset($serviceConfig['getAll'])) {
            if($container->has($serviceConfig['getAll'])){
                $getAll = $container->get($serviceConfig['getAll']);
            }else{
                throw new DataStoresException(
                    'There is getAll not created ' . $requestedName . 'in config \'dataStore\''
                );
            }
        } else {
            throw new DataStoresException(
                'There is getAll for ' . $requestedName . 'in config \'dataStore\''
            );
        }
        if(isset($serviceConfig['cashStore'])){
            if($container->has($serviceConfig['cashStore'])){
                $cashStore = $container->get($serviceConfig['cashStore']);
            }else{
                throw new DataStoresException(
                    'There is getAll for ' . $serviceConfig['cashStore'] . 'in config \'dataStore\''
                );
            }
        }else{
            $cashStore = null;
        }

        //$cashStore = isset($serviceConfig['cashStore']) ?  new $serviceConfig['cashStore']() : null;
        return new $requestedClassName($getAll, $cashStore);

    }

    /**
     * Can the factory create an instance for the service?
     *
     * For Service manager V3
     * Edit 'use' section if need:
     * Change:
     * 'use Zend\ServiceManager\AbstractFactoryInterface;' for V2 to
     * 'use Zend\ServiceManager\Factory\AbstractFactoryInterface;' for V3
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        $config = $container->get('config');
        if (!isset($config['dataStore'][$requestedName]['class'])) {
            return false;
        }
        $requestedClassName = $config['dataStore'][$requestedName]['class'];
        return is_a($requestedClassName, 'App\DataStore\Cashable\CashableStore\CashableStore', true);
    }
}