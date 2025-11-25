<?php
require_once __DIR__ . '/BBDD.php';

/**
 * Modelo de víctimas.
 *
 * Incluye operaciones de creación, obtención y búsqueda sobre la entidad Victima.
 */
class VictimaModelo {
    /**
     * Crea una nueva víctima.
     *
     * Los campos opcionales se transforman a NULL si están vacíos/no definidos.
     *
     * @param array $datos Claves esperadas: nombre, apellidos, tipo_documento, documento, telefono, observaciones.
     * @return bool true si la inserción fue correcta; false en caso contrario.
     */
    public static function crear($datos) {
        $bd = (new BD())->obtenerConexion();
        $sql = "INSERT INTO Victima (nombre, apellidos, tipo_documento, documento, telefono, observaciones) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        return $stmt->execute([
            $datos['nombre'] ?? null,
            $datos['apellidos'] ?? null,
            $datos['tipo_documento'] ?? null,
            $datos['documento'] ?? null,
            $datos['telefono'] ?? null,
            $datos['observaciones'] ?? null
        ]);
    }

    /**
     * Obtiene todas las víctimas.
     *
     * @return array Lista de víctimas (cada elemento es un array asociativo con los campos de la tabla).
     */
    public static function obtenerTodas() {
        $bd = (new BD())->obtenerConexion();
        $stmt = $bd->query("SELECT * FROM Victima ORDER BY nombre ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Busca víctimas por múltiples campos usando LIKE.
     *
     * @param string $texto Texto a buscar (se envuelve con % internamente).
     * @return array Resultados coincidentes.
     */
    public static function buscar($texto) {
        $bd = (new BD())->obtenerConexion();
        $texto = "%$texto%";
        $sql = "SELECT * FROM Victima WHERE nombre LIKE ? OR apellidos LIKE ? OR documento LIKE ? OR telefono LIKE ? OR observaciones LIKE ?";
        $stmt = $bd->prepare($sql);
        $stmt->execute([$texto, $texto, $texto, $texto, $texto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
