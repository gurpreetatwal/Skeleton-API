<?php

namespace SkeletonAPI\Lib;

use Respect\Validation\Rules\AllOf;
use Respect\Validation\Rules\Key;

/**
 * Extension of Respect\Validation that allows for the the validation rules to be specified by strings
 *
 * This class allows for an creation of a validator by specifying the rules as strings rather than chained methods on an
 * instance of the validator. This is fairly useful when trying to validate a model as the rules can be stored in a
 * property of the model and then used to create a validator whenever one is needed. The basic format of a rule is a
 * key, value pair where the key is the name of the field that needs to be validated and the value is a list of
 * validation rules separated by the pipe | character. If you wish to specify a name you can provide it as a part of the
 * key by inserting a colon followed by the name, do not use SetName. Most of rules provided in the Respect\Validation
 * package, other than the rules that act on other rules such as 'Group Validators' or Not/Optional should work.
 *
 * Example rules array:
 * $rules = [
 *      'email' => 'StringType|Email',
 *      'password' => 'StringType|Length(8,null)',
 *      'question:Security Question' => 'StringType|Length(8,null)',
 *      'answer:Security Answer' => 'StringType|Length(8,null)',
 * ]
 * @package     SkeletonAPI
 * @author      Gurpreet Atwal
 * @license     MIT
 * @see         https://github.com/Respect/Validation/blob/master/docs/VALIDATORS.md List of validation rules that can be used
 * @see         https://github.com/Respect/Validation/blob/master/docs/README.md     Usage
 * @todo        Write tests
 * @todo        Add support for class validation
 * @todo        Add support for rules that act on other rules
 */
class Validator extends \Respect\Validation\Validator
{
    /**
     * Validator constructor.
     * @param array $rules Array where each key corresponds to one attribute of the model and the value is a list of
     *                     validation rules supported by Respect\Validation separated by the pipe ("|") character.
     */
    public function __construct(array $rules)
    {
        foreach ($rules as $field => $rule) {
            $this->addAttribute($field, $rule);
        }
        parent::__construct();
    }

    /**
     * Adds a single attribute to this validator.
     * @param string $field The attribute these rules apply to. Optionally specify a name for the rules by adding a
     *                      colon(":") followed by the name. E.g. "question:Security Question"
     * @param string $rule List of validators from the Respect\Validation package separated by the pipe ("|") character.
     */
    public function addAttribute($field, $rule)
    {
        $attributeRule = new AllOf();
        $attributeRule->addRules($this->createRulesArray($rule));
        $name = $field;

        // Get name if one was provided
        if (($colon = strpos($field, ':')) !== false) {
            $name = substr($field, $colon + 1);
            $field = substr($field, 0, $colon);
        }

        $attribute = new Key($field, $attributeRule);
        $attribute->setName($name);
        $this->addRule($attribute);
    }

    /**
     * Takes a list of validation rules and returns them as an array of instantiated classes.
     * @param  string $_rules List of validators from the Respect\Validation package separated by the pipe ("|") character.
     * @return array         Array of validation rules
     */
    private function createRulesArray($_rules)
    {
        $rules = [];
        $_rules = explode("|", $_rules);
        foreach ($_rules as $rule) {
            array_push($rules, $this->createRule($rule));
        }
        return $rules;
    }

    /**
     * Takes a string that represents a validation rule and its arguments and returns it as a instantiated class.
     * @param  string $rule Name of the rule to create and its arguments in parenthesis. E.g. Length(8, null)
     * @return \Respect\Validation\Rules\AbstractRule
     */
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

    /**
     * Takes an array containing strings, trims ALL the values and converts numeric values to integers and 'null' to an
     * actual null value.
     * @param  array $_args An array of strings that need to be formatted
     * @return array Formatted arguments, contains strings, integers and null
     */
    private function formatArgs(array $_args)
    {
        $args = [];
        foreach ($_args as $arg) {
            $arg = trim($arg);

            if (strtolower($arg) === 'null') {
                $arg = null;
            } else if (is_numeric($arg)) {
                $arg = intval($arg);
            } //TODO: check if arg is rule, if so it should be instantiated

            array_push($args, $arg);
        }
        return $args;
    }
}