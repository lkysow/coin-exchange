<?php

namespace Coin\Model;

class TransactionRequest implements \JsonSerializable
{
    protected $quantity;
    protected $price;
    protected $origin;

    public function __construct($quantity, $price, $origin)
    {
        $this->origin = $origin;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    public function getOrigin()
    {
        return $this->origin;
    }

    public function jsonSerialize()
    {
        return [
            'origin' => $this->origin,
            'quantity' => $this->quantity,
            'price' => $this->price
        ];
    }
}
 