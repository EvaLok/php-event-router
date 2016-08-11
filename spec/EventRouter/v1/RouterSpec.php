<?php

namespace spec\EventRouter\v1;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('EventRouter\v1\Router');
    }
}
