<?php
require_once __DIR__ . '/BBDD.php';

/**
 * Modelo de manejo de agresiones.
 *
 * Proporciona operaciones de creación y búsqueda sobre la tabla Agresion.
 */
class AgresionModelo {
    /**
     * Inserta una nueva agresión en la base de datos.
     *
     * Campos obligatorios: id_victima, tipo_agresion, fecha_hora.
     * Los campos opcionales se insertan como NULL si no vienen informados.
     *
     * @param array $datos 
     * @return bool true en caso de éxito, false en caso de error de ejecución.
     */
    public static function crear($datos) {
        $bd = (new BD())->obtenerConexion();
        $sql = "INSERT INTO Agresion (id_victima, agresor, tipo_agresion, fecha_hora, observaciones) VALUES (?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        return $stmt->execute([
            $datos['id_victima'],
            $datos['agresor'] ?? null,
            $datos['tipo_agresion'],
            $datos['fecha_hora'],
            $datos['observaciones'] ?? null
        ]);
    }

    /**
     * Busca agresiones y datos básicos de la víctima relacionada.
     *
     * Realiza un LIKE sobre múltiples columnas para un texto dado y devuelve coincidencias ordenadas por fecha descendente.
     *
     * @param string $texto Texto de búsqueda (se envolverá internamente con %).
     * @return array Lista de resultados. Cada elemento contiene: victima_nombre, victima_apellidos, tipo_agresion, fecha_hora.
     */
    public static function buscar($texto) {
        $bd = (new BD())->obtenerConexion();
        $texto = "%$texto%";
        $sql = "SELECT v.nombre as victima_nombre, v.apellidos as victima_apellidos, a.tipo_agresion, a.fecha_hora 
                FROM Agresion a 
                JOIN Victima v ON a.id_victima = v.id 
                WHERE v.nombre LIKE ? 
                   OR v.apellidos LIKE ? 
                   OR v.documento LIKE ?
                   OR v.telefono LIKE ? 
                   OR v.observaciones LIKE ? 
                   OR a.agresor LIKE ? 
                   OR a.observaciones LIKE ?
                ORDER BY a.fecha_hora DESC";
        
        $stmt = $bd->prepare($sql);
        $stmt->execute([$texto, $texto, $texto, $texto, $texto, $texto, $texto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
