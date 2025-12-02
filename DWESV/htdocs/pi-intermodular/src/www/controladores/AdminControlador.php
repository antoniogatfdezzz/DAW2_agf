<?php
class AdminControlador {
    public function __construct(
        private UsuarioRepositorio $usuariosRepo = new UsuarioRepositorio(),
        private ArbitroRepositorio $arbitrosRepo = new ArbitroRepositorio(),
        private PartidoRepositorio $partidosRepo = new PartidoRepositorio(),
    ) {
        $this->requerirTipo('administrador');
    }

    public function dashboard(): void { vista('admin/dashboard'); }
    public function usuarios(): void { vista('admin/usuarios', ['usuarios' => $this->usuariosRepo->obtenerTodos()]); }
    public function arbitros(): void { vista('admin/arbitros', ['arbitros' => $this->arbitrosRepo->obtenerTodos()]); }
    public function partidos(): void { vista('admin/partidos', ['partidos' => $this->partidosRepo->obtenerTodos()]); }
    public function licencias(): void { vista('admin/licencias'); }
    public function liquidaciones(): void { vista('admin/liquidaciones'); }
    public function perfil(): void { vista('admin/perfil'); }

    private function requerirTipo(string $tipo): void {
        if (empty($_SESSION['user_id'])) { header('Location: /'); exit; }
        if (($_SESSION['user_type'] ?? null) !== $tipo) { header('Location: /unauthorized'); exit; }
        if (!empty($_SESSION['password_temporal'])) { header('Location: /auth/cambiar-password'); exit; }
    }
}
