<?php

namespace Coin\Validator;

use Coin\Model\TransactionRequest;

class RequestValidator
{
    public function transactionRequestIsValid(TransactionRequest $request, &$errorMessage)
    {
        $origin = $request->getOrigin();
        $price = $request->getPrice();
        $quantity = $request->getQuantity();
        if ($origin == null || $price == null || $quantity == null) {
            $errorMessage = "Origin, price, and quantity must be specified";
            return false;
        }

        $priceAsNumber = floatval($price);
        $roundedPrice = round($priceAsNumber * 100) / 100;
        if ($roundedPrice < 0) {
            $errorMessage = "Price can not be < 0";
            return false;
        }
        if ($roundedPrice > pow(2, 31) - 1) {
            $errorMessage = "Price can not be > 2^31 - 1";
            return false;
        }
        // doing this in here is a hack
        $request->setPrice($roundedPrice);

        $quantityAsNumber = floatval($quantity);
        if ($quantityAsNumber > pow(2, 31) - 1) {
            $errorMessage = "Quantity can not be > 2^31 - 1";
            return false;
        }
        $quantityAsInt = intval($quantity);
        if ($quantityAsInt <= 0) {
            $errorMessage = "Quantity can not be < 0";
            return false;
        }
        $request->setQuantity($quantityAsInt);

        return true;
    }

    public function limitIsValid($limit)
    {
        $limitAsNumber = intval($limit);
        return is_int($limitAsNumber) && $limitAsNumber > 0;
    }
}
 