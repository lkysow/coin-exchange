<?php

namespace Coin\Socket;

use Coin\Socket\JsonSocket;

class JsonReplySocket extends JsonSocket
{
    public function __construct()
    {
        $context = new \ZMQContext(1);
        $this->socket = new \ZMQSocket($context, \ZMQ::SOCKET_REP);
        $this->socket->bind(self::ADDRESS);
    }

}
 