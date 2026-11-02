<?php

require_once __DIR__.'/../models/Event.php';
class EventController
{
    private Event $eventModel;

    public function __construct(PDO $pdo)
    {
        $this->eventModel = new Event($pdo);
    }

    public function index(): array
    {
        return $this->eventModel->getAll();
    }
}
