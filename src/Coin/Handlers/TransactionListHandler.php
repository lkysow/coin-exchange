<?php

namespace Coin\Handlers;

class TransactionListHandler extends ExchangeAwareHandler
{
    public function handle($request)
    {
        $list = [];
        switch($request['type']) {
            case 'bids':
                $list = $this->exchange->getBidList();
                break;
            case 'asks':
                $list = $this->exchange->getAskList();
                break;
            case 'transactions':
                // reverse so most recent transaction is first
                $list = array_reverse($this->exchange->getTransactionList());
                break;
        }

        $limit = $request['limit'];
        $limitedList = array_slice($list, 0, $limit);

        return [
            'data' => $limitedList
        ];
    }
}
 