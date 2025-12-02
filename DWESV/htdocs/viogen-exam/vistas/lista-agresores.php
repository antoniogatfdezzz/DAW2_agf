<?php
require_once __DIR__ . '/../configuracion/config.php';
require_auth();
require_once __DIR__ . '/../modelos/agresores.php';
$q = trim((string)($_GET['q'] ?? ''));
$agresores = $q !== '' ? agresores_find_by_name($q) : agresores_all();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Agresores</title>
  <link rel="stylesheet" href="../assets/css/styles.css"/>
</head>
<body>
<style>
  .table th, .table td {
    text-align: center;
    vertical-align: middle;
  }
</style>
<?php $auth = auth_user(); ?>
<div class="header"><div><img src="../assets/img/viogen.png" alt="VioGén" style="height:60px;vertical-align:middle;margin-right:10px" /> Panel Viogen</div><div><?php echo h($auth['nombre'] ?? ''); ?></div></div>
<div class="container">
  <aside class="sidebar">
    <h3>Navegación</h3>
    <nav class="nav">
  <a href="form-victimas.php" class="btn-nueva-denuncia">+ Nueva denuncia</a>
      <a href="dashboard_policia.php">Dashboard</a>
      <a href="lista-victimas.php">Víctimas</a>
      <a href="lista-agresores.php">Agresores</a>
      <a href="../logout.php" class="btn danger" style="margin-top:8px">Cerrar sesión</a>
    </nav>
  </aside>
  <main class="main">
    <div class="card">
      <form class="search" method="get" action="">
        <input class="input" name="q" value="<?php echo h($q); ?>" placeholder="Buscar por nombre o nº documento..." style="max-width: 1250px;"/>
        <button class="btn secondary" type="submit">Buscar</button>
      </form>
    </div>
    <div class="card">
      <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Documento</th><th>Edad</th></tr></thead>
        <tbody>
        <?php foreach($agresores as $a): ?>
          <tr class="agresor-row">
            <td><?php echo (int)$a['id']; ?></td>
            <td><?php echo h(($a['agresor_nombre']??'') . ' ' . ($a['agresor_apellidos']??'')); ?></td>
            <td><?php echo h(($a['agresor_tipo_documento']??'') . ' ' . ($a['agresor_num_documento']??'')); ?></td>
            <td><?php echo h($a['agresor_edad'] ?? ''); ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
