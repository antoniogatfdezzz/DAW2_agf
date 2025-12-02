<?php
class AutenticacionModelo {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function login(string $usuario, string $password): bool {
        $conn = $this->conexion->obtenerConexion();
        if (!$conn) return false;

        $sql = "SELECT u.*, 
                        CASE WHEN u.tipo_usuario = 'administrador' THEN a.nombre
                             WHEN u.tipo_usuario = 'arbitro' THEN ar.nombre
                        END AS nombre,
                        CASE WHEN u.tipo_usuario = 'administrador' THEN a.apellidos
                             WHEN u.tipo_usuario = 'arbitro' THEN ar.apellidos
                        END AS apellidos_o_club,
                        u.password_temporal
                FROM usuarios u
                LEFT JOIN administradores a ON u.id = a.usuario_id
                LEFT JOIN arbitros ar ON u.id = ar.usuario_id
                WHERE (u.usuario = ? OR u.email = ?) AND u.activo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usuario, $usuario]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['tipo_usuario'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['nombre'] ?? '';
            $_SESSION['user_lastname'] = $user['apellidos_o_club'] ?? '';
            $_SESSION['password_temporal'] = (int)($user['password_temporal'] ?? 0);
            return true;
        }
        return false;
    }

    public function cambiarPassword(string $passwordActual, string $passwordNueva): array {
        $conn = $this->conexion->obtenerConexion();
        if (!$conn) return ['ok' => false, 'msg' => 'Sin conexión BD'];
        $uid = $_SESSION['user_id'] ?? null;
        if (!$uid) return ['ok' => false, 'msg' => 'Sesión no válida'];

        $stmt = $conn->prepare('SELECT password FROM usuarios WHERE id = ?');
        $stmt->execute([$uid]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($passwordActual, $user['password'])) {
            return ['ok' => false, 'msg' => 'La contraseña actual no es correcta'];
        }
        $hash = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $upd = $conn->prepare('UPDATE usuarios SET password = ?, password_temporal = 0 WHERE id = ?');
        $ok = $upd->execute([$hash, $uid]);
        if ($ok) {
            $_SESSION['password_temporal'] = 0;
            return ['ok' => true, 'msg' => 'Contraseña actualizada correctamente'];
        }
        return ['ok' => false, 'msg' => 'No se pudo actualizar la contraseña'];
    }
}
