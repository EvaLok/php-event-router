<?php

namespace EventRouter\v1;

use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
	public function testInstantiate()
	{
		$handler = $this->instantiate();
		$this->assertInstanceOf(Handler::class, $handler);
	}

	public function instantiate()
	{
		$handler = new Handler('testname', function(){
			return;
		});

		return $handler;
	}
}
