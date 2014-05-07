<?php namespace StudioIgnis\Form\Exception;

use Illuminate\Support\MessageBag;

class ValidationError extends \DomainException
{
    /**
     * @var \Illuminate\Support\MessageBag
     */
    private $errors;

    /**
     * @param string $message
     * @param MessageBag $errors
     */
    public function __construct($message, MessageBag $errors)
    {
        $this->errors = $errors;

        parent::__construct($message);
    }

    /**
     * Get validation errors
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }
} 
