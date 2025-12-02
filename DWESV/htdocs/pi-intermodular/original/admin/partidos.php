<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();
$message = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $message = createPartido($conn, $_POST);
            break;
        case 'edit':
            $message = editPartido($conn, $_POST);
            break;
        case 'delete':
            $message = deletePartido($conn, $_POST['partido_id']);
            break;
    }
}

// Obtener partidos
$query = "SELECT p.*, 
                 c.nombre as categoria_nombre,
                 p.pabellon_nombre,
                 p.equipo_local,
                 p.equipo_visitante,
                 p.observacion_partido,
                 CONCAT(a1.nombre, ' ', a1.apellidos) as arbitro1_nombre,
                 CONCAT(a2.nombre, ' ', a2.apellidos) as arbitro2_nombre,
                 CONCAT(an.nombre, ' ', an.apellidos) as anotador_nombre
          FROM partidos p
          LEFT JOIN categorias c ON p.categoria_id = c.id
          LEFT JOIN arbitros a1 ON p.arbitro_principal_id = a1.id
          LEFT JOIN arbitros a2 ON p.arbitro_segundo_id = a2.id
          LEFT JOIN arbitros an ON p.anotador_id = an.id
          ORDER BY p.fecha DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Los equipos han sido eliminados del sistema
$equipos = []; // Array vacío ya que no hay equipos

$query = "SELECT * FROM categorias ORDER BY nombre";
$stmt = $conn->prepare($query);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$query = "SELECT * FROM arbitros ORDER BY nombre, apellidos";
$stmt = $conn->prepare($query);
$stmt->execute();
$arbitros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Los pabellones ahora se introducen como texto libre
// $pabellones ya no es necesario

function createPartido($conn, $data) {
    try {
        $query = "INSERT INTO partidos (equipo_local, equipo_visitante, categoria_id, fecha, pabellon_nombre, arbitro_principal_id, arbitro_segundo_id, anotador_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            sanitize_input($data['equipo_local']),
            sanitize_input($data['equipo_visitante']),
            sanitize_input($data['categoria_id']),
            sanitize_input($data['fecha'] . ' ' . $data['hora']),
            sanitize_input($data['pabellon_nombre']),
            $data['arbitro_principal_id'] ?: null,
            $data['arbitro_segundo_id'] ?: null,
            $data['anotador_id'] ?: null
        ]);
        
        return success_message('Partido creado correctamente');
    } catch (Exception $e) {
        return error_message('Error al crear el partido: ' . $e->getMessage());
    }
}

function editPartido($conn, $data) {
    try {
        $query = "UPDATE partidos SET 
                    equipo_local = ?, equipo_visitante = ?, categoria_id = ?, 
                    fecha = ?, pabellon_nombre = ?, 
                    arbitro_principal_id = ?, arbitro_segundo_id = ?, anotador_id = ?
                  WHERE id = ?";
        
        $stmt = $conn->prepare($query);
        $stmt->execute([
            sanitize_input($data['equipo_local']),
            sanitize_input($data['equipo_visitante']),
            sanitize_input($data['categoria_id']),
            sanitize_input($data['fecha'] . ' ' . $data['hora']),
            sanitize_input($data['pabellon_nombre']),
            $data['arbitro_principal_id'] ?: null,
            $data['arbitro_segundo_id'] ?: null,
            $data['anotador_id'] ?: null,
            $data['partido_id']
        ]);
        
        return success_message('Partido actualizado correctamente');
    } catch (Exception $e) {
        return error_message('Error al actualizar el partido: ' . $e->getMessage());
    }
}

function deletePartido($conn, $partido_id) {
    try {
        $query = "DELETE FROM partidos WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$partido_id]);
        
        return success_message('Partido eliminado correctamente');
    } catch (Exception $e) {
        return error_message('Error al eliminar el partido');
    }
}

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
    <title>Gestión de Partidos - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/search-bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .equipos-container {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 200px;
        }
        
        .equipo-info {
            flex: 1;
            text-align: center;
        }
        
        .equipo-local, .equipo-visitante {
            display: block;
            font-size: 0.9em;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        
        .equipo-local {
            color: var(--primary-green);
        }
        
        .equipo-visitante {
            color: var(--primary-black);
        }
        
        .vs-separator {
            flex-shrink: 0;
            padding: 0 5px;
        }
        
        .vs-text {
            font-size: 0.8em;
            font-weight: bold;
            color: var(--medium-gray);
            background: var(--light-gray);
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        .required {
            color: #e74c3c;
            margin-left: 2px;
        }
        
        @media (max-width: 768px) {
            .equipos-container {
                flex-direction: column;
                gap: 5px;
                min-width: auto;
            }
            
            .vs-separator {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                    <i class="fas fa-volleyball-ball"></i>
                    <span>FEDEXVB - Administrador</span>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <div class="user-name"><?php echo $_SESSION['user_name'] . ' ' . $_SESSION['user_lastname']; ?></div>
                        <div class="user-role">Administrador</div>
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
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="usuarios.php"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
            <li><a href="partidos.php" class="active"><i class="fas fa-calendar-alt"></i> Gestión de Partidos</a></li>
            <li><a href="arbitros.php"><i class="fa-solid fa-person"></i> Gestión de Árbitros</a></li>
            <li><a href="licencias.php"><i class="fas fa-id-card"></i> Gestión de Licencias</a></li>
            <li><a href="liquidaciones.php"><i class="fas fa-file-invoice-dollar"></i> Liquidaciones</a></li>
            <li><a href="perfil.php"><i class="fas fa-user-cog"></i> Mi Perfil</a></li>
            <li class="sidebar-logout-mobile">
                <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Contenido Principal -->
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-calendar-alt"></i> Gestión de Partidos</h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> <a href="dashboard.php">Inicio</a> / Gestión de Partidos
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Botones crear partido y cargar desde Excel -->
        <div class="mb-3" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button onclick="openModal('createPartidoModal')" class="btn btn-primary">
                <i class="fas fa-calendar-plus"></i> Crear Partido
            </button>
            <button onclick="openModal('cargarPartidosModal')" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Cargar Partidos (Excel)
            </button>
        </div>

        <!-- Lista de partidos -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Lista de Partidos
                <span class="badge" style="background: var(--primary-green); color: white; margin-left: 10px;">
                    <span id="total-count"><?php echo count($partidos); ?></span> partidos
                </span>
            </div>
            <div class="card-body">
                <!-- Barra de búsqueda -->
                <div class="search-container">
                    <div class="search-input-group">
                        <div class="search-input-icon">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Buscar por equipos, categoría, pabellón, árbitros, fecha..." 
                                   class="search-input-field">
                        </div>
                        <button type="button" 
                                id="searchClear" 
                                class="btn search-clear-btn" 
                                style="display: none;"
                                title="Limpiar búsqueda">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                    <div id="searchInfo" class="search-info" style="display: none;">
                        <i class="fas fa-info-circle"></i> 
                        <span id="searchResults">0</span> partido(s) encontrado(s)
                    </div>
                    <div class="search-help">
                        <i class="fas fa-lightbulb"></i> 
                        <em>Busca por equipos, categoría, pabellón, árbitros o fecha. Usa ESC para limpiar.</em>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table data-table searchable-table" id="partidosTable">
                        <thead>
                            <tr>
                                <th data-sortable>Fecha</th>
                                <th data-sortable>Hora</th>
                                <th data-sortable>Equipos</th>
                                <th data-sortable>Resultado</th>
                                <th data-sortable>Categoría</th>
                                <th data-sortable>Pabellón</th>
                                <th>Árbitros</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($partidos as $partido): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($partido['fecha'])); ?></td>
                                <td><?php echo date('H:i', strtotime($partido['fecha'])); ?></td>
                                <td>
                                    <div class="equipos-container">
                                        <div class="equipo-info">
                                            <strong class="equipo-local"><?php echo htmlspecialchars($partido['equipo_local']); ?></strong>
                                            <small class="text-muted">Local</small>
                                        </div>
                                        <div class="vs-separator">
                                            <span class="vs-text">VS</span>
                                        </div>
                                        <div class="equipo-info">
                                            <strong class="equipo-visitante"><?php echo htmlspecialchars($partido['equipo_visitante']); ?></strong>
                                            <small class="text-muted">Visitante</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($partido['sets_local'] !== null && $partido['sets_visitante'] !== null): ?>
                                        <span class="badge" style="background: var(--success);">
                                            <?php echo $partido['sets_local']; ?> - <?php echo $partido['sets_visitante']; ?>
                                        </span>
                                        <br>
                                        <small style="color: var(--success);">Finalizado</small>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--warning);">
                                            Sin resultado
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($partido['observacion_partido'])): ?>
                                        <br>
                                        <span class="badge" style="background: var(--warning); margin-top: 5px;" title="Tiene observaciones">
                                            <i class="fas fa-comment"></i> Obs.
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--info);">
                                        <?php echo $partido['categoria_nombre']; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($partido['pabellon_nombre']); ?></strong>
                                </td>
                                <td>
                                    <small>
                                        <strong>1º:</strong> <?php echo $partido['arbitro1_nombre'] ?: '-'; ?><br>
                                        <strong>2º:</strong> <?php echo $partido['arbitro2_nombre'] ?: '-'; ?><br>
                                        <strong>Anot:</strong> <?php echo $partido['anotador_nombre'] ?: '-'; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    // Determinar color del botón según estado del partido
                                    $btnClass = 'btn'; // Por defecto: Sin resultado (Amarillo)
                                    $btnTitle = 'Sin resultado';
                                    
                                    if ($partido['sets_local'] !== null && $partido['sets_visitante'] !== null) {
                                        // Tiene resultado
                                        if (!empty($partido['foto_resultado'])) {
                                            // Con resultado y con acta (Verde)
                                            $btnClass = 'btn-success';
                                            $btnTitle = 'Con resultado y acta';
                                        } else {
                                            // Con resultado pero sin acta (Naranja)
                                            $btnClass = 'btn-warning';
                                            $btnTitle = 'Con resultado, sin acta';
                                        }
                                    }
                                    ?>
                                    <button onclick="verDetalles(<?php echo $partido['id']; ?>)" 
                                            class="btn <?php echo $btnClass; ?> btn-sm" 
                                            <?php if ($btnClass === 'btn'): ?>
                                            style="background-color: #8d8d8dff; color: white; border-color: #ff9800;"
                                            <?php endif; ?>
                                            title="<?php echo $btnTitle; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($partido['sets_local'] === null): ?>
                                        <button onclick="abrirResultados(<?php echo $partido['id']; ?>)" class="btn btn-success btn-sm">
                                            <i class="fas fa-trophy"></i>
                                        </button>
                                    <?php else: ?>
                                        <button onclick="editarResultados(<?php echo $partido['id']; ?>)" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="editPartido(<?php echo $partido['id']; ?>)" class="btn btn-info btn-sm">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <button onclick="deletePartido(<?php echo $partido['id']; ?>)" class="btn btn-danger btn-sm btn-delete" 
                                            data-message="¿Está seguro de eliminar este partido?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Crear Partido -->
    <div id="createPartidoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-plus"></i> Crear Nuevo Partido</h2>
                <span class="close" onclick="closeModal('createPartidoModal')">&times;</span>
            </div>
            <form method="POST" class="validate-form">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Equipo Local <span class="required">*</span></label>
                            <input type="text" 
                                   name="equipo_local" 
                                   class="form-control" 
                                   placeholder="Ej: Club Voleibol Badajoz" 
                                   maxlength="100" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Equipo Visitante <span class="required">*</span></label>
                            <input type="text" 
                                   name="equipo_visitante" 
                                   class="form-control" 
                                   placeholder="Ej: Club Deportivo Cáceres" 
                                   maxlength="100" 
                                   required>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="categoria_id" class="form-control" required>
                                <option value="">Seleccione categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hora</label>
                            <input type="time" name="hora" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pabellón <span class="required">*</span></label>
                        <input type="text" 
                               name="pabellon_nombre" 
                               class="form-control" 
                               placeholder="Ej: Pabellón Municipal de Badajoz - Badajoz" 
                               maxlength="200" 
                               required>
                        <small class="form-text text-muted">
                            Introduce el nombre completo del pabellón y la ciudad
                        </small>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label class="form-label">1º Árbitro</label>
                            <select name="arbitro_principal_id" class="form-control">
                                <option value="">Seleccione árbitro</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                <option value="<?php echo $arbitro['id']; ?>"><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">2º Árbitro</label>
                            <select name="arbitro_segundo_id" class="form-control">
                                <option value="">Seleccione árbitro</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                <option value="<?php echo $arbitro['id']; ?>"><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Anotador</label>
                            <select name="anotador_id" class="form-control">
                                <option value="">Seleccione anotador</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                <option value="<?php echo $arbitro['id']; ?>"><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createPartidoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Partido
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cargar Partidos desde Excel -->
    <div id="cargarPartidosModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2><i class="fas fa-file-excel"></i> Cargar Partidos desde Excel/CSV</h2>
                <span class="close" onclick="closeModal('cargarPartidosModal')">&times;</span>
            </div>
            <form id="formCargarExcel" onsubmit="event.preventDefault(); cargarPartidosDesdeExcel();">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="alert alert-info" style="margin-bottom: 20px;">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Instrucciones:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <li>El archivo debe ser CSV (convierte tu Excel a CSV primero)</li>
                            <li>La primera fila debe contener los nombres de las columnas</li>
                            <li>Los árbitros deben estar previamente configurados con sus alias</li>
                            <li>Las categorías se forman combinando: Categoría + Sexo (ej: "INFANTIL Femenino")</li>
                        </ul>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-columns"></i> Columnas requeridas del CSV
                        </div>
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 0.9em;">
                                <div><strong>1.</strong> Jo</div>
                                <div><strong>2.</strong> Jo (jornada)</div>
                                <div><strong>3.</strong> Equipo_local</div>
                                <div><strong>4.</strong> Equipo_visitante</div>
                                <div><strong>5.</strong> Fecha</div>
                                <div><strong>6.</strong> Hora</div>
                                <div><strong>7.</strong> Pabellon</div>
                                <div><strong>8.</strong> Categoria</div>
                                <div><strong>9.</strong> Grupo</div>
                                <div><strong>10.</strong> Sexo</div>
                                <div><strong>11.</strong> 1er Arbitro</div>
                                <div><strong>12.</strong> 2º Árbitro</div>
                                <div><strong>13.</strong> Anotador/a</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-file-upload"></i> Seleccionar archivo CSV
                        </label>
                        <input type="file" 
                               id="excel_file" 
                               name="excel_file" 
                               class="form-control" 
                               accept=".csv" 
                               required>
                        <small class="form-text text-muted">
                            Solo se permiten archivos CSV. Tamaño máximo: 5MB
                        </small>
                    </div>

                    <div id="cargaProgress" style="display: none; margin-top: 20px;">
                        <div style="background: #f0f0f0; border-radius: 5px; height: 30px; overflow: hidden; position: relative;">
                            <div id="progressBar" style="background: var(--primary-green); height: 100%; width: 0%; transition: width 0.3s;"></div>
                            <span id="progressText" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-weight: bold; color: #333;">
                                Procesando...
                            </span>
                        </div>
                    </div>

                    <div id="resultadoCarga" style="display: none; margin-top: 20px;">
                        <!-- Se mostrará el resultado de la carga -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('cargarPartidosModal')">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnCargarExcel">
                        <i class="fas fa-upload"></i> Cargar Partidos
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Partido -->
    <div id="editPartidoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Editar Partido</h2>
                <span class="close" onclick="closeModal('editPartidoModal')">&times;</span>
            </div>
            <form method="POST" class="validate-form" id="editPartidoForm">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="partido_id" id="edit_partido_id">
                    
                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Equipo Local <span class="required">*</span></label>
                            <input type="text" 
                                   name="equipo_local" 
                                   id="edit_equipo_local" 
                                   class="form-control" 
                                   placeholder="Ej: Club Voleibol Badajoz" 
                                   maxlength="100" 
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Equipo Visitante <span class="required">*</span></label>
                            <input type="text" 
                                   name="equipo_visitante" 
                                   id="edit_equipo_visitante" 
                                   class="form-control" 
                                   placeholder="Ej: Club Deportivo Cáceres" 
                                   maxlength="100" 
                                   required>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label class="form-label">Categoría</label>
                            <select name="categoria_id" id="edit_categoria_id" class="form-control" required>
                                <option value="">Seleccione categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" id="edit_fecha" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hora</label>
                            <input type="time" name="hora" id="edit_hora" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pabellón <span class="required">*</span></label>
                        <input type="text" 
                               name="pabellon_nombre" 
                               id="edit_pabellon_nombre" 
                               class="form-control" 
                               placeholder="Ej: Pabellón Municipal de Badajoz - Badajoz" 
                               maxlength="200" 
                               required>
                        <small class="form-text text-muted">
                            Introduce el nombre completo del pabellón y la ciudad
                        </small>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label class="form-label">1º Árbitro</label>
                            <select name="arbitro_principal_id" id="edit_arbitro_principal_id" class="form-control">
                                <option value="">Seleccione árbitro</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                <option value="<?php echo $arbitro['id']; ?>"><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">2º Árbitro</label>
                            <select name="arbitro_segundo_id" id="edit_arbitro_segundo_id" class="form-control">
                                <option value="">Seleccione árbitro</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                <option value="<?php echo $arbitro['id']; ?>"><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Anotador</label>
                            <select name="anotador_id" id="edit_anotador_id" class="form-control">
                                <option value="">Seleccione anotador</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                <option value="<?php echo $arbitro['id']; ?>"><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editPartidoModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Partido
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detalles Partido -->
    <div id="detallesPartidoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> Detalles del Partido</h2>
                <span class="close" onclick="closeModal('detallesPartidoModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div id="detallesPartidoContent">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('detallesPartidoModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Resultados Partido -->
    <div id="resultadosModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2 id="resultadosModalTitle"><i class="fas fa-trophy"></i> Registrar Resultado</h2>
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
                            <i class="fas fa-camera"></i> Foto del Acta (Opcional)
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

    <!-- Modal Ver Foto -->
    <div id="fotoModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2><i class="fas fa-camera"></i> Foto del Acta</h2>
                <span class="close" onclick="closeModal('fotoModal')">&times;</span>
            </div>
            <div class="modal-body text-center" style="max-height: 70vh; overflow-y: auto;">
                <img id="fotoResultadoImg" src="" alt="Foto del Acta" style="max-width: 100%; height: auto; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('fotoModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function editPartido(partidoId) {
            // Obtener datos del partido
            fetch(`api/partidos.php?id=${partidoId}`)
                .then(response => response.json())
                .then(partido => {
                    if (partido) {
                        document.getElementById('edit_partido_id').value = partido.id;
                        document.getElementById('edit_equipo_local').value = partido.equipo_local;
                        document.getElementById('edit_equipo_visitante').value = partido.equipo_visitante;
                        document.getElementById('edit_categoria_id').value = partido.categoria_id;
                        document.getElementById('edit_fecha').value = partido.fecha_original;
                        document.getElementById('edit_hora').value = partido.hora_original;
                        document.getElementById('edit_pabellon_nombre').value = partido.pabellon_nombre;
                        document.getElementById('edit_arbitro_principal_id').value = partido.arbitro_principal_id || '';
                        document.getElementById('edit_arbitro_segundo_id').value = partido.arbitro_segundo_id || '';
                        document.getElementById('edit_anotador_id').value = partido.anotador_id || '';
                        
                        openModal('editPartidoModal');
                    }
                })
                .catch(error => {
                    showNotification('Error al cargar los datos del partido', 'error');
                });
        }

        function deletePartido(partidoId) {
            if (confirm('¿Está seguro de eliminar este partido?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="partido_id" value="${partidoId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Validación para evitar que los equipos tengan el mismo nombre
        document.addEventListener('DOMContentLoaded', function() {
            const equipoLocalInputs = document.querySelectorAll('[name="equipo_local"]');
            const equipoVisitanteInputs = document.querySelectorAll('[name="equipo_visitante"]');

            function validateEquipos(localInput, visitanteInput) {
                function checkEquipos() {
                    const localValue = localInput.value.trim().toLowerCase();
                    const visitanteValue = visitanteInput.value.trim().toLowerCase();
                    
                    if (localValue && visitanteValue && localValue === visitanteValue) {
                        showNotification('Los equipos no pueden tener el mismo nombre', 'error');
                        return false;
                    }
                    return true;
                }

                localInput.addEventListener('blur', checkEquipos);
                visitanteInput.addEventListener('blur', checkEquipos);
                
                // Validación en tiempo real
                localInput.addEventListener('input', function() {
                    setTimeout(checkEquipos, 500);
                });
                visitanteInput.addEventListener('input', function() {
                    setTimeout(checkEquipos, 500);
                });
            }

            // Aplicar validación a todos los formularios
            for (let i = 0; i < equipoLocalInputs.length; i++) {
                if (equipoVisitanteInputs[i]) {
                    validateEquipos(equipoLocalInputs[i], equipoVisitanteInputs[i]);
                }
            }
        });

        // Funciones para gestión de resultados
        function verDetalles(partidoId) {
            fetch(`api/partidos.php?id=${partidoId}`)
                .then(response => response.json())
                .then(partido => {
                    if (partido && !partido.error) {
                        let html = `
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <h4>Información del Partido</h4>
                                    <p><strong>Fecha:</strong> ${partido.fecha}</p>
                                    <p><strong>Hora:</strong> ${partido.hora}</p>
                                    <p><strong>Pabellón:</strong> ${partido.pabellon}</p>
                                </div>
                                <div>
                                    <h4>Equipos</h4>
                                    <p><strong>Local:</strong> ${partido.equipo_local || 'No definido'}</p>
                                    <p><strong>Visitante:</strong> ${partido.equipo_visitante || 'No definido'}</p>
                                    <p><strong>Categoría:</strong> ${partido.categoria || 'No definida'}</p>
                                </div>
                            </div>
                            
                            <h4>Equipo Arbitral</h4>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                                <div class="text-center">
                                    <strong>1º Árbitro</strong><br>
                                    ${partido.arbitro1_nombre || 'Sin asignar'}
                                </div>
                                <div class="text-center">
                                    <strong>2º Árbitro</strong><br>
                                    ${partido.arbitro2_nombre || 'Sin asignar'}
                                </div>
                                <div class="text-center">
                                    <strong>Anotador</strong><br>
                                    ${partido.anotador_nombre || 'Sin asignar'}
                                </div>
                            </div>
                        `;
                        
                        // Agregar resultados si existen
                        if (partido.sets_local !== null && partido.sets_visitante !== null) {
                            html += `
                                <h4 style="margin-top: 20px;">Resultado Final</h4>
                                <div class="card" style="background: var(--light-gray); margin-bottom: 15px;">
                                    <div class="card-body text-center">
                                        <h3 style="margin: 0; color: var(--primary-green);">
                                            ${partido.equipo_local} ${partido.sets_local} - ${partido.sets_visitante} ${partido.equipo_visitante}
                                        </h3>
                                        <p style="margin: 5px 0 0 0; color: var(--medium-gray);">
                                            Estado: <span style="color: var(--success);">${partido.estado}</span>
                                        </p>
                                    </div>
                                </div>
                            `;
                            
                            if (partido.sets_detalle && partido.sets_detalle.length > 0) {
                                html += `
                                    <h4>Detalle por Sets</h4>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Set</th>
                                                    <th>${partido.equipo_local}</th>
                                                    <th>${partido.equipo_visitante}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                
                                partido.sets_detalle.forEach(set => {
                                    const ganador = parseInt(set.puntos_local) > parseInt(set.puntos_visitante) ? 'local' : 'visitante';
                                    html += `
                                        <tr>
                                            <td><strong>Set ${set.numero_set}</strong></td>
                                            <td class="${ganador === 'local' ? 'text-success' : ''}" style="font-weight: ${ganador === 'local' ? 'bold' : 'normal'};">
                                                ${set.puntos_local}
                                            </td>
                                            <td class="${ganador === 'visitante' ? 'text-success' : ''}" style="font-weight: ${ganador === 'visitante' ? 'bold' : 'normal'};">
                                                ${set.puntos_visitante}
                                            </td>
                                        </tr>
                                    `;
                                });
                                
                                html += `
                                            </tbody>
                                        </table>
                                    </div>
                                `;
                            }
                        } else {
                            html += `
                                <div style="margin-top: 20px; padding: 15px; background: var(--warning); color: white; border-radius: 5px; text-align: center;">
                                    <i class="fas fa-clock"></i> Partido sin resultado registrado
                                </div>
                            `;
                        }
                        
                        // Agregar botón para ver foto si existe
                        console.log('Foto resultado:', partido.foto_resultado);
                        if (partido.foto_resultado && partido.foto_resultado !== null && partido.foto_resultado !== '') {
                            html += `
                                <div style="margin-top: 20px; text-align: center;">
                                    <button onclick="verFotoResultado('${partido.foto_resultado}')" class="btn btn-info">
                                        <i class="fas fa-camera"></i> Ver Foto del Acta
                                    </button>
                                </div>
                            `;
                        } else {
                            console.log('No hay foto disponible para este partido');
                        }
                        
                        // Agregar observaciones del árbitro si existen
                        if (partido.observacion_partido && partido.observacion_partido.trim() !== '') {
                            html += `
                                <div style="margin-top: 20px;">
                                    <h4><i class="fas fa-comment"></i> Observaciones del Árbitro</h4>
                                    <div class="card" style="background: #fff3cd; border-left: 4px solid var(--warning);">
                                        <div class="card-body">
                                            <p style="white-space: pre-wrap; margin: 0;">${partido.observacion_partido}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        
                        document.getElementById('detallesPartidoContent').innerHTML = html;
                        openModal('detallesPartidoModal');
                    } else {
                        showNotification('Error al cargar los detalles del partido', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al cargar los detalles del partido', 'error');
                });
        }

        function abrirResultados(partidoId) {
            cargarFormularioResultados(partidoId, false);
        }

        function editarResultados(partidoId) {
            cargarFormularioResultados(partidoId, true);
        }

        function cargarFormularioResultados(partidoId, esEdicion) {
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
                        
                        // Si es edición, cargar datos existentes
                        if (esEdicion && partido.sets_local !== null && partido.sets_visitante !== null) {
                            document.getElementById('setsLocal').value = partido.sets_local;
                            document.getElementById('setsVisitante').value = partido.sets_visitante;
                            
                            // Cargar detalles de sets si existen
                            if (partido.sets_detalle && partido.sets_detalle.length > 0) {
                                generarSets();
                                // Llenar los datos de los sets
                                partido.sets_detalle.forEach((set, index) => {
                                    const setNum = index + 1;
                                    const localInput = document.querySelector(`input[name="set${setNum}_local"]`);
                                    const visitanteInput = document.querySelector(`input[name="set${setNum}_visitante"]`);
                                    if (localInput) localInput.value = set.puntos_local;
                                    if (visitanteInput) visitanteInput.value = set.puntos_visitante;
                                });
                            }
                        }
                        
                        document.getElementById('resultadosModalTitle').innerHTML = esEdicion ? 
                            '<i class="fas fa-edit"></i> Editar Resultado' : 
                            '<i class="fas fa-trophy"></i> Registrar Resultado';
                        
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
        
        function verFotoResultado(nombreFoto) {
            console.log('Intentando mostrar foto:', nombreFoto);
            const modal = document.getElementById('fotoModal');
            const img = document.getElementById('fotoResultadoImg');
            
            // Event listener para manejar errores de carga de imagen
            img.onerror = function() {
                console.error('Error cargando imagen:', nombreFoto);
                showNotification('Error al cargar la imagen', 'error');
                closeModal('fotoModal');
            };
            
            img.onload = function() {
                console.log('Imagen cargada correctamente');
            };
            
            img.src = `../assets/uploads/${nombreFoto}`;
            openModal('fotoModal');
        }

        // Función para cargar partidos desde Excel/CSV
        function cargarPartidosDesdeExcel() {
            const form = document.getElementById('formCargarExcel');
            const fileInput = document.getElementById('excel_file');
            const progressContainer = document.getElementById('cargaProgress');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            const resultDiv = document.getElementById('resultadoCarga');
            
            // Validar que se haya seleccionado un archivo
            if (!fileInput.files || fileInput.files.length === 0) {
                showNotification('Por favor seleccione un archivo CSV', 'error');
                return;
            }
            
            const file = fileInput.files[0];
            
            // Validar tipo de archivo
            if (!file.name.toLowerCase().endsWith('.csv')) {
                showNotification('Por favor seleccione un archivo CSV válido', 'error');
                return;
            }
            
            // Validar tamaño (máximo 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showNotification('El archivo es demasiado grande (máximo 5MB)', 'error');
                return;
            }
            
            // Preparar FormData
            const formData = new FormData();
            formData.append('excel_file', file);
            
            // Mostrar barra de progreso
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressText.textContent = 'Procesando...';
            resultDiv.innerHTML = '';
            resultDiv.style.display = 'none';
            
            // Deshabilitar botón de envío
            const submitBtn = document.getElementById('btnCargarExcel');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            // Simular progreso
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 10;
                if (progress <= 90) {
                    progressBar.style.width = progress + '%';
                    progressText.textContent = `Procesando... ${progress}%`;
                }
            }, 200);
            
            // Realizar petición AJAX
            fetch('api/cargar_partidos_excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Detener simulación de progreso
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                progressText.textContent = 'Completado 100%';
                
                setTimeout(() => {
                    progressContainer.style.display = 'none';
                    
                    // Mostrar resultado
                    resultDiv.style.display = 'block';
                    
                    if (data.success) {
                        resultDiv.className = 'alert alert-success';
                        resultDiv.innerHTML = `
                            <h4><i class="fas fa-check-circle"></i> ${data.message}</h4>
                            <p><strong>Partidos procesados:</strong> ${data.total_procesados || 0}</p>
                            <p><strong>Insertados correctamente:</strong> ${data.insertados || 0}</p>
                            ${data.errores && data.errores.length > 0 ? `
                                <hr>
                                <p><strong>Errores encontrados (${data.errores.length}):</strong></p>
                                <ul style="max-height: 200px; overflow-y: auto; margin-bottom: 0;">
                                    ${data.errores.map(error => `<li>${error}</li>`).join('')}
                                </ul>
                            ` : ''}
                        `;
                        
                        // Mostrar notificación de éxito
                        showNotification(data.message, 'success');
                        
                        // Recargar página después de 3 segundos si todo fue exitoso
                        if (!data.errores || data.errores.length === 0) {
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        }
                    } else {
                        resultDiv.className = 'alert alert-danger';
                        resultDiv.innerHTML = `
                            <h4><i class="fas fa-exclamation-circle"></i> Error al procesar el archivo</h4>
                            <p>${data.message || 'Error desconocido'}</p>
                            ${data.errores && data.errores.length > 0 ? `
                                <hr>
                                <p><strong>Detalles de los errores:</strong></p>
                                <ul style="max-height: 200px; overflow-y: auto; margin-bottom: 0;">
                                    ${data.errores.map(error => `<li>${error}</li>`).join('')}
                                </ul>
                            ` : ''}
                        `;
                        
                        showNotification(data.message || 'Error al cargar los partidos', 'error');
                    }
                }, 500);
            })
            .catch(error => {
                console.error('Error:', error);
                clearInterval(progressInterval);
                progressContainer.style.display = 'none';
                
                resultDiv.style.display = 'block';
                resultDiv.className = 'alert alert-danger';
                resultDiv.innerHTML = `
                    <h4><i class="fas fa-exclamation-circle"></i> Error de conexión</h4>
                    <p>No se pudo comunicar con el servidor. Por favor, intente de nuevo.</p>
                `;
                
                showNotification('Error de conexión con el servidor', 'error');
            })
            .finally(() => {
                // Rehabilitar botón
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
    </script>
    
    <script src="../assets/js/search-bar.js"></script>
    <script>
        // Inicializar búsqueda para la tabla de partidos
        document.addEventListener('DOMContentLoaded', function() {
            new TableSearchBar({
                searchInputId: 'searchInput',
                clearBtnId: 'searchClear',
                searchInfoId: 'searchInfo',
                searchResultsId: 'searchResults',
                totalCountId: 'total-count',
                tableSelector: 'tbody tr',
                columnsCount: 8,
                noResultsId: 'noResultsRow'
            });
        });
    </script>
</body>
</html>
