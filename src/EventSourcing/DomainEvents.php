<?php

namespace Library\EventSourcing;

use ArrayObject;
use Library\Events\DomainEvent;

/**
 * Collection of domain events
 */
class DomainEvents extends ArrayObject {

	public function __construct(array $domainEvents)
	{
		// type check for DomainEvent[]
		array_filter($domainEvents, function(DomainEvent $domainEvent) {});
		parent::__construct($domainEvents);
	}

}
