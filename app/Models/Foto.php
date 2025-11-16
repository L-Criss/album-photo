<?php
namespace App\Models;

use PDO;

class Foto
{
    private $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function allForUser($userId)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fotos WHERE usuario_id=? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fotos WHERE id=?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($usuario_id, $ruta, $descripcion)
    {
        $stmt = $this->pdo->prepare('INSERT INTO fotos (usuario_id,ruta,descripcion) VALUES (?,?,?)');
        return $stmt->execute([$usuario_id,$ruta,$descripcion]);
    }

    public function update($id, $descripcion)
    {
        $stmt = $this->pdo->prepare('UPDATE fotos SET descripcion=? WHERE id=?');
        return $stmt->execute([$descripcion,$id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM fotos WHERE id=?');
        return $stmt->execute([$id]);
    }

}
