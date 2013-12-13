<?php

namespace Coin\Controller;

use Coin\Socket\JsonRequestSocket;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * Creates routes for all api endpoints
 */
class ApiController
{
    private $app;
    private $socket;
    /** @var  ControllerCollection $api */
    private $api;

    public function __construct(Application $app, JsonRequestSocket $socket)
    {
        $this->app = $app;
        $this->socket = $socket;
        $this->api = $app['controllers_factory'];
    }

    public function boot()
    {
        $this->createPostRoutes($this->app, $this->socket);
        $this->createListRoutes($this->app, $this->socket);
        $this->createGetByIdRoutes($this->app, $this->socket);
        $this->createDeleteRoutes($this->app, $this->socket);

        return $this->api;
    }

    private function createPostRoutes($app, $socket)
    {
        $this->api->post(
            '/{transactionType}',
            function (Request $request, $transactionType) use ($app, $socket) {
                $errorMessage = '';
                $transaction = $app['transaction_factory']->create($request);
                if ($app['request_validator']->transactionRequestIsValid($transaction, $errorMessage)) {
                    $transactionName = $transactionType === 'bids' ? 'bid' : 'ask';
                    $socket->sendTransaction($transactionName, $transaction);

                    return $app->json($socket->receive());
                }

                return $app->json(['error' => $errorMessage], 400);
            }
        )->assert('transactionType', 'bids|asks');
    }

    private function createListRoutes($app, $socket)
    {
        $this->api->get(
            '/{list}',
            function (Request $request, $list) use ($app, $socket) {
                $limit = $request->query->get('limit');
                if ($app['request_validator']->limitIsValid($limit)) {
                    return $socket->sendAndReceive(['type' => $list, 'limit' => $limit]);
                }

                return $app->json(['error' => 'Invalid or unspecified limit.'], 400);
            }
        )->assert('list', 'bids|asks|transactions');
    }

    private function createGetByIdRoutes($app, $socket)
    {
        $this->api->get(
            '/{list}/{id}',
            function ($list, $id) use ($app, $socket) {
                return $socket->sendAndReceive(['type' => "get_$list", 'id' => $id]);
            }
        )->assert('list', 'bid|ask');
    }

    private function createDeleteRoutes($app, $socket)
    {
        $this->api->delete(
            '/{list}/{id}',
            function ($list, $id) use ($app, $socket) {
                $socket->send(['type' => "delete_$list", 'id' => $id]);
                $result = $socket->receive();
                if ($result === true) {
                    return '';
                }

                $app->abort(404);
            }
        )->assert('list', 'bid|ask');
    }
}
 