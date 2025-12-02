<?php
class PartidoRepositorio {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function obtenerTodos(): array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return [];
        $sql = "SELECT p.id, p.fecha, p.equipo_local, p.equipo_visitante, p.estado, c.nombre AS categoria
                FROM partidos p JOIN categorias c ON p.categoria_id = c.id
                ORDER BY p.fecha DESC";
        return $pdo->query($sql)->fetchAll() ?: [];
    }

    public function obtenerPorArbitroId(int $arbitroId): array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return [];
        $sql = "SELECT p.id, p.fecha, p.equipo_local, p.equipo_visitante, p.estado, c.nombre AS categoria,
                       CASE WHEN p.arbitro_principal_id = :aid THEN 'Principal'
                            WHEN p.arbitro_segundo_id = :aid THEN 'Segundo'
                            WHEN p.anotador_id = :aid THEN 'Anotador'
                            ELSE '' END AS rol
                FROM partidos p JOIN categorias c ON p.categoria_id = c.id
                WHERE p.arbitro_principal_id = :aid OR p.arbitro_segundo_id = :aid OR p.anotador_id = :aid
                ORDER BY p.fecha DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':aid' => $arbitroId]);
        return $stmt->fetchAll() ?: [];
    }

    public function crear(array $data): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        $req=['equipo_local','equipo_visitante','categoria_id','fecha'];
        foreach($req as $r){ if(empty($data[$r])) return ['ok'=>false,'msg'=>"Falta $r"]; }
        $stmt=$pdo->prepare('INSERT INTO partidos (equipo_local,equipo_visitante,categoria_id,fecha,estado,pabellon_nombre) VALUES (?,?,?,?,?,?)');
        try{ $stmt->execute([$data['equipo_local'],$data['equipo_visitante'],$data['categoria_id'],$data['fecha'],$data['estado']??'programado',$data['pabellon_nombre']??'']); return ['ok'=>true,'id'=>$pdo->lastInsertId()]; }catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; }
    }
    public function actualizar(int $id,array $data): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        $sets=[];$vals=[];
        foreach(['equipo_local','equipo_visitante','categoria_id','fecha','estado','pabellon_nombre','arbitro_principal_id','arbitro_segundo_id','anotador_id'] as $c){
            if(array_key_exists($c,$data)){ $sets[]="$c=?"; $vals[]=$data[$c]; }
        }
        if(!$sets) return ['ok'=>false,'msg'=>'Nada que actualizar'];
        $vals[]=$id; $sql='UPDATE partidos SET '.implode(',',$sets).' WHERE id=?';
        try{ $pdo->prepare($sql)->execute($vals); return ['ok'=>true]; }catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; }
    }
    public function eliminar(int $id): array { $pdo=$this->conexion->obtenerConexion(); if(!$pdo)return ['ok'=>false,'msg'=>'Sin conexión']; try{ $pdo->prepare('DELETE FROM partidos WHERE id=?')->execute([$id]); return ['ok'=>true]; }catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; } }
}
