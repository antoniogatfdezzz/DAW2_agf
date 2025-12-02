<?php
require_once __DIR__ . '/../configuración/config.php';
require_once __DIR__ . '/../modelos/agresores.php';
require_auth();

$mensaje = null; $errores = [];


$datosPrevios = [];
$origen = array_merge(
  $_GET,
  $_POST
);
foreach ($origen as $key => $value) {
  if (strpos($key, 'victima_') === 0 || strpos($key, 'tipo_hecho') === 0 || 
    in_array($key, ['fecha_hecho','hora_aproximada','lugar_hecho','descripcion_hechos',
            'lesiones_presentes','urgente_intervencion','ha_presentado_denuncia_antes_por_mismo_hecho',
            'testigos','observaciones_hecho'])) {
    $datosPrevios[$key] = $value;
  }
}

$old = $origen;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['agresor_nombre'])) {
  $req = ['agresor_nombre','agresor_apellidos','agresor_tipo_documento','agresor_num_documento','agresor_fecha_nacimiento'];
  foreach ($req as $r) { if (empty($_GET[$r])) $errores[$r] = 'Obligatorio'; }
  if (!$errores) {
    redirect('resumen-denuncia.php?' . http_build_query($_GET));
  }
}

if (is_post() && isset($_POST['_buscar_agresor'])) {
  $doc = trim($_POST['agresor_num_documento'] ?? '');
  $nom = trim($_POST['agresor_nombre'] ?? '');
  $ape = trim($_POST['agresor_apellidos'] ?? '');
  $found = null;
  if ($nom !== '' || $ape !== '') {
    if ($nom !== '' && $ape !== '') {
      foreach (agresores_all() as $a) {
        if (strcasecmp(trim($a['agresor_nombre'] ?? ''), $nom) === 0 && strcasecmp(trim($a['agresor_apellidos'] ?? ''), $ape) === 0) { $found = $a; break; }
      }
    }
    if (!$found && $nom !== '') {
      $term = mb_strtolower($nom);
      foreach (agresores_all() as $a) {
        $fullname = mb_strtolower(trim(($a['agresor_nombre'] ?? '').' '.($a['agresor_apellidos'] ?? '')));
        if ($term !== '' && mb_strpos($fullname, $term) !== false) { $found = $a; break; }
      }
    }
    if (!$found && $ape !== '') {
      $termApe = mb_strtolower($ape);
      foreach (agresores_all() as $a) {
        $apell = mb_strtolower(trim(($a['agresor_apellidos'] ?? '')));
        if ($termApe !== '' && mb_strpos($apell, $termApe) !== false) { $found = $a; break; }
      }
    }
    if (!$found && $doc !== '') {
      foreach (agresores_all() as $a) {
        $adoc = trim($a['agresor_num_documento'] ?? '');
        if (strcasecmp($adoc, $doc) === 0) { $found = $a; break; }
      }
    }
  } else if ($doc !== '') {
    foreach (agresores_all() as $a) {
      $adoc = trim($a['agresor_num_documento'] ?? '');
      if (strcasecmp($adoc, $doc) === 0) { $found = $a; break; }
    }
  }
  if ($found) {
    foreach ($found as $k=>$val) {
      if (strpos($k,'agresor_')===0) { $old[$k] = $val; }
    }
    $mensaje = 'Datos del agresor cargados.';
  } else {
    $errores['buscar'] = 'No se encontró coincidencia con los datos introducidos.';
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Alta de Agresor</title>
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
    <?php if($errores): ?><div class="alert error">Faltan campos obligatorios.</div><?php endif; ?>

    <div class="card">
      <h3>Paso 3: Datos del agresor</h3>
      <?php if(!empty($datosPrevios['victima_nombre'])): ?>
        <p><strong>Víctima:</strong> <?php echo h($datosPrevios['victima_nombre'].' '.$datosPrevios['victima_apellidos']); ?></p>
        <p><strong>Tipo de hecho:</strong> <?php echo h($datosPrevios['tipo_hecho'] ?? ''); ?></p>
      <?php else: ?>
        <div class="alert error">Faltan datos previos. <a href="form-victimas.php">Volver al inicio</a>.</div>
      <?php endif; ?>
    </div>

  <form method="post">
      <?php foreach ($datosPrevios as $key => $value): ?>
        <?php if (is_array($value)): ?>
          <?php foreach ($value as $v): ?>
            <input type="hidden" name="<?php echo h($key); ?>[]" value="<?php echo h($v); ?>" />
          <?php endforeach; ?>
        <?php else: ?>
          <input type="hidden" name="<?php echo h($key); ?>" value="<?php echo h($value); ?>" />
        <?php endif; ?>
      <?php endforeach; ?>
      
      <div class="card">
        <h3>Datos del agresor</h3>
        <div class="grid cols-3">
          <div class="form-row"><label>Nombre*</label><input id="agresor_nombre" class="input" name="agresor_nombre" list="agresor_nombre_list" required value="<?php echo h($old['agresor_nombre'] ?? ''); ?>" />
            <datalist id="agresor_nombre_list">
              <?php foreach (agresores_all() as $a): ?>
                <?php $nombre = trim(($a['agresor_nombre']??'').' '.($a['agresor_apellidos']??'')); ?>
                <?php if($nombre!==''): ?><option value="<?php echo h($nombre); ?>"></option><?php endif; ?>
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-row"><label>Apellidos*</label><input id="agresor_apellidos" class="input" name="agresor_apellidos" list="agresor_apellidos_list" required value="<?php echo h($old['agresor_apellidos'] ?? ''); ?>" />
            <datalist id="agresor_apellidos_list">
              <?php foreach (agresores_all() as $a): ?>
                <?php $ap = trim(($a['agresor_apellidos']??'')); ?>
                <?php if($ap!==''): ?><option value="<?php echo h($ap); ?>"></option><?php endif; ?>
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-row"><label>Fecha nacimiento*</label><input class="input" type="date" name="agresor_fecha_nacimiento" required value="<?php echo h($old['agresor_fecha_nacimiento'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Tipo doc.*</label>
            <select class="input" name="agresor_tipo_documento" required>
              <option <?php echo (isset($old['agresor_tipo_documento']) && $old['agresor_tipo_documento']==='DNI')? 'selected':''; ?>>DNI</option>
              <option <?php echo (isset($old['agresor_tipo_documento']) && $old['agresor_tipo_documento']==='NIE')? 'selected':''; ?>>NIE</option>
              <option <?php echo (isset($old['agresor_tipo_documento']) && $old['agresor_tipo_documento']==='Pasaporte')? 'selected':''; ?>>Pasaporte</option>
              <option <?php echo (isset($old['agresor_tipo_documento']) && $old['agresor_tipo_documento']==='Otro')? 'selected':''; ?>>Otro</option>
            </select></div>
          <div class="form-row"><label>Nº documento*</label><input id="agresor_num_documento" class="input" name="agresor_num_documento" list="agresor_num_documento_list" required value="<?php echo h($old['agresor_num_documento'] ?? ''); ?>" />
            <datalist id="agresor_num_documento_list">
              <?php foreach (agresores_all() as $a): ?>
                <?php $doc = trim(($a['agresor_num_documento']??'')); ?>
                <?php if($doc!==''): ?><option value="<?php echo h($doc); ?>"></option><?php endif; ?>
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-row" style="grid-column:1/-1">
            <button class="btn secondary" type="submit" name="_buscar_agresor" value="1" formaction="form-agresor.php" formmethod="post" formnovalidate>Buscar y rellenar</button>
          </div>
          <div class="form-row"><label>Nacionalidad</label><input class="input" name="agresor_nacionalidad" value="<?php echo h($old['agresor_nacionalidad'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Teléfonos (coma)</label><input class="input" name="agresor_telefonos" value="<?php echo h($old['agresor_telefonos'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Domicilio</label><input class="input" name="agresor_domicilio" value="<?php echo h($old['agresor_domicilio'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Lugares frecuentes (1 por línea)</label><textarea class="input" name="agresor_lugares_frecuentes" rows="3"><?php echo h($old['agresor_lugares_frecuentes'] ?? ''); ?></textarea></div>
          <div class="form-row"><label>Empleo</label>
            <select class="input" name="agresor_empleo">
              <option <?php echo ((($old['agresor_empleo'] ?? '')==='estable')? 'selected':''); ?>>estable</option>
              <option <?php echo ((($old['agresor_empleo'] ?? '')==='precario')? 'selected':''); ?>>precario</option>
              <option <?php echo ((($old['agresor_empleo'] ?? '')==='paro')? 'selected':''); ?>>paro</option>
            </select>
          </div>
          <div class="form-row"><label>Relación con la víctima</label>
            <select class="input" name="agresor_relacion_con_victima">
              <option <?php echo ((($old['agresor_relacion_con_victima'] ?? '')==='pareja actual')? 'selected':''); ?>>pareja actual</option>
              <option <?php echo ((($old['agresor_relacion_con_victima'] ?? '')==='expareja')? 'selected':''); ?>>expareja</option>
              <option <?php echo ((($old['agresor_relacion_con_victima'] ?? '')==='conviviente')? 'selected':''); ?>>conviviente</option>
              <option <?php echo ((($old['agresor_relacion_con_victima'] ?? '')==='otro')? 'selected':''); ?>>otro</option>
            </select>
          </div>
          <div class="form-row"><label>Convivencia actual</label>
            <select class="input" name="agresor_convivencia_actual">
              <option value="no" <?php echo ((($old['agresor_convivencia_actual'] ?? '')==='no')? 'selected':''); ?>>No</option>
              <option value="si" <?php echo ((($old['agresor_convivencia_actual'] ?? '')==='si')? 'selected':''); ?>>Sí</option>
            </select>
          </div>
          <div class="form-row" style="grid-column:1/-1"><label>Antecedentes penales</label><input class="input" name="agresor_antecedentes_penales" value="<?php echo h($old['agresor_antecedentes_penales'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Quebrantamientos previos</label><input class="input" name="agresor_quebrantamientos_previos" value="<?php echo h($old['agresor_quebrantamientos_previos'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Violencia a otra persona</label><input class="input" name="agresor_violencia_otra_persona" value="<?php echo h($old['agresor_violencia_otra_persona'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Alcohol/drogas</label><input class="input" name="agresor_alcohol_drogas" value="<?php echo h($old['agresor_alcohol_drogas'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Salud mental</label><input class="input" name="agresor_salud_mental" value="<?php echo h($old['agresor_salud_mental'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Posesión de armas</label><input class="input" name="agresor_posesion_armas" value="<?php echo h($old['agresor_posesion_armas'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Historia de agresiones previas</label><textarea class="input" name="agresor_historia_agresiones_previas" rows="3"><?php echo h($old['agresor_historia_agresiones_previas'] ?? ''); ?></textarea></div>
          <div class="form-row"><label>Intentos de suicidio</label>
            <select class="input" name="agresor_intentos_suicidio"><option <?php echo ((($old['agresor_intentos_suicidio'] ?? '')==='no')? 'selected':''); ?>>no</option><option <?php echo ((($old['agresor_intentos_suicidio'] ?? '')==='sí')? 'selected':''); ?>>sí</option></select>
          </div>
          <div class="form-row" style="grid-column:1/-1"><label>Observaciones</label><textarea class="input" name="agresor_observaciones" rows="4"><?php echo h($old['agresor_observaciones'] ?? ''); ?></textarea></div>
        </div>
      </div>
      <div class="card">
  <button class="btn secondary" type="submit" formaction="form-denuncia.php" formmethod="post" formnovalidate name="_back" value="1">← Volver</button>
        <button class="btn" type="submit" formaction="resumen-denuncia.php" formmethod="get">Ver resumen completo →</button>
      </div>
    </form>
  </main>
</div>
</body>
</html>
