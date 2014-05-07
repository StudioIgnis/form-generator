<?php

namespace StudioIgnis\Form;

use Illuminate\Support\Contracts\RenderableInterface;
use Illuminate\View\Environment as ViewEnvironment;

class Element implements RenderableInterface
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $html;

    /**
     * @var string
     */
    private $template;

    /**
     * @var \Illuminate\View\View
     */
    private $view;

    /**
     * @var string
     */
    private $viewPackage;

    public function __construct(ViewEnvironment $view, $type = null, $name = null, $label = null, $html = null, $template = null, $viewPackage = '')
    {
        $this->view = $view;
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->html = $html;
        $this->template = $template;
        $this->viewPackage = $viewPackage;
    }

    /**
     * Return a new element instance
     *
     * @param $type
     * @param $name
     * @param $label
     * @param $html
     * @param $template
     * @param $viewPackage
     * @return static
     */
    public function make($type, $name, $label, $html, $template, $viewPackage)
    {
        return new static($this->view, $type, $name, $label, $html, $template, $viewPackage);
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        if (!preg_match('/^(__input\:|form\.)/', $this->name))
        {
            if ($this->findTemplate($this->type, $this->name))
            {
                $data = [
                    'name' => $this->name,
                    'label' => $this->label,
                    'html' => $this->html,
                ];

                return $this->view->make($this->template, $data);
            }
        }

        return $this->getHtml();
    }

    protected function findTemplate($type, $name)
    {
        if ($template = $this->getTemplate()) return $template;

        $finder = $this->view->getFinder();
        $packages = ['form-generator', $this->getViewPackage(), ''];
        $tries = [$name."_$type", $type, 'input'];

        foreach ($packages as $package)
        {
            if ($package) $package .= '::';

            foreach ($tries as $view)
            {
                try {
                    $finder->find($package.$view);
                } catch(\InvalidArgumentException $ex) {
                    continue;
                }

                $this->template = $package.$view;
                break 2;
            }
        }


        return $this->getTemplate();
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Append html to the element
     *
     * @param string $html
     */
    public function appendHtml($html)
    {
        $this->html .= $html;
    }

    /**
     * Prepend html to the element
     *
     * @param string $html
     */
    public function prependHtml($html)
    {
        $this->html = $html . $this->html;
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewPackage()
    {
        return $this->viewPackage;
    }

    /**
     * @param string $viewPackage
     */
    public function setViewPackage($viewPackage)
    {
        $this->viewPackage = $viewPackage;
    }
}
