<?php

namespace spec\EventRouter\v1;

use PhpSpec\ObjectBehavior;

class EventSpec extends ObjectBehavior
{
    function it_should_instantiate(){
        $this->instantiate()->shouldHaveType('EventRouter\v1\Event');
    }

    public function instantiate()
    {
	    $this->beConstructedWith('testname', []);

    	return $this;
    }
}
