<?php

namespace Coin\Model;

class TransactionRecord implements \JsonSerializable
{
    private $buyer;
    private $seller;
    private $price;
    private $quantity;

    public function __construct(Transaction $ask, Transaction $bid, $amountSold)
    {
        $this->buyer = $bid->getOrigin();
        $this->seller = $ask->getOrigin();
        $this->price = $ask->getPrice();
        $this->quantity = $amountSold;
    }

    /**
     * @return mixed
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getSeller()
    {
        return $this->seller;
    }



    public function jsonSerialize()
    {
        return [
            'buyer' => $this->buyer,
            'seller' => $this->seller,
            'price' => $this->price,
            'quantity' => $this->quantity
        ];
    }
}
 