<?php
require_once __DIR__ . '/../configuracion/config.php';
require_auth();

$tipos = ['violencia física','psicológica','sexual','económica','patrimonial','control','amenazas','stalking','otros'];
$mensaje = null; $errores = [];

$datosVictima = [];
foreach ($_POST as $key => $value) {
  if (strpos($key, 'victima_') === 0) {
    $datosVictima[$key] = $value;
  }
}

$old = array_merge($_POST, $_GET);

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Datos de la denuncia</title>
  <link rel="stylesheet" href="../assets/css/styles.css" />
</head>
<body>
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
    <?php if($mensaje): ?><div class="alert ok"><?php echo h($mensaje); ?></div><?php endif; ?>
    <?php if($errores): ?><div class="alert error">Revisa los campos obligatorios.</div><?php endif; ?>

    <div class="card">
      <h3>Paso 2: Datos de la denuncia</h3>
      <?php if(!empty($datosVictima['victima_nombre'])): ?>
        <p><strong>Víctima:</strong> <?php echo h($datosVictima['victima_nombre'].' '.$datosVictima['victima_apellidos']); ?></p>
      <?php else: ?>
        <div class="alert error">No hay datos de víctima. <a href="form-victimas.php">Volver al paso 1</a>.</div>
      <?php endif; ?>
    </div>

  <form id="denuncia-form" method="post" enctype="multipart/form-data">
  <?php
    $state = $old;
    $denunciaKeys = ['tipo_hecho','fecha_hecho','hora_aproximada','lugar_hecho','descripcion_hechos','lesiones_presentes','urgente_intervencion','ha_presentado_denuncia_antes_por_mismo_hecho','testigos','observaciones_hecho','importancia_check'];
    foreach ($state as $hk => $hv):
      $isDenuncia = in_array($hk, $denunciaKeys, true);
      if ($isDenuncia) continue;
      if (is_array($hv)):
        foreach ($hv as $hvv): ?>
          <input type="hidden" name="<?php echo h($hk); ?>[]" value="<?php echo h($hvv); ?>" />
        <?php endforeach;
      else: ?>
        <input type="hidden" name="<?php echo h($hk); ?>" value="<?php echo h($hv); ?>" />
      <?php endif;
    endforeach; ?>
      <?php ?>
        <div class="card">
        <h3>Hecho denunciado</h3>
        <div class="grid cols-3">
          <div class="form-row" style="grid-column:1/-1"><label>Tipo de hecho*</label>
            <div style="display:flex;flex-wrap:wrap;gap:8px">
              <?php foreach($tipos as $t): ?>
                <label class="badge"><input type="radio" name="tipo_hecho" value="<?php echo h($t); ?>" required <?php echo ((($old['tipo_hecho'] ?? '') === $t) ? 'checked' : ''); ?> /> <?php echo h($t); ?></label>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="form-row"><label>Fecha del hecho*</label><input class="input" type="date" name="fecha_hecho" required value="<?php echo h($old['fecha_hecho'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Hora aproximada</label><input class="input" type="time" name="hora_aproximada" value="<?php echo h($old['hora_aproximada'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Lugar del hecho</label><input class="input" name="lugar_hecho" value="<?php echo h($old['lugar_hecho'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Descripción de los hechos*</label><textarea class="input" name="descripcion_hechos" rows="6" required><?php echo h($old['descripcion_hechos'] ?? ''); ?></textarea></div>
          <div class="form-row"><label>¿Lesiones presentes?</label>
            <select class="input" name="lesiones_presentes"><option value="no" <?php echo ((($old['lesiones_presentes'] ?? '')==='no')? 'selected':''); ?>>No</option><option value="si" <?php echo ((($old['lesiones_presentes'] ?? '')==='si')? 'selected':''); ?>>Sí</option></select>
          </div>
          <div class="form-row"><label>¿Intervención urgente?</label>
            <select class="input" name="urgente_intervencion"><option value="no" <?php echo ((($old['urgente_intervencion'] ?? '')==='no')? 'selected':''); ?>>No</option><option value="si" <?php echo ((($old['urgente_intervencion'] ?? '')==='si')? 'selected':''); ?>>Sí</option></select>
          </div>
          <div class="form-row" style="grid-column:1/-1"><label>¿Denuncia previa por mismo hecho?</label>
            <select class="input" name="ha_presentado_denuncia_antes_por_mismo_hecho"><option value="no" <?php echo ((($old['ha_presentado_denuncia_antes_por_mismo_hecho'] ?? '')==='no')? 'selected':''); ?>>No</option><option value="si" <?php echo ((($old['ha_presentado_denuncia_antes_por_mismo_hecho'] ?? '')==='si')? 'selected':''); ?>>Sí</option></select>
          </div>
          <div class="form-row" style="grid-column:1/-1"><label>Testigos (1 por línea)</label><textarea class="input" name="testigos" rows="3"><?php echo h($old['testigos'] ?? ''); ?></textarea></div>
          <div class="form-row" style="grid-column:1/-1"><label>Observaciones del hecho</label><textarea class="input" name="observaciones_hecho" rows="3"><?php echo h($old['observaciones_hecho'] ?? ''); ?></textarea></div>
        </div>
      </div>
      <div class="card">
        <h3>Importancia con CheckBoxes</h3>
        <?php $selImp = $old['importancia_check'] ?? []; if(!is_array($selImp)) { $selImp = [$selImp]; } ?>
        <div class="grid cols-3" style="row-gap:10px">
          <label class="badge"><input type="checkbox" name="importancia_check[]" value="3" <?php echo in_array('3', array_map('strval', $selImp), true)?'checked':''; ?> /> α Alfa (3)</label>
          <label class="badge"><input type="checkbox" name="importancia_check[]" value="5" <?php echo in_array('5', array_map('strval', $selImp), true)?'checked':''; ?> /> β Beta (5)</label>
          <label class="badge"><input type="checkbox" name="importancia_check[]" value="8" <?php echo in_array('8', array_map('strval', $selImp), true)?'checked':''; ?> /> γ Gamma (8)</label>
          <label class="badge"><input type="checkbox" name="importancia_check[]" value="13" <?php echo in_array('13', array_map('strval', $selImp), true)?'checked':''; ?> /> δ Delta (13)</label>
          <label class="badge"><input type="checkbox" name="importancia_check[]" value="21" <?php echo in_array('21', array_map('strval', $selImp), true)?'checked':''; ?> /> ε Épsilon (21)</label>
        </div>
      </div>
      <div class="card">
        <button class="btn secondary" type="submit" formaction="form-victimas.php" formmethod="post" formnovalidate name="_back" value="1">← Volver</button>
        <button class="btn" type="submit" formaction="form-agresor.php" formmethod="post">Continuar con datos del agresor →</button>
      </div>
    </form>
  </main>
</div>
</body>
</html>
