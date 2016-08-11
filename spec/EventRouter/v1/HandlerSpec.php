<?php

namespace spec\EventRouter\v1;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HandlerSpec extends ObjectBehavior
{
    function it_should_instantiate()
    {
        $this->instantiate()->shouldHaveType('EventRouter\v1\Handler');
    }

	public function instantiate()
	{
		$this->beConstructedWith('testname', function(){
			return;
		});

		return $this;
	}
}
