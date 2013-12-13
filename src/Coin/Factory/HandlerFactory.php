<?php

namespace Coin\Factory;

class HandlerFactory
{
    public function getHandler($request, \Pimple $container)
    {
        if (!isset($request['type'])) {
            return null;
        }

        switch($request['type']) {
            case 'bid':
            case 'ask':
                return $container['transaction_handler'];
            case 'bids':
            case 'asks':
            case 'transactions':
                return $container['transaction_list_handler'];
            case 'get_bid':
            case 'get_ask':
                return $container['get_by_id_handler'];
            case 'delete_bid':
            case 'delete_ask':
                return $container['delete_handler'];
        }

        return null;
    }
}
 