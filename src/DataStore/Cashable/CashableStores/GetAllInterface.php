<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 31.03.16
 * Time: 17:40
 */

namespace DataStore\Cashable\CashableStores;


interface GetAllInterface
{
    /**
     * return array or iterator 
     * @return array
     */
    public function getAll();
}