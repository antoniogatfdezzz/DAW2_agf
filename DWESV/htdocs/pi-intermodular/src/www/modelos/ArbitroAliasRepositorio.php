<?php
class ArbitroAliasRepositorio {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function obtenerPorArbitro(int $arbitroId): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return [];
        $st=$pdo->prepare('SELECT id, alias, fecha_creacion FROM arbitro_alias WHERE arbitro_id=? ORDER BY alias');
        $st->execute([$arbitroId]); return $st->fetchAll() ?: [];
    }

    public function listarTodosConAlias(): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return [];
        $sql="SELECT a.id, a.nombre, a.apellidos, GROUP_CONCAT(aa.alias ORDER BY aa.alias SEPARATOR '|') as alias_list, COUNT(aa.id) as total_alias
              FROM arbitros a LEFT JOIN arbitro_alias aa ON a.id=aa.arbitro_id
              WHERE a.nombre != '' OR a.apellidos != ''
              GROUP BY a.id, a.nombre, a.apellidos
              ORDER BY a.apellidos, a.nombre";
        return $pdo->query($sql)->fetchAll() ?: [];
    }

    public function crear(int $arbitroId, string $alias): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['success'=>false,'message'=>'Sin conexión'];
        // Verificar arbitro
        $chk=$pdo->prepare('SELECT id FROM arbitros WHERE id=?'); $chk->execute([$arbitroId]); if(!$chk->fetchColumn()){ return ['success'=>false,'message'=>'Árbitro no encontrado']; }
        // Verificar alias único
        $ck=$pdo->prepare('SELECT id FROM arbitro_alias WHERE alias=?'); $ck->execute([$alias]); if($ck->fetchColumn()){ return ['success'=>false,'message'=>'Alias ya en uso']; }
        $st=$pdo->prepare('INSERT INTO arbitro_alias (arbitro_id, alias) VALUES (?,?)'); $st->execute([$arbitroId,$alias]);
        return ['success'=>true,'id'=>$pdo->lastInsertId()];
    }

    public function eliminar(int $id): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['success'=>false,'message'=>'Sin conexión'];
        $st=$pdo->prepare('DELETE FROM arbitro_alias WHERE id=?'); $st->execute([$id]);
        return ['success'=> $st->rowCount()>0, 'message'=> $st->rowCount()>0? 'Alias eliminado correctamente':'Alias no encontrado'];
    }
}
