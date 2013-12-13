<?php

namespace Coin\Handlers;

use Coin\Exchange;

abstract class ExchangeAwareHandler implements Handler
{
    protected $exchange;

    public function __construct(Exchange $exchange)
    {
        $this->exchange = $exchange;
    }
}
 