<?php
require_once __DIR__ . '/BBDD.php';

/**
 * Modelo de usuarios.
 *
 * Proporciona operaciones relacionadas con autenticación básica.
 */
class Usuario {
    /**
     * Autentica un usuario por nombre y clave.
     *
     * La clave se compara en la base de datos mediante MD5.
     *
     * @param string $nombre Nombre de usuario introducido.
     * @param string $clave  Clave en texto plano (se aplicará MD5 antes de la consulta).
     * @return array|false Devuelve array asociativo con datos del usuario si coincide; false si no hay coincidencia.
     */
    public static function autenticar($nombre, $clave) {
        $bd = (new BD())->obtenerConexion();
        $stmt = $bd->prepare('SELECT * FROM Usuario WHERE nombre = ? AND clave = ? LIMIT 1');
        $stmt->execute([$nombre, md5($clave)]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            return $usuario;
        }
        return false;
    }
}
