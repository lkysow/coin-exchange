<?php

namespace Coin\Socket;

/**
 * Wraps ZMQSocket for JSON requests and responses
 */
abstract class JsonSocket
{
    const ADDRESS = "ipc:///usr/local/feed";

    protected $socket;

    public function receive()
    {
        return json_decode($this->socket->recv(), true);
    }

    public function send($msg)
    {
        $this->socket->send(json_encode($msg));
    }
}
 