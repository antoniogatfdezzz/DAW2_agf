<?php
class DisponibilidadRepositorio {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function obtenerPorFecha(string $fecha): array {
        $pdo = $this->conexion->obtenerConexion(); if(!$pdo) return [];
        $sql = "SELECT a.id, a.nombre, a.apellidos,
                       CONCAT(a.nombre,' ',a.apellidos) AS nombre_completo,
                       COALESCE(da.manana,0) AS manana,
                       COALESCE(da.tarde,0) AS tarde,
                       da.observacion_manana,
                       da.observacion_tarde,
                       a.licencia,
                       a.ciudad
                FROM arbitros a
                INNER JOIN usuarios u ON a.usuario_id = u.id
                LEFT JOIN disponibilidad_arbitros da ON a.id = da.arbitro_id AND da.fecha = ?
                WHERE u.activo = 1
                ORDER BY a.nombre, a.apellidos";
        $st = $pdo->prepare($sql); $st->execute([$fecha]);
        return $st->fetchAll() ?: [];
    }

    public function obtenerRango(int $arbitroId, string $inicio, string $fin): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return [];
        $st=$pdo->prepare("SELECT fecha, COALESCE(manana,0) manana, COALESCE(tarde,0) tarde, 
                                   COALESCE(observacion_manana,'') observacion_manana,
                                   COALESCE(observacion_tarde,'') observacion_tarde
                            FROM disponibilidad_arbitros WHERE arbitro_id=? AND fecha BETWEEN ? AND ? ORDER BY fecha");
        $st->execute([$arbitroId,$inicio,$fin]);
        return $st->fetchAll() ?: [];
    }

    public function upsert(int $arbitroId, string $fecha, int $manana, int $tarde, string $obs): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        // comprobar existencia
        $ck=$pdo->prepare('SELECT id FROM disponibilidad_arbitros WHERE arbitro_id=? AND fecha=?');
        $ck->execute([$arbitroId,$fecha]);
        if($ck->fetch()){
            $st=$pdo->prepare('UPDATE disponibilidad_arbitros SET manana=?,tarde=?,observacion_manana=?,observacion_tarde=? WHERE arbitro_id=? AND fecha=?');
            $st->execute([$manana,$tarde,$manana? $obs:'',$tarde? $obs:'',$arbitroId,$fecha]);
            return ['ok'=>true,'action'=>'update'];
        }
        $st=$pdo->prepare('INSERT INTO disponibilidad_arbitros (arbitro_id,fecha,manana,tarde,observacion_manana,observacion_tarde) VALUES (?,?,?,?,?,?)');
        $st->execute([$arbitroId,$fecha,$manana,$tarde,$manana? $obs:'',$tarde? $obs:'']);
        return ['ok'=>true,'action'=>'insert'];
    }

    public function eliminar(int $arbitroId, string $fecha): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        $st=$pdo->prepare('DELETE FROM disponibilidad_arbitros WHERE arbitro_id=? AND fecha=?');
        $st->execute([$arbitroId,$fecha]);
        return ['ok'=> $st->rowCount()>0];
    }
}
