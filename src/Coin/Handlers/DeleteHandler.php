<?php

namespace Coin\Handlers;

class DeleteHandler extends ExchangeAwareHandler
{
    public function handle($request)
    {
        $id = $request['id'];
        if ($request['type'] === 'delete_bid') {
            return $this->exchange->deleteBid($id);
        }
        return $this->exchange->deleteAsk($id);
    }
}
 