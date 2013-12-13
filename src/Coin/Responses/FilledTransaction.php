<?php

namespace Coin\Responses;

class FilledTransaction implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return ['status' => 'filled'];
    }
}
 