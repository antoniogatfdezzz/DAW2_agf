<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireUserType('arbitro');

$database = new Database();
$conn = $database->getConnection();

// Obtener ID del árbitro
$query = "SELECT id FROM arbitros WHERE usuario_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$arbitro_id = $stmt->fetchColumn();

// Obtener partidos asignados
$query = "SELECT p.*, p.pabellon_nombre as pabellon, cat.nombre as categoria,
                 ap.nombre as arbitro_principal, as2.nombre as arbitro_segundo, an.nombre as anotador
          FROM partidos p
          JOIN categorias cat ON p.categoria_id = cat.id
          LEFT JOIN arbitros ap ON p.arbitro_principal_id = ap.id
          LEFT JOIN arbitros as2 ON p.arbitro_segundo_id = as2.id
          LEFT JOIN arbitros an ON p.anotador_id = an.id
          WHERE p.arbitro_principal_id = ? OR p.arbitro_segundo_id = ? OR p.anotador_id = ?
          ORDER BY p.fecha ASC";
$stmt = $conn->prepare($query);
$stmt->execute([$arbitro_id, $arbitro_id, $arbitro_id]);
$partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas del árbitro
$stats = [];
$stats['partidos_total'] = count($partidos);
$stats['partidos_completados'] = count(array_filter($partidos, function($p) { return $p['finalizado']; }));
$stats['partidos_pendientes'] = $stats['partidos_total'] - $stats['partidos_completados'];

// Próximos partidos
$proximosPartidos = array_filter($partidos, function($p) {
    return strtotime($p['fecha']) >= time();
});

/**
 * Antonio Gat Fernández 
 * antoniogatfdez.me
 * me@antoniogatfdez.me
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Árbitro - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
   <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-volleyball-ball"></i>
                    <span>FEDEXVB - Árbitro</span>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <div class="user-name"><?php echo $_SESSION['user_name'] . ' ' . $_SESSION['user_lastname']; ?></div>
                        <div class="user-role">Árbitro</div>
                    </div>
                    <a href="../includes/logout.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="disponibilidad.php"><i class="fas fa-calendar-check"></i> Mi Disponibilidad</a></li>
            <li><a href="partidos.php"><i class="fa-solid fa-globe"></i> Mis Partidos</a></li>
            <li><a href="liquidaciones.php"><i class="fas fa-file-invoice-dollar"></i> Mis Liquidaciones</a></li>
            <li><a href="arbitros.php"><i class="fas fa-users"></i> Lista de Árbitros</a></li>
            <li><a href="perfil.php"><i class="fas fa-user-cog"></i> Mi Perfil</a></li>
            <li class="sidebar-logout-mobile">
                <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </li>
        </ul>

        <div class="sidebar-aviso" style="padding: 16px; margin-top: 50px; border-top: 1px solid #eee; background: #fffbe6; color: #333; font-size: 0.95em;">
                <strong><i class="fas fa-info-circle"></i> Aviso</strong><br>
                En el caso de tener un problema contacta con:<br>
                <span style="display:block;margin-top:4px;">
                    <i class="fas fa-envelope"></i> <a href="mailto:me@antoniogatfdez.me">me@antoniogatfdez.me</a><br>
                    <i class="fas fa-envelope"></i> <a href="mailto:soporte_tecnico@fedexvoleibol.com">soporte_tecnico@fedexvoleibol.com</a>
                </span>
            </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-table"></i> Mis Partidos Asignados</h1>
        </div>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-check"></i> Partidos asignados
            </div>
            <div class="card-body">
                <?php if (count($partidos) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Partido</th>
                                <th>Categoría</th>
                                <th>Pabellón</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partidos as $partido): ?>
                            <?php if ($partido['sets_local'] === null || $partido['sets_visitante'] === null): ?>
                            <tr>
                                <td><?php echo format_datetime($partido['fecha'], 'd/m/Y'); ?></td>
                                <td><?php echo format_datetime($partido['fecha'], 'H:i'); ?></td>
                                <td>
                                    <?php
                                    if (!empty($partido['equipo_local']) && !empty($partido['equipo_visitante'])) {
                                        echo htmlspecialchars($partido['equipo_local']) . ' vs ' . htmlspecialchars($partido['equipo_visitante']);
                                    } else {
                                        echo 'Partido ID: ' . $partido['id'];
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($partido['categoria']); ?></td>
                                    <td><?php echo htmlspecialchars($partido['pabellon']); ?></td>
                                <td>
                                    <?php if ($partido['fecha'] < date('Y-m-d H:i:s')): ?>
                                        <button class="btn btn-primary btn-sm" onclick="abrirResultados(<?php echo $partido['id']; ?>)">
                                            <i class="fas fa-edit"></i> Introducir Resultado
                                        </button>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--warning); color: white;">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center p-4">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--medium-gray);"></i>
                    <h4 class="mt-3">No tienes partidos asignados</h4>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Modal Resultados Partido (igual que partidos.php) -->
        <div id="resultadosModal" class="modal">
            <div class="modal-content" style="max-width: 600px;">
                <div class="modal-header">
                    <h2><i class="fas fa-trophy"></i> Registrar Resultado</h2>
                    <span class="close" onclick="closeModal('resultadosModal')">&times;</span>
                </div>
                <form id="formResultado" onsubmit="event.preventDefault(); guardarResultado();" enctype="multipart/form-data">
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <input type="hidden" id="resultadoPartidoId" name="partido_id">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h4 class="text-center mb-3">
                                    <span id="resultadoEquipoLocal"></span> vs <span id="resultadoEquipoVisitante"></span>
                                </h4>
                                <p class="text-center text-muted" id="resultadoFecha"></p>
                            </div>
                        </div>
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div class="form-group">
                                <label class="form-label">Sets ganados - <span id="equipoLocalLabel">Local</span></label>
                                <select id="setsLocal" name="sets_local" class="form-control" onchange="generarSets()" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sets ganados - <span id="equipoVisitanteLabel">Visitante</span></label>
                                <select id="setsVisitante" name="sets_visitante" class="form-control" onchange="generarSets()" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>
                        <div id="setsContainer">
                            <!-- Se generarán dinámicamente los campos de sets -->
                        </div>
                        
                        <!-- Campo para subir foto del resultado -->
                        <div class="form-group mt-3">
                            <label class="form-label">
                                <i class="fas fa-camera"></i> Foto del Resultado (Opcional)
                            </label>
                            <input type="file" name="foto_resultado" class="form-control" accept="image/*,.heic,.heif">
                            <small class="form-text text-muted">
                                Formatos permitidos: JPG, PNG, GIF, HEIC. Tamaño máximo: 5MB
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('resultadosModal')">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar Resultado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="../assets/js/app.js"></script>
    <script>
        function abrirResultados(partidoId) {
            // Cargar datos del partido
            fetch(`api/partidos.php?id=${partidoId}`)
                .then(response => response.json())
                .then(partido => {
                    if (partido && !partido.error) {
                        document.getElementById('resultadoPartidoId').value = partidoId;
                        document.getElementById('resultadoEquipoLocal').textContent = partido.equipo_local;
                        document.getElementById('resultadoEquipoVisitante').textContent = partido.equipo_visitante;
                        document.getElementById('resultadoFecha').textContent = `${partido.fecha} ${partido.hora}`;
                        document.getElementById('equipoLocalLabel').textContent = partido.equipo_local;
                        document.getElementById('equipoVisitanteLabel').textContent = partido.equipo_visitante;
                        // Reset form
                        document.getElementById('formResultado').reset();
                        document.getElementById('resultadoPartidoId').value = partidoId;
                        document.getElementById('setsContainer').innerHTML = '';
                        openModal('resultadosModal');
                    } else {
                        showNotification('Error al cargar los datos del partido', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al cargar los datos del partido', 'error');
                });
        }

        function generarSets() {
            const setsLocal = parseInt(document.getElementById('setsLocal').value) || 0;
            const setsVisitante = parseInt(document.getElementById('setsVisitante').value) || 0;
            const totalSets = setsLocal + setsVisitante;
            if (totalSets < 3 || totalSets > 5) {
                showNotification('Un partido de voleibol debe tener entre 3 y 5 sets', 'error');
                return;
            }
            if (setsLocal === setsVisitante) {
                showNotification('No puede haber empate en voleibol', 'error');
                return;
            }
            if ((setsLocal > 3 || setsVisitante > 3) || (setsLocal < 2 && setsVisitante < 2)) {
                showNotification('Resultado inválido para voleibol', 'error');
                return;
            }
            const container = document.getElementById('setsContainer');
            const equipoLocal = document.getElementById('equipoLocalLabel').textContent;
            const equipoVisitante = document.getElementById('equipoVisitanteLabel').textContent;
            container.innerHTML = '';
            for (let i = 1; i <= totalSets; i++) {
                const setDiv = document.createElement('div');
                setDiv.className = 'form-row';
                setDiv.innerHTML = `
                    <h4>Set ${i}</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">${equipoLocal}</label>
                            <input type="number" name="set${i}_local" class="form-control" min="0" max="50" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">${equipoVisitante}</label>
                            <input type="number" name="set${i}_visitante" class="form-control" min="0" max="50" required>
                        </div>
                    </div>
                `;
                container.appendChild(setDiv);
            }
        }

        function guardarResultado() {
            // Validar que se hayan seleccionado los sets
            const setsLocal = document.getElementById('setsLocal').value;
            const setsVisitante = document.getElementById('setsVisitante').value;
            
            if (!setsLocal || !setsVisitante) {
                showNotification('Debe seleccionar los sets ganados por cada equipo', 'error');
                return;
            }
            
            const formData = new FormData(document.getElementById('formResultado'));
            formData.append('action', 'guardar_resultado');
            
            console.log('Enviando datos:', formData);
            
            fetch('api/partidos.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        showNotification('Resultado guardado correctamente', 'success');
                        closeModal('resultadosModal');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Error al guardar el resultado', 'error');
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    showNotification('Error en la respuesta del servidor', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al guardar el resultado', 'error');
            });
        }
    </script>
    <?php if ($_SESSION['password_temporal']): ?>
    <script>
        // Mostrar modal para cambio de contraseña si es temporal
        document.addEventListener('DOMContentLoaded', function() {
            if (confirm('Debe cambiar su contraseña temporal. ¿Desea hacerlo ahora?')) {
                window.location.href = 'cambiar-password.php';
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
