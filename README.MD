Mapping domain events handler
================

Allows you to map the domain events to atomic calls, which will persist the desired event's state change. Useful if you're not having/not able to implement Event Sourcing into your system,
but you want to make use of Domain Events as a primary source of change within your code

To install it, use Composer:

```
$ composer require --dev ormin/mapping-domain-events-handler:dev-master
```

To use it, inject the EventBasedRepository class into your domain repository implementation and pass the AggregateRoot for it to save


```
class Member implements AggregateRoot { ... }

class DBALMemberRepository implements MemberRepository {

	private $repository;

	public function __construct(EventBasedRepository $repository) {
		$this->repository = $repository;
	}

	public function save(Member $aggregateRoot) {
		$this->repository->save($aggregateRoot);
	}

}


```

You instantiate one EventBasedRepository per aggregate, with an event store implementation. This project ships with a SimpleMappingEventStore, which will map the events to the POPO objects' calls.
Create the POPO handler object, and instantiate SimpleMappingEventStore with it injected as a constructor parameter:


```
class UserAddedAProductToBasket { ... }

class DBALMappingMemberEventStore {

	public function handleUserAddedAProductToBasket() {
		//Handle persisting of state of what happened within the aggregate
		$statement = $this->connection->prepare("INSERT INTO ...");
	}

}


class User extends EventSourcedAggregateRoot {

	public function addToBasket(Product $product) {
		$this->apply(new UserAddedAProductToBasket($product));
	}

	public function handleUserAddedAProductToBasket(UserAddedAProductToBasket $event) {
		//Handle the event within the aggregate
	}

	//When this aggregate will be sent for saving, its uncommitted events' will be sent to event store and mapped to the DBALMappingMemberEventStore::handleUserAddedAProductToBasket()

}


```



