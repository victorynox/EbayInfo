<?php

namespace DataStore\Cashable\CashableStores;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 31.03.16
 * Time: 16:59
 */
interface RefreshableInterface
{

    /**
     * @return null
     * @throws \Exception
     */
    public function refresh();
    
}