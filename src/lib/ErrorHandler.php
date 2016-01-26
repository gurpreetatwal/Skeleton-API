<?php
namespace SkeletonAPI\lib;

use Exception;
use Illuminate\Database\QueryException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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

    protected $development;

    /**
     * Constructor
     *
     * @param boolean $development Set to true to display full details
     */
    public function __construct($development = false)
    {
        $this->development = (bool)$development;
    }

    /**
     * Invoke error handler
     *
     * @param ServerRequestInterface $request The most recent Request object
     * @param ResponseInterface $response The most recent Response object
     * @param Exception $e The caught Exception object
     * @todo  Make it actually work
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $e)
    {
        /** @var \Monolog\Logger $logger */
        $logger = $c->get('logger');
        $body = $request->getParsedBody();
        if ($e instanceof NestedValidationException) {
            $logger->addNotice("ValidationException {$e->getMainMessage()}", $body);
            $messages = $this->formatMessages($e);
            return $response->withJson($e->getFullMessage(), 400);
        } elseif ($e instanceof QueryException) {
            $logger->addCritical("QueryException {$e->getMessage()}", $body);
        } else {
            $logger->addAlert("Exception {$e->getMessage()}", $body);
        }
        return $response->withStatus(500);
    }
}