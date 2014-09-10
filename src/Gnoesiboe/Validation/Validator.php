<?php

namespace Gnoesiboe\Validation;

/**
 * Class Validator
 */
abstract class Validator
{

    /**
     * @var ValidationRuleSetInterface
     */
    private $validatorSet;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Configures this validator
     */
    protected function configure()
    {
        $this->defineValidationRuleSet();
    }

    /**
     * Defines the business rules set to be applied on the input that
     * validate() is called with..
     */
    protected function defineValidationRuleSet()
    {
        $validatorSet = $this->configureValidationRuleSet();

        if (($validatorSet instanceof ValidationRuleSetInterface) === false) {
            throw new \UnexpectedValueException('configureValidatorSet should return an implementation of Gnoesiboe\Validation\ValidationRuleSetInterface');
        }

        $this->validatorSet = $validatorSet;
    }

    /**
     * @return ValidationRuleSetInterface
     */
    abstract protected function configureValidationRuleSet();

    /**
     * @param array $input
     *
     * @return array
     *
     * @throws ValidatorException
     */
    public function validate(array $input)
    {
        $input = $this->normalizeInput($input);

        $onFailure = function (array $input, \Illuminate\Validation\Validator $validator) {
            throw new ValidatorException(\Lang::get('validator.fails'), $validator->getMessageBag(), $input);
        };

        $onSuccess = function (array $input) {
            return $input;
        };

        return $this->validatorSet->apply($input, $onFailure, $onSuccess);
    }

    /**
     * @param array $input
     *
     * @return array
     */
    abstract protected function normalizeInput(array $input);

    /**
     * @param array $parameters
     *
     * @return static
     */
    public static function createInstance(array $parameters = array())
    {
        // Using App::make to automatically supply any validator dependencies from the ioc container

        return \App::make(get_called_class(), $parameters);
    }

    /**
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }
}
