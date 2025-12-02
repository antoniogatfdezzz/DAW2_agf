<?php
require_once __DIR__ . '/../configuración/config.php';
require_once __DIR__ . '/../modelos/victimas.php';
require_auth();
$old = array_merge($_GET, $_POST);
$mensaje = null; $errores = [];

if (is_post() && isset($_POST['_buscar_victima'])) {
  require_once __DIR__ . '/../modelos/victimas.php';
  $doc = trim($_POST['victima_num_documento'] ?? '');
  $nom = trim($_POST['victima_nombre'] ?? '');
  $ape = trim($_POST['victima_apellidos'] ?? '');
  $found = null;
  if ($nom !== '' || $ape !== '') {
    if ($nom !== '' && $ape !== '') {
      foreach (victimas_all() as $v) {
        if (strcasecmp(trim($v['victima_nombre'] ?? ''), $nom) === 0 && strcasecmp(trim($v['victima_apellidos'] ?? ''), $ape) === 0) { $found = $v; break; }
      }
    }
    if (!$found && $nom !== '') {
      $term = mb_strtolower($nom);
      foreach (victimas_all() as $v) {
        $fullname = mb_strtolower(trim(($v['victima_nombre'] ?? '').' '.($v['victima_apellidos'] ?? '')));
        if ($term !== '' && mb_strpos($fullname, $term) !== false) { $found = $v; break; }
      }
    }
    if (!$found && $ape !== '') {
      $termApe = mb_strtolower($ape);
      foreach (victimas_all() as $v) {
        $apell = mb_strtolower(trim(($v['victima_apellidos'] ?? '')));
        if ($termApe !== '' && mb_strpos($apell, $termApe) !== false) { $found = $v; break; }
      }
    }
    if (!$found && $doc !== '') {
      foreach (victimas_all() as $v) {
        $vdoc = trim($v['victima_num_documento'] ?? '');
        if (strcasecmp($vdoc, $doc) === 0) { $found = $v; break; }
      }
    }
  } else if ($doc !== '') {
    foreach (victimas_all() as $v) {
      $vdoc = trim($v['victima_num_documento'] ?? '');
      if (strcasecmp($vdoc, $doc) === 0) { $found = $v; break; }
    }
  }
  if ($found) {
    foreach ($found as $k=>$val) {
      if (strpos($k,'victima_')===0) { $old[$k] = $val; }
    }
    $mensaje = 'Datos de víctima cargados.';
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
  <title>Alta de Víctima</title>
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
      <h3>Nueva denuncia - Paso 1: Datos de la víctima</h3>
      <p class="muted">Los datos se guardarán al finalizar todo el proceso.</p>
    </div>

  <form method="post">
      <?php
        $denunciaKeys = ['tipo_hecho','fecha_hecho','hora_aproximada','lugar_hecho','descripcion_hechos','lesiones_presentes','urgente_intervencion','ha_presentado_denuncia_antes_por_mismo_hecho','testigos','observaciones_hecho'];
        foreach ($old as $k=>$v):
          $isDenuncia = in_array($k, $denunciaKeys, true);
          $isAgresor = strpos($k,'agresor_')===0;
          if ($isDenuncia || $isAgresor):
            if (is_array($v)) {
              foreach ($v as $vv) {
                echo '<input type="hidden" name="'.h($k).'[]" value="'.h($vv).'" />';
              }
            } else {
              echo '<input type="hidden" name="'.h($k).'" value="'.h($v).'" />';
            }
          endif;
        endforeach;
      ?>
      <div class="card">
        <h3>Datos de la víctima</h3>
        <div class="grid cols-3">
          <div class="form-row"><label>Nombre*</label><input id="victima_nombre" class="input" name="victima_nombre" list="victima_nombre_list" required value="<?php echo h($old['victima_nombre'] ?? ''); ?>" />
            <datalist id="victima_nombre_list">
              <?php foreach (victimas_all() as $v): ?>
                <?php $nombre = trim(($v['victima_nombre']??'').' '.($v['victima_apellidos']??'')); ?>
                <?php if($nombre!==''): ?><option value="<?php echo h($nombre); ?>"></option><?php endif; ?>
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-row"><label>Apellidos*</label><input id="victima_apellidos" class="input" name="victima_apellidos" list="victima_apellidos_list" required value="<?php echo h($old['victima_apellidos'] ?? ''); ?>" />
            <datalist id="victima_apellidos_list">
              <?php foreach (victimas_all() as $v): ?>
                <?php $ap = trim(($v['victima_apellidos']??'')); ?>
                <?php if($ap!==''): ?><option value="<?php echo h($ap); ?>"></option><?php endif; ?>
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-row"><label>Fecha nacimiento*</label><input class="input" type="date" name="victima_fecha_nacimiento" required value="<?php echo h($old['victima_fecha_nacimiento'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Tipo doc.*</label>
            <select class="input" name="victima_tipo_documento" required>
              <option <?php echo (isset($old['victima_tipo_documento']) && $old['victima_tipo_documento']==='DNI')? 'selected':''; ?>>DNI</option>
              <option <?php echo (isset($old['victima_tipo_documento']) && $old['victima_tipo_documento']==='NIE')? 'selected':''; ?>>NIE</option>
              <option <?php echo (isset($old['victima_tipo_documento']) && $old['victima_tipo_documento']==='Pasaporte')? 'selected':''; ?>>Pasaporte</option>
              <option <?php echo (isset($old['victima_tipo_documento']) && $old['victima_tipo_documento']==='Otro')? 'selected':''; ?>>Otro</option>
            </select></div>
          <div class="form-row"><label>Nº documento*</label><input id="victima_num_documento" class="input" name="victima_num_documento" list="victima_num_documento_list" required value="<?php echo h($old['victima_num_documento'] ?? ''); ?>" />
            <datalist id="victima_num_documento_list">
              <?php foreach (victimas_all() as $v): ?>
                <?php $doc = trim(($v['victima_num_documento']??'')); ?>
                <?php if($doc!==''): ?><option value="<?php echo h($doc); ?>"></option><?php endif; ?>
              <?php endforeach; ?>
            </datalist>
          </div>
          <div class="form-row" style="grid-column:1/-1">
            <button class="btn secondary" type="submit" name="_buscar_victima" value="1" formaction="form-victimas.php" formmethod="post" formnovalidate>Buscar y rellenar</button>
          </div>
          <div class="form-row"><label>Nacionalidad</label><input class="input" name="victima_nacionalidad" value="<?php echo h($old['victima_nacionalidad'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Sexo</label><input class="input" name="victima_sexo" value="<?php echo h($old['victima_sexo'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Domicilio</label><input class="input" name="victima_domicilio" value="<?php echo h($old['victima_domicilio'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Lugares frecuentes (1 por línea)</label><textarea class="input" name="victima_lugares_frecuentes" rows="3"><?php echo h($old['victima_lugares_frecuentes'] ?? ''); ?></textarea></div>
          <div class="form-row" style="grid-column:1/-1"><label>Teléfonos (separados por coma)</label><input class="input" name="victima_telefonos" value="<?php echo h($old['victima_telefonos'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Preferencia de contacto</label><input class="input" name="preferencia_contacto" value="<?php echo h($old['preferencia_contacto'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Idioma</label><input class="input" name="victima_idioma" value="<?php echo h($old['victima_idioma'] ?? ''); ?>" /></div>
          <div class="form-row"><label>¿Necesita intérprete?</label>
            <select class="input" name="necesita_interprete"><option value="no" <?php echo (($old['necesita_interprete'] ?? 'no')==='no')? 'selected':''; ?>>No</option><option value="si" <?php echo (($old['necesita_interprete'] ?? '')==='si')? 'selected':''; ?>>Sí</option></select>
          </div>
          <div class="form-row"><label>Estado reproductivo</label><input class="input" name="victima_estado_reproductivo" value="<?php echo h($old['victima_estado_reproductivo'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Discapacidad</label><input class="input" name="victima_discapacidad" value="<?php echo h($old['victima_discapacidad'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Enfermedad crónica</label><input class="input" name="victima_enfermedad_cronica" value="<?php echo h($old['victima_enfermedad_cronica'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Consumo tóxicos</label><input class="input" name="victima_consumo_toxicos" value="<?php echo h($old['victima_consumo_toxicos'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Salud mental</label><input class="input" name="victima_salud_mental" value="<?php echo h($old['victima_salud_mental'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Situación económica</label>
            <select class="input" name="victima_situacion_economica"><option>independiente</option><option>dependiente económicamente</option></select>
          </div>
          <div class="form-row"><label>Red de apoyo</label>
            <select class="input" name="victima_red_apoyo"><option>familiar</option><option>amigos</option><option>servicios sociales</option><option>ONG</option><option>ninguna</option></select>
          </div>
          <div class="form-row"><label>¿Vivienda compartida con agresor?</label>
            <select class="input" name="victima_vivienda_compartida_con_agresor"><option value="no">No</option><option value="si">Sí</option></select>
          </div>
          <div class="form-row"><label>Relación laboral</label><input class="input" name="victima_relacion_laboral" value="<?php echo h($old['victima_relacion_laboral'] ?? ''); ?>" /></div>
          <div class="form-row"><label>Custodia hijos</label><input class="input" name="victima_custodia_hijos" value="<?php echo h($old['victima_custodia_hijos'] ?? ''); ?>" /></div>
          <div class="form-row" style="grid-column:1/-1"><label>Observaciones</label><textarea class="input" name="victima_observaciones" rows="4"><?php echo h($old['victima_observaciones'] ?? ''); ?></textarea></div>
        </div>
      </div>
      <div class="card"><button class="btn" type="submit" formaction="form-denuncia.php" formmethod="post">Continuar con datos de la denuncia →</button></div>
    </form>
  </main>
</div>
</body>
</html>
