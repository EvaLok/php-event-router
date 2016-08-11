<?php

namespace Aedea\v2\Hooks;

class Handler
{
	protected $name;
	protected $fn;

	/**
	 * Handler constructor.
	 * @param string $name
	 * @param callable $fn
	 */
	public function __construct( $name, $fn )
	{
		$this->setName($name);
		$this->fn = $fn;
	}

	public function handle( $data )
	{
		return call_user_func($this->fn, $data);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name )
	{
		$this->name = (string)$name;
	}


}
