<?php namespace StudioIgnis\Form;

use Illuminate\Validation\Factory;
use StudioIgnis\Form\Exception\ValidationError;

class Validator
{
    /**
     * @var array
     */
    protected $rules;

    /**
     * @var \Illuminate\Validation\Factory
     */
    private $validator;

    /**
     * @var \Illuminate\Validation\Validator
     */
    private $validation;

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $formData)
    {
        $rules = $this->getRules();

        if (empty($rules))
        {
            throw new \RuntimeException('There are no rules defined');
        }

        $this->validation = $this->validator->make($formData, $this->getRules());

        if ($this->validation->fails())
        {
            throw new ValidationError('Validation failed', $this->getErrors());
        }

        return true;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     * @return $this
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        if ($this->validation)
        {
            return $this->validation->errors();
        }
    }
}
