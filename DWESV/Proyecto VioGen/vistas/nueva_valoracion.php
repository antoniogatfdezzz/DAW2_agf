<?php
/**
 * NUEVA VALORACIÓN VPR
 * Formulario completo con los 35 indicadores del protocolo VPR 5.0-H
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modelos/valoraciones.php';
require_once __DIR__ . '/../modelos/victimas.php';
require_once __DIR__ . '/../modelos/agresores.php';

// Verificar autenticación
if (!estaAutenticado() || !tieneRol(ROL_POLICIA) && !tieneRol(ROL_ADMIN)) {
    header('Location: login.html?error=Debe iniciar sesión como policía');
    exit;
}

// Obtener ID de víctima si viene por parámetro
$victima_id = $_GET['victima_id'] ?? null;
$victima = null;
if ($victima_id) {
    global $victimas;
    $victima = $victimas[$victima_id] ?? null;
}

// Obtener todas las víctimas y agresores para los selectores
global $victimas, $agresores;
$lista_victimas = array_values($victimas);
$lista_agresores = array_values($agresores);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'victima_id' => $_POST['victima_id'],
        'agresor_id' => $_POST['agresor_id'],
        'tipo' => 'VPR',
        'evaluador_id' => $_SESSION['usuario_id'],
        'indicadores' => []
    ];
    
    // Recoger los 35 indicadores
    for ($i = 1; $i <= 35; $i++) {
        $indicador_key = "indicador_$i";
        $datos['indicadores'][$indicador_key] = isset($_POST[$indicador_key]) ? 'SI' : 'NO';
    }
    
    // Observaciones y ajustes
    $datos['observaciones_evaluador'] = $_POST['observaciones_evaluador'] ?? '';
    $datos['medidas_recomendadas'] = $_POST['medidas_recomendadas'] ?? '';
    
    // Crear valoración (incluye cálculo automático de riesgo)
    $resultado = crearValoracionVPR($datos);
    
    if (isset($resultado['error'])) {
        $error = $resultado['error'];
    } else {
        guardarValoraciones();
        header('Location: ver_valoracion.php?id=' . $resultado['id'] . '&mensaje=Valoración VPR creada correctamente');
        exit;
    }
}

global $INDICADORES_VPR;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Nueva Valoración VPR</title>
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
        .indicador {
            background: #F5F5F5;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 6px;
            border-left: 4px solid #E0E0E0;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .indicador.critico {
            border-left-color: #D32F2F;
            background: #FFEBEE;
        }
        .indicador.alto {
            border-left-color: #FF5722;
            background: #FBE9E7;
        }
        .indicador.medio {
            border-left-color: #FF9800;
            background: #FFF3E0;
        }
        .indicador.bajo {
            border-left-color: #FFC107;
            background: #FFFDE7;
        }
        .indicador.positivo {
            border-left-color: #4CAF50;
            background: #E8F5E9;
        }
        .indicador-checkbox {
            flex-shrink: 0;
            margin-top: 4px;
        }
        .indicador-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        .indicador-content {
            flex: 1;
        }
        .indicador-numero {
            font-weight: bold;
            color: #D32F2F;
            margin-right: 0.5rem;
        }
        .indicador-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }
        .badge-CRITICO { background: #D32F2F; color: white; }
        .badge-ALTO { background: #FF5722; color: white; }
        .badge-MEDIO { background: #FF9800; color: white; }
        .badge-BAJO { background: #FFC107; color: #333; }
        .badge-POSITIVO { background: #4CAF50; color: white; }
        
        .calculo-auto {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .contador-indicadores {
            position: sticky;
            top: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            margin-bottom: 1rem;
        }
        .contador-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.5rem;
            margin-top: 0.75rem;
        }
        .contador-item {
            text-align: center;
            padding: 0.5rem;
            border-radius: 4px;
            background: #F5F5F5;
        }
        .contador-num {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
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
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="registrar_agresor.php">✏️ Registrar Agresor</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header-bar">
                <h1>📋 Nueva Valoración Policial del Riesgo (VPR)</h1>
                <a href="valoraciones_lista.php" class="btn btn-secondary">← Volver</a>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <div class="calculo-auto">
                <h3 style="margin-top: 0;">⚡ Cálculo Automático de Riesgo</h3>
                <p>El nivel de riesgo se calculará automáticamente según los indicadores marcados y los pesos establecidos en el protocolo VPR 5.0-H.</p>
                <p><strong>Puntuaciones:</strong> CRÍTICO: 8 puntos | ALTO: 6 puntos | MEDIO: 4 puntos | BAJO: 2 puntos | POSITIVO: -4 puntos</p>
            </div>

            <div class="contador-indicadores" id="contador">
                <strong>📊 Indicadores Marcados:</strong>
                <div class="contador-grid">
                    <div class="contador-item">
                        <div class="contador-num" style="color: #D32F2F;" id="count-critico">0</div>
                        <div>CRÍTICO</div>
                    </div>
                    <div class="contador-item">
                        <div class="contador-num" style="color: #FF5722;" id="count-alto">0</div>
                        <div>ALTO</div>
                    </div>
                    <div class="contador-item">
                        <div class="contador-num" style="color: #FF9800;" id="count-medio">0</div>
                        <div>MEDIO</div>
                    </div>
                    <div class="contador-item">
                        <div class="contador-num" style="color: #FFC107;" id="count-bajo">0</div>
                        <div>BAJO</div>
                    </div>
                    <div class="contador-item">
                        <div class="contador-num" style="color: #4CAF50;" id="count-positivo">0</div>
                        <div>POSITIVO</div>
                    </div>
                    <div class="contador-item">
                        <div class="contador-num" style="color: #333;" id="count-total">0</div>
                        <div>TOTAL</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="" id="formVPR">
                <!-- SELECCIÓN VÍCTIMA Y AGRESOR -->
                <div class="form-section">
                    <h3>👥 Víctima y Agresor</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="victima_id">Víctima *</label>
                            <select id="victima_id" name="victima_id" required>
                                <option value="">-- Seleccionar Víctima --</option>
                                <?php foreach ($lista_victimas as $v): ?>
                                <option value="<?php echo $v['id']; ?>" <?php echo $victima && $victima['id'] === $v['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($v['nombre'] . ' ' . $v['apellidos'] . ' - ' . $v['num_documento']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="agresor_id">Agresor *</label>
                            <select id="agresor_id" name="agresor_id" required>
                                <option value="">-- Seleccionar Agresor --</option>
                                <?php foreach ($lista_agresores as $a): ?>
                                <option value="<?php echo $a['id']; ?>">
                                    <?php echo htmlspecialchars($a['nombre'] . ' ' . $a['apellidos'] . ' - ' . ($a['num_documento'] ?? 'Sin documento')); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- FACTOR 1: GRAVEDAD DEL EPISODIO ACTUAL -->
                <div class="form-section">
                    <h3>🚨 FACTOR 1: Gravedad del Episodio Actual</h3>
                    
                    <?php 
                    $factor1 = array_slice($INDICADORES_VPR, 0, 8, true);
                    foreach ($factor1 as $key => $indicador): 
                        $num = (int)str_replace('indicador_', '', $key);
                        $peso = $indicador['peso'];
                        $clase_peso = strtolower($peso);
                    ?>
                    <div class="indicador <?php echo $clase_peso; ?>">
                        <div class="indicador-checkbox">
                            <input type="checkbox" 
                                   id="<?php echo $key; ?>" 
                                   name="<?php echo $key; ?>" 
                                   data-peso="<?php echo $peso; ?>"
                                   onchange="actualizarContador()">
                        </div>
                        <div class="indicador-content">
                            <label for="<?php echo $key; ?>">
                                <span class="indicador-numero"><?php echo $num; ?>.</span>
                                <?php echo htmlspecialchars($indicador['texto']); ?>
                                <span class="indicador-badge badge-<?php echo $peso; ?>"><?php echo $peso; ?></span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- FACTOR 2: HISTORIAL DE VIOLENCIA -->
                <div class="form-section">
                    <h3>📜 FACTOR 2: Historial de Violencia</h3>
                    
                    <?php 
                    $factor2 = array_slice($INDICADORES_VPR, 8, 6, true);
                    foreach ($factor2 as $key => $indicador): 
                        $num = (int)str_replace('indicador_', '', $key);
                        $peso = $indicador['peso'];
                        $clase_peso = strtolower($peso);
                    ?>
                    <div class="indicador <?php echo $clase_peso; ?>">
                        <div class="indicador-checkbox">
                            <input type="checkbox" 
                                   id="<?php echo $key; ?>" 
                                   name="<?php echo $key; ?>"
                                   data-peso="<?php echo $peso; ?>"
                                   onchange="actualizarContador()">
                        </div>
                        <div class="indicador-content">
                            <label for="<?php echo $key; ?>">
                                <span class="indicador-numero"><?php echo $num; ?>.</span>
                                <?php echo htmlspecialchars($indicador['texto']); ?>
                                <span class="indicador-badge badge-<?php echo $peso; ?>"><?php echo $peso; ?></span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- FACTOR 3: PERFIL DEL AGRESOR -->
                <div class="form-section">
                    <h3>😡 FACTOR 3: Perfil del Agresor</h3>
                    
                    <?php 
                    $factor3 = array_slice($INDICADORES_VPR, 14, 9, true);
                    foreach ($factor3 as $key => $indicador): 
                        $num = (int)str_replace('indicador_', '', $key);
                        $peso = $indicador['peso'];
                        $clase_peso = strtolower($peso);
                    ?>
                    <div class="indicador <?php echo $clase_peso; ?>">
                        <div class="indicador-checkbox">
                            <input type="checkbox" 
                                   id="<?php echo $key; ?>" 
                                   name="<?php echo $key; ?>"
                                   data-peso="<?php echo $peso; ?>"
                                   onchange="actualizarContador()">
                        </div>
                        <div class="indicador-content">
                            <label for="<?php echo $key; ?>">
                                <span class="indicador-numero"><?php echo $num; ?>.</span>
                                <?php echo htmlspecialchars($indicador['texto']); ?>
                                <span class="indicador-badge badge-<?php echo $peso; ?>"><?php echo $peso; ?></span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- FACTOR 4: VULNERABILIDAD DE LA VÍCTIMA -->
                <div class="form-section">
                    <h3>💔 FACTOR 4: Vulnerabilidad de la Víctima</h3>
                    
                    <?php 
                    $factor4 = array_slice($INDICADORES_VPR, 23, 7, true);
                    foreach ($factor4 as $key => $indicador): 
                        $num = (int)str_replace('indicador_', '', $key);
                        $peso = $indicador['peso'];
                        $clase_peso = strtolower($peso);
                    ?>
                    <div class="indicador <?php echo $clase_peso; ?>">
                        <div class="indicador-checkbox">
                            <input type="checkbox" 
                                   id="<?php echo $key; ?>" 
                                   name="<?php echo $key; ?>"
                                   data-peso="<?php echo $peso; ?>"
                                   onchange="actualizarContador()">
                        </div>
                        <div class="indicador-content">
                            <label for="<?php echo $key; ?>">
                                <span class="indicador-numero"><?php echo $num; ?>.</span>
                                <?php echo htmlspecialchars($indicador['texto']); ?>
                                <span class="indicador-badge badge-<?php echo $peso; ?>"><?php echo $peso; ?></span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- FACTOR 5: FACTORES PROTECTORES -->
                <div class="form-section">
                    <h3>✅ FACTOR 5: Factores Protectores (Restan Riesgo)</h3>
                    
                    <?php 
                    $factor5 = array_slice($INDICADORES_VPR, 30, 5, true);
                    foreach ($factor5 as $key => $indicador): 
                        $num = (int)str_replace('indicador_', '', $key);
                        $peso = $indicador['peso'];
                        $clase_peso = strtolower($peso);
                    ?>
                    <div class="indicador <?php echo $clase_peso; ?>">
                        <div class="indicador-checkbox">
                            <input type="checkbox" 
                                   id="<?php echo $key; ?>" 
                                   name="<?php echo $key; ?>"
                                   data-peso="<?php echo $peso; ?>"
                                   onchange="actualizarContador()">
                        </div>
                        <div class="indicador-content">
                            <label for="<?php echo $key; ?>">
                                <span class="indicador-numero"><?php echo $num; ?>.</span>
                                <?php echo htmlspecialchars($indicador['texto']); ?>
                                <span class="indicador-badge badge-<?php echo $peso; ?>"><?php echo $peso; ?> (resta)</span>
                            </label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- OBSERVACIONES Y MEDIDAS -->
                <div class="form-section">
                    <h3>📝 Observaciones y Medidas</h3>
                    
                    <div class="form-group">
                        <label for="observaciones_evaluador">Observaciones del Evaluador</label>
                        <textarea id="observaciones_evaluador" name="observaciones_evaluador" rows="4" placeholder="Añada cualquier observación relevante sobre el caso..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="medidas_recomendadas">Medidas de Protección Recomendadas</label>
                        <textarea id="medidas_recomendadas" name="medidas_recomendadas" rows="4" placeholder="Ej: Orden de alejamiento, protección policial, teleasistencia, medidas sobre menores..."></textarea>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="form-section">
                    <div class="form-row">
                        <button type="submit" class="btn btn-danger">
                            ✅ Crear Valoración VPR (Cálculo Automático)
                        </button>
                        <a href="valoraciones_lista.php" class="btn btn-secondary">
                            ❌ Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        // Contador de indicadores por peso
        const contadores = {
            'CRITICO': 0,
            'ALTO': 0,
            'MEDIO': 0,
            'BAJO': 0,
            'POSITIVO': 0
        };

        function actualizarContador() {
            // Resetear contadores
            Object.keys(contadores).forEach(key => contadores[key] = 0);
            
            // Contar checkboxes marcados por peso
            const checkboxes = document.querySelectorAll('input[type="checkbox"][data-peso]');
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const peso = cb.getAttribute('data-peso');
                    contadores[peso]++;
                }
            });
            
            // Actualizar display
            document.getElementById('count-critico').textContent = contadores['CRITICO'];
            document.getElementById('count-alto').textContent = contadores['ALTO'];
            document.getElementById('count-medio').textContent = contadores['MEDIO'];
            document.getElementById('count-bajo').textContent = contadores['BAJO'];
            document.getElementById('count-positivo').textContent = contadores['POSITIVO'];
            
            const total = contadores['CRITICO'] + contadores['ALTO'] + contadores['MEDIO'] + contadores['BAJO'] + contadores['POSITIVO'];
            document.getElementById('count-total').textContent = total;
        }

        // Validación antes de enviar
        document.getElementById('formVPR').addEventListener('submit', function(e) {
            const victimaId = document.getElementById('victima_id').value;
            const agresorId = document.getElementById('agresor_id').value;
            
            if (!victimaId || !agresorId) {
                e.preventDefault();
                alert('Debe seleccionar una víctima y un agresor');
                return false;
            }
            
            // Confirmar envío
            const total = parseInt(document.getElementById('count-total').textContent);
            if (total === 0) {
                if (!confirm('No ha marcado ningún indicador. ¿Desea continuar?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</body>
</html>
