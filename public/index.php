<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Iniciar sesión
session_name($_ENV['SESSION_NAME'] ?? 'album_session');
session_start();

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Método HTTP real (simulación PUT/DELETE)
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Inicializar DB y Router
$db = Database::getInstance();
$router = new Router($db);

// Rutas
$router->get('/', 'App\\Controllers\\HomeController@index');

// Usuarios
$router->get('/usuarios/crear', 'App\\Controllers\\AuthController@showRegister');
$router->post('/usuarios', 'App\\Controllers\\AuthController@register');
$router->get('/usuarios/{id}', 'App\\Controllers\\UsuarioController@mostrar');
$router->get('/usuarios/{id}/editar', 'App\\Controllers\\UsuarioController@editar');
$router->put('/usuarios/{id}', 'App\\Controllers\\UsuarioController@actualizar');
$router->delete('/usuarios/{id}', 'App\\Controllers\\UsuarioController@destruir');

// Auth (login/logout)
$router->get('/login', 'App\\Controllers\\AuthController@showLogin');
$router->post('/login', 'App\\Controllers\\AuthController@login');
$router->get('/logout', 'App\\Controllers\\AuthController@logout');

// Fotos
$router->get('/fotos', 'App\\Controllers\\FotoController@index');
$router->get('/fotos/crear', 'App\\Controllers\\FotoController@crear');
$router->post('/fotos', 'App\\Controllers\\FotoController@guardar');
$router->get('/fotos/{id}', 'App\\Controllers\\FotoController@mostrar');
$router->get('/fotos/{id}/editar', 'App\\Controllers\\FotoController@editar');
$router->put('/fotos/{id}', 'App\\Controllers\\FotoController@actualizar');
$router->delete('/fotos/{id}', 'App\\Controllers\\FotoController@destruir');

// Ejecutar
$router->dispatch($uri, $method);
