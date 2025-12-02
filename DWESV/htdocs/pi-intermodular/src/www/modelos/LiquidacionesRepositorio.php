<?php
class LiquidacionesRepositorio {
    public function __construct(private Conexion $conexion = new Conexion()) {}

    public function obtenerDetalle(int $id): ?array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return null;
        $q = "SELECT l.*, DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y') as fecha_inicio, DATE_FORMAT(l.fecha_fin, '%d/%m/%Y') as fecha_fin,
                     CONCAT(a.nombre,' ',a.apellidos) as arbitro_nombre
              FROM liquidaciones l LEFT JOIN arbitros a ON l.arbitro_id=a.id WHERE l.id=?";
        $st=$pdo->prepare($q); $st->execute([$id]); $liq=$st->fetch(); if(!$liq) return null;
        $q2 = "SELECT lp.*, DATE_FORMAT(p.fecha, '%d/%m/%Y %H:%i') as fecha,
                       CONCAT(COALESCE(p.equipo_local,'Equipo Local'),' vs ',COALESCE(p.equipo_visitante,'Equipo Visitante')) as equipos,
                       c.nombre as categoria_nombre
               FROM liquidaciones_partidos lp
               LEFT JOIN partidos p ON lp.partido_id=p.id
               LEFT JOIN categorias c ON p.categoria_id=c.id
               WHERE lp.liquidacion_id=? ORDER BY p.fecha";
        $st2=$pdo->prepare($q2); $st2->execute([$id]); $liq['partidos']=$st2->fetchAll() ?: [];
        $total=0.0; foreach($liq['partidos'] as $p){ $total += (float)($p['importe_partido']??0) + (float)($p['importe_dieta']??0) + (float)($p['importe_kilometraje']??0); }
        $liq['total_importe'] = number_format($total,2);
        return $liq;
    }

    public function actualizarImportes(int $liquidacionId, array $importes): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return ['success'=>false,'error'=>'Sin conexión'];
        try{
            $pdo->beginTransaction();
            $st=$pdo->prepare('SELECT id FROM liquidaciones_partidos WHERE liquidacion_id=? ORDER BY id');
            $st->execute([$liquidacionId]);
            $ids = $st->fetchAll(PDO::FETCH_COLUMN) ?: [];
            foreach($importes as $idx=>$imp){
                if (!isset($ids[$idx])) continue;
                $u=$pdo->prepare('UPDATE liquidaciones_partidos SET importe_partido=?, importe_dieta=?, importe_kilometraje=? WHERE id=?');
                $u->execute([(float)($imp['partido']??0),(float)($imp['dieta']??0),(float)($imp['kilometraje']??0),$ids[$idx]]);
            }
            $pdo->commit(); return ['success'=>true];
        }catch(Throwable $e){ $pdo->rollBack(); return ['success'=>false,'error'=>$e->getMessage()]; }
    }

    public function listarRectificaciones(): array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return [];
        $q="SELECT r.*, CONCAT(COALESCE(a.nombre,''),' ',COALESCE(a.apellidos,'')) as arbitro_nombre,
                    DATE_FORMAT(r.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                    DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta,
                    CONCAT('Del ', DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y'),' al ',DATE_FORMAT(l.fecha_fin, '%d/%m/%Y')) as periodo_liquidacion
           FROM rectificaciones_liquidaciones r
           LEFT JOIN arbitros a ON r.arbitro_id=a.id
           LEFT JOIN liquidaciones l ON r.liquidacion_id=l.id
           ORDER BY r.fecha_solicitud DESC";
        return $pdo->query($q)->fetchAll() ?: [];
    }

    public function obtenerRectificacion(int $id): ?array {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo) return null;
        $q="SELECT r.*, CONCAT(COALESCE(a.nombre,''),' ',COALESCE(a.apellidos,'')) as arbitro_nombre,
                    DATE_FORMAT(r.fecha_solicitud, '%d/%m/%Y %H:%i') as fecha_solicitud,
                    DATE_FORMAT(r.fecha_respuesta, '%d/%m/%Y %H:%i') as fecha_respuesta,
                    CONCAT('Del ', DATE_FORMAT(l.fecha_inicio, '%d/%m/%Y'),' al ',DATE_FORMAT(l.fecha_fin, '%d/%m/%Y')) as periodo_liquidacion
           FROM rectificaciones_liquidaciones r
           LEFT JOIN arbitros a ON r.arbitro_id=a.id
           LEFT JOIN liquidaciones l ON r.liquidacion_id=l.id
           WHERE r.id = ?";
        $st=$pdo->prepare($q); $st->execute([$id]); $res=$st->fetch(); return $res?:null;
    }
}
