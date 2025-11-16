<?php
namespace App\Models;

use PDO;

/**
 * Modelo Usuario
 * 
 * Maneja todas las operaciones CRUD sobre la tabla `usuarios`.
 * Campos considerados:
 *  - nombre
 *  - apellido
 *  - user
 *  - email
 *  - password
 */
class Usuario
{
    private $pdo;

    /**
     * Constructor recibe la conexión PDO
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    /**
     * Crear nuevo usuario
     *
     * @param string $nombre   Nombre
     * @param string $apellido Apellido
     * @param string $user     Usuario
     * @param string $email    Email único
     * @param string $password Contraseña hasheada
     */
    public function create($nombre, $apellido, $user, $email, $password)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios (nombre, apellido, user, email, password)
            VALUES (?, ?, ?, ?, ?)
        ");

        return $stmt->execute([$nombre, $apellido, $user, $email, $password]);
    }


    /**
     * Buscar usuario por email (para login)
     */
    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM usuarios WHERE email = ?
        ");

        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Buscar usuario por usuairo
     */
    public function findByUser($user)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE user = :user LIMIT 1");
        $stmt->execute([':user' => $user]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }



    /**
     * Buscar usuario por ID
     */
    public function find($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM usuarios WHERE id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetch();
    }


    /**
     * Actualizar solo el nombre
     */
    public function updateName($id, $nombre)
    {
        $stmt = $this->pdo->prepare("
            UPDATE usuarios SET nombre = ? WHERE id = ?
        ");

        return $stmt->execute([$nombre, $id]);
    }


    /**
     * Eliminar usuario por ID
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM usuarios WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}
