<?php

namespace spec\StudioIgnis\Form;

use Mockery as m;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidatorSpec extends ObjectBehavior
{
    private $validator;

    private $validatorInstance;



    function let()
    {
        $this->validatorInstance = m::mock('Illuminate\Validation\Validator');

        $this->validator = m::mock('Illuminate\Validation\Factory');
        $this->validator->shouldReceive('make')->andReturn($this->validatorInstance);

        /** @noinspection PhpParamsInspection */
        $this->beConstructedWith($this->validator);
    }

    function let_go()
    {
        m::close();
    }



    function it_is_initializable()
    {
        $this->shouldHaveType('StudioIgnis\Form\Validator');
    }

    function it_can_validate_input()
    {
        // Validation should pass
        $this->validatorInstance->shouldReceive('fails')->andReturn(false);

        $this
            ->setRules(['the' => 'rules'])
            ->validate([])->shouldReturn(true);
    }

    function it_fails_when_input_doesn_not_match_rules()
    {
        // Validation should fail
        $this->validatorInstance->shouldReceive('fails')->andReturn(true);
        $this->validatorInstance->shouldReceive('errors')->andReturn(
            m::mock('Illuminate\Support\MessageBag')
        );

        $this->setRules(['the' => 'rules']);
        $this->shouldThrow('StudioIgnis\Form\Exception\ValidationError')
            ->duringValidate([]);
    }

    function it_should_set_the_rules_correctly()
    {
        $rules = $this->setRules(['foo' => 'bar'])->getRules();

        $rules->shouldBeArray();
        $rules->shouldHaveKey('foo');
        $rules['foo']->shouldBe('bar');
    }
}
