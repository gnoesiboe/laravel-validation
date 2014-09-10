<?php

namespace Gnoesiboe\Validation;

/**
 * Class ValidatorSet
 *
 * @todo add merge functionality to merge multiple validation rule sets
 */
class ValidationRuleSet implements ValidationRuleSetInterface
{

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $customErrorMessages = array();

    /**
     * @var array
     */
    protected $customAttributes = array();

    /**
     * @var array
     */
    protected $conditionalRules = array();

    /**
     * @var ValidationRuleSetInterface
     */
    protected $chained = null;

    /**
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->setRules($rules);
    }

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->clearRules();

        foreach ($rules as $key => $value) {
            $this->setRule($key, $value);
        }

        return $this;
    }

    protected function clearRules()
    {
        $this->rules = array();
    }

    /**
     * @param string       $key
     * @param string|array $value
     *
     * @return $this
     */
    public function setRule($key, $value)
    {
        if (is_string($key) === false) {
            throw new \UnexpectedValueException('Key should be of type string');
        }

        if (is_string($value) === false && is_array($value) === false) {
            throw new \UnexpectedValueException('Value should either be an array or a string');
        }

        $this->rules[$key] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $messages
     *
     * @return $this
     */
    public function setCustomErrorMessages(array $messages)
    {
        $this->customErrorMessages = $messages;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomErrorMessages()
    {
        return $this->customErrorMessages;
    }

    /**
     * @param array $customAttributes
     *
     * @return $this
     */
    public function setCustomAttributes(array $customAttributes)
    {
        $this->customAttributes = $customAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->customAttributes;
    }

    /**
     * @param string       $key
     * @param string|array $rule
     * @param callable     $callback
     *
     * @return $this
     */
    public function setConditionalRule($key, $rule, \Closure $callback)
    {
        $this->conditionalRules[$key] = array(
            'rule'     => $rule,
            'callback' => $callback
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getConditionalRules()
    {
        return $this->conditionalRules;
    }

    /**
     * @param ValidationRuleSetInterface $validatorSet
     *
     * @return $this
     */
    public function chain(ValidationRuleSetInterface $validatorSet)
    {
        $this->chained = $validatorSet;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasChained()
    {
        return $this->chained instanceof ValidationRuleSetInterface;
    }

    /**
     * @return ValidationRuleSetInterface|null
     */
    public function getChained()
    {
        return $this->chained;
    }

    /**
     * @param array    $input
     * @param callable $onFailure
     * @param callable $onSuccess
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function apply(array $input, \Closure $onFailure, \Closure $onSuccess)
    {
        $validator = $this->createValidator($input);

        if ($validator->fails() === true) {
            return $onFailure($input, $validator);
        }

        // Check if another validator set was chained to our current validator set. If it is
        // apply it first before triggering the success callback

        if ($this->hasChained() === true) {
            $this->getChained()->apply($input, $onFailure, $onSuccess);
        }

        return $onSuccess($input);
    }

    /**
     * @param array $input
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function createValidator(array $input)
    {
        $validator = \Validator::make($input, $this->rules, $this->customErrorMessages, $this->customAttributes);

        foreach ($this->getConditionalRules() as $key => $data) {
            $validator->sometimes($key, $data['rule'], $data['callback']);
        }

        return $validator;
    }
}
