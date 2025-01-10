<?php

namespace EventRouter\v1;

use EventRouter\v1\Router\Exception\UnknownEventName;
use PHPUnit\Framework\TestCase;

use EventRouter\v1\Handler;
use EventRouter\v1\Event;

class RouterTest extends TestCase
{
	public function testIsInitializable()
	{
		$router = $this->instantiate();
		$this->assertInstanceOf(Router::class, $router);
	}

	public function testShouldRegisterEventHandler()
	{
		$router = $this->instantiate();
		$handler1 = new Handler('test.handler.1', function($data) {
			$data['count'] += 15;
			return $data;
		});

		$router->registerHandler(['test.event.1'], $handler1);
		$handlers = $router->getHandlers();

		$this->assertInstanceOf(Handler::class, $handlers['test.event.1'][0]);
		$this->assertEquals('test.handler.1', $handlers['test.event.1'][0]->getName());

		$results = $router->handleEvent(new Event('test.event.1', ['count' => 1]));
		$this->assertEquals(16, $results['test.handler.1']['count']);
	}

	public function testShouldRegisterMultipleEventHandlers()
	{
		$router = $this->instantiate();
		$handler1 = new Handler('test.handler.1', function($data) {
			$data['count'] += 15;
			return $data;
		});

		$handler2 = new Handler('test.handler.2', function($data) {
			$data['count'] += 100;
			return $data;
		});

		$router->registerHandler(['test.event.1'], $handler1);
		$router->registerHandler(['test.event.1'], $handler2);
		$handlers = $router->getHandlers();

		$this->assertInstanceOf(Handler::class, $handlers['test.event.1'][0]);
		$this->assertInstanceOf(Handler::class, $handlers['test.event.1'][1]);
		$this->assertEquals('test.handler.1', $handlers['test.event.1'][0]->getName());
		$this->assertEquals('test.handler.2', $handlers['test.event.1'][1]->getName());

		$results = $router->handleEvent(new Event('test.event.1', ['count' => 1]));
		$this->assertEquals(16, $results['test.handler.1']['count']);
		$this->assertEquals(101, $results['test.handler.2']['count']);
	}

	public function testShouldRegisterEventHandlerForMultipleEvents()
	{
		$router = $this->instantiate();
		$handler1 = new Handler('test.handler.1', function($data) {
			$data['count'] += 15;
			return $data;
		});

		$events = ['test.event.1', 'test.event.2', 'test.event.3'];
		$router->registerHandler($events, $handler1);
		$handlers = $router->getHandlers();

		foreach ($events as $event) {
			$this->assertInstanceOf(Handler::class, $handlers[$event][0]);
			$this->assertEquals('test.handler.1', $handlers[$event][0]->getName());

			$results = $router->handleEvent(new Event($event, ['count' => 1]));
			$this->assertEquals(16, $results['test.handler.1']['count']);
		}
	}

	public function testShouldNotHandleUnregisteredEvents()
	{
		$router = $this->instantiate();
		$handler1 = new Handler('test.handler.1', function($data) {
			$data['count'] += 15;
			return $data;
		});

		$events = ['test.event.1', 'test.event.2', 'test.event.3'];
		$router->registerHandler(['test.event.1', 'test.event.3'], $handler1);

		foreach ($events as $event) {
			switch ($event) {
				case 'test.event.1':
				case 'test.event.3':
					$results = $router->handleEvent(new Event($event, ['count' => 1]));
					$this->assertEquals(16, $results['test.handler.1']['count']);
					break;

				case 'test.event.2':
					$event = new Event($event, ['count' => 1]);
					$this->expectException(UnknownEventName::class);
					$router->handleEvent($event);
					break;
			}
		}
	}

	public function testShouldErrorIfUnknownEventTypePassedToRouter()
	{
		$router = $this->instantiate();
		$eventWithNoHandler = new Event("event.no.handler", ['some' => 'data']);

		$this->expectException(UnknownEventName::class);
		$router->handleEvent($eventWithNoHandler);
	}

	private function instantiate()
	{
		return new Router();
	}
}
