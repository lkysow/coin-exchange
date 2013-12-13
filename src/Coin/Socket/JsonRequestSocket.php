<?php

namespace Coin\Socket;

use Coin\Socket\JsonSocket;
use Coin\Model\TransactionRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class JsonRequestSocket extends JsonSocket
{
    public function __construct()
    {
        $context = new \ZMQContext(1);
        $this->socket = new \ZMQSocket($context, \ZMQ::SOCKET_REQ);
        $this->socket->connect(self::ADDRESS);
    }

    public function sendTransaction($type, TransactionRequest $transaction)
    {
        $this->send(
            [
                'type' => $type,
                'data' => $transaction
            ]
        );
    }

    public function sendAndReceive($msg)
    {
        $this->send($msg);

        return new JsonResponse($this->receive());
    }
}
 