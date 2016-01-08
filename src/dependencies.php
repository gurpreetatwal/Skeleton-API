<?php
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\QueryException;
use Monolog\Logger;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Container;
use Slim\Handlers\Error;
use Slim\Http\Request;
use Slim\Http\Response;

$container = $app->getContainer();

// Monolog
$container['logger'] = function (Container $c) {
    $settings = $c['settings']['logger'];
    $logger = new Logger($c['app-name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Logger::DEBUG));
    return $logger;
};

// Error handler
// First attempt at building an error handler
// TODO clean this up and move into its own class
// TODO check environment
$container['errorHandler'] = function (Container $c) {
    return function (Request $request, Response $response, Exception $e) use ($c) {

        $logger = $c->get('logger');
        $body = $request->getParsedBody();
        if ($e instanceof NestedValidationException) {
            $logger->addNotice("ValidationException {$e->getMainMessage()}", $body);

            // needs UtilTrait
            // $messages = $this->formatMessages($e);

            return $response->withJson($e->getFullMessage(), 400);
        } elseif ($e instanceof QueryException) {
            $logger->addCritical("QueryException {$e->getMessage()}", $body);
            return $response->withStatus(500);
        } else {
            $logger->addAlert("Exception {$e->getMessage()}", $body);
            $errorHandler = new Error($c->get('settings')['displayErrorDetails']);
            return $errorHandler($request, $response, $e);
        }
    };
};
$container['capsule'] = function (Container $c) {
    $settings = array_merge(
        $c['settings']['database'],
        parse_ini_file(__DIR__ . '/../environment.ini', true)['database']
    );
    $capsule = new Capsule();
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};
$container['capsule'];
