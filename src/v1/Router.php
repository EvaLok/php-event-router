<?php

namespace EventRouter\v1;

use EventRouter\v1\Event;
use EventRouter\v1\Handler;

class Router
{
	private static $instance;

	protected $handlers = [];

	public function __construct()
	{
		static::$instance = $this;

		return $this;
	}

	/**
	 * triggers registered handlers for a particular event name (passes data)
	 *
	 * @param \EventRouter\v1\Event $event
	 *
	 * @return array $results
	 */
	public function handleEvent( Event $event )
	{
		$results = [];

		if( ! is_array($this->handlers[$event->getName()]) ){
			trigger_error("unknown event {$event->getName()}", E_NOTICE);
		} else {
			foreach( $this->handlers[$event->getName()] as $handler ){
				$results[$handler->getName()] = (
					call_user_func($handler->handle, $event->getData())
				);
			}
		}

		return $results;
	}

	/**
	 * binds the handler to all aliases supplied
	 *
	 * @param array $aliases
	 * @param \EventRouter\v1\Handler $handler
	 *
	 * @return $this;
	 */
	public function registerHandler( Array $aliases, Handler $handler )
	{
		foreach( $aliases as $alias ){
			if( ! is_array($this->handlers[$alias]) ){
				$this->handlers[$alias] = [];
			}

			$this->handlers[$alias][] = $handler;
		}

		return $this;
	}

	static public function getInstance()
	{
		return static::$instance = (
		! is_null(static::$instance)
			?
			static::$instance
			:
			new static
		);
	}
}
