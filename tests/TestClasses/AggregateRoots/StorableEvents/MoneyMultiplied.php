<?php

namespace Spatie\EventSourcing\Tests\TestClasses\AggregateRoots\StorableEvents;

use Spatie\EventSourcing\ShouldBeStored;

class MoneyMultiplied extends ShouldBeStored
{
    public int $amount;

    public function __construct(int $amount)
    {
        $this->amount = $amount;
    }
}
