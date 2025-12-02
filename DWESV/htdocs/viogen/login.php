<?php
require __DIR__ . '/configuración/config.php';
require_once __DIR__ . '/modelos/policias.php';
$err = null;
if (is_post()) {
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    $policias = policias_all();
    foreach ($policias as $p) {
        if (strcasecmp($p['email'] ?? '', $email) === 0 && password_verify($pass, $p['password'] ?? '')) {
      auth_set(['id'=>$p['id'], 'nombre'=>$p['nombre'] ?? '', 'email'=>$p['email']]);
      redirect('vistas/dashboard_policia.php');
        }
    }
    $err = 'Credenciales inválidas';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Acceso Viogen</title>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body>
  <div class="container" style="display:block;max-width:480px;margin:10vh auto">
    <div class="card">
      <div style="text-align:center;margin-bottom:20px">
        <img src="assets/img/viogen.png" alt="VioGén" style="max-width:280px;height:auto" />
      </div>
      <h2>Acceso Policía</h2>
      <?php if($err): ?><div class="alert error"><?php echo h($err); ?></div><?php endif; ?>
      <form method="post">
        <div class="form-row">
          <label>Email</label>
          <input class="input" type="email" name="email" required />
        </div>
        <div class="form-row">
          <label>Contraseña</label>
          <input class="input" type="password" name="password" required />
        </div>
        <button class="btn" type="submit">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>
