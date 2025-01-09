# php-event-router
simple event router with Router, Event, and Handler objects

# install
`composer require evalok/php-event-router`

# example usage
```php
use EventRouter\v1\Router;
use EventRouter\v1\Handler;
use EventRouter\v1\Event;

// use the router singleton instance
$router = Router::getInstance();

// set handlers..
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

// register handlers for specific events (in this case, test.event.1)
$router->registerHandler(['test.event.1'], $handler1);
$router->registerHandler(['test.event.1'], $handler2);

// trigger the handler, and grab the results if you need them
$results = $router->handleEvent(
	new Event('test.event.1', ['count' => 1])
);

echo $results['test.handler.1']['count']; // 16
echo $results['test.handler.2']['count']; // 101
```
