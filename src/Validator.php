<?php
/**
 * Created by PhpStorm.
 * User: gurpr_000
 * Date: 12/31/2015
 * Time: 2:51 AM
 */

namespace SkeletonAPI;

use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\Attribute;

/**
 * Allow for using Siriusphp/validation-like syntax to create rules for the respect validation package
 * Still needs a good amount of work, this is more of a prototype than anything
 *
 * Rule Syntax
 *  "field" => "StringType|Length(1,15)|Email|"
 *
 * Class Validator
 * @package SkeletonAPI
 */
class Validator
{
    public $fields = [];
    public $rules = [];
    public $validator;

    public function __construct(array $rules)
    {
        $this->validator = new AllOf();

        foreach ($rules as $field => $rule) {
            $this->addField($field, $rule);
        }
    }

    public function addField($field, $rule)
    {
        $fieldValidator = new AllOf();
        $fieldValidator->addRules($this->createRuleArray($rule));
        $this->validator->addRule(
            new Attribute($field, $fieldValidator)
        );
    }

    private function createRuleArray($rules)
    {
        $validators = [];
        $rules = explode("|", $rules);
        foreach ($rules as $rule) {
            array_push($validators, $this->createRule($rule));
        }
        return $validators;
    }

    private function createRule($rule)
    {
        $args = [];
        $rule = trim($rule);
        $paren = strpos($rule, '(');
        if ($paren) {
            $rule = substr($rule, 0, $paren);
            $args = substr($rule, $paren + 1, strlen($rule) - $paren - 2);
            $args = explode(',', $args);
            $args = $this->formatArgs($args);
        };
        $rule = 'Respect\\Validation\\Rules\\' . $rule;
        return new $rule(...$args);
    }

    private function formatArgs($args)
    {
        $new_args = [];
        foreach ($args as $arg) {
            if (strtolower($arg) === 'null') {
                $arg = null;
            } else if (is_numeric($arg)) {
                $arg = intval($arg);
            }
            array_push($new_args, $arg);
        }
        return $new_args;
    }
}