<?php

namespace Spatie\EventSourcing\Tests\TestClasses\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\Tests\TestClasses\Events\MoneyAddedEvent;
use Spatie\EventSourcing\Tests\TestClasses\Events\MoneySubtractedEvent;

class BalanceProjector extends Projector
{
    public function onMoneyAdded(MoneyAddedEvent $event)
    {
        $event->account->addMoney($event->amount);
    }

    public function onMoneySubtracted(MoneySubtractedEvent $event)
    {
        $event->account->subtractMoney($event->amount);
    }

    public function onStartingEventReplay()
    {
    }

    public function onFinishedEventReplay()
    {
    }
}
