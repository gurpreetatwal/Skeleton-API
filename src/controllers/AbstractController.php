<?php

namespace SkeletonAPI\Controllers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Monolog\Logger;
use Slim\Container;

abstract class AbstractController
{
    /**
     * @var Container Slim DI Container
     */
    protected $app;

    /**
     * @var Logger Logger provided by Slim
     */
    protected $logger;

    /**
     * @var Capsule Eloquent database connection
     */
    protected $DB;

    public function __construct(Container $container)
    {
        $this->app = $container;
        $this->DB = $container->get('capsule');
        $this->logger = $container->get('logger');
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return Capsule
     */
    public function getDB()
    {
        return $this->DB;
    }
}