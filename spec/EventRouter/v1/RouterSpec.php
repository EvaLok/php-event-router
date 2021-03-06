<?php

namespace spec\EventRouter\v1;

use EventRouter\v1\Router\Exception\UnknownEventName;
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

	function it_should_register_event_handler()
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

	function it_should_register_multiple_event_handlers()
	{
		$handler1 = (new Handler(
			'test.handler.1',
			function( $data ){
				$data['count'] += 15;

				return $data;
			})
		);

		$handler2 = (new Handler(
			'test.handler.2',
			function( $data ){
				$data['count'] += 100;

				return $data;
			})
		);

		$this->registerHandler(['test.event.1'], $handler1);
		$this->registerHandler(['test.event.1'], $handler2);

		$handlers = $this->getHandlers();

		$handlers['test.event.1'][0]
			->shouldHaveType('EventRouter\v1\Handler');
		$handlers['test.event.1'][1]
			->shouldHaveType('EventRouter\v1\Handler');

		$handlers['test.event.1'][0]->getName()
			->shouldBe('test.handler.1');
		$handlers['test.event.1'][1]->getName()
			->shouldBe('test.handler.2');

		$results = $this->handleEvent(
			new Event('test.event.1', ['count' => 1])
		);

		$results['test.handler.1']['count']->shouldEqual(16);
		$results['test.handler.2']['count']->shouldEqual(101);
	}

	function it_should_register_event_handler_for_multiple_events()
	{
		$handler1 = (new Handler(
			'test.handler.1',
			function( $data ){
				$data['count'] += 15;

				return $data;
			})
		);

		$events = [
			'test.event.1',
			'test.event.2',
			'test.event.3'
		];

		$this->registerHandler($events, $handler1);

		$handlers = $this->getHandlers();

		foreach( $events as $event ){
			$handlers[$event][0]
				->shouldHaveType('EventRouter\v1\Handler');

			$handlers[$event][0]->getName()
				->shouldBe('test.handler.1');

			$results = $this->handleEvent(
				new Event($event, ['count' => 1])
			);

			$results['test.handler.1']['count']->shouldEqual(16);
		}
	}

	function it_should_not_handle_unregistered_events()
	{
		$handler1 = (new Handler(
			'test.handler.1',
			function( $data ){
				$data['count'] += 15;

				return $data;
			})
		);

		$events = [
			'test.event.1',
			'test.event.2',
			'test.event.3'
		];

		$this->registerHandler([
			'test.event.1',
			'test.event.3'
		], $handler1);

		foreach( $events as $event ){
			switch( $event ){
				case 'test.event.1':
				case 'test.event.3':
					$results = $this->handleEvent(
						new Event($event, ['count' => 1])
					);

					$results['test.handler.1']['count']->shouldEqual(16);
					break;

				case 'test.event.2':
					$event = new Event($event, ['count' => 1]);

					$this
						->shouldThrow('EventRouter\v1\Router\Exception\UnknownEventName')
						->duringHandleEvent($event);

					try {
						$this->handleEvent($event);

						throw new \Exception("should not get here");
					}

					catch ( UnknownEventName $ex ) {
						// we expect this exception
					}

					break;
			}
		}
	}

	function it_should_error_if_unknown_event_type_passed_to_router() {
		$eventWithNoHandler = new Event(
			"event.no.handler",
			[
				'some' => 'data'
			]
		);

		$this
			->shouldThrow('EventRouter\v1\Router\Exception\UnknownEventName')
			->duringHandleEvent($eventWithNoHandler);

		try {
			$this->handleEvent($eventWithNoHandler);

			throw new \Exception("should not get here");
		}

		catch ( UnknownEventName $ex ) {
			// we expect this exception
		}
	}
}
