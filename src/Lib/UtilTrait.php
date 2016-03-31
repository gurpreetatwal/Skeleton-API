<?php
namespace SkeletonAPI\Lib;

use Firebase\JWT\JWT;
use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Utility functions for use across the API
 *
 * Class UtilTrait
 * @package SkeletonAPI\Lib
 * @author      Gurpreet AtwaSl
 * @license     MIT
 */
trait UtilTrait
{

    /**
     * Formats the messages returned by Respect Validator's NestedValidationException
     *
     * Instead of returning all the messages as a simple array it maps the message to the name of the input field so
     * that way it is easier to parse the messages when they're sent back to the front end.
     *
     * Heavily inspired by NestedValidationException->getAllMessages()
     * @todo make sure $input isn't null
     * @todo handle collision in keys
     * @param NestedValidationException $exceptions
     * @return array
     */
    public function formatMessages(NestedValidationException $exceptions)
    {
        $messages = [$exceptions->getFullMessage()];
        foreach ($exceptions as $exception) {
            $input = $exception->getParams()["input"];
            $message = $exception->getMessage();
            $messages[$input] = $message;
        }

        if (count($messages) > 1) {
            array_shift($messages);
        }

        return $messages;
    }


    /**
     * Uses Firebase JWT to encode a JWT for user authentication, merges the array based in with the base token defined
     * here
     *
     * NOTE: All data in the token is accessible by the user, do not store passwords or other secrets in the token
     * @todo move base jwt somewhere else (settings.php or environment.ini)
     * @param array $token Data that is merged in with the base JWT to
     * @return string       Encoded JWT
     */
    public function encodeJWT(array $token)
    {
        $secret = parse_ini_file(__DIR__ . '/../../environment.ini')['jwt-secret'];
        $token = array_merge(
            [
                "iss" => "https://api.website.com",
                "aud" => "https://website.com",
                "iat" => time(),
                "nbf" => time(),
            ],
            $token
        );

        return JWT::encode($token, $secret, 'HS512');
    }
}