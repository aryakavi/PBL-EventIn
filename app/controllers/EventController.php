<?php

require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Storage.php';
class EventController
{
    private Event $eventModel;
    private Category $categoryModel;
    private Storage $storageModel;

    public function __construct(PDO $pdo)
    {
        $this->eventModel = new Event($pdo);
        $this->categoryModel = new Category($pdo);
        $this->storageModel = new Storage();
    }

    public function index(): array
    {
        return $this->eventModel->getAll();
    }

    public function getCategory(): array
    {
        return $this->categoryModel->getAll();
    }

    public function addEvent(array $data)
    {

        $poster = $this->storageModel->upload($_FILES['poster'], 'events');
        $result = $this->eventModel->insert(
            $_SESSION['user']['id_admin_ukm'],
            $data['id_kategori'],
            $data['nama_event'],
            $data['deskripsi'],
            $data['tanggal_mulai'] . ' ' . $data['jam_mulai'],
            $data['tanggal_selesai'] . ' ' . $data['jam_selesai'],
            $data['lokasi'],
            $data['kuota'],
            $data['harga'],
            $poster
        );
        $_SESSION['addEvent'] = $result;
        if ($result['success']) {
            header('Location: ?page:admin/event');
            exit;
        } else {
            header('Location: ?page:admin/event#form-event');
            exit;
        }
    }
}
