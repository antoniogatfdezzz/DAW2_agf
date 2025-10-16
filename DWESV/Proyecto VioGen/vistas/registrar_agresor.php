<?php
/**
 * REGISTRAR AGRESOR
 * Formulario completo para registrar un nuevo agresor
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/agresores.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        // Datos básicos
        'nombre' => $_POST['nombre'] ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
        'num_documento' => $_POST['num_documento'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
        'nacionalidad' => $_POST['nacionalidad'] ?? '',
        
        // Contacto
        'domicilio' => $_POST['domicilio'] ?? '',
        'domicilio_coincide_victima' => isset($_POST['domicilio_coincide_victima']),
        'telefono' => $_POST['telefono'] ?? '',
        
        // Situación laboral
        'empleo' => $_POST['empleo'] ?? 'Desconocido',
        'situacion_empleo' => $_POST['situacion_empleo'] ?? 'Desconocida',
        
        // Relación con víctima
        'relacion_con_victima' => $_POST['relacion_con_victima'] ?? 'Pareja',
        'convivencia_actual' => isset($_POST['convivencia_actual']),
        
        // Antecedentes (CRÍTICO)
        'antecedentes_penales' => isset($_POST['antecedentes_penales']),
        'detalles_antecedentes' => $_POST['detalles_antecedentes'] ?? '',
        'quebrantamientos_previos' => isset($_POST['quebrantamientos_previos']),
        'detalles_quebrantamientos' => $_POST['detalles_quebrantamientos'] ?? '',
        
        // Historial de agresiones
        'historia_agresiones_previas' => isset($_POST['historia_agresiones_previas']),
        'agresiones_fisicas' => isset($_POST['agresiones_fisicas']),
        'agresiones_sexuales' => isset($_POST['agresiones_sexuales']),
        'detalles_agresiones' => $_POST['detalles_agresiones'] ?? '',
        'violencia_otra_persona' => isset($_POST['violencia_otra_persona']),
        'detalles_violencia_otra_persona' => $_POST['detalles_violencia_otra_persona'] ?? '',
        'violencia_otras_parejas' => isset($_POST['violencia_otras_parejas']),
        
        // Adicciones
        'alcohol_drogas' => isset($_POST['alcohol_drogas']),
        'tipo_sustancias' => $_POST['tipo_sustancias'] ?? [],
        'gravedad_adiccion' => $_POST['gravedad_adiccion'] ?? '',
        
        // Salud mental
        'trastorno_diagnosticado' => isset($_POST['trastorno_diagnosticado']),
        'tipo_trastorno' => $_POST['tipo_trastorno'] ?? '',
        'en_tratamiento' => isset($_POST['en_tratamiento']),
        'intentos_suicidio' => isset($_POST['intentos_suicidio']),
        'ideas_suicidas' => isset($_POST['ideas_suicidas']),
        'detalles_salud_mental' => $_POST['detalles_salud_mental'] ?? '',
        
        // Armas (LETALIDAD)
        'posesion_armas' => isset($_POST['posesion_armas']),
        'tipo_armas' => $_POST['tipo_armas'] ?? [],
        'tiene_acceso_armas' => isset($_POST['tiene_acceso_armas']),
        'detalles_armas' => $_POST['detalles_armas'] ?? '',
        
        // Comportamiento
        'celos_exagerados' => isset($_POST['celos_exagerados']),
        'conductas_control' => isset($_POST['conductas_control']),
        'conductas_acoso' => isset($_POST['conductas_acoso']),
        'problemas_personales_recientes' => isset($_POST['problemas_personales_recientes']),
        'detalles_problemas_personales' => $_POST['detalles_problemas_personales'] ?? '',
        
        // Paradero
        'paradero_conocido' => isset($_POST['paradero_conocido']),
        'fugado' => isset($_POST['fugado']),
        'ubicacion_actual' => $_POST['ubicacion_actual'] ?? '',
        
        // Medidas judiciales
        'medidas_cautelares_activas' => isset($_POST['medidas_cautelares_activas']),
        'tipo_medidas' => $_POST['tipo_medidas'] ?? [],
        'cumplimiento_medidas' => $_POST['cumplimiento_medidas'] ?? 'No aplica',
        
        // Antecedentes familiares
        'antecedentes_familiares_violencia' => isset($_POST['antecedentes_familiares_violencia']),
        
        'observaciones' => $_POST['observaciones'] ?? '',
        'registrado_por' => $_SESSION['usuario_id']
    ];
    
    $resultado = crearAgresor($datos);
    
    if (isset($resultado['error'])) {
        $error = $resultado['error'];
    } else {
        guardarAgresores();
        header('Location: agresores_lista.php?mensaje=Agresor registrado correctamente');
        exit;
    }
}

global $TIPOS_DOCUMENTO, $RELACIONES_VICTIMA;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Registrar Agresor</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .form-section {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-section h3 {
            color: #D32F2F;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .form-section.critico {
            border-left: 4px solid #D32F2F;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        .warning-banner {
            background: #FFF3E0;
            border-left: 4px solid #FF9800;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <nav>
                <ul>
                    <li><a href="dashboard_policia.php">📊 Dashboard</a></li>
                    <li><a href="victimas_lista.php">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header-bar">
                <h1>⚠️ Registrar Nuevo Agresor</h1>
                <a href="agresores_lista.php" class="btn btn-secondary">← Volver</a>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <div class="warning-banner">
                <strong>⚠️ Importante:</strong> Los datos marcados como CRÍTICOS son fundamentales para la valoración del riesgo.
            </div>

            <form method="POST" action="">
                <!-- DATOS PERSONALES -->
                <div class="form-section">
                    <h3>📋 Datos Personales (Obligatorios)</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre *</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="apellidos">Apellidos *</label>
                            <input type="text" id="apellidos" name="apellidos" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_documento">Tipo de Documento</label>
                            <select id="tipo_documento" name="tipo_documento">
                                <?php foreach ($TIPOS_DOCUMENTO as $tipo): ?>
                                <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="num_documento">Número de Documento</label>
                            <input type="text" id="num_documento" name="num_documento">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        
                        <div class="form-group">
                            <label for="nacionalidad">Nacionalidad</label>
                            <input type="text" id="nacionalidad" name="nacionalidad">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="domicilio">Domicilio</label>
                        <input type="text" id="domicilio" name="domicilio">
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="domicilio_coincide_victima" name="domicilio_coincide_victima">
                        <label for="domicilio_coincide_victima">⚠️ El domicilio coincide con el de la víctima</label>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono">
                    </div>
                </div>

                <!-- RELACIÓN CON VÍCTIMA -->
                <div class="form-section">
                    <h3>💔 Relación con la Víctima</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="relacion_con_victima">Tipo de Relación</label>
                            <select id="relacion_con_victima" name="relacion_con_victima">
                                <?php foreach ($RELACIONES_VICTIMA as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="situacion_empleo">Situación Laboral</label>
                            <select id="situacion_empleo" name="situacion_empleo">
                                <option value="Empleado estable">Empleado estable</option>
                                <option value="Empleo precario">Empleo precario</option>
                                <option value="Desempleo">Desempleo</option>
                                <option value="Desconocida">Desconocida</option>
                            </select>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="convivencia_actual" name="convivencia_actual">
                        <label for="convivencia_actual">⚠️ Convivencia actual con la víctima</label>
                    </div>
                </div>

                <!-- ANTECEDENTES PENALES (CRÍTICO) -->
                <div class="form-section critico">
                    <h3>🚨 Antecedentes Penales (CRÍTICO)</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="antecedentes_penales" name="antecedentes_penales" onchange="toggleAntecedentes()">
                        <label for="antecedentes_penales"><strong>⚠️ Tiene antecedentes penales</strong></label>
                    </div>

                    <div class="form-group" id="detalles_antecedentes_container" style="display: none;">
                        <label for="detalles_antecedentes">Detalles de los Antecedentes</label>
                        <textarea id="detalles_antecedentes" name="detalles_antecedentes" rows="3"></textarea>
                    </div>

                    <div class="checkbox-group mt-2">
                        <input type="checkbox" id="quebrantamientos_previos" name="quebrantamientos_previos" onchange="toggleQuebrantamientos()">
                        <label for="quebrantamientos_previos"><strong>🚨 Quebrantamientos de medidas previos</strong></label>
                    </div>

                    <div class="form-group" id="detalles_quebrantamientos_container" style="display: none;">
                        <label for="detalles_quebrantamientos">Detalles de los Quebrantamientos</label>
                        <textarea id="detalles_quebrantamientos" name="detalles_quebrantamientos" rows="3"></textarea>
                    </div>
                </div>

                <!-- HISTORIAL DE AGRESIONES (CRÍTICO) -->
                <div class="form-section critico">
                    <h3>🚨 Historial de Agresiones (CRÍTICO)</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="historia_agresiones_previas" name="historia_agresiones_previas">
                        <label for="historia_agresiones_previas"><strong>Historial de agresiones previas</strong></label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="agresiones_fisicas" name="agresiones_fisicas">
                        <label for="agresiones_fisicas"><strong>⚠️ Agresiones físicas</strong></label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="agresiones_sexuales" name="agresiones_sexuales">
                        <label for="agresiones_sexuales"><strong>🚨 Agresiones sexuales</strong></label>
                    </div>

                    <div class="form-group">
                        <label for="detalles_agresiones">Detalles de las Agresiones</label>
                        <textarea id="detalles_agresiones" name="detalles_agresiones" rows="3"></textarea>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="violencia_otra_persona" name="violencia_otra_persona">
                        <label for="violencia_otra_persona">Violencia a terceros/menores/animales</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="violencia_otras_parejas" name="violencia_otras_parejas">
                        <label for="violencia_otras_parejas">Violencia con otras parejas</label>
                    </div>
                </div>

                <!-- ADICCIONES -->
                <div class="form-section">
                    <h3>🍺 Adicciones y Consumo de Sustancias</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="alcohol_drogas" name="alcohol_drogas" onchange="toggleAdicciones()">
                        <label for="alcohol_drogas"><strong>⚠️ Consumo de alcohol/drogas</strong></label>
                    </div>

                    <div id="adicciones_container" style="display: none;">
                        <div class="form-group">
                            <label for="gravedad_adiccion">Gravedad de la Adicción</label>
                            <select id="gravedad_adiccion" name="gravedad_adiccion">
                                <option value="Leve">Leve</option>
                                <option value="Moderada">Moderada</option>
                                <option value="Grave">Grave</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SALUD MENTAL -->
                <div class="form-section">
                    <h3>🧠 Salud Mental</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="trastorno_diagnosticado" name="trastorno_diagnosticado" onchange="toggleTrastorno()">
                        <label for="trastorno_diagnosticado">Trastorno mental diagnosticado</label>
                    </div>

                    <div id="trastorno_container" style="display: none;">
                        <div class="form-group">
                            <label for="tipo_trastorno">Tipo de Trastorno</label>
                            <input type="text" id="tipo_trastorno" name="tipo_trastorno">
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="en_tratamiento" name="en_tratamiento">
                            <label for="en_tratamiento">En tratamiento actualmente</label>
                        </div>
                    </div>

                    <div class="checkbox-group mt-2">
                        <input type="checkbox" id="intentos_suicidio" name="intentos_suicidio">
                        <label for="intentos_suicidio">⚠️ Intentos de suicidio</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="ideas_suicidas" name="ideas_suicidas">
                        <label for="ideas_suicidas">Ideas suicidas</label>
                    </div>

                    <div class="form-group">
                        <label for="detalles_salud_mental">Detalles Salud Mental</label>
                        <textarea id="detalles_salud_mental" name="detalles_salud_mental" rows="3"></textarea>
                    </div>
                </div>

                <!-- ARMAS (LETALIDAD) -->
                <div class="form-section critico">
                    <h3>🔫 Posesión de Armas (LETALIDAD)</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="posesion_armas" name="posesion_armas" onchange="toggleArmas()">
                        <label for="posesion_armas"><strong>🚨 Posee armas o tiene acceso a ellas</strong></label>
                    </div>

                    <div id="armas_container" style="display: none;">
                        <div class="form-group">
                            <label for="detalles_armas">Tipo y Detalles de las Armas</label>
                            <textarea id="detalles_armas" name="detalles_armas" rows="2" placeholder="Ej: Arma de fuego registrada, arma blanca, etc."></textarea>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="tiene_acceso_armas" name="tiene_acceso_armas">
                            <label for="tiene_acceso_armas">Tiene acceso fácil a las armas</label>
                        </div>
                    </div>
                </div>

                <!-- COMPORTAMIENTO -->
                <div class="form-section">
                    <h3>😡 Comportamiento y Conductas</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="celos_exagerados" name="celos_exagerados">
                        <label for="celos_exagerados">Celos exagerados</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="conductas_control" name="conductas_control">
                        <label for="conductas_control">Conductas de control</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="conductas_acoso" name="conductas_acoso">
                        <label for="conductas_acoso">Conductas de acoso</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="problemas_personales_recientes" name="problemas_personales_recientes">
                        <label for="problemas_personales_recientes">Problemas personales recientes (pérdida trabajo, crisis, etc.)</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="antecedentes_familiares_violencia" name="antecedentes_familiares_violencia">
                        <label for="antecedentes_familiares_violencia">Antecedentes familiares de violencia</label>
                    </div>
                </div>

                <!-- PARADERO Y MEDIDAS -->
                <div class="form-section">
                    <h3>📍 Paradero y Medidas Judiciales</h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="paradero_conocido" name="paradero_conocido" checked>
                        <label for="paradero_conocido">Paradero conocido</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="fugado" name="fugado">
                        <label for="fugado">🚨 Fugado / Paradero desconocido</label>
                    </div>

                    <div class="form-group">
                        <label for="ubicacion_actual">Ubicación Actual (si se conoce)</label>
                        <input type="text" id="ubicacion_actual" name="ubicacion_actual">
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="medidas_cautelares_activas" name="medidas_cautelares_activas" onchange="toggleMedidas()">
                        <label for="medidas_cautelares_activas">Tiene medidas cautelares activas</label>
                    </div>

                    <div id="medidas_container" style="display: none;">
                        <div class="form-group">
                            <label for="cumplimiento_medidas">Cumplimiento de Medidas</label>
                            <select id="cumplimiento_medidas" name="cumplimiento_medidas">
                                <option value="Cumple">Cumple</option>
                                <option value="No cumple">No cumple</option>
                                <option value="Parcial">Cumplimiento parcial</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- OBSERVACIONES -->
                <div class="form-section">
                    <h3>📝 Observaciones</h3>
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones Generales</label>
                        <textarea id="observaciones" name="observaciones" rows="4"></textarea>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="form-section">
                    <div class="form-row">
                        <button type="submit" class="btn btn-danger">
                            ✅ Registrar Agresor
                        </button>
                        <a href="agresores_lista.php" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        function toggleAntecedentes() {
            const checkbox = document.getElementById('antecedentes_penales');
            const container = document.getElementById('detalles_antecedentes_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleQuebrantamientos() {
            const checkbox = document.getElementById('quebrantamientos_previos');
            const container = document.getElementById('detalles_quebrantamientos_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleAdicciones() {
            const checkbox = document.getElementById('alcohol_drogas');
            const container = document.getElementById('adicciones_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleTrastorno() {
            const checkbox = document.getElementById('trastorno_diagnosticado');
            const container = document.getElementById('trastorno_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleArmas() {
            const checkbox = document.getElementById('posesion_armas');
            const container = document.getElementById('armas_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleMedidas() {
            const checkbox = document.getElementById('medidas_cautelares_activas');
            const container = document.getElementById('medidas_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
</body>
</html>
