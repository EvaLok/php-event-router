<?php

namespace EventRouter\v1;

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
	public function testInstantiate()
	{
		$event = $this->instantiate();
		$this->assertInstanceOf(Event::class, $event);
	}

	public function instantiate()
	{
		$event = new Event('testname', []);
		return $event;
	}
}
