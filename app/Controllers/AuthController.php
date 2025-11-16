<?php
namespace App\Controllers;

use App\Models\Usuario;

/**
 * AuthController seguro
 *
 * - Valida y sanitiza datos enviados por el usuario
 * - Previene ataques de fuerza bruta básicos
 * - Protege contra CSRF en Login y Registro
 * - Evita enumeración de usuarios
 * - Usa password_hash / password_verify
 */
class AuthController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    //     Mostrar formulario Login
    public function showLogin()
    {
        // Generar CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/auth/login.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    //            Login seguro
    public function login()
    {
        // Validación CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            http_response_code(403);
            echo "Token CSRF inválido";
            exit;
        }

        // Obtener datos
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $_SESSION['flash_error'] = "Credenciales inválidas";
            header('Location: /login');
            exit;
        }

        // Modelo
        $model = new Usuario($this->pdo);

        // Buscar usuario
        $user = $model->findByEmail($email);


        //   Defensa contra enumeración
        // Si el correo no existe => hacer un fake delay
        if (!$user) {
            usleep(random_int(50000, 150000)); // retraso aleatorio 50-150ms
            $_SESSION['flash_error'] = "Credenciales inválidas";
            header('Location: /login');
            exit;
        }

        // Validar contraseña
        if (!password_verify($pass, $user['password'])) {
            $_SESSION['flash_error'] = "Credenciales inválidas";
            header('Location: /login');
            exit;
        }

        // Regenerar ID de sesión
        session_regenerate_id(true);

        // Guardar datos mínimos en sesión
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];

        header('Location: /fotos');
        exit;
    }

    //            Logout
    public function logout()
    {
        session_unset();
        session_destroy();

        header('Location: /');
        exit;
    }

    //    Mostrar formulario Registro
    public function showRegister()
    {
        // Generar CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require __DIR__ . '/../Views/partials/header.php';
        require __DIR__ . '/../Views/auth/register.php';
        require __DIR__ . '/../Views/partials/footer.php';
    }

    //       Registrar usuario seguro
    public function register()
    {
        // Validación CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            http_response_code(403);
            echo "Token CSRF inválido";
            exit;
        }

        // Sanitizar entradas
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $user     = trim($_POST['user'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $pass     = $_POST['password'] ?? '';

        $errors = [];

        // Validaciones fuertes
        if (!$nombre || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
            $errors[] = "Nombre inválido.";
        }

        if (!$apellido || mb_strlen($apellido) < 2 || mb_strlen($apellido) > 100) {
            $errors[] = "Apellido inválido.";
        }

        if (!$user || !preg_match('/^[a-zA-Z0-9._-]{3,30}$/', $user)) {
            $errors[] = "Usuario inválido (solo letras, números, . _ -)";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $errors[] = "Email inválido.";
        }

        if (mb_strlen($pass) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres.";
        }

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(" ", $errors);
            header('Location: /usuarios/crear');
            exit;
        }

        // Modelo
        $model = new Usuario($this->pdo);

        // Email único
        if ($model->findByEmail($email)) {
            $_SESSION['flash_error'] = 'El email ya está en uso';
            header('Location: /usuarios/crear');
            exit;
        }

        // Usuario único
        if ($model->findByUser($user)) {
            $_SESSION['flash_error'] = 'El username ya está en uso';
            header('Location: /usuarios/crear');
            exit;
        }


        // Hash seguro
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        // Crear usuario
        $model->create($nombre, $apellido, $user, $email, $hash);

        $_SESSION['flash_success'] = 'Registro exitoso';
        header('Location: /login');
        exit;
    }
}
