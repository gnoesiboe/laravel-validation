<?php

namespace Gnoesiboe\Validation;

/**
 * Interface ValidatorSetInterface
 */
interface ValidationRuleSetInterface
{

    /**
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules);

    /**
     * @param string $key
     * @param string|array $value
     *
     * @return $this
     */
    public function setRule($key, $value);

    /**
     * @return array
     */
    public function getRules();

    /**
     * @param array $messages
     *
     * @return $this
     */
    public function setCustomErrorMessages(array $messages);

    /**
     * @return array
     */
    public function getCustomErrorMessages();

    /**
     * @param array $customAttributes
     *
     * @return $this
     */
    public function setCustomAttributes(array $customAttributes);

    /**
     * @return array
     */
    public function getCustomAttributes();

    /**
     * @param string $key
     * @param string|array $rule
     * @param callable $callback
     *
     * @return $this
     */
    public function setConditionalRule($key, $rule, \Closure $callback);

    /**
     * @return array
     */
    public function getConditionalRules();

    /**
     * @param ValidationRuleSetInterface $validatorSet
     *
     * @return $this
     */
    public function chain(ValidationRuleSetInterface $validatorSet);

    /**
     * @return bool
     */
    public function hasChained();

    /**
     * @return ValidationRuleSetInterface|null
     */
    public function getChained();

    /**
     * @param array    $input
     * @param callable $onFailure
     * @param callable $onSuccess
     *
     * @return mixed
     *
     * @throws ValidatorException
     */
    public function apply(array $input, \Closure $onFailure, \Closure $onSuccess);
}
