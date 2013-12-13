<?php

namespace Coin\Handlers;

class GetByIdHandler extends ExchangeAwareHandler
{
    public function handle($request)
    {
        $type = $request['type'];
        $id = $request['id'];

        return $type === 'get_bid' ? $this->exchange->getBid($id) : $this->exchange->getAsk($id);
    }
}