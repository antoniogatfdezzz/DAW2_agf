<?php
class ApiResultadosControlador {
    public function __construct(private Conexion $conexion = new Conexion()){
        $this->requerirAdmin();
    }
    private function requerirAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['user_type'] ?? null) !== 'administrador') { respuesta_json(['success'=>false,'message'=>'No autorizado'],401); }
        if (!empty($_SESSION['password_temporal'])) { respuesta_json(['success'=>false,'message'=>'Debe cambiar la contraseña'],409); }
    }

    public function guardar(int $partidoId): void {
        $pdo=$this->conexion->obtenerConexion(); if(!$pdo){ respuesta_json(['success'=>false,'message'=>'Sin conexión BD'],500); }
        try{
            $sets_local = isset($_POST['sets_local']) ? (int)$_POST['sets_local'] : (int)($_POST['setsLocal'] ?? 0);
            $sets_visitante = isset($_POST['sets_visitante']) ? (int)$_POST['sets_visitante'] : (int)($_POST['setsVisitante'] ?? 0);
            if ($sets_local<0 || $sets_visitante<0 || $sets_local>5 || $sets_visitante>5) { throw new Exception('Número de sets inválido'); }
            if ($sets_local === $sets_visitante) { throw new Exception('No puede haber empate en voleibol'); }
            $pdo->beginTransaction();
            // Subida de foto (opcional)
            $foto_nombre = null;
            if (isset($_FILES['foto_resultado']) && $_FILES['foto_resultado']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['foto_resultado']['error'] !== UPLOAD_ERR_OK) { throw new Exception('Error al subir archivo'); }
                $archivo = $_FILES['foto_resultado'];
                $tipos_permitidos=['image/jpeg','image/png','image/gif','image/heic','image/heif'];
                $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
                $ext_ok = in_array($ext,['jpg','jpeg','png','gif','heic','heif']);
                if (!in_array($archivo['type'],$tipos_permitidos) && !$ext_ok) { throw new Exception('Tipo de archivo no permitido'); }
                if ($archivo['size'] > 5*1024*1024) { throw new Exception('Archivo demasiado grande (máx 5MB)'); }
                $foto_nombre = 'resultado_'.$partidoId.'_'.time().'.'.$ext;
                $ruta = __DIR__.'/../assets/uploads/'.$foto_nombre;
                $dir = dirname($ruta); if(!is_dir($dir)) { mkdir($dir,0755,true); }
                if (!move_uploaded_file($archivo['tmp_name'],$ruta)) { throw new Exception('Error al guardar la foto'); }
            }
            // Actualizar partido
            $up=$pdo->prepare('UPDATE partidos SET sets_local=?, sets_visitante=?, foto_resultado=?, estado=\'finalizado\', fecha_actualizacion=NOW() WHERE id=?');
            $up->execute([$sets_local,$sets_visitante,$foto_nombre,$partidoId]);
            // Borrar sets previos
            $pdo->prepare('DELETE FROM sets_partidos WHERE partido_id=?')->execute([$partidoId]);
            // Insertar sets (aceptamos set{i}_local/visitante en POST)
            $totalSets = $sets_local + $sets_visitante;
            for($i=1;$i<=$totalSets;$i++){
                $pl = isset($_POST["set{$i}_local"]) ? (int)$_POST["set{$i}_local"] : null;
                $pv = isset($_POST["set{$i}_visitante"]) ? (int)$_POST["set{$i}_visitante"] : null;
                if ($pl===null || $pv===null) { continue; }
                if ($pl<0 || $pv<0) { throw new Exception("Puntos inválidos en set $i"); }
                $pdo->prepare('INSERT INTO sets_partidos (partido_id, numero_set, puntos_local, puntos_visitante) VALUES (?,?,?,?)')->execute([$partidoId,$i,$pl,$pv]);
            }
            $pdo->commit();
            // TODO: Enviar a API externa vScorer si es necesario (omitir por ahora)
            respuesta_json(['success'=>true,'message'=>'Resultado guardado correctamente']);
        }catch(Throwable $e){ $pdo->rollBack(); respuesta_json(['success'=>false,'message'=>$e->getMessage()],400); }
    }
}
