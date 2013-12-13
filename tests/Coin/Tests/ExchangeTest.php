<?php

namespace Coin\Tests;

use Coin\Exchange;
use Coin\Model\Transaction;
use Coin\Model\TransactionRecord;
use Coin\Responses\FilledTransaction;
use Coin\Responses\UnfulfilledTransaction;

class ExchangeTest extends \PHPUnit_Framework_TestCase
{
    public function testWhenNoAsksBidIsUnfilled()
    {
        $exchange = new Exchange();
        $bid = new Transaction(1, 12.50, 'origin');
        $response = $exchange->placeBid($bid);

        $expectedResponse = new UnfulfilledTransaction($bid);

        $this->assertEquals($expectedResponse, $response);
        $this->assertEquals([$bid], $exchange->getBidList());
        $this->assertEquals([], $exchange->getTransactionList());
    }

    public function testBidIsFilled()
    {
        $exchange = new Exchange();
        $bid = new Transaction(1, 12.50, 'bid');
        $ask = new Transaction(1, 12.50, 'ask');
        $bidResponse = $exchange->placeBid($bid);
        $this->assertEquals([$bid], $exchange->getBidList());

        $askResponse = $exchange->placeAsk($ask);

        $this->assertEquals(new UnfulfilledTransaction($bid), $bidResponse);
        $this->assertInstanceOf('Coin\Responses\FilledTransaction', $askResponse);

        $transactionList = $exchange->getTransactionList();
        $this->assertCount(1, $transactionList);

        /** @var TransactionRecord $transaction */
        $transaction = $transactionList[0];
        $this->assertEquals(1, $transaction->getQuantity());
        $this->assertEquals(12.50, $transaction->getPrice());
        $this->assertEquals('bid', $transaction->getBuyer());
        $this->assertEquals('ask', $transaction->getSeller());

        $this->assertEquals([], $exchange->getBidList());
        $this->assertEquals([], $exchange->getAskList());
    }

    public function testPartialBid()
    {
        $exchange = new Exchange();
        $bid = new Transaction(10, 12.50, 'bid');
        $ask = new Transaction(5, 12, 'ask');

        $bidResponse = $exchange->placeBid($bid);
        $this->assertEquals(new UnfulfilledTransaction($bid), $bidResponse);

        $askResponse = $exchange->placeAsk($ask);
        $this->assertEquals(new FilledTransaction(), $askResponse);

        $transactions = $exchange->getTransactionList();
        $this->assertCount(1, $transactions);

        /** @var TransactionRecord $transaction */
        $transaction = $transactions[0];
        $this->assertEquals(5, $transaction->getQuantity());
        $this->assertEquals(12, $transaction->getPrice());
        $this->assertEquals('bid', $transaction->getBuyer());
        $this->assertEquals('ask', $transaction->getSeller());

        $bidList = $exchange->getBidList();
        $this->assertCount(1, $bidList);

        /** @var Transaction $bid */
        $bid = $bidList[0];
        $this->assertEquals(5, $bid->getQuantity());
        $this->assertEquals(12.50, $bid->getPrice());
    }

    public function testBidListIsSorted()
    {
        $exchange = new Exchange();
        $exchange->placeBid(new Transaction(10, 12.50, 'bid'));
        $exchange->placeBid(new Transaction(10, 10.00, 'bid'));
        $exchange->placeBid(new Transaction(10, 13.50, 'bid'));

        $bidList = $exchange->getBidList();
        $prices = array_map(
            function (Transaction $transaction) {
                return $transaction->getPrice();
            },
            $bidList
        );
        $this->assertEquals([13.50, 12.50, 10.00], $prices);

    }

    public function testAskListIsSorted()
    {
        $exchange = new Exchange();

        $exchange->placeAsk(new Transaction(5, 11.50, 'ask'));
        $exchange->placeAsk(new Transaction(5, 1, 'ask'));
        $exchange->placeAsk(new Transaction(5, 14.77, 'ask'));

        $askList = $exchange->getAskList();
        $prices = array_map(
            function (Transaction $transaction) {
                return $transaction->getPrice();
            },
            $askList
        );
        $this->assertEquals([1, 11.50, 14.77], $prices);
    }

}
 