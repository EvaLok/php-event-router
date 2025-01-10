<?php

namespace EventRouter\v2;


use EventRouter\v2\Router\Exception\UnknownEventName;

class Router
{
	private static ?Router $_instance = null;

	protected array $handlers = [];

	public function __construct()
	{

	}

	/**
	 * triggers registered handlers for a particular event name (passes data)
	 *
	 * @throws UnknownEventName
	 */
	public function handleEvent( Event $event ): array
	{
		$results = [];

		if( ! array_key_exists($event::class, $this->handlers) ){
			throw new UnknownEventName($event::class);
		} else {
			/** @var Handler $handler */
			foreach( $this->handlers[$event::class] as $handler ){
				$results[$handler::class] = $handler->handle($event);
			}
		}

		return $results;
	}

	/**
	 * binds the handler to all aliases supplied
	 */
	public function registerHandler( Array $aliases, Handler $handler ): void
	{
		foreach( $aliases as $alias ){
			if( ! array_key_exists($alias, $this->handlers) ){
				$this->handlers[$alias] = [];
			}

			$this->handlers[$alias][] = $handler;
		}
	}

	static public function GetInstance(): Router
	{
		return static::$_instance = (
			! is_null(static::$_instance)
				? static::$_instance
				: new static
		);
	}

	public function getHandlers(): array {
		return $this->handlers;
	}
}
