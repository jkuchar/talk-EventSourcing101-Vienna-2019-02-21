<?php

namespace Library\EventSourcing;

use Library\Events\DomainEvent;

abstract class AbstractAggregate implements RecordsEvents, EventsApplicable, ReconstitutesFromHistory
{

	// ------------- implementation of EventApplicable ------------------------
	/**
	 * @param DomainEvent $domainEvent
	 * @return string
	 */
	private function getApplyMethodForDomainEvent(DomainEvent $domainEvent): string
	{
		$parts = \explode('\\', get_class($domainEvent));
		return 'apply' . \end($parts);
	}

	public function applyIfAccepts(DomainEvent $domainEvent): void
	{
		if(method_exists($this, $this->getApplyMethodForDomainEvent($domainEvent))) {
			$this->apply($domainEvent);
		}
	}

	public function apply(DomainEvent $domainEvent): void
	{
		$method = $this->getApplyMethodForDomainEvent($domainEvent);
		$this->$method($domainEvent);
	}

	// ---------------------------------------------------------------------

	/** @return static */
	protected static function createInstanceForGivenHistory(AggregateHistory $aggregateHistory)
	{
		return new static($aggregateHistory->getAggregateId());
	}

	/**
	 * @param AggregateHistory $aggregateHistory
	 * @return static
	 */
	public static function reconstituteFrom(AggregateHistory $aggregateHistory)
	{
		$aggregate = static::createInstanceForGivenHistory($aggregateHistory);

		foreach($aggregateHistory as $event) {
			$aggregate->apply($event);
		}
		return $aggregate;
	}

	// -------- implementation of RecordsEvents ------------------
	/** @var DomainEvent[] */
	private $recordedEvents = [];

	public function getRecordedEvents(): DomainEvents
	{
		return new DomainEvents($this->recordedEvents);
	}

	public function clearRecordedEvents(): void
	{
		$this->recordedEvents = [];
	}

	protected function recordThat(DomainEvent $domainEvent): void
	{
		$this->recordedEvents[] = $domainEvent;
		$this->apply($domainEvent);
	}

}
