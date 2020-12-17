<?php

namespace Spatie\EventSourcing\EventHandlers;

use Spatie\EventSourcing\Attributes\Handler;
use Spatie\EventSourcing\StoredEvents\StoredEvent;

trait AppliesEvents
{
    protected function apply(StoredEvent ...$storedEvents): void
    {
        foreach ($storedEvents as $storedEvent) {
            $this->applyStoredEvent($storedEvent);
        }
    }

    private function applyStoredEvent(StoredEvent $storedEvent)
    {
        Handler::find($storedEvent->event, $this)->each(
            fn (Handler $handler) => $this->{$handler->method}($storedEvent->event)
        );
    }
}
