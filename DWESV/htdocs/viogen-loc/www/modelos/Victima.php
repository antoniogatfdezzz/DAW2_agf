<?php
class Victima
{
    public static function create(PDO $pdo, array $data)
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

    public static function all(PDO $pdo)
    {
        $stmt = $pdo->query('SELECT * FROM Victima ORDER BY nombre');
        return $stmt->fetchAll();
    }
}
