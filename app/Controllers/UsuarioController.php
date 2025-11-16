<?php
namespace App\Controllers;

use App\Models\Usuario;

/**
 * UsuarioController seguro
 *
 * - Valida y sanitiza entradas
 * - Protege contra IDOR (solo el dueño del recurso puede modificarlo)
 * - Requiere token CSRF para operaciones que cambian estado (POST/PUT/DELETE)
 * - Usa PDO prepared statements (evita SQL injection)
 */
class UsuarioController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Mostrar perfil de usuario
     *
     * @param int $id
     */
    public function mostrar($id)
    {
        // Validar ID
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            http_response_code(400);
            echo "ID inválido";
            exit;
        }

        $model = new Usuario($this->pdo);
        $user = $model->find($id);

        if (!$user) {
            http_response_code(404);
            echo "Usuario no encontrado";
            exit;
        }

        // para las vistas estáticas
        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/usuarios/mostrar.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    /**
     * Formulario para editar usuario
     *
     * @param int $id
     */
    public function editar($id)
    {
        // Validar ID
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            http_response_code(400);
            echo "ID inválido";
            exit;
        }

        // Debe existir sesión
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Solo el propio usuario puede editar su perfil (evita IDOR)
        if ((int)$_SESSION['user_id'] !== (int)$id) {
            http_response_code(403);
            echo "No tienes permiso para editar este usuario";
            exit;
        }

        $model = new Usuario($this->pdo);
        $user = $model->find($id);

        if (!$user) {
            http_response_code(404);
            echo "Usuario no encontrado";
            exit;
        }

        // Generar CSRF token si no existe
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/usuarios/editar.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    /**
     * Actualizar usuario (se espera _method = PUT desde formulario)
     * @param int $id
     */
    public function actualizar($id)
    {
        // Validar ID
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            http_response_code(400);
            echo "ID inválido";
            exit;
        }

        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Solo el propio usuario puede actualizar su perfil
        if ((int)$_SESSION['user_id'] !== (int)$id) {
            http_response_code(403);
            echo "No tienes permiso para actualizar este usuario";
            exit;
        }

        // CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            http_response_code(403);
            echo "Token CSRF inválido";
            exit;
        }

        // Capturar y normalizar inputs
        $nombre   = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
        $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : null;
        $user     = isset($_POST['user']) ? trim($_POST['user']) : null;
        $email    = isset($_POST['email']) ? trim($_POST['email']) : null;
        $password = isset($_POST['password']) ? $_POST['password'] : null; // password en claro temporalmente

        // Validaciones básicas
        $errors = [];

        // nombre/apellido: obligatorios, longitud y caracteres permitidos
        if (!$nombre || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
            $errors[] = "El nombre es obligatorio y debe tener entre 2 y 100 caracteres.";
        }
        if (!$apellido || mb_strlen($apellido) < 2 || mb_strlen($apellido) > 100) {
            $errors[] = "El apellido es obligatorio y debe tener entre 2 y 100 caracteres.";
        }

        // user: obligatorio, alfanumérico con puntos/guiones/guiones_bajos, longitud
        if (!$user || !preg_match('/^[a-zA-Z0-9._-]{3,30}$/', $user)) {
            $errors[] = "El usuario es obligatorio, 3-30 caracteres. Solo letras, números, puntos, guiones y guión bajo.";
        }

        // email: formato válido
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $errors[] = "Email inválido o demasiado largo.";
        }

        // password: opcional, si viene debe tener mínimo 8 caracteres
        $passwordHash = null;
        if ($password !== null && $password !== '') {
            if (mb_strlen($password) < 8) {
                $errors[] = "La contraseña debe tener al menos 8 caracteres.";
            } else {
                // generar hash seguro
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            header("Location: /usuarios/{$id}/editar");
            exit;
        }

        // Antes de actualizar, verificar unicidad de email y user (si cambiaron)
        $model = new Usuario($this->pdo);

        $existingByEmail = $model->findByEmail($email);
        if ($existingByEmail && (int)$existingByEmail['id'] !== (int)$id) {
            $_SESSION['flash_error'] = "El email ya está en uso por otro usuario.";
            header("Location: /usuarios/{$id}/editar");
            exit;
        }

        $existingByUser = $model->findByUser($user);
        if ($existingByUser && (int)$existingByUser['id'] !== (int)$id) {
            $_SESSION['flash_error'] = "El nombre de usuario ya está en uso.";
            header("Location: /usuarios/{$id}/editar");
            exit;
        }

        // Preparar sentencia de actualización dinámica según campos recibidos
        $fields = [];
        $params = [];

        $fields[] = "nombre = :nombre";
        $params[':nombre'] = $nombre;

        $fields[] = "apellido = :apellido";
        $params[':apellido'] = $apellido;

        $fields[] = "user = :user";
        $params[':user'] = $user;

        $fields[] = "email = :email";
        $params[':email'] = $email;

        if ($passwordHash !== null) {
            $fields[] = "password = :password";
            $params[':password'] = $passwordHash;
        }

        $params[':id'] = $id;

        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['flash_success'] = 'Usuario actualizado';
        header("Location: /usuarios/{$id}");
        exit;
    }

    /**
     * Eliminar usuario (DELETE)
     *
     * @param int $id
     */
    public function destruir($id)
    {
        // Validar ID
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            http_response_code(400);
            echo "ID inválido";
            exit;
        }

        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Solo el propio usuario puede eliminar su cuenta (evita IDOR)
        if ((int)$_SESSION['user_id'] !== (int)$id) {
            http_response_code(403);
            echo "No tienes permiso para eliminar este usuario";
            exit;
        }

        // CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            http_response_code(403);
            echo "Token CSRF inválido";
            exit;
        }

        // Borrar usuario
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        $stmt->execute([$id]);

        // Cerrar sesión
        session_unset();
        session_destroy();

        header('Location: /');
        exit;
    }
}