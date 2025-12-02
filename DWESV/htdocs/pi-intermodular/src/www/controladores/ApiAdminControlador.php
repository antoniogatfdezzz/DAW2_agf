<?php
class ApiAdminControlador {
    public function __construct(
        private UsuarioRepositorio $usuariosRepo = new UsuarioRepositorio(),
        private ArbitroRepositorio $arbitrosRepo = new ArbitroRepositorio(),
        private PartidoRepositorio $partidosRepo = new PartidoRepositorio(),
    ) {
        $this->requerirAdmin();
    }

    private function requerirAdmin(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $usuario = $_SESSION['usuario'] ?? null;
        if (!$usuario || ($usuario['tipo_usuario'] ?? '') !== 'administrador') {
            respuesta_json(['error' => 'No autorizado'], 401);
        }
        if (!empty($usuario['password_temporal'])) {
            respuesta_json(['error' => 'Debe cambiar la contraseña temporal'], 409);
        }
    }

    public function usuarios(): void {
        $data = $this->usuariosRepo->obtenerTodos();
        respuesta_json(['datos' => $data]);
    }

    public function arbitros(): void {
        $data = $this->arbitrosRepo->obtenerTodos();
        respuesta_json(['datos' => $data]);
    }

    public function partidos(): void {
        $data = $this->partidosRepo->obtenerTodos();
        respuesta_json(['datos' => $data]);
    }

    // CRUD Usuarios
    public function crearUsuario(): void {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $res = $this->usuariosRepo->crear($payload);
        $status = $res['ok']?201:400; respuesta_json($res,$status);
    }
    public function actualizarUsuario(int $id): void {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $res = $this->usuariosRepo->actualizar($id,$payload); $status=$res['ok']?200:400; respuesta_json($res,$status);
    }
    public function eliminarUsuario(int $id): void {
        $res = $this->usuariosRepo->eliminar($id); $status=$res['ok']?200:400; respuesta_json($res,$status);
    }

    // CRUD Partidos
    public function crearPartido(): void { $payload=json_decode(file_get_contents('php://input'),true)??[]; $res=$this->partidosRepo->crear($payload); respuesta_json($res,$res['ok']?201:400); }
    public function actualizarPartido(int $id): void { $payload=json_decode(file_get_contents('php://input'),true)??[]; $res=$this->partidosRepo->actualizar($id,$payload); respuesta_json($res,$res['ok']?200:400); }
    public function eliminarPartido(int $id): void { $res=$this->partidosRepo->eliminar($id); respuesta_json($res,$res['ok']?200:400); }

    // CRUD Árbitros (creación vía usuario: tipo arbitro)
    public function crearArbitro(): void {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $payload['tipo_usuario'] = 'arbitro';
        $res = (new UsuarioRepositorio())->crear($payload);
        respuesta_json($res, $res['ok']?201:400);
    }
    public function actualizarArbitro(int $id): void {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        $resArb = $this->arbitrosRepo->actualizar($id, $payload['arbitro'] ?? $payload);
        // Opcional: actualizar usuario asociado
        if (!empty($payload['usuario'])) {
            $arb = $this->arbitrosRepo->obtenerPorId($id);
            if ($arb) { (new UsuarioRepositorio())->actualizar((int)$arb['usuario_id'], $payload['usuario']); }
        }
        respuesta_json($resArb, $resArb['ok']?200:400);
    }
    public function eliminarArbitro(int $id): void {
        $arb = $this->arbitrosRepo->obtenerPorId($id);
        if (!$arb) { respuesta_json(['ok'=>false,'msg'=>'Árbitro no encontrado'],404); return; }
        $res = (new UsuarioRepositorio())->eliminar((int)$arb['usuario_id']);
        respuesta_json($res, $res['ok']?200:400);
    }
}
