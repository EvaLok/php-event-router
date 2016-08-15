[![Build Status](https://travis-ci.org/EvaLok/php-event-router.svg?branch=master)](https://travis-ci.org/EvaLok/php-event-router)

# php-event-router
simple event router with Router, Event, and Handler objects

# install
`composer require evalok/php-event-router`

# example usage
```php
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
```
