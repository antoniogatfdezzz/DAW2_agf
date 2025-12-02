<?php
require_once __DIR__ . '/../configuracion/config.php';
require_auth();

$datosVictima = [];
$datosDenuncia = [];
$datosAgresor = [];

$source = !empty($_GET) ? $_GET : $_POST;

foreach ($source as $key => $value) {
  if (strpos($key, 'victima_') === 0) {
    $datosVictima[$key] = $value;
  } elseif (strpos($key, 'agresor_') === 0) {
    $datosAgresor[$key] = $value;
  } elseif (in_array($key, ['tipo_hecho','fecha_hecho','hora_aproximada','lugar_hecho',
                 'descripcion_hechos','lesiones_presentes','urgente_intervencion',
                 'ha_presentado_denuncia_antes_por_mismo_hecho','testigos','observaciones_hecho','importancia_check'])) {
    $datosDenuncia[$key] = $value;
  }
}


$guardado = false;
if (is_post() && isset($_POST['confirmar_guardado'])) {
    $all = $_POST;
    $record = [
      'id' => uniqid('den_', true),
      'created_at' => date('c'),
      'policia' => auth_user(),
      'data' => $all
    ];
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) @mkdir($dataDir, 0755, true);
    $file = $dataDir . '/denuncias.json';
    $list = [];
    if (file_exists($file)) {
      $txt = @file_get_contents($file);
      $list = $txt ? json_decode($txt, true) ?? [] : [];
    }
    $list[] = $record;
    @file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $guardado = true;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Resumen de Denuncia</title>
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
    <?php if($guardado): ?>
      <div class="alert ok">✓ Denuncia procesada correctamente. <a href="form-victimas.php">Nueva denuncia</a> | <a href="dashboard_policia.php">Volver al dashboard</a></div>
    <?php endif; ?>

    <h2>Resumen completo de la denuncia</h2>
    <?php
      $tipoSel = mb_strtolower(trim($datosDenuncia['tipo_hecho'] ?? ''));
      $mucha = ['violencia física','sexual','amenazas'];
      $media = ['psicológica','patrimonial','control','stalking'];
      $poca  = ['económica','otros'];
      $importancia = null; $color = null; $bgSoft = null; $border = null;
      if ($tipoSel !== '') {
        if (in_array($tipoSel, $mucha, true)) { $importancia = 'ALTA'; $color = '#ef4444'; $bgSoft = 'rgba(239,68,68,.15)'; }
        elseif (in_array($tipoSel, $media, true)) { $importancia = 'MEDIA'; $color = '#f97316'; $bgSoft = 'rgba(249,115,22,.15)'; }
        elseif (in_array($tipoSel, $poca, true)) { $importancia = 'BAJA'; $color = '#f59e0b'; $bgSoft = 'rgba(245,158,11,.15)'; }
        if ($importancia) { $border = '6px solid '.$color; }
      }
      // Cálculo de puntuación por checkboxes y nivel (EXTREMO/ALTO/MEDIO/BAJO)
      $impSel = $datosDenuncia['importancia_check'] ?? [];
      if (!is_array($impSel)) { $impSel = [$impSel]; }
      $impSel = array_map('intval', $impSel);
      $sumaImp = array_sum($impSel);
      $map = [3=>'α Alfa',5=>'β Beta',8=>'γ Gamma',13=>'δ Delta',21=>'ε Épsilon'];
      $labels = [];
      foreach ($impSel as $v) { if (isset($map[$v])) { $labels[] = $map[$v]; } }
      // Umbrales asumidos: BAJO 0-7, MEDIO 8-15, ALTO 16-25, EXTREMO 26+
      $nivelRiesgo = 'BAJO';
      $nivelColor = '#10b981'; // verde
      $nivelBg    = 'rgba(16,185,129,.15)';
      if ($sumaImp >= 26) { $nivelRiesgo = 'EXTREMO'; $nivelColor = '#991b1b'; $nivelBg = 'rgba(153,27,27,.15)'; }
      elseif ($sumaImp >= 16) { $nivelRiesgo = 'ALTO'; $nivelColor = '#ef4444'; $nivelBg = 'rgba(239,68,68,.15)'; }
      elseif ($sumaImp >= 8) { $nivelRiesgo = 'MEDIO'; $nivelColor = '#f59e0b'; $nivelBg = 'rgba(245,158,11,.15)'; }
    ?>
    <!-- Card principal: Nivel (por checkboxes) y motivo principal, más la puntuación total -->
    <div class="card">
      <h3>✅ Nivel y puntuación</h3>
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="display:inline-block;padding:4px 10px;border-radius:999px;border:1px solid <?php echo h($nivelColor); ?>;background:<?php echo h($nivelBg); ?>;color:#fff;font-weight:700;min-width:95px;text-align:center;">
          <?php echo h($nivelRiesgo); ?>
        </span>
        <span class="muted">Motivo Principal: <strong><?php echo h($datosDenuncia['tipo_hecho'] ?? ''); ?></strong></span>
      </div>
      <div style="margin-top:10px">
        <strong>Puntuación de CheckBox:</strong> <?php echo h((string)$sumaImp); ?>
      </div>
    </div>
    <div class="card">
      <h3>📋 Datos de la Víctima</h3>
      <?php if(!empty($datosVictima)): ?>
        <div class="grid cols-2">
          <div><strong>Nombre completo:</strong> <?php echo h(($datosVictima['victima_nombre']??'').' '.($datosVictima['victima_apellidos']??'')); ?></div>
          <div><strong>Documento:</strong> <?php echo h(($datosVictima['victima_tipo_documento']??'').' '.($datosVictima['victima_num_documento']??'')); ?></div>
          <div><strong>Fecha nacimiento:</strong> <?php echo h($datosVictima['victima_fecha_nacimiento'] ?? ''); ?></div>
          <div><strong>Nacionalidad:</strong> <?php echo h($datosVictima['victima_nacionalidad'] ?? ''); ?></div>
          <div><strong>Sexo:</strong> <?php echo h($datosVictima['victima_sexo'] ?? ''); ?></div>
          <div><strong>Domicilio:</strong> <?php echo h($datosVictima['victima_domicilio'] ?? ''); ?></div>
          <?php if(!empty($datosVictima['victima_telefonos'])): ?>
            <div style="grid-column:1/-1"><strong>Teléfonos:</strong> <?php echo h($datosVictima['victima_telefonos']); ?></div>
          <?php endif; ?>
          <?php if(!empty($datosVictima['victima_lugares_frecuentes'])): ?>
            <div style="grid-column:1/-1"><strong>Lugares frecuentes:</strong> <?php echo h($datosVictima['victima_lugares_frecuentes']); ?></div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="alert error">No hay datos de víctima</div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3>⚠️ Datos del Hecho Denunciado</h3>
      <?php if(!empty($datosDenuncia)): ?>
        <div class="grid cols-2">
          <div><strong>Tipo de hecho:</strong> <?php echo h($datosDenuncia['tipo_hecho'] ?? ''); ?></div>
          <div><strong>Fecha:</strong> <?php echo h($datosDenuncia['fecha_hecho'] ?? ''); ?> <?php if(!empty($datosDenuncia['hora_aproximada'])) echo h($datosDenuncia['hora_aproximada']); ?></div>
          <div style="grid-column:1/-1"><strong>Lugar:</strong> <?php echo h($datosDenuncia['lugar_hecho'] ?? ''); ?></div>
          <div style="grid-column:1/-1"><strong>Descripción:</strong><br/><pre style="white-space:pre-wrap;margin-top:8px"><?php echo h($datosDenuncia['descripcion_hechos'] ?? ''); ?></pre></div>
          <div><strong>Lesiones presentes:</strong> <?php echo h($datosDenuncia['lesiones_presentes'] ?? 'no'); ?></div>
          <div><strong>Intervención urgente:</strong> <?php echo h($datosDenuncia['urgente_intervencion'] ?? 'no'); ?></div>
          <?php if(!empty($datosDenuncia['testigos'])): ?>
            <div style="grid-column:1/-1"><strong>Testigos:</strong> <?php echo h($datosDenuncia['testigos']); ?></div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="alert error">No hay datos del hecho</div>
      <?php endif; ?>
    </div>

    <div class="card">
      <h3>👤 Datos del Agresor</h3>
      <?php if(!empty($datosAgresor)): ?>
        <div class="grid cols-2">
          <div><strong>Nombre completo:</strong> <?php echo h(($datosAgresor['agresor_nombre']??'').' '.($datosAgresor['agresor_apellidos']??'')); ?></div>
          <div><strong>Documento:</strong> <?php echo h(($datosAgresor['agresor_tipo_documento']??'').' '.($datosAgresor['agresor_num_documento']??'')); ?></div>
          <div><strong>Fecha nacimiento:</strong> <?php echo h($datosAgresor['agresor_fecha_nacimiento'] ?? ''); ?></div>
          <div><strong>Nacionalidad:</strong> <?php echo h($datosAgresor['agresor_nacionalidad'] ?? ''); ?></div>
          <div><strong>Domicilio:</strong> <?php echo h($datosAgresor['agresor_domicilio'] ?? ''); ?></div>
          <div><strong>Empleo:</strong> <?php echo h($datosAgresor['agresor_empleo'] ?? ''); ?></div>
          <div><strong>Relación con víctima:</strong> <?php echo h($datosAgresor['agresor_relacion_con_victima'] ?? ''); ?></div>
          <div><strong>Convivencia actual:</strong> <?php echo h($datosAgresor['agresor_convivencia_actual'] ?? 'no'); ?></div>
          <?php if(!empty($datosAgresor['agresor_antecedentes_penales'])): ?>
            <div style="grid-column:1/-1"><strong>Antecedentes penales:</strong> <?php echo h($datosAgresor['agresor_antecedentes_penales']); ?></div>
          <?php endif; ?>
          <?php if(!empty($datosAgresor['agresor_posesion_armas'])): ?>
            <div style="grid-column:1/-1"><strong>Posesión de armas:</strong> <?php echo h($datosAgresor['agresor_posesion_armas']); ?></div>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="alert error">No hay datos del agresor</div>
      <?php endif; ?>
    </div>

    <?php if (!$guardado): ?>
    <form method="post">
      <!-- Mantener todos los datos como hidden para reenvío si se confirma; usamos la sesión si existe -->
  <?php $allData = !empty($_GET) ? $_GET : $_POST; ?>
      <?php foreach ($allData as $key => $value): ?>
        <?php if (is_array($value)): ?>
          <?php foreach ($value as $v): ?>
            <input type="hidden" name="<?php echo h($key); ?>[]" value="<?php echo h($v); ?>" />
          <?php endforeach; ?>
        <?php else: ?>
          <input type="hidden" name="<?php echo h($key); ?>" value="<?php echo h($value); ?>" />
        <?php endif; ?>
      <?php endforeach; ?>
      <input type="hidden" name="confirmar_guardado" value="1" />
      
      <div class="card">
        <button class="btn" type="submit" style="background:#28a745">✓ Confirmar y finalizar denuncia</button>
        <a href="form-victimas.php" class="btn" style="background:#6c757d;margin-left:10px">Cancelar</a>
      </div>
    </form>
    <?php endif; ?>
  </main>
</div>
</body>
</html>
