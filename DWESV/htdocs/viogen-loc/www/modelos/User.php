<?php
class User
{
    public static function findByCredentials(PDO $pdo, string $nombre, string $clave)
    {
        $sql = 'SELECT * FROM Usuario WHERE nombre = :nombre AND clave = :clave LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nombre' => $nombre, ':clave' => $clave]);
        return $stmt->fetch();
    }
}
