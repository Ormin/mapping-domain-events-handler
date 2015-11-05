<?php

namespace Ormin\EventHandling\EventStore;

use Broadway\Domain\DomainEventStreamInterface;

/**
 * Class SimpleMappingEventStore
 *
 * This class is a proxy , which allows inserting POPO event stores with persisting methods' convention of
 * persist<EventName>Event methods, allowing for clean separation of persistent storage change for
 * every event. This is useful if you do not have real event sourcing, but want to grasp the benefits
 * of making state changes depending on domain events.
 * @package Ormin\EventHandling\Repository
 */
class SimpleMappingEventStore implements EventStoreInterface
{

    private $eventStore;

    /**
     * Event store proxy - used for having a convenient way to define methods for saving specific methods
     * which looks really nice and clear while we're not serializing and saving events but mapping them
     * to state changes
     * @param mixed $eventStore
     */
    public function __construct($eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function append($id, DomainEventStreamInterface $domainEvents)
    {

        foreach ($domainEvents as $domainEvent) {
            $classParts = explode('\\', get_class($domainEvent));

            $persistMethod = "persist" . end($classParts) . "Event";

            if (!method_exists($this->eventStore, $persistMethod)) {
                throw new \DomainException("Persist event method " . $persistMethod . " does not exist on " . get_class($this->eventStore) . " event store handler");
            }

            $this->eventStore->$persistMethod($domainEvent);
        }

    }

}
