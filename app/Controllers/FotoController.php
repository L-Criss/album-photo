<?php
namespace App\Controllers;

use App\Models\Foto;

class FotoController
{
    private $pdo;

    /**
     * Constructor
     *
     * @param PDO $pdo Conexión PDO a la base de datos
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Verifica que haya sesión activa.
     * Si no, redirige a login.
     */
    private function ensureAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Listar todas las fotos del usuario logueado
     */
    public function index()
    {
        $this->ensureAuth();

        $model = new Foto($this->pdo);
        $fotos = $model->allForUser($_SESSION['user_id']);

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/fotos/index.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    /**
     * Mostrar formulario para subir nueva foto
     */
    public function crear()
    {
        $this->ensureAuth();

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/fotos/crear.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    /**
     * Guardar nueva foto en filesystem y base de datos
     */
    public function guardar()
    {
        $this->ensureAuth();

        // Verificar que se haya subido un archivo
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Error al subir el archivo';
            header('Location: /fotos/crear');
            exit;
        }

        // Validar tipo de imagen
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['foto']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            $_SESSION['flash_error'] = 'Formato de imagen no permitido';
            header('Location: /fotos/crear');
            exit;
        }

        // Carpeta donde se guardarán las imágenes (debe estar dentro de public)
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        // Generar nombre único
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . $ext;
        $dest = $uploadDir . $filename;

        // Mover archivo desde tmp a carpeta uploads
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
            $_SESSION['flash_error'] = 'No se pudo guardar el archivo';
            header('Location: /fotos/crear');
            exit;
        }

        // Ruta pública que se guardará en la DB y será accesible desde navegador
        $ruta_rel = '/uploads/' . $filename;
        $descripcion = trim($_POST['descripcion'] ?? '');

        $model = new Foto($this->pdo);
        $model->create($_SESSION['user_id'], $ruta_rel, $descripcion);

        $_SESSION['flash_success'] = 'Foto subida correctamente';
        header('Location: /fotos');
        exit;
    }

    /**
     * Mostrar foto individual
     */
    public function mostrar($id)
    {
        $this->ensureAuth();

        $model = new Foto($this->pdo);
        $foto = $model->find($id);

        if (!$foto) {
            http_response_code(404);
            echo "Foto no encontrada";
            exit;
        }

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/fotos/mostrar.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    /**
     * Mostrar formulario para editar descripción
     */
    public function editar($id)
    {
        $this->ensureAuth();

        $model = new Foto($this->pdo);
        $foto = $model->find($id);

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/fotos/editar.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    /**
     * Actualizar descripción de la foto
     */
    public function actualizar($id)
    {
        $this->ensureAuth();

        $descripcion = trim($_POST['descripcion'] ?? '');

        $model = new Foto($this->pdo);
        $model->update($id, $descripcion);

        $_SESSION['flash_success'] = 'Foto actualizada';
        header("Location: /fotos/{$id}");
        exit;
    }

    /**
     * Eliminar foto
     */
    public function destruir($id)
    {
        $this->ensureAuth();

        $model = new Foto($this->pdo);
        $foto = $model->find($id);

        if ($foto) {
            // Borrar archivo físico
            $realPath = __DIR__ . '/../../public/' . ltrim($foto['ruta'], '/');
            if (file_exists($realPath)) unlink($realPath);

            // Borrar registro en DB
            $model->delete($id);
        }

        $_SESSION['flash_success'] = 'Foto eliminada';
        header('Location: /fotos');
        exit;
    }
}
