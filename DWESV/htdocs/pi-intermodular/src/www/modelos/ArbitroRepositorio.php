<?php
class ArbitroRepositorio {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function obtenerTodos(): array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return [];
        $sql = "SELECT id, usuario_id, nombre, apellidos, ciudad, licencia, numero_licencia FROM arbitros ORDER BY id DESC";
        return $pdo->query($sql)->fetchAll() ?: [];
    }

    public function obtenerPorUsuarioId(int $usuarioId): ?array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return null;
        $stmt = $pdo->prepare("SELECT * FROM arbitros WHERE usuario_id=?");
        $stmt->execute([$usuarioId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function obtenerPorId(int $id): ?array {
        $pdo = $this->conexion->obtenerConexion();
        if (!$pdo) return null;
        $stmt = $pdo->prepare("SELECT * FROM arbitros WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function actualizar(int $id, array $data): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        $sets=[];$vals=[];
        foreach(['nombre','apellidos','dni','telefono','ciudad','licencia','numero_licencia','iban'] as $campo){
            if(array_key_exists($campo,$data)){ $sets[]="$campo=?"; $vals[]=$data[$campo]; }
        }
        if(!$sets) return ['ok'=>false,'msg'=>'Nada que actualizar'];
        $vals[]=$id;
        $sql='UPDATE arbitros SET '.implode(',',$sets).' WHERE id=?';
        try{ $pdo->prepare($sql)->execute($vals); return ['ok'=>true]; }catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; }
    }
    public function eliminar(int $id): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['ok'=>false,'msg'=>'Sin conexión'];
        try{ $pdo->prepare('DELETE FROM arbitros WHERE id=?')->execute([$id]); return ['ok'=>true]; }catch(Throwable $e){ return ['ok'=>false,'msg'=>$e->getMessage()]; }
    }
}
