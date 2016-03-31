<?php
namespace SkeletonAPI\Lib;

use Exception;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Custom error handler that takes care of logging and switches behavior based on environment
 *
 * In development, the great flip/whoops package is used to handle exceptions and display relevant information to the
 * developer. In production the error is logged to the log file and the application returns a generic 500 error so as to
 * not expose any information to any malicious actors. Heavily based on the Slim\Handlers\Error class.
 *
 * @package SkeletonAPI\Lib
 * @author  Gurpreet Atwal
 * @license MIT
 */
class ErrorHandler
{
    use utilTrait;

    protected $isLoggingEnabled;
    protected $isDevelopment;

    /** @var  LoggerInterface */
    protected $logger;
    /** @var  ServerRequestInterface */
    protected $request;
    /** @var  ResponseInterface */
    protected $response;
    /** @var  Exception */
    protected $e;

    protected $devHandlers = [
        'NestedValidationException' => 'handleNestedValidationException',
        'QueryException' => 'handleQueryException'
    ];

    protected $productionHandlers = [];

    protected $logLevels = [
        'NestedValidationException' => 'notice',
        'QueryException' => 'critical'
    ];

    /**
     * ErrorHandler constructor.
     * @param LoggerInterface $logger
     * @param bool $loggingEnabled
     * @param bool $showDetails if true, the application returns detailed information about the exception
     */
    public function __construct(LoggerInterface $logger, $loggingEnabled = true, $showDetails = false)
    {
        $this->logger = $logger;
        $this->isDevelopment = (bool)$showDetails;
        $this->isLoggingEnabled = (bool)$loggingEnabled;

        //@todo remove this, only here because implementation is not complete yet
        $this->isDevelopment = false;
    }

    /**
     * Store the request, response and exception as parameters of the instance and call the relevant error handler
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Exception $e
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $e)
    {
        $this->request = $request;
        $this->response = $response;
        $this->e = $e;

        if ($this->isLoggingEnabled) {
            $this->log();
        }

        return $this->handle();
    }

    /**
     * @return ResponseInterface
     */
    protected function handle() {
        $handlers = $this->isDevelopment ? $this->devHandlers :  $this->productionHandlers;
        $class = get_class($this->e);

        if (array_key_exists($class, $handlers)) {
            $handler = $handlers[$class];
            return call_user_func($handler);
        }

        if ($this->isDevelopment) {
            return $this->handleDevError();
        }
        return $this->response->withStatus(500);
    }

    /**
     * Logs the error
     */
    protected function log() {
        $body = $this->getParsedBodySafe();
        $class = get_class($this->e);
        $level = array_key_exists($class, $this->logLevels) ? $this->logLevels[$class] : 'alert';
        $this->logger->log($level, "{$class} {$this->e->getMessage()}", $body);
    }

    private function handleNestedValidationException()
    {
        /** @var NestedValidationException $e */
        $e = $this->e;
        $messages = $this->formatMessages($e);
        return $this->response->withJson($e->getFullMessage(), 400);
    }

    private function getParsedBodySafe()
    {
        try {
            $body = $this->request->getParsedBody();
        } catch (Exception $e) {
            $body = ["Body could not be parsed {$e->getMessage()}"];
        } finally {
            return $body;
        }
    }
}