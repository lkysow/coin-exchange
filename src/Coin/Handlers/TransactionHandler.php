<?php

namespace Coin\Handlers;

use Coin\Model\Transaction;

class TransactionHandler extends ExchangeAwareHandler
{
    public function handle($request) {
        $requestData = $request['data'];
        $transaction = new Transaction($requestData['quantity'], $requestData['price'], $requestData['origin']);

        if ($request['type'] === 'bid') {
            return $this->exchange->placeBid($transaction);
        }

        return $this->exchange->placeAsk($transaction);
    }
}
 