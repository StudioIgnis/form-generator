<?php

namespace spec\StudioIgnis\Form;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mockery as m;

class ElementSpec extends ObjectBehavior
{
    private $view;

    private $viewFinder;

    function let()
    {
        $this->view = m::mock('Illuminate\View\Environment');
        $this->viewFinder = m::mock('Illuminate\View\FileViewFinder');
        $this->view->shouldReceive('getFinder')->andReturn($this->viewFinder);

        $this->beConstructedWith($this->view, 'type', 'foo', 'Foo', '<input>');
    }

    function letgo()
    {
        m::close();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Illuminate\Support\Contracts\RenderableInterface');
        $this->shouldHaveType('StudioIgnis\Form\Element');
    }

    function it_should_render_an_input()
    {
        $this->viewFinder->shouldReceive('find')->andThrow('InvalidArgumentException');

        $this->render()->shouldBe('<input>');
    }

    function it_should_render_an_input_with_a_view()
    {
        $this->view
            ->shouldReceive('make')
            ->with('foo_template', [
                'name' => 'foo',
                'label' => 'Foo',
                'html' => '<input>',
            ])
            ->andReturn('the view contents');

        $this->setTemplate('foo_template')
            ->render()->shouldBe('the view contents');
    }

    function it_should_automagically_find_the_correct_template_view()
    {
        $this->viewFinder->shouldReceive('find')->andReturn('/path/to/view.blade.php');
        $this->view
            ->shouldReceive('make')
            ->with(
                'form-generator::foo_type', // {name}_{type}
                [
                    'name' => 'foo',
                    'label' => 'Foo',
                    'html' => '<input>',
                ]
            )
            ->andReturn('the view contents');

        $this->render()->shouldBe('the view contents');
    }

    function it_can_make_another_element()
    {
        $element = $this->make('type', 'bar', 'Bar', '<input>', 'template', '');
        $element->shouldBeAnInstanceOf('StudioIgnis\Form\Element');
        $element->getName()->shouldBe('bar');
        $element->getLabel()->shouldBe('Bar');
        $element->getHtml()->shouldBe('<input>');
        $element->getTemplate()->shouldBe('template');
    }

    function it_can_append_html()
    {
        $this->viewFinder->shouldReceive('find')->andThrow('InvalidArgumentException');

        $this->appendHtml('foo');
        $this->render()->shouldReturn('<input>foo');
    }

    function it_can_prepend_html()
    {
        $this->viewFinder->shouldReceive('find')->andThrow('InvalidArgumentException');

        $this->prependHtml('foo');
        $this->render()->shouldReturn('foo<input>');
    }
}
