<?php

namespace Coin\Model;

class Transaction extends TransactionRequest
{
    protected $id;

    public function __construct($quantity, $price, $origin) {
        $this->id = uniqid();
        parent::__construct($quantity, $price, $origin);
    }

    public function decrementQuantity($decrement)
    {
        $this->quantity -= $decrement;
        if ($this->quantity < 0) {
            $this->quantity = 0;
        }
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function jsonSerialize()
    {
        return array_merge(['id' => $this->id], parent::jsonSerialize());
    }
}
 