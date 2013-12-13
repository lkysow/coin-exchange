<?php
use Coin\ContainerBuilder;
use Coin\Socket\JsonReplySocket;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new Pimple();
$builder = new ContainerBuilder($container);
$builder->build();
run($container);

function run($container)
{
    $socket = new JsonReplySocket();

    // NOTE: should use React event loop here
    while (true) {
        $request = $socket->receive();
        echo 'new message: ' . json_encode($request) . PHP_EOL;

        $validRequest = false;

        if ($request !== null) {
            $handler = $container['handler_factory']->getHandler($request, $container);
            if ($handler !== null) {
                $validRequest = true;
                $response = $handler->handle($request);
                echo 'response found: ' . json_encode($response) . PHP_EOL;
                $socket->send($response);
            }
        }
        if (!$validRequest) {
            echo 'response not found' . PHP_EOL;
            $socket->send(null);
        }
    }
}