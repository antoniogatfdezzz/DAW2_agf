<?php
class ApiLiquidacionesControlador {
    public function __construct(private LiquidacionesRepositorio $repo = new LiquidacionesRepositorio()){
        $this->requerirAdmin();
    }
    private function requerirAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['user_type'] ?? null) !== 'administrador') { respuesta_json(['error'=>'No autorizado'],401); }
        if (!empty($_SESSION['password_temporal'])) { respuesta_json(['error'=>'Debe cambiar la contraseña'],409); }
    }

    public function manejar(): void {
        $m = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($m==='GET') {
            if (!empty($_GET['id'])) { $id=(int)$_GET['id']; $det=$this->repo->obtenerDetalle($id); if(!$det){ respuesta_json(['error'=>'Liquidación no encontrada'],404);} else { respuesta_json($det); } return; }
            if (!empty($_GET['action']) && $_GET['action']==='rectificaciones') { respuesta_json($this->repo->listarRectificaciones()); return; }
            if (!empty($_GET['action']) && $_GET['action']==='rectificacion_detalle' && !empty($_GET['id'])) { $det=$this->repo->obtenerRectificacion((int)$_GET['id']); if(!$det){ respuesta_json(['error'=>'Rectificación no encontrada'],404);} else { respuesta_json($det); } return; }
            respuesta_json(['error'=>'Acción no válida'],400); return;
        }
        if ($m==='POST') {
            $input=json_decode(file_get_contents('php://input'), true) ?? [];
            if (($input['action'] ?? '') === 'actualizar_importes') {
                $lid=(int)($input['liquidacion_id'] ?? 0); $imp=$input['importes'] ?? [];
                if ($lid<=0 || !is_array($imp)) { respuesta_json(['success'=>false,'error'=>'Datos inválidos'],400); }
                $res=$this->repo->actualizarImportes($lid,$imp); respuesta_json($res, $res['success']?200:400); return;
            }
            respuesta_json(['success'=>false,'error'=>'Acción no válida'],400); return;
        }
        respuesta_json(['error'=>'Método no permitido'],405);
    }
}
