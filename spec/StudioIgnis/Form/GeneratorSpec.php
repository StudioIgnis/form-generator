<?php

namespace spec\StudioIgnis\Form;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Mockery as m;
use StudioIgnis\Form\Element;
use StudioIgnis\Form\Validator;

class GeneratorSpec extends ObjectBehavior
{
    private $form;

    private $request;

    function let(Validator $validator)
    {
        $this->form = m::mock('Illuminate\Html\FormBuilder');
        $this->request = m::mock('Illuminate\Http\Request');
        $view = m::mock('Illuminate\View\Environment');
        $view->shouldReceive('make')->andReturn('the view contents');
        $viewFinder = m::mock('Illuminate\View\FileViewFinder');
        $viewFinder->shouldReceive('find')->andThrow('InvalidArgumentException');
        $view->shouldReceive('getFinder')->andReturn($viewFinder);

        $this->beConstructedWith($validator, new Element($view), $this->form, $this->request);
    }

    function let_go()
    {
        m::close();
    }



    function it_is_initializable()
    {
        $this->shouldHaveType('StudioIgnis\Form\Generator');
    }

    function it_can_create_a_regular_input()
    {
        $this->form->shouldReceive('input')->andReturn('<input type="text">');

        $this
            ->text('foo', 'Foo bar', null, 'required')
            ->get('foo')
            ->shouldBe('<input type="text">');
    }
    
    function it_can_create_a_special_input()
    {
        $this->form->shouldReceive('checkbox')->andReturn('<input type="checkbox">');

        $this
            ->checkbox('foo', 'Foo bar')
            ->get('foo')
            ->shouldBe('<input type="checkbox">');
    }

    function it_can_append_html()
    {
        $this->form->shouldReceive('input')->andReturn('<input type="text">');

        $this
            ->text('foo', 'Foo bar', null, 'required')
            ->appendHtml('<foo>')
            ->get('foo')
            ->shouldBe('<input type="text"><foo>');
    }

    function it_can_prepend_html()
    {
        $this->form->shouldReceive('input')->andReturn('<input type="text">');

        $this
            ->text('foo', 'Foo bar', null, 'required')
            ->prependHtml('<foo>')
            ->get('foo')
            ->shouldBe('<foo><input type="text">');
    }

    function it_can_return_a_raw_input_element()
    {
        $this->form->shouldReceive('checkbox')->andReturn('<input type="checkbox">');

        $element = $this
            ->checkbox('foo', 'Foo bar')
            ->get('foo', false);

        $element->shouldBeAnInstanceOf('StudioIgnis\Form\Element');
        $element->getName()->shouldBe('foo');
        $element->getLabel()->shouldBe('Foo bar');
        $element->getHtml()->shouldBe('<input type="checkbox">');
        $element->getTemplate()->shouldBeNull();
    }

    function it_can_create_a_form()
    {
        $this->form->shouldReceive('open')->andReturn('<form>');
        $this->form->shouldReceive('input')->andReturn(
            '<input type="email">',
            '<input type="password">'
        );
        $this->form->shouldReceive('close')->andReturn('</form>');

        // Prepare form
        $this->open(['url' => 'foo/bar', 'method' => 'post']);
        $this->email('email', 'E-mail', '', 'required|email');
        $this->password('password', 'Password', 'required');
        $this->close();

        // Render the form
        $this->render()->shouldBe(
            '<form>'.
            '<input type="email">'.
            '<input type="password">'.
            '</form>'
        );
    }

    function it_can_validate_the_form()
    {
        $formData = ['foo' => 'bar'];

        $this->request->shouldReceive('only')->andReturn($formData);

        $this->validate($formData);
    }
}
