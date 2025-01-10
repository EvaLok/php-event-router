<?php


use EventRouter\v2\Router\Exception\UnknownEventName;
use PHPUnit\Framework\TestCase;

use EventRouter\v2\Router;
use EventRouter\v2\Handler;
use EventRouter\v2\Event;
use EventRouter\v2\EventResult;

class TestHandlerResult extends EventResult {
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
		return new TestHandlerResult($event->count + 15);
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

	public function testShouldRegisterMultipleEventHandlers()
	{
		$router = $this->instantiate();
		$handler1 = new TestHandler1();
		$handler2 = new class extends Handler {
			public function handle(TestEvent1|Event $event): EventResult
			{
				return new TestHandlerResult($event->count + 100);
			}
		};

		$router->registerHandler([TestEvent1::class], $handler1);
		$router->registerHandler([TestEvent1::class], $handler2);
		$handlers = $router->getHandlers();

		$this->assertInstanceOf(TestHandler1::class, $handlers[TestEvent1::class][0]);
		$this->assertInstanceOf(Handler::class, $handlers[TestEvent1::class][1]);
		$this->assertEquals(TestHandler1::class, $handlers[TestEvent1::class][0]::class);
		$this->assertEquals($handler2::class, $handlers[TestEvent1::class][1]::class);

		$results = $router->handleEvent(new TestEvent1(1));
		$this->assertEquals(16, $results[TestHandler1::class]->count);
		$this->assertEquals(101, $results[$handler2::class]->count);
	}

	public function testShouldRegisterEventHandlerForMultipleEvents()
	{
		$router = $this->instantiate();

		$event1 = new class(0) extends Event {
			public function __construct(
				public readonly int $count,
			) {

			}
		};

		$event2 = new class(0) extends Event {
			public function __construct(
				public readonly int $countTwo,
			) {

			}
		};

		$event3 = new class(0) extends Event {
			public function __construct(
				public readonly int $countThree,
			) {

			}
		};

		$multiHandler = new class($event1::class, $event2::class, $event3::class) extends Handler {

			public function __construct(
				private $event1,
				private $event2,
				private $event3,
			) {

			}


			// no type declaration due to use of anonymous classes
			public function handle( $event ): TestHandlerResult
			{
				switch( $event::class ) {
					case $this->event1:
						return new TestHandlerResult($event->count + 1);
					case $this->event2:
						return new TestHandlerResult($event->countTwo * 5);
					case $this->event3:
						return new TestHandlerResult(($event->countThree + 1) * 5 );
				}

				throw new \Exception("unknown event type " . $event::class);
			}
		};

		// events intentionally not sequential
		$events = [$event2::class, $event3::class, $event1::class];
		$router->registerHandler($events, $multiHandler);

		foreach ($events as $event) {
			switch ( $event ) {
				case $event1::class:
					$result = $router->handleEvent(new $event(5));
					$this->assertEquals(6, $result[$multiHandler::class]->count);
					break;

				case $event2::class:
					$result = $router->handleEvent(new $event(5));
					$this->assertEquals(25, $result[$multiHandler::class]->count);
					break;

				case $event3::class:
					$result = $router->handleEvent(new $event(5));
					$this->assertEquals(30, $result[$multiHandler::class]->count);
					break;

				default:
					throw new \Exception("should not reach here");
			}
		}
	}

	public function testShouldNotHandleUnregisteredEvents()
	{
		$router = $this->instantiate();
		$event1 = new class(0) extends Event {
			public function __construct(
				public readonly int $count,
			) {

			}
		};

		$event2 = new class(0) extends Event {
			public function __construct(
				public readonly int $countTwo,
			) {

			}
		};

		$event3 = new class(0) extends Event {
			public function __construct(
				public readonly int $countThree,
			) {

			}
		};

		$multiHandler = new class($event1::class, $event2::class, $event3::class) extends Handler {
			public function __construct(
				private $event1,
				private $event2,
				private $event3,
			) {

			}

			// no type declaration due to use of anonymous classes
			public function handle( $event ): TestHandlerResult
			{
				switch( $event::class ) {
					case $this->event1:
						return new TestHandlerResult($event->count + 1);
					case $this->event2:
						return new TestHandlerResult($event->countTwo * 5);
					case $this->event3:
						return new TestHandlerResult(($event->countThree + 1) * 5 );
				}

				throw new \Exception("unknown event type " . $event::class);
			}
		};

		$events = [new $event1(5), new $event3(5), new $event2(5)];
		$router->registerHandler([$event1::class, $event3::class], $multiHandler);

		foreach ($events as $event) {
			switch ($event::class) {
				case $event1::class:
					$result = $router->handleEvent($event);
					$this->assertEquals(6, $result[$multiHandler::class]->count);
					break;

				case $event2::class:
					$this->expectException(UnknownEventName::class);
					$result = $router->handleEvent($event);
					$this->assertEmpty($result);
					break;

				case $event3::class:
					$result = $router->handleEvent($event);
					$this->assertEquals(30, $result[$multiHandler::class]->count);
					break;

				default:
					throw new \Exception("should not reach here");
			}
		}
	}

	public function testShouldErrorIfUnknownEventTypePassedToRouter()
	{
		$router = $this->instantiate();
		$eventWithNoHandler = new class() extends Event {};

		$this->expectException(UnknownEventName::class);
		$router->handleEvent($eventWithNoHandler);
	}

	public function testShouldInstantiateOnceFromGetInstance()
	{
		$instance = Router::GetInstance();
		$this->assertInstanceOf(Router::class, $instance);
		$this->assertEquals(0, count($instance->getHandlers()));

		$sameInstance = Router::GetInstance();
		$instance->registerHandler([TestEvent1::class], new TestHandler1());
		$this->assertEquals(1, count($instance->getHandlers()));
		$this->assertEquals(1, count($sameInstance->getHandlers()));
	}

	private function instantiate(): Router
	{
		return new Router();
	}
}



