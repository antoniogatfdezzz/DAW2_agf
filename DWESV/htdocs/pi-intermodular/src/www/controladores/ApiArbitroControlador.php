<?php
class ApiArbitroControlador {
    public function __construct(
        private ArbitroRepositorio $arbitrosRepo = new ArbitroRepositorio(),
        private PartidoRepositorio $partidosRepo = new PartidoRepositorio(),
    ) {
        $this->requerirArbitro();
    }

    private function requerirArbitro(): void {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $usuario = $_SESSION['usuario'] ?? null;
        if (!$usuario || ($usuario['tipo_usuario'] ?? '') !== 'arbitro') {
            respuesta_json(['error' => 'No autorizado'], 401);
        }
        if (!empty($usuario['password_temporal'])) {
            respuesta_json(['error' => 'Debe cambiar la contraseña temporal'], 409);
        }
    }

    public function misPartidos(): void {
        $usuarioId = $_SESSION['usuario']['id'] ?? null;
        $arbitro = $usuarioId ? $this->arbitrosRepo->obtenerPorUsuarioId((int)$usuarioId) : null;
        $partidos = $arbitro ? $this->partidosRepo->obtenerPorArbitroId((int)$arbitro['id']) : [];
        respuesta_json(['datos' => $partidos]);
    }
}
