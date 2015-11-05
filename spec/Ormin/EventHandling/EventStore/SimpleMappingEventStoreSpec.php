<?php

namespace spec\Ormin\EventHandling\EventStore;

use Broadway\Domain\DomainEventStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class SimpleMappingEventStoreSpec
 * @package spec\Ormin\EventHandling\EventStore
 */
class SimpleMappingEventStoreSpec extends ObjectBehavior
{
    function it_is_initializable(StubbedEventStore $eventStore)
    {
        $this->beConstructedWith($eventStore);
        $this->shouldHaveType('Ormin\EventHandling\EventStore\SimpleMappingEventStore');
    }

    function it_should_persist_existing_domain_event(StubbedEventStore $eventStore, DomainEventStream $eventStream) {
        $this->beConstructedWith($eventStore);

        $domainEvent = new StubbedDomainCalled();
        $iterator = new \ArrayIterator([$domainEvent]);
        $eventStream->getIterator()->willReturn($iterator);

        $eventStore->persistStubbedDomainCalledEvent($domainEvent)->shouldBeCalled();
        $this->append(123, $eventStream);
    }

    function it_should_throw_domain_exception_if_we_do_not_know_how_to_persist(StubbedEventStore $eventStore, DomainEventStream $eventStream) {
        $this->beConstructedWith($eventStore);
        $iterator = new \ArrayIterator([new UnknownThingHappened()]);
        $eventStream->getIterator()->willReturn($iterator);
        $this->shouldThrow("\\DomainException")->duringAppend(123, $eventStream);
    }

}

class StubbedDomainCalled { }
class UnknownThingHappened { }

class StubbedEventStore {


    public function persistStubbedDomainCalledEvent(StubbedDomainCalled $event) {
        //Nothing is happening - it exists purely to test the behavior
    }


}