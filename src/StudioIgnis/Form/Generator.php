<?php namespace StudioIgnis\Form;

use Illuminate\Html\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Contracts\RenderableInterface;

/**
 * Class Generator
 * @package Form
 *
 * @method Generator text(string $name, string $label = null, string $value = null, mixed $rules = null, array $options = [], $template = null)
 * @method Generator email(string $name, string $label = null, string $value = null, mixed $rules = null, array $options = [], $template = null)
 * @method Generator url(string $name, string $label = null, string $value = null, mixed $rules = null, array $options = [], $template = null)
 *
 * @method Generator checkbox(string $name, string $label = null, string $value = 1, bool $checked = null, mixed $rules = '', array $options = [], $template = null)
 * @method Generator radio(string $name, string $label = null, string $value = null, bool $checked = null, mixed $rules = '', array $options = [], $template = null)
 *
 * @method Generator button(string $name, string $value = null, array $options = [], $template = null)
 * @method Generator submit(string $name, string $value = null, array $options = [], $template = null)
 * @method Generator reset(string $name, $value, array $attributes = [], $template = null)
 */
class Generator implements RenderableInterface
{

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var Element
     */
    private $element;

    /**
     * @var \Illuminate\Html\FormBuilder
     */
    private $form;

    /**
     * @var \Illuminate\Http\Request
     */
    private $input;

    /**
     * @var Element[]
     */
    protected $elements = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    private $buttonTypes = ['reset', 'submit', 'button'];

    /**
     * @var array
     */
    private $checkableTypes = ['checkbox', 'radio'];

    /**
     * The key of the last element added
     * @var string
     */
    private $lastKey;

    public function __construct(Validator $validator, Element $element, FormBuilder $form, Request $input)
    {
        $this->validator = $validator;
        $this->element = $element;
        $this->form = $form;
        $this->input = $input;

        $this->init();
    }

    /**
     * Initialize the form
     */
    protected function init() {}

    /**
     * Open up a new HTML form
     *
     * @param array $options
     * @return $this
     */
    public function open(array $options = [])
    {
        return $this->addElement('form', 'form.open', null, $this->getForm()->open($options));
    }

    /**
     * Create a new model based form builder
     *
     * @param $model
     * @param array $options
     * @return $this
     */
    public function model($model, array $options = [])
    {
        return $this->addElement('form', 'form.open', null, $this->getForm()->model($model, $options));
    }

    /**
     * Close the current form
     *
     * @return $this
     */
    public function close()
    {
        return $this->addElement('form', 'form.close', null, $this->getForm()->close());
    }



    /**
     * Add inputs magically
     *
     * @param $name
     * @param $arguments
     * @return $this
     */
    function __call($name, $arguments)
    {
        if ($this->isButton($name))
        {
            $element_name = array_shift($arguments);
            $html = call_user_func_array([$this->getForm(), $name], $arguments);

            return $this->addElement('button', $element_name, null, $html, null, @$arguments[2]);
        }

        $method = !$this->isCheckable($name) ? 'input' : 'checkable';

        array_unshift($arguments, $name);

        return call_user_func_array([$this, $method], $arguments);
    }

    public function hidden($name, $value = null, $rules = null, array $options = [], $template = null)
    {
        return $this->input(__FUNCTION__, $name, null, $value, $rules, $options, $template);
    }

    public function password($name, $label = null, $rules = null, array $options = [], $template = null)
    {
        return $this->input(__FUNCTION__, $name, $label, null, $rules, $options, $template);
    }

    public function file($name, $label = null, $rules = null, array $options = [], $template = null)
    {
        return $this->input(__FUNCTION__, $name, $label, null, $rules, $options, $template);
    }

    public function image($url, $name = null, $label = null, $rules = null, array $attributes = [], $template = null)
    {
        $html = $this->getForm()->image($url, $name, $attributes);

        return $this->addElement('image', $name, $label, $html, $rules, $template);
    }

    public function checkable($type, $name, $label = null, $value = null, $checked = null, $rules = null, array $options = [], $template = null)
    {
        $method = $type;

        $html = $this->getForm()->$method($name, $value, $checked, $options);

        return $this->addElement($type, $name, $label, $html, $rules, $template);
    }



    public function textarea($name, $label = null, $value = null, $rules = null, array $options = [], $template = null)
    {
        $html = $this->getForm()->textarea($name, $value, $options);

        return $this->addElement('textarea', $name, $label, $html, $rules, $template);
    }

    public function select($name, $label = null, array $list = [], $selected = null, $rules = null, array $options = [], $template = null)
    {
        $html = $this->getForm()->select($name, $list, $selected, $options);

        return $this->addElement('select', $name, $label, $html, $rules, $template);
    }

    public function selectRange($name, $begin, $end, $label = null, $selected = null, $rules = null, array $options = [], $template = null)
    {
        $html = $this->getForm()->selectRange($name, $begin, $end, $selected, $options);

        return $this->addElement('select', $name, $label, $html, $rules, $template);
    }

    public function selectYear($name, $begin, $end, $label = null, $selected = null, $rules = null, array $options = [], $template = null)
    {
        return $this->selectRange('select', $name, $begin, $end, $label, $selected, $rules, $options, $template);
    }

    public function selectMonth($name, $label = null, $selected = null, $rules = null, array $options = [], $template = null)
    {
        $html = $this->getForm()->selectMonth($name, $selected, $options);

        return $this->addElement('select', $name, $label, $html, $rules, $template);
    }

    public function input($type, $name, $label = null, $value = null, $rules = '', array $options = [], $template = null)
    {
        $html = $this->getForm()->input($type, $name, $value, $options);

        return $this->addElement($type, $name, $label, $html, $rules, $template);
    }

    protected function addElement($type, $name, $label, $html, $rules = null, $template = null)
    {
        $key = $name ?: '__input:'.microtime();
        $this->lastKey = $key;

        $this->elements[$key] = $this->element->make($type, $name, $label, $html, $template, $this->getViewPackage());

        if (!empty($rules))
        {
            $this->rules[$name] = $rules;
        }

        return $this;
    }

    /**
     * Append custom html to the last input element
     *
     * @param $html
     * @return $this
     */
    public function appendHtml($html)
    {
        /** @var $element Element */
        $element = $this->elements[$this->lastKey];
        $element->appendHtml($html);

        return $this;
    }

    /**
     * Prepend custom html to the last input element
     *
     * @param $html
     * @return $this
     */
    public function prependHtml($html)
    {
        /** @var $element Element */
        $element = $this->elements[$this->lastKey];
        $element->prependHtml($html);

        return $this;
    }

    /**
     * Get a form element
     *
     * @param $name
     * @param bool $render
     * @return string|Element
     */
    public function get($name, $render = true)
    {
        if ($this->has($name))
        {
            /** @var $element Element */
            $element = $this->elements[$name];

            if ($render) return $element->render();

            return $element;
        }

    }

    /**
     * Check if it has a form element by name
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->elements[$name]);
    }

    public function render()
    {
        $html = '';
        foreach ($this->elements as $element)
        {
            $html .= $element->render();
        }

        return $html;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function validate($formData)
    {
        $inputKeys = array_filter(array_keys($this->elements), function($name)
        {
            return !preg_match('/^__input\:/', $name);
        });

        $this->input->only($inputKeys);

        $this->validator->setRules($this->rules);

        return $this->validator->validate($formData);
    }

    protected function isCheckable($type)
    {
        return in_array($type, $this->checkableTypes);
    }

    protected function isButton($type)
    {
        return in_array($type, $this->buttonTypes);
    }

    /**
     * @return \Illuminate\Html\FormBuilder
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * Returns the package to be used to fetch the views
     *
     * It defaults to '', so it will try to fetch views from the app.
     * Override this method to return a custom package.
     *
     * @return string
     */
    public function getViewPackage()
    {
        return '';
    }

    /**
     * Get the form validator
     * @return \StudioIgnis\Form\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }
}
