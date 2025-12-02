<?php
class ApiArbitroAliasControlador {
    public function __construct(private ArbitroAliasRepositorio $repo = new ArbitroAliasRepositorio()){
        $this->requerirAdmin();
    }
    private function requerirAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['user_type'] ?? null) !== 'administrador') { respuesta_json(['success'=>false,'message'=>'No autorizado'],401); }
        if (!empty($_SESSION['password_temporal'])) { respuesta_json(['success'=>false,'message'=>'Debe cambiar la contraseña'],409); }
    }

    public function manejar(): void {
        $m = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($m === 'GET') {
            if (!empty($_GET['arbitro_id'])) { $aid=(int)$_GET['arbitro_id']; respuesta_json(['success'=>true,'alias'=>$this->repo->obtenerPorArbitro($aid)]); return; }
            respuesta_json(['success'=>true,'arbitros'=>$this->repo->listarTodosConAlias()]); return;
        }
        if ($m === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            if (!isset($data['arbitro_id'],$data['alias'])) { respuesta_json(['success'=>false,'message'=>'Faltan datos requeridos'],400); }
            $alias = trim((string)$data['alias']); if ($alias===''){ respuesta_json(['success'=>false,'message'=>'El alias no puede estar vacío'],400); }
            $res=$this->repo->crear((int)$data['arbitro_id'],$alias); respuesta_json($res,$res['success']?201:400); return;
        }
        if ($m === 'DELETE') {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; if($id<=0){ respuesta_json(['success'=>false,'message'=>'ID inválido'],400); }
            $res=$this->repo->eliminar($id); respuesta_json($res,$res['success']?200:404); return;
        }
        respuesta_json(['success'=>false,'message'=>'Método no permitido'],405);
    }
}
