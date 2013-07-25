<?php

namespace Core;

use Core\Config\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SyEventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

class EventDispatcher extends SyEventDispatcher
{
    private $events = [];

    public function attachEvent(Event $event)
    {
        $fn = function (GenericEvent $genericEvent) use ($event) {
            if ($event->isCallable($genericEvent)) {
                $event->call($genericEvent);
            }
        };

        $this->addListener($event->getKey(), $fn);
        $this->events[] = [
            'key' => $event->getKey(),
            'event' => $event,
            'callback' => $fn
        ];
    }

    public function getAttachedEvents()
    {
        return $this->events;
    }

    public function detachEvents()
    {
        foreach ($this->events as $eventInfo) {
            $this->removeListener($eventInfo['key'], $eventInfo['callback']);
        }

        $this->events = [];
    }

}