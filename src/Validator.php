<?php

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
class Validator extends \Respect\Validation\Validator
{
    public function __construct(array $rules)
    {
        foreach ($rules as $field => $rule) {
            $this->addAttribute($field, $rule);
        }
        parent::__construct();
    }

    /**
     * @param $field
     * @param $rule
     */
    public function addAttribute($field, $rule)
    {
        $attributeRule = new AllOf();
        $attributeRule->addRules($this->createRuleArray($rule));
        $name = $field;

        // Get name if one was provided
        if (($colon = strpos($field, ':')) !== false) {
            $name = substr($field, $colon + 1);
            $field = substr($field, 0, $colon);
        }

        $attribute = new Attribute($field, $attributeRule);
        $attribute->setName($name);
        $this->addRule($attribute);
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

        // Get function args if provided
        if (($paren = strpos($rule, '(')) !== false) {
            $args = substr($rule, $paren + 1, strlen($rule) - $paren - 2);
            $rule = substr($rule, 0, $paren);
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
            $arg = trim($arg);
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