<?php

namespace Coin;

use Coin\Model\Transaction;
use Coin\Model\TransactionRecord;
use Coin\Responses\FilledTransaction;
use Coin\Responses\UnfulfilledTransaction;

/**
 * God class that manages the exchange using arrays
 */
class Exchange
{
    private $bidList;
    private $askList;
    private $transactionList;

    public function __construct()
    {
        $this->bidList = [];
        $this->askList = [];
        $this->transactionList = [];
    }

    public function placeBid(Transaction $bid)
    {
        foreach ($this->askList as $i => $ask) {
            if ($this->canSatisfyBid($ask, $bid)) {
                $this->doTransaction($ask, $bid, $i, $this->askList, 'bid');
                if ($bid->getQuantity() === 0) {
                    return new FilledTransaction();
                }
            } else {
                // if the bid can't be satisfied then since the list is sorted, no reason to go further
                break;
            }
        }

        // the bid hasn't been filled so add it to the list
        $this->bidList[] = $bid;
        $this->sort($this->bidList, false);

        return new UnfulfilledTransaction($bid);
    }

    public function placeAsk(Transaction $ask)
    {
        foreach ($this->bidList as $i => $bid) {
            if ($this->canSatisfyBid($ask, $bid)) {
                $this->doTransaction($ask, $bid, $i, $this->bidList, 'ask');
                if ($ask->getQuantity() === 0) {
                    return new FilledTransaction();
                }
            } else {
                break;
            }
        }

        $this->askList[] = $ask;
        $this->sort($this->askList, true);

        return new UnfulfilledTransaction($ask);
    }

    private function canSatisfyBid(Transaction $ask, Transaction $bid)
    {
        return $ask->getPrice() <= $bid->getPrice();
    }

    private function doTransaction(Transaction $ask, Transaction $bid, $listIndex, &$list, $transactionType)
    {
        $quantityDiff = $bid->getQuantity() - $ask->getQuantity();
        $quantitySold = $quantityDiff > 0 ? $ask->getQuantity() : $bid->getQuantity();

        $ask->decrementQuantity($quantitySold);
        $bid->decrementQuantity($quantitySold);

        // record transaction
        $transaction = new TransactionRecord($ask, $bid, $quantitySold);
        $this->transactionList[] = $transaction;

        // if ask or bid is satisfied, remove from array
        if ($this->transactionShouldBeRemoved($transactionType, $ask, $bid)) {
            array_splice($list, $listIndex, 1);
        }
    }

    private function transactionShouldBeRemoved($transactionType, Transaction $ask, Transaction $bid)
    {
        return ($transactionType === 'bid' && $ask->getQuantity() === 0)
        || ($transactionType === 'ask' && $bid->getQuantity() === 0);
    }

    private function sort(&$list, $ascending)
    {
        usort(
            $list,
            function (Transaction $transactionA, Transaction $transactionB) use ($ascending) {
                $difference = $transactionB->getPrice() - $transactionA->getPrice();

                return $ascending ? -($difference) : $difference;
            }
        );
    }

    private function getFromListById(array $list, $id)
    {
        foreach ($list as $transaction) {
            if ($transaction->getId() === $id) {
                return $transaction;
            }
        }

        return null;
    }

    private function deleteFromList(&$list, $id)
    {
        for($i = 0; $i < count($list); $i++) {
            $transaction = $list[$i];
            if ($transaction->getId() === $id) {
                array_splice($list, $i, 1);
                return true;
            }
        }

        return false;
    }

    public function getTransactionList()
    {
        return $this->transactionList;
    }

    public function getAskList()
    {
        return $this->askList;
    }

    public function getBidList()
    {
        return $this->bidList;
    }

    public function getBid($id)
    {
        return $this->getFromListById($this->bidList, $id);
    }

    public function getAsk($id)
    {
        return $this->getFromListById($this->askList, $id);
    }

    public function deleteBid($id)
    {
        return $this->deleteFromList($this->bidList, $id);
    }

    public function deleteAsk($id)
    {
        return $this->deleteFromList($this->askList, $id);
    }
}
 