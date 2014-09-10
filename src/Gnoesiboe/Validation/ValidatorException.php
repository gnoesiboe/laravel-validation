<?php

namespace Gnoesiboe\Validation;

use Exception;
use Illuminate\Support\MessageBag;

/**
 * Class ValidatorException
 */
class ValidatorException extends \Exception
{

    /**
     * @var MessageBag
     */
    protected $messageBag;

    /**
     * @var array
     */
    protected $input;

    /**
     * @param string     $message
     * @param MessageBag $messageBag
     * @param array      $input
     */
    public function __construct($message = '', MessageBag $messageBag, array $input)
    {
        parent::__construct($message);

        $this->messageBag = $messageBag;
        $this->input = $input;
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return MessageBag
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }
}
