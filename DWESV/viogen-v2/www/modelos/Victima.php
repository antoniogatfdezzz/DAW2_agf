<?php
/**
 * Modelo de víctimas.
 * @package Modelos
 */
class Victima
{
    /**
     * Crea víctima.
     * @param PDO $pdo Conexión
     * @param array $data Datos sanitizados
     * @return int|string ID generado
     */
    public static function crear(PDO $pdo, array $data)
    {
        $sql = 'INSERT INTO Victima (nombre, apellidos, tipo_documento, documento, telefono, observaciones) VALUES (:nombre, :apellidos, :tipo_documento, :documento, :telefono, :observaciones)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'] ?? null,
            ':apellidos' => $data['apellidos'] ?? null,
            ':tipo_documento' => $data['tipo_documento'] ?? null,
            ':documento' => $data['documento'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':observaciones' => $data['observaciones'] ?? null,
        ]);
        return $pdo->lastInsertId();
    }

    /**
     * Lista todas las víctimas.
     * @param PDO $pdo Conexión
     * @return array Víctimas
     */
    public static function todas(PDO $pdo)
    {
        $stmt = $pdo->query('SELECT * FROM Victima ORDER BY nombre');
        return $stmt->fetchAll();
    }
}
