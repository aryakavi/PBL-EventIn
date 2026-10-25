<?php

class EventController
{
    private Event $eventModel;

    public function __construct(Event $eventModel)
    {
        $this->eventModel = $eventModel;
    }

    public function index(): array
    {
        return $this->eventModel->getAll();
    }
}
