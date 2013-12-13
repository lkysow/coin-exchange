<?php

use Coin\Controller\ApiController;
use Coin\Factory\TransactionFactory;
use Coin\Socket\JsonRequestSocket;
use Coin\Validator\RequestValidator;
use Silex\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application();
$socket = new JsonRequestSocket();
$controller = new ApiController($app, $socket);

$api = $controller->boot();
$app->mount('/', $api);

loadContainer($app);

$app->run();


function loadContainer(Application $app)
{
    $app['transaction_factory'] = $app->share(
        function () {
            return new TransactionFactory();
        }
    );

    $app['request_validator'] = $app->share(
        function () {
            return new RequestValidator();
        }
    );
}