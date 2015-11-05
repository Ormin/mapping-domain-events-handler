<?php

namespace Ormin\EventHandling\EventStore;

use Broadway\Domain\DomainEventStreamInterface;

/**
 * Stores events.
 */
interface EventStoreInterface
{
    /**
     * @param mixed                      $id
     * @param DomainEventStreamInterface $domainEvents
     */
    public function append($id, DomainEventStreamInterface $domainEvents);
}