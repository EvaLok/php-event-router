<?php

namespace spec\EventRouter\v1;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use EventRouter\v1\Handler;
use EventRouter\v1\Event;

class RouterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('EventRouter\v1\Router');
    }

    function it_should_register_event_handler_properly()
    {
    	$handler1 = (new Handler(
		    'test.handler.1',
		    function( $data ){
		    	$data['count'] += 15;

			    return $data;
		    })
	    );

    	$this->registerHandler(['test.event.1'], $handler1);

	    $handlers = $this->getHandlers();

	    $handlers['test.event.1'][0]
		    ->shouldHaveType('EventRouter\v1\Handler');

	    $handlers['test.event.1'][0]->getName()
		    ->shouldBe('test.handler.1');

		$results = $this->handleEvent(
			new Event('test.event.1', ['count' => 1])
		);

	    $results['test.handler.1']['count']->shouldEqual(16);
    }
}
