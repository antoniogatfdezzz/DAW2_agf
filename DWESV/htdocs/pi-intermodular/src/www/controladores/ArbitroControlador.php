<?php
class ArbitroControlador {
    public function __construct(
        private ArbitroRepositorio $arbitrosRepo = new ArbitroRepositorio(),
        private PartidoRepositorio $partidosRepo = new PartidoRepositorio(),
    ) {
        $this->requerirTipo('arbitro');
    }

    public function dashboard(): void { vista('arbitro/dashboard'); }
    public function disponibilidad(): void { vista('arbitro/disponibilidad'); }
    public function liquidaciones(): void { vista('arbitro/liquidaciones'); }
    public function partidos(): void {
        $usuarioId = $_SESSION['user_id'] ?? null;
        $arbitro = $usuarioId ? $this->arbitrosRepo->obtenerPorUsuarioId((int)$usuarioId) : null;
        $partidos = $arbitro ? $this->partidosRepo->obtenerPorArbitroId((int)$arbitro['id']) : [];
        vista('arbitro/partidos', ['partidos' => $partidos]);
    }
    public function perfil(): void { vista('arbitro/perfil'); }

    private function requerirTipo(string $tipo): void {
        if (empty($_SESSION['user_id'])) { header('Location: /'); exit; }
        if (($_SESSION['user_type'] ?? null) !== $tipo) { header('Location: /unauthorized'); exit; }
        if (!empty($_SESSION['password_temporal'])) { header('Location: /auth/cambiar-password'); exit; }
    }
}
