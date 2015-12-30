<?php

namespace SkeletonAPI\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Container;

abstract class AbstractController
{
    /**
     * @var Container   The container of the Slim app that creates instantiates this class
     */
    protected $app;

    /**
     * @var mixed   Logger provided by Slim
     */
    protected $logger;

    /**
     * @var Capsule Database connection
     */
    protected $DB;

    public function __construct(Container $container)
    {
        $this->app = $container;
        $this->DB = $container->get('capsule');
        $this->logger = $container->get('logger');
    }

}