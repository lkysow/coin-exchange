<?php

namespace Coin;

use Coin\Factory\HandlerFactory;
use Coin\Handlers\DeleteHandler;
use Coin\Handlers\GetByIdHandler;
use Coin\Handlers\TransactionHandler;
use Coin\Handlers\TransactionListHandler;

class ContainerBuilder
{
    /** @var \Pimple $container */
    private $container;

    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    public function build()
    {
        $this->container['exchange'] = $this->container->share(
            function () {
                return new Exchange();
            }
        );

        $this->container['transaction_handler'] = $this->container->share(
            function ($container) {
                return new TransactionHandler($container['exchange']);
            }
        );

        $this->container['transaction_list_handler'] = $this->container->share(
            function ($container) {
                return new TransactionListHandler($container['exchange']);
            }
        );

        $this->container['get_by_id_handler'] = $this->container->share(
            function($container) {
                return new GetByIdHandler($container['exchange']);
            }
        );

        $this->container['delete_handler'] = $this->container->share(
            function($container) {
                return new DeleteHandler($container['exchange']);
            }
        );

        $this->container['handler_factory'] = $this->container->share(
            function () {
                return new HandlerFactory();
            }
        );
    }
}
 