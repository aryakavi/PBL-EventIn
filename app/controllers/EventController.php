<?php

require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Category.php';
class EventController
{
    private Event $eventModel;
    private Category $categoryModel;

    public function __construct(PDO $pdo)
    {
        $this->eventModel = new Event($pdo);
        $this->categoryModel = new Category($pdo);
    }

    public function index(): array
    {
        return $this->eventModel->getAll();
    }

    public function getCategory(): array 
    {
        return $this->categoryModel->getAll();
    }
}
