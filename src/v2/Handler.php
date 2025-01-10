<?php

namespace EventRouter\v2;

abstract class Handler
{
	abstract public function handle( Event $event ): EventResult;
}
