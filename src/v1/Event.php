<?php

namespace EventRouter\v1;

class Event
{
	protected $name;
	protected $data;

	public function __construct( $name, $data = [] )
	{
		$this->setName($name);
		$this->setData($data);
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name )
	{
		$this->name = (string)$name;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData( Array $data )
	{
		$this->data = $data;
	}
}
