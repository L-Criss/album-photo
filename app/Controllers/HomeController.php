<?php
namespace App\Controllers;

class HomeController
{
    public function index()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /fotos');
            exit;
        }
        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/welcome.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }
}
