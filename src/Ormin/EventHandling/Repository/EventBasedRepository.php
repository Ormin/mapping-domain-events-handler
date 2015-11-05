<?php
namespace Ormin\EventHandling\Repository;

use Assert\Assertion as Assert;
use Ormin\EventHandling\EventStore\EventStoreInterface;
use Broadway\Domain\AggregateRoot;
use Broadway\Domain\DomainEventStreamInterface;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventSourcing\EventStreamDecoratorInterface;

/**
 * Class EventBasedRepository
 * A cutted-down EventSourcingRepository from Broadway, suited to save and publish events, but not to load aggregates
 * from events
 * @package Ormin\EventHandling\Repository
 */
class EventBasedRepository
{

    private $eventStore;
    private $eventBus;
    private $aggregateClass;
    private $eventStreamDecorators = array();

    /**
     * @param EventStoreInterface $eventStore
     * @param EventBusInterface $eventBus
     * @param string $aggregateClass
     * @param EventStreamDecoratorInterface[] $eventStreamDecorators
     */
    public function __construct(
        EventStoreInterface $eventStore,
        EventBusInterface $eventBus,
        $aggregateClass,
        array $eventStreamDecorators = array()
    )
    {

        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->aggregateClass = $aggregateClass;
        $this->eventStreamDecorators = $eventStreamDecorators;
    }

    /**
     * {@inheritDoc}
     */
    public function save(AggregateRoot $aggregate)
    {
        // maybe we can get generics one day.... ;)
        Assert::isInstanceOf($aggregate, $this->aggregateClass);

        $domainEventStream = $aggregate->getUncommittedEvents();
        $eventStream = $this->decorateForWrite($aggregate, $domainEventStream);
        $this->eventStore->append($aggregate->getAggregateRootId(), $eventStream);
        $this->eventBus->publish($eventStream);
    }

    private function decorateForWrite(AggregateRoot $aggregate, DomainEventStreamInterface $eventStream)
    {
        $aggregateIdentifier = $aggregate->getAggregateRootId();

        foreach ($this->eventStreamDecorators as $eventStreamDecorator) {
            $eventStream = $eventStreamDecorator->decorateForWrite($this->aggregateClass, $aggregateIdentifier, $eventStream);
        }

        return $eventStream;
    }

}