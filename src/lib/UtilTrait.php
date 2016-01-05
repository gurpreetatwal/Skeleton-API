<?php
namespace SkeletonAPI\lib;

use Respect\Validation\Exceptions\NestedValidationException;

/**
 * Utility functions for use across the API
 *
 * Class UtilTrait
 * @package SkeletonAPI\lib
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
     * @param NestedValidationException $exceptions
     * @return array
     */
    public function formatMessages(NestedValidationException $exceptions)
    {
        $messages = [$exceptions->getMessage()];
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
}