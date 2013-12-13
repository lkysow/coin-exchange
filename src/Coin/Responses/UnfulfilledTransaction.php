<?php

namespace Coin\Responses;

use Coin\Model\Transaction;

class UnfulfilledTransaction implements \JsonSerializable
{
    private $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function jsonSerialize()
    {
        return [
            'status' => 'unfilled',
            'id' => $this->transaction->getId(),
            'origin' => $this->transaction->getOrigin(),
            'price' => $this->transaction->getPrice(),
            'quantity' => $this->transaction->getQuantity()
        ];
    }
}
 