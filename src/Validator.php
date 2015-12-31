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
        $fieldValidator->addRules($this->parse_rules($rule));
        $this->validator->addRule(
            new Attribute($field, $fieldValidator)
        );
    }

    private function parse_rules($rules)
    {
        $validators = [];
        $rules = explode("|", $rules);
        foreach ($rules as $rule) {
            $rule = trim($rule);
            $args = [];
            if (strpos($rule, '(')) {
                $args = substr($rule, strpos($rule, '(') + 1, strlen($rule) - strpos($rule, '(') - 2);
                $args = explode(',', $args);
                array_walk($args, array($this, "test"));
                $rule = substr($rule, 0, strpos($rule, '('));
            };
            $rule = 'Respect\\Validation\\Rules\\' . $rule;
            array_push($validators, new $rule(...$args));
        }
        return $validators;
    }

    function test(&$arg, $key)
    {
        if (is_int($arg)) {
            $arg = intval($arg);
        } else if (strtolower($arg) === 'null') {
            $arg = null;
        }
    }
}