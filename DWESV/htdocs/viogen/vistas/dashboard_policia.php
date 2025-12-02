<?php
require_once __DIR__ . '/../configuración/config.php';
require_auth();
require_once __DIR__ . '/../modelos/victimas.php';
require_once __DIR__ . '/../modelos/agresores.php';
require_once __DIR__ . '/../modelos/policias.php';
$victimas = victimas_all();
$agresores = agresores_all();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Dashboard Policía</title>
  <link rel="stylesheet" href="../assets/css/styles.css"/>
</head>
<body>
<?php $auth = auth_user(); ?>
<div class="header"><div><img src="../assets/img/viogen.png" alt="VioGén" style="height:60px;vertical-align:middle;margin-right:10px" /> Panel Viogen</div><div><?php echo h($auth['nombre'] ?? ''); ?></div></div>
<div class="container">
  <aside class="sidebar">
    <h3>Navegación</h3>
    <nav class="nav">
  <a href="form-victimas.php" class="btn-nueva-denuncia">+ Nueva denuncia</a>
      <a href="lista-victimas.php">Víctimas</a>
      <a href="lista-agresores.php">Agresores</a>
      <a href="../logout.php" class="btn danger" style="margin-top:8px">Cerrar sesión</a>
    </nav>
  </aside>
  <main class="main">
    <div class="grid cols-3">
      <div class="card"><h3>Nuevas denuncias</h3><div class="badge">5</div></div>
      <div class="card"><h3>Víctimas registradas</h3><div class="badge"><?php echo count($victimas); ?></div></div>
      <div class="card"><h3>Agresores registrados</h3><div class="badge"><?php echo count($agresores); ?></div></div>
    </div>
    
  </main>
</div>
</body>
</html>
