<?php


use EventRouter\v2\Router\Exception\UnknownEventName;
use PHPUnit\Framework\TestCase;

use EventRouter\v2\Router;
use EventRouter\v2\Handler;
use EventRouter\v2\Event;
use EventRouter\v2\EventResult;

class TestEvent1Result extends EventResult {
	public function __construct(
		public readonly int $count,
	){

	}
}
class TestEvent1 extends Event
{
	public function __construct(
		public readonly int $count,
	)
	{

	}
}

class TestHandler1 extends Handler
{
	public function handle( TestEvent1|Event $event ): EventResult
	{
		return new TestEvent1Result($event->count + 15);
	}
}





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
		$handler1 = new TestHandler1();

		$router->registerHandler([TestEvent1::class], $handler1);
		$handlers = $router->getHandlers();

		$this->assertInstanceOf( TestHandler1::class, $handlers[TestEvent1::class][0]);
		$this->assertEquals(TestHandler1::class, $handlers[TestEvent1::class][0]::class);

		$results = $router->handleEvent(new TestEvent1(1));
		$this->assertEquals(16, $results[TestHandler1::class]->count);
	}

//	public function testShouldRegisterMultipleEventHandlers()
//	{
//		$router = $this->instantiate();
//		$handler1 = new Handler('test.handler.1', function($data) {
//			$data['count'] += 15;
//			return $data;
//		});
//
//		$handler2 = new Handler('test.handler.2', function($data) {
//			$data['count'] += 100;
//			return $data;
//		});
//
//		$router->registerHandler(['test.event.1'], $handler1);
//		$router->registerHandler(['test.event.1'], $handler2);
//		$handlers = $router->getHandlers();
//
//		$this->assertInstanceOf(Handler::class, $handlers['test.event.1'][0]);
//		$this->assertInstanceOf(Handler::class, $handlers['test.event.1'][1]);
//		$this->assertEquals('test.handler.1', $handlers['test.event.1'][0]->getName());
//		$this->assertEquals('test.handler.2', $handlers['test.event.1'][1]->getName());
//
//		$results = $router->handleEvent(new Event('test.event.1', ['count' => 1]));
//		$this->assertEquals(16, $results['test.handler.1']['count']);
//		$this->assertEquals(101, $results['test.handler.2']['count']);
//	}
//
//	public function testShouldRegisterEventHandlerForMultipleEvents()
//	{
//		$router = $this->instantiate();
//		$handler1 = new Handler('test.handler.1', function($data) {
//			$data['count'] += 15;
//			return $data;
//		});
//
//		$events = ['test.event.1', 'test.event.2', 'test.event.3'];
//		$router->registerHandler($events, $handler1);
//		$handlers = $router->getHandlers();
//
//		foreach ($events as $event) {
//			$this->assertInstanceOf(Handler::class, $handlers[$event][0]);
//			$this->assertEquals('test.handler.1', $handlers[$event][0]->getName());
//
//			$results = $router->handleEvent(new Event($event, ['count' => 1]));
//			$this->assertEquals(16, $results['test.handler.1']['count']);
//		}
//	}
//
//	public function testShouldNotHandleUnregisteredEvents()
//	{
//		$router = $this->instantiate();
//		$handler1 = new Handler('test.handler.1', function($data) {
//			$data['count'] += 15;
//			return $data;
//		});
//
//		$events = ['test.event.1', 'test.event.2', 'test.event.3'];
//		$router->registerHandler(['test.event.1', 'test.event.3'], $handler1);
//
//		foreach ($events as $event) {
//			switch ($event) {
//				case 'test.event.1':
//				case 'test.event.3':
//					$results = $router->handleEvent(new Event($event, ['count' => 1]));
//					$this->assertEquals(16, $results['test.handler.1']['count']);
//					break;
//
//				case 'test.event.2':
//					$event = new Event($event, ['count' => 1]);
//					$this->expectException(UnknownEventName::class);
//					$router->handleEvent($event);
//					break;
//			}
//		}
//	}
//
//	public function testShouldErrorIfUnknownEventTypePassedToRouter()
//	{
//		$router = $this->instantiate();
//		$eventWithNoHandler = new Event("event.no.handler", ['some' => 'data']);
//
//		$this->expectException(UnknownEventName::class);
//		$router->handleEvent($eventWithNoHandler);
//	}

	private function instantiate()
	{
		return new \EventRouter\v2\Router();
	}
}



