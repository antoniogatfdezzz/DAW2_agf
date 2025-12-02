<?php
class Agresion
{
    public static function create(PDO $pdo, array $data)
    {
        $sql = 'INSERT INTO Agresion (id_victima, agresor, tipo_agresion, fecha_hora, observaciones) VALUES (:id_victima, :agresor, :tipo_agresion, :fecha_hora, :observaciones)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_victima' => $data['id_victima'],
            ':agresor' => $data['agresor'] ?? null,
            ':tipo_agresion' => $data['tipo_agresion'],
            ':fecha_hora' => $data['fecha_hora'],
            ':observaciones' => $data['observaciones'] ?? null,
        ]);
        return $pdo->lastInsertId();
    }

    public static function search(PDO $pdo, string $term)
    {
        $like = '%' . $term . '%';
        $sql = "SELECT A.id, V.nombre, V.apellidos, A.tipo_agresion, A.fecha_hora, A.observaciones AS obs_agresion
                FROM Agresion A
                JOIN Victima V ON A.id_victima = V.id
                WHERE V.nombre LIKE :like
                   OR V.apellidos LIKE :like
                   OR V.telefono LIKE :like
                   OR V.observaciones LIKE :like
                   OR A.agresor LIKE :like
                   OR A.observaciones LIKE :like
                ORDER BY A.fecha_hora DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':like' => $like]);
        return $stmt->fetchAll();
    }
}
