<?php

namespace Coin\Factory;

use Coin\Model\TransactionRequest;
use Symfony\Component\HttpFoundation\Request;

class TransactionFactory
{
    public function create(Request $request)
    {
        $postRequest = $request->request;

        $origin = $postRequest->get('origin');
        $price = $postRequest->get('price');
        $quantity = $postRequest->get('quantity');

        return new TransactionRequest($quantity, $price, $origin);
    }
}
 