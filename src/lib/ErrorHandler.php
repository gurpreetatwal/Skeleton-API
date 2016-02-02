<?php
namespace SkeletonAPI\lib;

use Exception;
use Illuminate\Database\QueryException;
use MongoDB\Driver\Server;
use Monolog\Logger;
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
 * @package SkeletonAPI\lib
 * @author  Gurpreet Atwal
 * @license MIT
 */
class ErrorHandler
{
    use utilTrait;

    protected $showDetails;

    /** @var  LoggerInterface */
    protected $logger;
    /** @var  ServerRequestInterface */
    protected $request;
    /** @var  ResponseInterface */
    protected $response;
    /** @var  Exception */
    protected $e;

    protected $handlers = [
        'NestedValidationException' => 'handleNestedValidationException',
        'QueryException' => 'handleQueryException'
    ];

    /**
     * ErrorHandler constructor.
     * @param LoggerInterface $logger
     * @param bool $showDetails if true, the application returns detailed information about the exception
     */
    public function __construct(LoggerInterface $logger, $showDetails = false)
    {
        $this->logger = $logger;
        $this->showDetails = (bool)$showDetails;

        //@todo remove this, only here because implementation is not complete yet
        $this->showDetails = false;
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

        if ($this->showDetails) {
            return $this->showDetailed();
        }
        return $this->showSimple();
    }

    /**
     * @return ResponseInterface
     */
    protected function showSimple()
    {
        return $this->response->withStatus(500);
    }

    protected function showDetailed()
    {
        $body = $this->getParsedBodySafe();
        $class = get_class($this->e);
        $handler = $this-> handlers[$class];
        call_user_func($handler, $body);
        return $this->response->withStatus(500);
    }

    private function handleNestedValidationException($body)
    {
        /** @var NestedValidationException $e */
        $e = $this->e;
        $this->logger->notice("ValidationException {$e->getMainMessage()}", $body);
        $messages = $this->formatMessages($e);
        return $this->response->withJson($e->getFullMessage(), 400);
    }


    private function handleQueryException($body)
    {
        /** @var QueryException $e */
        $e = $this->e;
        $this->logger->critical("QueryException {$e->getMessage()}", $body);
    }

    private function handleGeneralException($body)
    {
        $this->logger->alert("Exception {$this->e->getMessage()}", $body);
    }

    private function getParsedBodySafe()
    {
        try {
            $body = $this->request->getParsedBody();
        } catch (Exception $e) {
            $body = "Body could not be parsed {$e->getMessage()}";
        } finally {
            return $body;
        }
    }
}