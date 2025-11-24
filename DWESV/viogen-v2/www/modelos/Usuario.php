<?php
/**
 * Modelo de autenticación (Usuario).
 * @package Modelos
 */
class Usuario
{
    /**
     * Busca usuario por credenciales (SHA1).
     * @param PDO $pdo Conexión
     * @param string $nombre Nombre
     * @param string $clave Clave en texto plano
     * @return array|false Usuario o false
     */
    public static function buscarPorCredenciales(PDO $pdo, string $nombre, string $clave)
    {
        // Sanitizar el nombre (el controlador ya hace trim, aquí reforzamos)
        // Permitimos letras, números, guiones y guión bajo. Eliminamos otros chars.
        $nombre = preg_replace('/[^a-zA-Z0-9_\-]/u', '', $nombre);
        // Generar hash SHA1 de la clave introducida (BD almacena SHA1)
        $hash = sha1($clave);

        // Limitar columnas devueltas (defensa adicional para no exponer la clave)
        $sql = 'SELECT id, nombre FROM Usuario WHERE nombre = :nombre AND clave = :clave LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':clave' => $hash,
        ]);
        return $stmt->fetch();
    }
}
