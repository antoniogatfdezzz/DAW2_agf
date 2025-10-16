<?php
/**
 * REGISTRAR VÍCTIMA
 * Formulario completo para registrar una nueva víctima
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/victimas.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Procesar formulario si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        // Datos básicos
        'nombre' => $_POST['nombre'] ?? '',
        'apellidos' => $_POST['apellidos'] ?? '',
        'tipo_documento' => $_POST['tipo_documento'] ?? 'DNI',
        'num_documento' => $_POST['num_documento'] ?? '',
        'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? '',
        'nacionalidad' => $_POST['nacionalidad'] ?? 'Española',
        'sexo' => $_POST['sexo'] ?? 'Mujer',
        
        // Contacto
        'domicilio' => $_POST['domicilio'] ?? '',
        'domicilio_coincide_agresor' => isset($_POST['domicilio_coincide_agresor']),
        'telefono' => $_POST['telefono'] ?? '',
        'email' => $_POST['email'] ?? '',
        'idioma' => $_POST['idioma'] ?? 'Español',
        'necesita_interprete' => isset($_POST['necesita_interprete']),
        
        // Salud
        'embarazada' => isset($_POST['embarazada']),
        'fecha_probable_parto' => $_POST['fecha_probable_parto'] ?? null,
        'discapacidad' => $_POST['discapacidad'] ?? 'No',
        'tipo_discapacidad' => $_POST['tipo_discapacidad'] ?? '',
        'enfermedad_cronica' => isset($_POST['enfermedad_cronica']),
        'detalles_enfermedad' => $_POST['detalles_enfermedad'] ?? '',
        'consumo_toxicos' => $_POST['consumo_toxicos'] ?? 'No',
        
        // Salud mental
        'ideas_suicidas' => isset($_POST['ideas_suicidas']),
        'intentos_suicidas_previos' => isset($_POST['intentos_suicidas_previos']),
        'detalles_salud_mental' => $_POST['detalles_salud_mental'] ?? '',
        
        // Situación social y económica
        'situacion_economica' => $_POST['situacion_economica'] ?? 'Independiente',
        'dependiente_economicamente' => isset($_POST['dependiente_economicamente']),
        'tiene_apoyo_familiar' => isset($_POST['tiene_apoyo_familiar']),
        'tiene_apoyo_amigos' => isset($_POST['tiene_apoyo_amigos']),
        'servicios_sociales' => isset($_POST['servicios_sociales']),
        
        // Vivienda y empleo
        'vivienda_compartida_con_agresor' => isset($_POST['vivienda_compartida_con_agresor']),
        'relacion_laboral' => $_POST['relacion_laboral'] ?? 'Desempleo',
        
        // Menores
        'tiene_menores' => isset($_POST['tiene_menores']),
        'custodia_hijos' => $_POST['custodia_hijos'] ?? 'No aplica',
        
        // Observaciones
        'observaciones' => $_POST['observaciones'] ?? '',
        
        // Metadata
        'registrado_por' => $_SESSION['usuario_id']
    ];
    
    $resultado = crearVictima($datos);
    
    if (isset($resultado['error'])) {
        $error = $resultado['error'];
    } else {
        guardarVictimas();
        header('Location: victimas_lista.php?mensaje=Víctima registrada correctamente');
        exit;
    }
}

global $TIPOS_DOCUMENTO;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Registrar Víctima</title>
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
            color: #1976D2;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
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
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <nav>
                <ul>
                    <li><a href="dashboard_policia.php">📊 Dashboard</a></li>
                    <li><a href="victimas_lista.php">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php" class="active">✏️ Registrar Víctima</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <main class="main-content">
            <div class="header-bar">
                <h1>✏️ Registrar Nueva Víctima</h1>
                <a href="victimas_lista.php" class="btn btn-secondary">← Volver a lista</a>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- SECCIÓN 1: DATOS PERSONALES -->
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
                            <label for="tipo_documento">Tipo de Documento *</label>
                            <select id="tipo_documento" name="tipo_documento" required>
                                <?php foreach ($TIPOS_DOCUMENTO as $tipo): ?>
                                <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="num_documento">Número de Documento *</label>
                            <input type="text" id="num_documento" name="num_documento" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento *</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nacionalidad">Nacionalidad</label>
                            <input type="text" id="nacionalidad" name="nacionalidad" value="Española">
                        </div>
                        
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo">
                                <option value="Mujer">Mujer</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: CONTACTO -->
                <div class="form-section">
                    <h3>📞 Datos de Contacto (Obligatorios)</h3>
                    
                    <div class="form-group">
                        <label for="domicilio">Domicilio Completo *</label>
                        <input type="text" id="domicilio" name="domicilio" placeholder="Calle, número, piso, ciudad, código postal" required>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="domicilio_coincide_agresor" name="domicilio_coincide_agresor">
                        <label for="domicilio_coincide_agresor">⚠️ El domicilio coincide con el del agresor</label>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefono">Teléfono Principal *</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="600000000" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="email@ejemplo.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="idioma">Idioma</label>
                            <input type="text" id="idioma" name="idioma" value="Español">
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="necesita_interprete" name="necesita_interprete">
                        <label for="necesita_interprete">Necesita intérprete</label>
                    </div>
                </div>

                <!-- SECCIÓN 3: SALUD -->
                <div class="form-section">
                    <h3>🏥 Salud</h3>
                    
                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="embarazada" name="embarazada" onchange="toggleFechaParto()">
                        <label for="embarazada">Embarazada</label>
                    </div>

                    <div class="form-group" id="fecha_parto_container" style="display: none;">
                        <label for="fecha_probable_parto">Fecha Probable de Parto</label>
                        <input type="date" id="fecha_probable_parto" name="fecha_probable_parto">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="discapacidad">Discapacidad</label>
                            <select id="discapacidad" name="discapacidad" onchange="toggleDiscapacidad()">
                                <option value="No">No</option>
                                <option value="Física">Física</option>
                                <option value="Psíquica">Psíquica</option>
                                <option value="Sensorial">Sensorial</option>
                                <option value="Mixta">Mixta</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="tipo_discapacidad_container" style="display: none;">
                            <label for="tipo_discapacidad">Detalles de la Discapacidad</label>
                            <input type="text" id="tipo_discapacidad" name="tipo_discapacidad">
                        </div>
                    </div>

                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="enfermedad_cronica" name="enfermedad_cronica" onchange="toggleEnfermedad()">
                        <label for="enfermedad_cronica">Enfermedad Crónica</label>
                    </div>

                    <div class="form-group" id="detalles_enfermedad_container" style="display: none;">
                        <label for="detalles_enfermedad">Detalles de la Enfermedad</label>
                        <textarea id="detalles_enfermedad" name="detalles_enfermedad" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="consumo_toxicos">Consumo de Sustancias</label>
                        <select id="consumo_toxicos" name="consumo_toxicos">
                            <option value="No">No</option>
                            <option value="Alcohol">Alcohol</option>
                            <option value="Drogas">Drogas</option>
                            <option value="Fármacos">Fármacos</option>
                            <option value="Múltiples">Múltiples</option>
                        </select>
                    </div>
                </div>

                <!-- SECCIÓN 4: SALUD MENTAL -->
                <div class="form-section">
                    <h3>🧠 Salud Mental</h3>
                    
                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="ideas_suicidas" name="ideas_suicidas">
                        <label for="ideas_suicidas">⚠️ Ideas suicidas actuales</label>
                    </div>

                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="intentos_suicidas_previos" name="intentos_suicidas_previos">
                        <label for="intentos_suicidas_previos">⚠️ Intentos de suicidio previos</label>
                    </div>

                    <div class="form-group">
                        <label for="detalles_salud_mental">Detalles / Diagnósticos / Tratamientos</label>
                        <textarea id="detalles_salud_mental" name="detalles_salud_mental" rows="3"></textarea>
                    </div>
                </div>

                <!-- SECCIÓN 5: SITUACIÓN SOCIAL Y ECONÓMICA -->
                <div class="form-section">
                    <h3>💼 Situación Social y Económica</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="situacion_economica">Situación Económica</label>
                            <select id="situacion_economica" name="situacion_economica">
                                <option value="Independiente">Independiente económicamente</option>
                                <option value="Dependiente">Dependiente económicamente</option>
                                <option value="Precaria">Situación precaria</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="relacion_laboral">Situación Laboral</label>
                            <select id="relacion_laboral" name="relacion_laboral">
                                <option value="Empleada">Empleada</option>
                                <option value="Desempleo">Desempleo</option>
                                <option value="Estudiante">Estudiante</option>
                                <option value="Jubilada">Jubilada</option>
                                <option value="Ama de casa">Ama de casa</option>
                            </select>
                        </div>
                    </div>

                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="dependiente_economicamente" name="dependiente_economicamente">
                        <label for="dependiente_economicamente">Dependiente económicamente del agresor</label>
                    </div>

                    <h4 style="margin-top: 1.5rem; margin-bottom: 0.5rem;">Red de Apoyo</h4>
                    
                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="tiene_apoyo_familiar" name="tiene_apoyo_familiar">
                        <label for="tiene_apoyo_familiar">Tiene apoyo familiar</label>
                    </div>

                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="tiene_apoyo_amigos" name="tiene_apoyo_amigos">
                        <label for="tiene_apoyo_amigos">Tiene apoyo de amigos</label>
                    </div>

                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="servicios_sociales" name="servicios_sociales">
                        <label for="servicios_sociales">Atendida por servicios sociales</label>
                    </div>
                </div>

                <!-- SECCIÓN 6: VIVIENDA -->
                <div class="form-section">
                    <h3>🏠 Vivienda</h3>
                    
                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="vivienda_compartida_con_agresor" name="vivienda_compartida_con_agresor">
                        <label for="vivienda_compartida_con_agresor">⚠️ Comparte vivienda con el agresor</label>
                    </div>
                </div>

                <!-- SECCIÓN 7: MENORES -->
                <div class="form-section">
                    <h3>👶 Menores a Cargo</h3>
                    
                    <div class="checkbox-group mb-2">
                        <input type="checkbox" id="tiene_menores" name="tiene_menores" onchange="toggleMenores()">
                        <label for="tiene_menores">Tiene menores a cargo</label>
                    </div>

                    <div id="menores_container" style="display: none;">
                        <div class="form-group">
                            <label for="custodia_hijos">Tipo de Custodia</label>
                            <select id="custodia_hijos" name="custodia_hijos">
                                <option value="No aplica">No aplica</option>
                                <option value="Exclusiva">Custodia exclusiva</option>
                                <option value="Compartida">Custodia compartida</option>
                                <option value="Sin custodia">Sin custodia</option>
                                <option value="En disputa">En disputa judicial</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 8: OBSERVACIONES -->
                <div class="form-section">
                    <h3>📝 Observaciones</h3>
                    
                    <div class="form-group">
                        <label for="observaciones">Observaciones Generales</label>
                        <textarea id="observaciones" name="observaciones" rows="4" placeholder="Información adicional relevante..."></textarea>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="form-section">
                    <div class="form-row">
                        <button type="submit" class="btn btn-primary">
                            ✅ Registrar Víctima
                        </button>
                        <a href="victimas_lista.php" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        function toggleFechaParto() {
            const checkbox = document.getElementById('embarazada');
            const container = document.getElementById('fecha_parto_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleDiscapacidad() {
            const select = document.getElementById('discapacidad');
            const container = document.getElementById('tipo_discapacidad_container');
            container.style.display = select.value !== 'No' ? 'block' : 'none';
        }

        function toggleEnfermedad() {
            const checkbox = document.getElementById('enfermedad_cronica');
            const container = document.getElementById('detalles_enfermedad_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }

        function toggleMenores() {
            const checkbox = document.getElementById('tiene_menores');
            const container = document.getElementById('menores_container');
            container.style.display = checkbox.checked ? 'block' : 'none';
        }
    </script>
</body>
</html>
