<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();
$message = '';

// Ejecutar limpieza automática de licencias vencidas
desactivar_licencias_vencidas($conn);

// Obtener estadísticas de árbitros
$query = "SELECT a.*, 
                 COALESCE(stats.partidos_count, 0) as partidos_arbitrados,
                 COALESCE(disponibilidad_count, 0) as dias_disponibles
          FROM arbitros a
          LEFT JOIN (
              SELECT arbitro_principal_id as arbitro_id, COUNT(*) as partidos_count
              FROM partidos 
              WHERE arbitro_principal_id IS NOT NULL
              UNION ALL
              SELECT arbitro_segundo_id as arbitro_id, COUNT(*) as partidos_count
              FROM partidos 
              WHERE arbitro_segundo_id IS NOT NULL
              UNION ALL
              SELECT anotador_id as arbitro_id, COUNT(*) as partidos_count
              FROM partidos 
              WHERE anotador_id IS NOT NULL
          ) stats ON a.id = stats.arbitro_id
          LEFT JOIN (
              SELECT arbitro_id, COUNT(*) as disponibilidad_count
              FROM disponibilidad_arbitros
              WHERE (COALESCE(manana,0) = 1 OR COALESCE(tarde,0) = 1) AND fecha >= CURDATE()
              GROUP BY arbitro_id
          ) disp ON a.id = disp.arbitro_id
          GROUP BY a.id
          ORDER BY a.nombre, a.apellidos";
$stmt = $conn->prepare($query);
$stmt->execute();
$arbitros = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener disponibilidad de árbitros para el mes actual
$currentMonth = date('Y-m');
$firstDay = $currentMonth . '-01';
$lastDay = date('Y-m-t', strtotime($firstDay));

$query = "SELECT a.id, a.nombre, a.apellidos, 
                 GROUP_CONCAT(CASE WHEN (COALESCE(da.manana,0)=1 OR COALESCE(da.tarde,0)=1) THEN da.fecha END ORDER BY da.fecha) as fechas_disponibles,
                 SUM(CASE WHEN (COALESCE(da.manana,0)=1 OR COALESCE(da.tarde,0)=1) THEN 1 ELSE 0 END) as dias_disponibles_mes
          FROM arbitros a
          LEFT JOIN disponibilidad_arbitros da ON a.id = da.arbitro_id 
              AND da.fecha BETWEEN ? AND ?
          GROUP BY a.id
          ORDER BY a.nombre, a.apellidos";
$stmt = $conn->prepare($query);
$stmt->execute([$firstDay, $lastDay]);
$disponibilidad_mes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Gestión de Árbitros - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- styles moved to assets/css/style.css -->
    <link rel="stylesheet" href="../assets/css/search-bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <li><a href="partidos.php"><i class="fas fa-calendar-alt"></i> Gestión de Partidos</a></li>
            <li><a href="arbitros.php" class="active"><i class="fa-solid fa-person"></i> Gestión de Árbitros</a></li>
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
            <h1><i class="fa-solid fa-person"></i> Gestión de Árbitros</h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> <a href="dashboard.php">Inicio</a> / Gestión de Árbitros
            </div>
        </div>

        <!-- Estadísticas generales 
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card">
                <div class="card-header" style="background: var(--info);">
                    <i class="fas fa-users"></i> Total Árbitros
                </div>
                <div class="card-body text-center">
                    <h2 style="margin: 0; color: var(--info);"><?php echo count($arbitros); ?></h2>
                    <p style="margin: 0; color: var(--medium-gray);">Árbitros registrados</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="background: var(--success);">
                    <i class="fas fa-calendar-check"></i> Disponibles Hoy
                </div>
                <div class="card-body text-center">
                    <?php
                    $today = date('Y-m-d');
                    $query = "SELECT COUNT(*) FROM disponibilidad_arbitros WHERE fecha = ? AND (COALESCE(manana,0) = 1 OR COALESCE(tarde,0) = 1)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$today]);
                    $disponibles_hoy = $stmt->fetchColumn();
                    ?>
                    <h2 style="margin: 0; color: var(--success);"><?php echo $disponibles_hoy; ?></h2>
                    <p style="margin: 0; color: var(--medium-gray);">Disponibles hoy</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header" style="background: var(--warning);">
                    <i class="fas fa-calendar-week"></i> Fin de Semana
                </div>
                <div class="card-body text-center">
                    <?php
                    $nextSaturday = date('Y-m-d', strtotime('next saturday'));
                    $nextSunday = date('Y-m-d', strtotime('next sunday'));
                    $query = "SELECT COUNT(DISTINCT arbitro_id) FROM disponibilidad_arbitros 
                              WHERE fecha IN (?, ?) AND (COALESCE(manana,0) = 1 OR COALESCE(tarde,0) = 1)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$nextSaturday, $nextSunday]);
                    $disponibles_finde = $stmt->fetchColumn();
                    ?>
                    <h2 style="margin: 0; color: var(--warning);"><?php echo $disponibles_finde; ?></h2>
                    <p style="margin: 0; color: var(--medium-gray);">Fin de semana próximo</p>
                </div>
            </div>
        </div> -->

        <!-- Consulta de disponibilidad por fecha -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-search"></i> Consultar Disponibilidad por Fecha
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: auto 1fr auto; gap: 15px; align-items: end; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="fechaConsulta" class="form-label">Seleccionar Fecha:</label>
                        <input type="date" 
                               id="fechaConsulta" 
                               class="form-control"
                               style="min-width: 180px;"
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <button type="button" 
                                onclick="consultarDisponibilidadFecha()" 
                                class="btn btn-primary">
                            <i class="fas fa-search"></i> Consultar Disponibilidad
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <button type="button" 
                                onclick="limpiarConsultaFecha()" 
                                class="btn btn-secondary"
                                id="btnLimpiarFecha"
                                style="display: none;">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
                
                <!-- Resultados de la consulta -->
                <div id="resultadosConsultaFecha" style="display: none;">
                    <div class="alert alert-info" id="infoConsultaFecha">
                        <i class="fas fa-info-circle"></i> 
                        <span id="textoInfoConsulta"></span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="tablaArbitrosFecha">
                            <thead>
                                <tr>
                                        <th>Árbitro</th>
                                        <th>Ciudad</th>
                                        <th>Licencia</th>
                                        <th>Disponibilidad</th>
                                        <th>Acciones</th>
                                    </tr>
                            </thead>
                            <tbody id="bodyArbitrosFecha">
                                <!-- Se llenará dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de árbitros y estadísticas -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Lista de Árbitros y Estadísticas
                <span class="badge" style="background: var(--primary-green); color: white; margin-left: 10px;">
                    <span id="total-count"><?php echo count($arbitros); ?></span> árbitros
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
                                   placeholder="Buscar por nombre, ciudad, licencia, número de licencia..." 
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
                        <span id="searchResults">0</span> árbitro(s) encontrado(s)
                    </div>
                    <div class="search-help">
                        <i class="fas fa-lightbulb"></i> 
                        <em>Busca por nombre, ciudad, tipo de licencia o número de licencia. Usa ESC para limpiar.</em>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table data-table searchable-table" id="arbitrosTable">
                        <thead>
                            <tr>
                                <th data-sortable>Nombre</th>
                                <th data-sortable>Ciudad</th>
                                <th data-sortable>Licencia</th>
                                <th data-sortable>Nº Licencia</th>
                                <th data-sortable>Partidos Temporada</th>
                                <th data-sortable>Días Disponibles</th>
                                <th>Disponibilidad Mes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arbitros as $arbitro): ?>
                            <tr data-licencia="<?php echo $arbitro['licencia']; ?>" data-ciudad="<?php echo $arbitro['ciudad']; ?>">
                                <td>
                                    <strong><?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos']; ?></strong>
                                </td>
                                <td><?php echo $arbitro['ciudad']; ?></td>
                                <td>
                                    <span class="badge" style="background: 
                                        <?php 
                                        switch($arbitro['licencia']) {
                                            case 'n3a':
                                                echo 'var(--success)';
                                                break;
                                            case 'n3b':
                                                echo '#28a745';
                                                break;
                                            case 'n3c':
                                                echo '#20c997';
                                                break;
                                            case 'n2':
                                                echo 'var(--info)';
                                                break;
                                            case 'n1':
                                                echo 'var(--warning)';
                                                break;
                                            case 'anotador':
                                                echo '#fd7e14';
                                                break;
                                            case 'colaborador':
                                            default:
                                                echo 'var(--medium-gray)';
                                                break;
                                        }
                                        ?>">
                                        <?php 
                                        switch($arbitro['licencia']) {
                                            case 'colaborador':
                                                echo 'COLABORADOR/A';
                                                break;
                                            case 'anotador':
                                                echo 'ANOTADOR';
                                                break;
                                            case 'habilitado_n1':
                                                echo 'HABILITADO N1';
                                                break;
                                            case 'habilitado_n2':
                                                echo 'HABILITADO N2';
                                                break;
                                            case 'habilitado_n3':
                                                echo 'HABILITADO N3';
                                                break;
                                            case 'n1':
                                                echo 'NIVEL I';
                                                break;
                                            case 'n2':
                                                echo 'NIVEL II';
                                                break;
                                            case 'n3c':
                                                echo 'NIVEL III C';
                                                break;
                                            case 'n3b':
                                                echo 'NIVEL III B';
                                                break;
                                            case 'n3a':
                                                echo 'NIVEL III A';
                                                break;
                                            default:
                                                echo strtoupper($arbitro['licencia']);
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($arbitro['numero_licencia']): ?>
                                        <span class="badge" style="background: var(--success); color: white;">
                                            <i class="fas fa-id-card"></i> <?php echo $arbitro['numero_licencia']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--warning); color: white;">
                                            <i class="fas fa-exclamation-triangle"></i> Sin asignar
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--primary-green);">
                                        <?php echo $arbitro['partidos_arbitrados']; ?> partidos
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background: var(--info);">
                                        <?php echo $arbitro['dias_disponibles']; ?> días
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $disponibilidad_arbitro = array_filter($disponibilidad_mes, function($d) use ($arbitro) {
                                        return $d['id'] == $arbitro['id'];
                                    });
                                    $disp = reset($disponibilidad_arbitro);
                                    ?>
                                    <small><?php echo $disp['dias_disponibles_mes'] ?? 0; ?> días este mes</small>
                                </td>
                                <td>
                                    <?php if (!$arbitro['numero_licencia']): ?>
                                        <button onclick="asignarNumeroLicencia(<?php echo $arbitro['id']; ?>, '<?php echo addslashes($arbitro['nombre'] . ' ' . $arbitro['apellidos']); ?>')" class="btn btn-warning btn-sm">
                                            <i class="fas fa-id-card-o"></i> Asignar Nº Licencia
                                        </button>
                                    <?php endif; ?>
                                    <button onclick="gestionarAlias(<?php echo $arbitro['id']; ?>, '<?php echo addslashes($arbitro['nombre'] . ' ' . $arbitro['apellidos']); ?>')" class="btn btn-primary btn-sm">
                                        <i class="fas fa-user-tag"></i> Alias
                                    </button>
                                    <button onclick="modificarDisponibilidad(<?php echo $arbitro['id']; ?>, '<?php echo addslashes($arbitro['nombre'] . ' ' . $arbitro['apellidos']); ?>')" class="btn btn-info btn-sm">
                                        <i class="fas fa-calendar"></i> Modificar Disponibilidad
                                    </button>
                                    <button onclick="verEstadisticas(<?php echo $arbitro['id']; ?>)" class="btn btn-success btn-sm">
                                        <i class="fas fa-chart-bar"></i> Estadísticas
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Disponibilidad del mes actual 
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i> Disponibilidad General - <?php echo date('F Y'); ?>
            </div>
            <div class="card-body">
                <div id="disponibilidadChart" style="min-height: 300px;">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Árbitro</th>
                                    <th>Días Disponibles</th>
                                    <th>Fechas Disponibles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($disponibilidad_mes as $disp): ?>
                                <tr>
                                    <td><?php echo $disp['nombre'] . ' ' . $disp['apellidos']; ?></td>
                                    <td>
                                        <span class="badge" style="background: var(--success);">
                                            <?php echo $disp['dias_disponibles_mes']; ?> días
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php 
                                            if ($disp['fechas_disponibles']) {
                                                $fechas = explode(',', $disp['fechas_disponibles']);
                                                echo implode(', ', array_slice($fechas, 0, 5));
                                                if (count($fechas) > 5) echo '...';
                                            } else {
                                                echo 'Sin disponibilidad';
                                            }
                                            ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> -->
    </main>

    <!-- Modal Asignar Número de Licencia -->
    <div id="numeroLicenciaModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2><i class="fas fa-id-card"></i> Asignar Número de Licencia</h2>
                <span class="close" onclick="closeModal('numeroLicenciaModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <form id="numeroLicenciaForm">
                    <input type="hidden" id="arbitroId" name="arbitro_id">
                    
                    <div class="form-group">
                        <label for="arbitroNombre" class="form-label">Árbitro:</label>
                        <div class="form-control-readonly" id="arbitroNombre" style="background: #f8f9fa; padding: 10px; border: 1px solid #e0e0e0; border-radius: 4px; font-weight: 600;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="numeroLicencia" class="form-label">Número de Licencia: *</label>
                        <input type="text" 
                               class="form-control" 
                               id="numeroLicencia" 
                               name="numero_licencia" 
                               placeholder="Ej: 19000, 05488, etc."
                               pattern="[A-Za-z0-9]{3,20}"
                               maxlength="20"
                               required>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Solo números, 5 caracteres. Debe ser único.
                        </small>
                    </div>
                    
                    <div class="alert alert-info" style="margin-top: 15px;">
                        <i class="fas fa-lightbulb"></i>
                        <strong>Sugerencias de formato:</strong>
                        <ul style="margin: 8px 0 0 20px;">
                            <li><strong>#####</strong> - Secuencia de cinco números</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('numeroLicenciaModal')">Cancelar</button>
                <button type="submit" form="numeroLicenciaForm" class="btn btn-warning">
                    <i class="fas fa-save"></i> Asignar Número
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Gestionar Alias -->
    <div id="aliasModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h2><i class="fas fa-user-tag"></i> Gestionar Alias del Árbitro</h2>
                <span class="close" onclick="closeModal('aliasModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <input type="hidden" id="aliasArbitroId">
                
                <div class="form-group">
                    <label class="form-label">Árbitro:</label>
                    <div class="form-control-readonly" id="aliasArbitroNombre" style="background: #f8f9fa; padding: 10px; border: 1px solid #e0e0e0; border-radius: 4px; font-weight: 600;"></div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>¿Qué son los alias?</strong> Los alias permiten identificar al árbitro con diferentes nombres en archivos CSV. 
                    Por ejemplo, si el árbitro es "Abel Acero Martínez", puedes crear alias como "Abel", "Abel Acero", "A. Acero", etc.
                </div>

                <!-- Formulario para agregar nuevo alias -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-plus"></i> Agregar Nuevo Alias
                    </div>
                    <div class="card-body">
                        <form id="formAgregarAlias" onsubmit="event.preventDefault(); agregarAlias();">
                            <div class="form-group" style="display: flex; gap: 10px; align-items: flex-end;">
                                <div style="flex: 1;">
                                    <label for="nuevoAlias" class="form-label">Alias:</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nuevoAlias" 
                                           placeholder="Ej: Abel, Abel Acero, A. Acero"
                                           maxlength="100"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de alias existentes -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list"></i> Alias Configurados
                        <span class="badge" id="totalAliasCount" style="background: var(--primary-green); color: white; margin-left: 10px;">0</span>
                    </div>
                    <div class="card-body">
                        <div id="listaAlias">
                            <div class="text-center text-muted" style="padding: 20px;">
                                <i class="fas fa-spinner fa-spin"></i> Cargando alias...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('aliasModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Ver Disponibilidad -->
    <div id="disponibilidadModal" class="modal">
        <div class="modal-content" style="max-width: 800px; width: 90%;">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-check"></i> Disponibilidad del Árbitro</h2>
                <span class="close" onclick="closeModal('disponibilidadModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div id="calendarioDisponibilidad">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('disponibilidadModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Modificar Disponibilidad -->
    <div id="modificarDisponibilidadModal" class="modal">
        <div class="modal-content" style="max-width: 900px; width: 95%;">
            <div class="modal-header">
                <h2><i class="fas fa-calendar-edit"></i> Modificar Disponibilidad del Árbitro</h2>
                <span class="close" onclick="closeModal('modificarDisponibilidadModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <input type="hidden" id="modificarArbitroId">
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Instrucciones:</strong>
                    <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                        <li>Seleccione el mes y año para ver y modificar la disponibilidad</li>
                        <li>Haga clic en un día para cambiar su disponibilidad (Mañana/Tarde/No disponible)</li>
                        <li>Puede agregar observaciones opcionales para cada día</li>
                        <li>Los cambios se guardan automáticamente</li>
                    </ul>
                </div>

                <div id="calendarioModificar">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modificarDisponibilidadModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal Estadísticas -->
    <div id="estadisticasModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-chart-bar"></i> Estadísticas del Árbitro</h2>
                <span class="close" onclick="closeModal('estadisticasModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div id="estadisticasContent">
                    <!-- Se cargará dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('estadisticasModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="test-elements.js"></script>
    <script>
        // Función auxiliar para formatear fecha en formato YYYY-MM-DD sin problemas de zona horaria
        function formatDateString(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function verDisponibilidad(arbitroId) {
            // Empezar siempre con el mes actual usando método robusto
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
            const currentMonthStr = `${currentYear}-${currentMonth}`;
            
            loadDisponibilidadCalendar(arbitroId, currentMonthStr);
        }
        
        function loadDisponibilidadCalendar(arbitroId, monthStr) {
            // Mostrar loading mientras se carga
            document.getElementById('calendarioDisponibilidad').innerHTML = `
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Cargando disponibilidad...</p>
                </div>
            `;
            
            fetch(`api/disponibilidad.php?arbitro_id=${arbitroId}&month=${monthStr}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('=== ADMIN: Datos de disponibilidad recibidos ===');
                    console.log('Datos recibidos:', data);
                    console.log('Mes solicitado:', monthStr);
                    console.log('====================================');
                    
                    const [year, month] = monthStr.split('-');
                    const yearNum = parseInt(year);
                    const monthNum = parseInt(month) - 1; // JavaScript months are 0-based
                    
                    console.log(`Procesando: año ${yearNum}, mes ${monthNum} (${monthNum + 1})`);
                    
                    const monthNames = [
                        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ];
                    
                    // Obtener nombre del árbitro
                    const arbitroRow = document.querySelector(`button[onclick="verDisponibilidad(${arbitroId})"]`).closest('tr');
                    const arbitroNombre = arbitroRow.querySelector('td:first-child strong').textContent;
                    
                    let html = `
                        <div class="calendar-disponibilidad">
                            <div class="calendar-header-admin">
                                <div class="calendar-navigation-admin">
                                    <button onclick="navigateMonth(${arbitroId}, '${monthStr}', -1)" class="btn-nav-admin" title="Mes anterior">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <h4>${arbitroNombre} - ${monthNames[monthNum]} ${yearNum}</h4>
                                    <button onclick="navigateMonth(${arbitroId}, '${monthStr}', 1)" class="btn-nav-admin" title="Mes siguiente">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="calendar-actions-admin">
                                    <button onclick="goToCurrentMonth(${arbitroId})" class="btn-today-admin" title="Ir al mes actual">
                                        <i class="fas fa-calendar-day"></i> Mes Actual (${new Date().toLocaleDateString('es-ES', {month: 'long', year: 'numeric'})})
                                    </button>
                                </div>
                            </div>
                            
                            <div class="calendar-grid-admin">
                                <!-- Headers de días -->
                                <div class="calendar-day-header-admin">Lun</div>
                                <div class="calendar-day-header-admin">Mar</div>
                                <div class="calendar-day-header-admin">Mié</div>
                                <div class="calendar-day-header-admin">Jue</div>
                                <div class="calendar-day-header-admin">Vie</div>
                                <div class="calendar-day-header-admin">Sáb</div>
                                <div class="calendar-day-header-admin">Dom</div>
                    `;
                    
                    // Calcular primer día del mes y ajustar para empezar en Lunes
                    const firstDay = new Date(yearNum, monthNum, 1);
                    let dayOfWeek = firstDay.getDay();
                    dayOfWeek = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Convertir domingo (0) a 6, resto -1
                    
                    const startDate = new Date(firstDay);
                    startDate.setDate(startDate.getDate() - dayOfWeek);
                    
                    const currentDateObj = new Date(startDate);
                    const today = new Date();
                    
                    // Generar 42 días (6 semanas completas)
                    for (let i = 0; i < 42; i++) {
                        const dayNumber = currentDateObj.getDate();
                        const isCurrentMonth = currentDateObj.getMonth() === monthNum;
                        const dateStr = formatDateString(currentDateObj);
                        const isToday = currentDateObj.toDateString() === today.toDateString();
                        
                        // Buscar disponibilidad para esta fecha
                        const dataForDate = data.find(d => d.fecha === dateStr);
                        const mananaFlag = dataForDate && (dataForDate.manana == 1 || dataForDate.manana === '1');
                        const tardeFlag = dataForDate && (dataForDate.tarde == 1 || dataForDate.tarde === '1');
                        const disponible = mananaFlag || tardeFlag;
                        const tieneInfo = dataForDate !== undefined && dataForDate !== null;
                        const observaciones = dataForDate ? ((dataForDate.observacion_manana || '') + (dataForDate.observacion_tarde ? '\n' + dataForDate.observacion_tarde : '')) : '';
                        
                        // Debug para fechas del mes actual
                        if (isCurrentMonth && i < 10) { // Solo los primeros 10 días para no saturar
                            console.log(`Día ${dayNumber}: dateStr=${dateStr}, tieneInfo=${tieneInfo}, disponible=${disponible}`);
                        }
                        
                        let dayClass = 'calendar-day-admin';
                        if (!isCurrentMonth) dayClass += ' other-month';
                        if (isToday) dayClass += ' today';
                        if (isCurrentMonth && tieneInfo && disponible) dayClass += ' available';
                        if (isCurrentMonth && tieneInfo && !disponible) dayClass += ' unavailable';
                        
                        let iconHtml = '';
                        if (isCurrentMonth) {
                            if (tieneInfo) {
                                iconHtml = disponible ? '<i class="fas fa-check"></i>' : '<i class="fas fa-times"></i>';
                                // Agregar icono de observaciones si las hay
                                if (disponible && observaciones && observaciones.trim() !== '') {
                                    iconHtml += '<i class="fas fa-comment" style="margin-left: 4px; font-size: 0.7em; opacity: 0.8;" title="Tiene observaciones"></i>';
                                }
                            } else {
                                // Si no hay información específica, asumir no disponible por defecto
                                iconHtml = '<i class="fas fa-times" style="opacity: 0.3;"></i>';
                            }
                        } else {
                            iconHtml = '';
                        }
                        
                        // Crear tooltip con observaciones si las hay
                        let title = '';
                        if (isCurrentMonth && observaciones && observaciones.trim() !== '') {
                            title = `title="Observaciones: ${observaciones.replace(/"/g, '&quot;')}"`;
                        }
                        
                        html += `
                            <div class="${dayClass}" ${title} ${observaciones && observaciones.trim() !== '' ? `onclick="showObservacionesInfo('${dateStr}', '${observaciones.replace(/'/g, "\\'")}', '${dayNumber}')"` : ''} style="${observaciones && observaciones.trim() !== '' ? 'cursor: pointer;' : ''}">
                                <div class="day-number-admin">${dayNumber}</div>
                                <div class="availability-icon-admin">${iconHtml}</div>
                            </div>
                        `;
                        
                        currentDateObj.setDate(currentDateObj.getDate() + 1);
                    }
                    
                    html += `
                            </div>
                            
                            <div class="legend-admin">
                                <div class="legend-item-admin">
                                    <div class="legend-color-admin available"></div>
                                    <span><i class="fas fa-check" style="color: var(--light-green);"></i> Disponible</span>
                                </div>
                                <div class="legend-item-admin">
                                    <div class="legend-color-admin unavailable"></div>
                                    <span><i class="fas fa-times" style="color: #f44336;"></i> No disponible</span>
                                </div>
                                <div class="legend-item-admin">
                                    <div class="legend-color-admin default"></div>
                                    <span><i class="fas fa-times" style="color: #ccc;"></i> No disponible por defecto</span>
                                </div>
                                <div class="legend-item-admin">
                                    <div style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-comment" style="color: var(--primary-green); font-size: 0.8em;"></i>
                                    </div>
                                    <span><i class="fas fa-comment-dots" style="color: var(--primary-green);"></i> Con observaciones (click para ver)</span>
                                </div>
                                <div class="info-text-admin">
                                    <small><i class="fas fa-info-circle"></i> Mostrando ${monthNames[monthNum]} ${yearNum} - Los días sin configuración específica se consideran no disponibles</small>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('calendarioDisponibilidad').innerHTML = html;
                    
                    // Solo abrir el modal si no está ya abierto
                    if (!document.getElementById('disponibilidadModal').style.display || 
                        document.getElementById('disponibilidadModal').style.display === 'none') {
                        openModal('disponibilidadModal');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('calendarioDisponibilidad').innerHTML = `
                        <div style="text-align: center; padding: 50px; color: #d32f2f;">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <p>Error al cargar la disponibilidad</p>
                            <p><small>${error.message}</small></p>
                        </div>
                    `;
                    showNotification('Error al cargar disponibilidad', 'error');
                });
        }
        
        function navigateMonth(arbitroId, currentMonth, direction) {
            console.log(`Navegando desde ${currentMonth} en dirección ${direction}`);
            
            const [year, month] = currentMonth.split('-');
            const date = new Date(parseInt(year), parseInt(month) - 1, 1);
            date.setMonth(date.getMonth() + direction);
            
            // Usar método más robusto para evitar problemas de zona horaria
            const newYear = date.getFullYear();
            const newMonth = String(date.getMonth() + 1).padStart(2, '0');
            const newMonthStr = `${newYear}-${newMonth}`;
            
            console.log(`Nuevo mes: ${newMonthStr}`);
            
            loadDisponibilidadCalendar(arbitroId, newMonthStr);
        }
        
        function goToCurrentMonth(arbitroId) {
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
            const currentMonthStr = `${currentYear}-${currentMonth}`;
            
            console.log(`Yendo al mes actual: ${currentMonthStr}`);
            loadDisponibilidadCalendar(arbitroId, currentMonthStr);
        }

        function verEstadisticas(arbitroId) {
            // Mostrar loading
            document.getElementById('estadisticasContent').innerHTML = `
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p>Cargando estadísticas completas...</p>
                </div>
            `;
            
            openModal('estadisticasModal');
            
            fetch(`api/estadisticas.php?arbitro_id=${arbitroId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    console.log('Estadísticas completas:', data);
                    
                    let html = `
                        <div class="estadisticas-completas">
                            <!-- Header con información del árbitro -->
                            <div class="arbitro-header" style="background: linear-gradient(135deg, var(--primary-green), var(--light-green)); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: center;">
                                    <div>
                                        <h3 style="margin: 0; font-size: 1.5rem;">
                                            <i class="fas fa-user-circle"></i> ${data.arbitro_info.nombre} ${data.arbitro_info.apellidos}
                                        </h3>
                                        <p style="margin: 5px 0; opacity: 0.9;">
                                            <i class="fas fa-envelope"></i> ${data.arbitro_info.email} | 
                                            <i class="fas fa-phone"></i> ${data.arbitro_info.telefono || 'No especificado'} | 
                                            <i class="fas fa-map-marker-alt"></i> ${data.arbitro_info.ciudad}
                                        </p>
                                        <p style="margin: 5px 0; opacity: 0.9;">
                                            <i class="fas fa-id-card"></i> Licencia: ${data.arbitro_info.licencia.toUpperCase()} 
                                            ${data.arbitro_info.numero_licencia ? `| Nº ${data.arbitro_info.numero_licencia}` : '| Sin número asignado'}
                                        </p>
                                    </div>
                                    <div style="text-align: center; background: rgba(255,255,255,0.1); padding: 15px; border-radius: 6px;">
                                        <div style="font-size: 2rem; font-weight: bold;">${data.resumen.total_partidos}</div>
                                        <div style="font-size: 0.9rem; opacity: 0.9;">Partidos Totales</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen estadísticas principales -->
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 25px;">
                                <div class="stat-card-mini" style="background: #e8f5e8; border-left: 4px solid var(--success);">
                                    <div class="stat-icon"><i class="fas fa-calendar-check" style="color: var(--success);"></i></div>
                                    <div class="stat-value">${data.resumen.partidos_temporada_actual}</div>
                                    <div class="stat-label">Temporada Actual</div>
                                </div>
                                <div class="stat-card-mini" style="background: #e3f2fd; border-left: 4px solid var(--info);">
                                    <div class="stat-icon"><i class="fas fa-file-invoice-dollar" style="color: var(--info);"></i></div>
                                    <div class="stat-value">${data.resumen.total_liquidaciones}</div>
                                    <div class="stat-label">Liquidaciones</div>
                                </div>
                                <div class="stat-card-mini" style="background: #fff3e0; border-left: 4px solid var(--warning);">
                                    <div class="stat-icon"><i class="fas fa-euro-sign" style="color: var(--warning);"></i></div>
                                    <div class="stat-value">${data.resumen.total_cobrado}€</div>
                                    <div class="stat-label">Total Cobrado</div>
                                </div>
                                <div class="stat-card-mini" style="background: #f3e5f5; border-left: 4px solid #9c27b0;">
                                    <div class="stat-icon"><i class="fas fa-certificate" style="color: #9c27b0;"></i></div>
                                    <div class="stat-value">${data.resumen.licencias_activas}</div>
                                    <div class="stat-label">Licencias Activas</div>
                                </div>
                                <div class="stat-card-mini" style="background: #fce4ec; border-left: 4px solid #e91e63;">
                                    <div class="stat-icon"><i class="fas fa-calendar-times" style="color: #e91e63;"></i></div>
                                    <div class="stat-value">${data.resumen.dias_disponibles_futuros}</div>
                                    <div class="stat-label">Días Disponibles</div>
                                </div>
                                <div class="stat-card-mini" style="background: #e8eaf6; border-left: 4px solid #3f51b5;">
                                    <div class="stat-icon"><i class="fas fa-history" style="color: #3f51b5;"></i></div>
                                    <div class="stat-value">${data.resumen.años_arbitrando}</div>
                                    <div class="stat-label">Años Arbitrando</div>
                                </div>
                            </div>

                            <!-- Tabs para organizar la información -->
                            <div class="tabs-estadisticas">
                                <div class="tab-buttons" style="display: flex; border-bottom: 2px solid #e0e0e0; margin-bottom: 20px;">
                                    <button class="tab-btn active" onclick="showTab('partidos')" style="flex: 1; padding: 12px; border: none; background: var(--primary-green); color: white; cursor: pointer; transition: all 0.3s;">
                                        <i class="fas fa-volleyball-ball"></i> Partidos
                                    </button>
                                    <button class="tab-btn" onclick="showTab('licencias')" style="flex: 1; padding: 12px; border: none; background: #f5f5f5; color: #666; cursor: pointer; transition: all 0.3s;">
                                        <i class="fas fa-certificate"></i> Licencias
                                    </button>
                                    <button class="tab-btn" onclick="showTab('liquidaciones')" style="flex: 1; padding: 12px; border: none; background: #f5f5f5; color: #666; cursor: pointer; transition: all 0.3s;">
                                        <i class="fas fa-euro-sign"></i> Liquidaciones
                                    </button>
                                    <button class="tab-btn" onclick="showTab('disponibilidad')" style="flex: 1; padding: 12px; border: none; background: #f5f5f5; color: #666; cursor: pointer; transition: all 0.3s;">
                                        <i class="fas fa-calendar-alt"></i> Disponibilidad
                                    </button>
                                </div>

                                <!-- Tab Partidos -->
                                <div id="tab-partidos" class="tab-content">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--primary-green);">${data.estadisticas_partidos.como_principal}</div>
                                            <div class="stat-label">Como Principal</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--info);">${data.estadisticas_partidos.como_segundo}</div>
                                            <div class="stat-label">Como Segundo</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--warning);">${data.estadisticas_partidos.como_anotador}</div>
                                            <div class="stat-label">Como Anotador</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--success);">${data.estadisticas_partidos.partidos_finalizados}</div>
                                            <div class="stat-label">Finalizados</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: #ff9800;">${data.estadisticas_partidos.partidos_pendientes}</div>
                                            <div class="stat-label">Pendientes</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: #f44336;">${data.estadisticas_partidos.partidos_cancelados}</div>
                                            <div class="stat-label">Cancelados</div>
                                        </div>
                                    </div>

                                    <!-- Estadísticas por categoría -->
                                    ${data.estadisticas_por_categoria.length > 0 ? `
                                    <h5><i class="fas fa-chart-pie"></i> Por Categoría</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Categoría</th>
                                                    <th>Total</th>
                                                    <th>Principal</th>
                                                    <th>Segundo</th>
                                                    <th>Anotador</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.estadisticas_por_categoria.map(cat => `
                                                <tr>
                                                    <td><strong>${cat.categoria}</strong></td>
                                                    <td><span class="badge" style="background: var(--primary-green);">${cat.partidos}</span></td>
                                                    <td>${cat.como_principal}</td>
                                                    <td>${cat.como_segundo}</td>
                                                    <td>${cat.como_anotador}</td>
                                                </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    ` : '<p><i class="fas fa-info-circle"></i> No hay partidos registrados</p>'}

                                    <!-- Últimos partidos -->
                                    ${data.ultimos_partidos.length > 0 ? `
                                    <h5><i class="fas fa-history"></i> Últimos Partidos</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Equipos</th>
                                                    <th>Categoría</th>
                                                    <th>Rol</th>
                                                    <th>Estado</th>
                                                    <th>Resultado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.ultimos_partidos.slice(0, 10).map(partido => `
                                                <tr>
                                                    <td>${new Date(partido.fecha).toLocaleDateString('es-ES')}</td>
                                                    <td><strong>${partido.equipo_local} vs ${partido.equipo_visitante}</strong></td>
                                                    <td><small>${partido.categoria}</small></td>
                                                    <td><span class="badge" style="background: ${partido.rol === '1º Árbitro' ? 'var(--primary-green)' : partido.rol === '2º Árbitro' ? 'var(--info)' : 'var(--warning)'};">${partido.rol}</span></td>
                                                    <td><span class="badge" style="background: ${partido.estado === 'finalizado' ? 'var(--success)' : partido.estado === 'programado' ? 'var(--warning)' : 'var(--danger)'};">${partido.estado}</span></td>
                                                    <td>${partido.sets_local !== null ? `${partido.sets_local}-${partido.sets_visitante}` : '-'}</td>
                                                </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    ` : '<p><i class="fas fa-info-circle"></i> No hay partidos registrados</p>'}
                                </div>

                                <!-- Tab Licencias -->
                                <div id="tab-licencias" class="tab-content" style="display: none;">
                                    ${data.licencias.length > 0 ? `
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nivel</th>
                                                    <th>Fecha Curso</th>
                                                    <th>Lugar</th>
                                                    <th>Vigencia</th>
                                                    <th>Estado</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.licencias.map(licencia => `
                                                <tr class="${licencia.estado_licencia === 'activa' ? 'table-success' : licencia.estado_licencia === 'vencida' ? 'table-danger' : 'table-warning'}">
                                                    <td><strong>${licencia.nivel_licencia.toUpperCase()}</strong></td>
                                                    <td>${new Date(licencia.fecha_curso).toLocaleDateString('es-ES')}</td>
                                                    <td>${licencia.lugar_curso}</td>
                                                    <td>
                                                        <small>
                                                            <strong>Inicio:</strong> ${new Date(licencia.fecha_inicio).toLocaleDateString('es-ES')}<br>
                                                            <strong>Vence:</strong> ${new Date(licencia.fecha_vencimiento).toLocaleDateString('es-ES')}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background: ${licencia.estado_licencia === 'activa' ? 'var(--success)' : licencia.estado_licencia === 'vencida' ? 'var(--danger)' : 'var(--warning)'};">
                                                            ${licencia.estado_licencia === 'activa' ? 'ACTIVA' : licencia.estado_licencia === 'vencida' ? 'VENCIDA' : 'INACTIVA'}
                                                        </span>
                                                        ${licencia.estado_licencia === 'activa' && licencia.dias_hasta_vencimiento <= 30 ? 
                                                            `<br><small style="color: orange;"><i class="fas fa-exclamation-triangle"></i> Vence en ${licencia.dias_hasta_vencimiento} días</small>` : ''}
                                                    </td>
                                                    <td><small>${licencia.observaciones || '-'}</small></td>
                                                </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    ` : '<p><i class="fas fa-info-circle"></i> No hay licencias registradas</p>'}
                                </div>

                                <!-- Tab Liquidaciones -->
                                <div id="tab-liquidaciones" class="tab-content" style="display: none;">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--warning);">${data.estadisticas_liquidaciones.pendientes}</div>
                                            <div class="stat-label">Pendientes</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--info);">${data.estadisticas_liquidaciones.aprobadas}</div>
                                            <div class="stat-label">Aprobadas</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--success);">${data.estadisticas_liquidaciones.pagadas}</div>
                                            <div class="stat-label">Pagadas</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--danger);">${data.estadisticas_liquidaciones.en_rectificacion}</div>
                                            <div class="stat-label">En Rectificación</div>
                                        </div>
                                    </div>

                                    ${data.ultimas_liquidaciones.length > 0 ? `
                                    <h5><i class="fas fa-list"></i> Últimas Liquidaciones</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Período</th>
                                                    <th>Partidos</th>
                                                    <th>Importe</th>
                                                    <th>Estado</th>
                                                    <th>Fecha Creación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${data.ultimas_liquidaciones.map(liq => `
                                                <tr>
                                                    <td>
                                                        <small>
                                                            ${new Date(liq.fecha_inicio).toLocaleDateString('es-ES')} - 
                                                            ${new Date(liq.fecha_fin).toLocaleDateString('es-ES')}
                                                        </small>
                                                    </td>
                                                    <td><span class="badge" style="background: var(--info);">${liq.numero_partidos}</span></td>
                                                    <td><strong>${parseFloat(liq.importe_total).toFixed(2)}€</strong></td>
                                                    <td>
                                                        <span class="badge" style="background: ${
                                                            liq.estado === 'pagada' ? 'var(--success)' : 
                                                            liq.estado === 'aprobada' ? 'var(--info)' : 
                                                            liq.estado === 'rectificacion' ? 'var(--danger)' : 'var(--warning)'
                                                        };">
                                                            ${liq.estado.toUpperCase()}
                                                        </span>
                                                    </td>
                                                    <td><small>${new Date(liq.fecha_creacion).toLocaleDateString('es-ES')}</small></td>
                                                </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                    ` : '<p><i class="fas fa-info-circle"></i> No hay liquidaciones registradas</p>'}
                                </div>

                                <!-- Tab Disponibilidad -->
                                <div id="tab-disponibilidad" class="tab-content" style="display: none;">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 20px;">
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--primary-green);">${data.estadisticas_disponibilidad.total_dias_configurados}</div>
                                            <div class="stat-label">Días Configurados</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--success);">${data.estadisticas_disponibilidad.dias_disponibles}</div>
                                            <div class="stat-label">Disponibles</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--danger);">${data.estadisticas_disponibilidad.dias_no_disponibles}</div>
                                            <div class="stat-label">No Disponibles</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--info);">${data.estadisticas_disponibilidad.dias_disponibles_futuros}</div>
                                            <div class="stat-label">Futuros Disponibles</div>
                                        </div>
                                        <div class="stat-card-detailed">
                                            <div class="stat-number" style="color: var(--warning);">${data.estadisticas_disponibilidad.dias_con_observaciones}</div>
                                            <div class="stat-label">Con Observaciones</div>
                                        </div>
                                    </div>

                                    <div style="text-align: center; margin-top: 20px;">
                                        <!---<button onclick="verDisponibilidad(${arbitroId})" class="btn btn-info">
                                            <i class="fas fa-calendar-check"></i> Ver Calendario Completo
                                        </button>--->
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('estadisticasContent').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('estadisticasContent').innerHTML = `
                        <div style="text-align: center; padding: 50px; color: #d32f2f;">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                            <p>Error al cargar las estadísticas</p>
                            <p><small>${error.message}</small></p>
                        </div>
                    `;
                    showNotification('Error al cargar estadísticas', 'error');
                });
        }

        // Función para cambiar entre tabs
        function showTab(tabName) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Remover clase active de todos los botones
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.style.background = '#f5f5f5';
                btn.style.color = '#666';
                btn.classList.remove('active');
            });
            
            // Mostrar tab seleccionado
            document.getElementById(`tab-${tabName}`).style.display = 'block';
            
            // Activar botón correspondiente
            event.target.style.background = 'var(--primary-green)';
            event.target.style.color = 'white';
            event.target.classList.add('active');
        }
        
        function asignarNumeroLicencia(arbitroId, arbitroNombre) {
            console.log('=== INICIO ASIGNACIÓN NÚMERO LICENCIA ===');
            console.log('ArbitroId:', arbitroId);
            console.log('ArbitroNombre:', arbitroNombre);
            
            // Limpiar el formulario
            document.getElementById('numeroLicenciaForm').reset();
            
            // Establecer los datos del árbitro
            document.getElementById('arbitroId').value = arbitroId;
            document.getElementById('arbitroNombre').textContent = arbitroNombre;
            
            // Limpiar campo de número de licencia
            document.getElementById('numeroLicencia').value = '';
            
            // Abrir el modal
            openModal('numeroLicenciaModal');
            
            // Enfocar el campo de número de licencia
            setTimeout(() => {
                document.getElementById('numeroLicencia').focus();
            }, 300);
            
            console.log('Modal abierto correctamente');
        }
        
        // Manejar envío del formulario de número de licencia
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('numeroLicenciaForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const arbitroId = document.getElementById('arbitroId').value;
                    const numeroLicencia = document.getElementById('numeroLicencia').value.trim();
                    const arbitroNombre = document.getElementById('arbitroNombre').textContent;
                    
                    if (!numeroLicencia) {
                        showNotification('Por favor, ingrese un número de licencia', 'error');
                        return;
                    }
                    
                    // Validar formato
                    if (!/^[A-Za-z0-9]{3,20}$/.test(numeroLicencia)) {
                        showNotification('El número de licencia debe contener solo letras y números (3-20 caracteres)', 'error');
                        return;
                    }
                    
                    // Confirmar asignación
                    if (!confirm(`¿Está seguro de asignar el número de licencia "${numeroLicencia}" a ${arbitroNombre}?\n\nEste cambio no se puede deshacer.`)) {
                        return;
                    }
                    
                    // Deshabilitar botón de envío
                    const submitBtn = document.querySelector('button[form="numeroLicenciaForm"][type="submit"]');
                    if (!submitBtn) {
                        console.error('No se encontró el botón de envío');
                        showNotification('Error: No se encontró el botón de envío', 'error');
                        return;
                    }
                    
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';
                    
                    // Enviar petición
                    console.log('Enviando petición a API:', {
                        arbitro_id: arbitroId,
                        numero_licencia: numeroLicencia
                    });
                    
                    fetch('api/numero_licencia.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            arbitro_id: arbitroId,
                            numero_licencia: numeroLicencia
                        })
                    })
                    .then(response => {
                        console.log('Respuesta recibida:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos de respuesta:', data);
                        if (data.success) {
                            showNotification(data.message, 'success');
                            closeModal('numeroLicenciaModal');
                            
                            // Recargar la página para mostrar los cambios
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            showNotification(data.error || 'Error al asignar número de licencia', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error completo:', error);
                        showNotification('Error de conexión: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Rehabilitar botón
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            console.log('Botón rehabilitado');
                        }
                    });
                });
                
                // Formatear automáticamente el número de licencia (mayúsculas)
                const numeroLicenciaInput = document.getElementById('numeroLicencia');
                if (numeroLicenciaInput) {
                    numeroLicenciaInput.addEventListener('input', function(e) {
                        // Convertir a mayúsculas y eliminar caracteres no válidos
                        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                    });
                    console.log('Event listener de formateo añadido');
                }
            } else {
                console.error('No se encontró el formulario numeroLicenciaForm');
            }
        });
        
        function showObservacionesInfo(fecha, observaciones, dia) {
            if (!observaciones || observaciones.trim() === '') return;
            
            // Usar formateo más robusto para evitar problemas de zona horaria
            const fechaObj = new Date(fecha + 'T12:00:00'); // Usar mediodía para evitar problemas
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Crear modal temporal para mostrar observaciones
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            modal.innerHTML = `
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3><i class="fas fa-comment"></i> Observaciones del Árbitro</h3>
                        <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 6px;">
                            <strong><i class="fas fa-calendar-alt"></i> ${fechaFormateada}</strong>
                        </div>
                        <div style="background: #e8f5e8; padding: 15px; border-radius: 6px; border-left: 4px solid var(--primary-green);">
                            <div style="margin-bottom: 8px;"><strong><i class="fas fa-check-circle" style="color: var(--primary-green);"></i> Estado: Disponible</strong></div>
                            <div><strong><i class="fas fa-comment-dots"></i> Observaciones:</strong></div>
                            <div style="margin-top: 8px; padding: 10px; background: white; border-radius: 4px; font-style: italic;">
                                "${observaciones}"
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cerrar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        // ===== FUNCIONES PARA GESTIÓN DE ALIAS =====
        
        function gestionarAlias(arbitroId, arbitroNombre) {
            console.log('Gestionando alias para:', arbitroId, arbitroNombre);
            
            // Establecer datos del árbitro
            document.getElementById('aliasArbitroId').value = arbitroId;
            document.getElementById('aliasArbitroNombre').textContent = arbitroNombre;
            
            // Limpiar formulario
            document.getElementById('formAgregarAlias').reset();
            
            // Cargar lista de alias
            cargarAlias(arbitroId);
            
            // Abrir modal
            openModal('aliasModal');
        }
        
        // ===== FUNCIONES PARA MODIFICAR DISPONIBILIDAD =====
        
        function modificarDisponibilidad(arbitroId, arbitroNombre) {
            console.log('Modificando disponibilidad para:', arbitroId, arbitroNombre);
            
            // Establecer datos del árbitro
            document.getElementById('modificarArbitroId').value = arbitroId;
            
            // Cargar calendario del mes actual
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
            const currentMonthStr = `${currentYear}-${currentMonth}`;
            
            cargarCalendarioModificar(arbitroId, currentMonthStr, arbitroNombre);
            
            // Abrir modal
            openModal('modificarDisponibilidadModal');
        }
        
        function cargarCalendarioModificar(arbitroId, monthStr, arbitroNombre) {
            const calendarioDiv = document.getElementById('calendarioModificar');
            
            // Mostrar loading
            calendarioDiv.innerHTML = `
                <div style="text-align: center; padding: 50px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: var(--primary-green);"></i>
                    <p style="margin-top: 15px; color: var(--medium-gray);">Cargando calendario...</p>
                </div>
            `;
            
            fetch(`api/disponibilidad.php?arbitro_id=${arbitroId}&month=${monthStr}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderCalendarioModificar(data, arbitroId, monthStr, arbitroNombre);
                    } else {
                        calendarioDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i> ${data.message || 'Error al cargar disponibilidad'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    calendarioDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Error de conexión al cargar disponibilidad
                        </div>
                    `;
                });
        }
        
        function renderCalendarioModificar(data, arbitroId, monthStr, arbitroNombre) {
            const calendarioDiv = document.getElementById('calendarioModificar');
            const [year, month] = monthStr.split('-');
            const monthName = new Date(year, month - 1, 1).toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
            
            let html = `
                <div class="calendar-disponibilidad">
                    <div class="calendar-header-admin">
                        <div style="margin-bottom: 15px; text-align: center;">
                            <h4 style="margin: 0; font-size: 1.2rem;">${arbitroNombre}</h4>
                        </div>
                        <div class="calendar-navigation-admin">
                            <button class="btn-nav-admin" onclick="navegarMesModificar(${arbitroId}, '${monthStr}', -1, '${arbitroNombre.replace(/'/g, "\\'")}')">
                                <i class="fas fa-chevron-left"></i> Anterior
                            </button>
                            <h4 style="text-transform: capitalize;">${monthName}</h4>
                            <button class="btn-nav-admin" onclick="navegarMesModificar(${arbitroId}, '${monthStr}', 1, '${arbitroNombre.replace(/'/g, "\\'")}')">
                                Siguiente <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="calendar-actions-admin">
                            <button class="btn-today-admin" onclick="irMesActualModificar(${arbitroId}, '${arbitroNombre.replace(/'/g, "\\'")}')">
                                <i class="fas fa-calendar-day"></i> Mes Actual
                            </button>
                        </div>
                    </div>
                    
                    <div class="calendar-grid-admin">
                        <div class="calendar-day-header-admin">Lun</div>
                        <div class="calendar-day-header-admin">Mar</div>
                        <div class="calendar-day-header-admin">Mié</div>
                        <div class="calendar-day-header-admin">Jue</div>
                        <div class="calendar-day-header-admin">Vie</div>
                        <div class="calendar-day-header-admin">Sáb</div>
                        <div class="calendar-day-header-admin">Dom</div>
            `;
            
            // Renderizar días del calendario
            data.calendar.forEach(day => {
                const isOtherMonth = day.isOtherMonth ? 'other-month' : '';
                const isToday = day.isToday ? 'today' : '';
                const isPast = new Date(day.date) < new Date().setHours(0, 0, 0, 0) ? 'past-day' : '';
                
                let dayClass = '';
                let icon = '<i class="fas fa-minus availability-icon-admin" style="color: #ccc;"></i>';
                
                if (day.manana && day.tarde) {
                    dayClass = 'available-full';
                    icon = '<i class="fas fa-check-circle availability-icon-admin"></i> M/T';
                } else if (day.manana) {
                    dayClass = 'available-manana';
                    icon = '<i class="fas fa-sun availability-icon-admin"></i> M';
                } else if (day.tarde) {
                    dayClass = 'available-tarde';
                    icon = '<i class="fas fa-moon availability-icon-admin"></i> T';
                }
                
                const hasObservaciones = day.observaciones ? `<i class="fas fa-comment" style="font-size: 0.7rem; color: var(--primary-green); margin-left: 4px;"></i>` : '';
                
                html += `
                    <div class="calendar-day-admin calendar-day-editable ${isOtherMonth} ${isToday} ${dayClass} ${isPast}" 
                         data-date="${day.date}"
                         data-arbitro-id="${arbitroId}"
                         onclick="editarDiaDisponibilidad('${day.date}', ${arbitroId}, ${day.manana ? 1 : 0}, ${day.tarde ? 1 : 0}, '${(day.observaciones || '').replace(/'/g, "\\'")}', '${arbitroNombre.replace(/'/g, "\\'")}', '${monthStr}')">
                        <div class="day-number-admin">${day.day} ${hasObservaciones}</div>
                        <div class="availability-icon-admin">${icon}</div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                    
                    <div class="legend-admin">
                        <div class="legend-item-admin">
                            <div class="legend-color-admin available-full"></div>
                            <span>Mañana y Tarde</span>
                        </div>
                        <div class="legend-item-admin">
                            <div class="legend-color-admin available-manana"></div>
                            <span>Solo Mañana</span>
                        </div>
                        <div class="legend-item-admin">
                            <div class="legend-color-admin available-tarde"></div>
                            <span>Solo Tarde</span>
                        </div>
                        <div class="legend-item-admin">
                            <div class="legend-color-admin default"></div>
                            <span>No disponible</span>
                        </div>
                        <div class="info-text-admin">
                            <i class="fas fa-info-circle"></i> Haga clic en un día para modificar su disponibilidad
                        </div>
                    </div>
                </div>
            `;
            
            calendarioDiv.innerHTML = html;
        }
        
        function navegarMesModificar(arbitroId, currentMonth, direction, arbitroNombre) {
            const [year, month] = currentMonth.split('-');
            const date = new Date(parseInt(year), parseInt(month) - 1, 1);
            date.setMonth(date.getMonth() + direction);
            
            const newYear = date.getFullYear();
            const newMonth = String(date.getMonth() + 1).padStart(2, '0');
            const newMonthStr = `${newYear}-${newMonth}`;
            
            cargarCalendarioModificar(arbitroId, newMonthStr, arbitroNombre);
        }
        
        function irMesActualModificar(arbitroId, arbitroNombre) {
            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();
            const currentMonth = String(currentDate.getMonth() + 1).padStart(2, '0');
            const currentMonthStr = `${currentYear}-${currentMonth}`;
            
            cargarCalendarioModificar(arbitroId, currentMonthStr, arbitroNombre);
        }
        
        function editarDiaDisponibilidad(fecha, arbitroId, mananaActual, tardeActual, observacionesActuales, arbitroNombre, monthStr) {
            // Verificar si es un día del pasado
            if (new Date(fecha) < new Date().setHours(0, 0, 0, 0)) {
                showNotification('No se puede modificar la disponibilidad de días pasados', 'warning');
                return;
            }
            
            const fechaObj = new Date(fecha + 'T12:00:00');
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            // Crear modal para editar
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            modal.id = 'modalEditarDia';
            modal.innerHTML = `
                <div class="modal-content" style="max-width: 600px;">
                    <div class="modal-header">
                        <h3><i class="fas fa-calendar-edit"></i> Editar Disponibilidad</h3>
                        <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px; text-align: center;">
                            <strong style="font-size: 1.1rem; text-transform: capitalize;">${fechaFormateada}</strong>
                        </div>
                        
                        <form id="formEditarDisponibilidad">
                            <input type="hidden" name="arbitro_id" value="${arbitroId}">
                            <input type="hidden" name="fecha" value="${fecha}">
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-clock"></i> Disponibilidad</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <label class="checkbox-card" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                                        <input type="checkbox" name="manana" id="checkManana" ${mananaActual ? 'checked' : ''} style="margin-right: 10px; width: 20px; height: 20px;">
                                        <div>
                                            <i class="fas fa-sun" style="color: #FFA726; margin-right: 5px;"></i>
                                            <strong>Mañana</strong>
                                        </div>
                                    </label>
                                    <label class="checkbox-card" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                                        <input type="checkbox" name="tarde" id="checkTarde" ${tardeActual ? 'checked' : ''} style="margin-right: 10px; width: 20px; height: 20px;">
                                        <div>
                                            <i class="fas fa-moon" style="color: #5C6BC0; margin-right: 5px;"></i>
                                            <strong>Tarde</strong>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-comment"></i> Observaciones (Opcional)</label>
                                <textarea name="observaciones" class="form-control" rows="3" placeholder="Agregar observaciones sobre la disponibilidad...">${observacionesActuales}</textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancelar</button>
                        <button type="button" class="btn btn-danger" onclick="eliminarDisponibilidadDia('${fecha}', ${arbitroId}, '${arbitroNombre.replace(/'/g, "\\'")}', '${monthStr}')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="guardarDisponibilidadDia('${arbitroNombre.replace(/'/g, "\\'")}', '${monthStr}')">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            // Agregar estilos para las tarjetas de checkbox
            const style = document.createElement('style');
            style.textContent = `
                .checkbox-card:has(input:checked) {
                    border-color: var(--primary-green) !important;
                    background: #e8f5e8 !important;
                }
                .checkbox-card:hover {
                    border-color: var(--light-green);
                    transform: translateY(-2px);
                }
            `;
            document.head.appendChild(style);
        }
        
        function guardarDisponibilidadDia(arbitroNombre, monthStr) {
            const form = document.getElementById('formEditarDisponibilidad');
            const formData = new FormData(form);
            
            const data = {
                arbitro_id: parseInt(formData.get('arbitro_id')),
                fecha: formData.get('fecha'),
                manana: formData.get('manana') ? 1 : 0,
                tarde: formData.get('tarde') ? 1 : 0,
                observaciones: formData.get('observaciones') || ''
            };
            
            // Validar que al menos una franja esté seleccionada
            if (!data.manana && !data.tarde) {
                showNotification('Debe seleccionar al menos una franja horaria (Mañana o Tarde)', 'warning');
                return;
            }
            
            fetch('api/disponibilidad.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Disponibilidad actualizada correctamente', 'success');
                    document.getElementById('modalEditarDia').remove();
                    cargarCalendarioModificar(data.arbitro_id, monthStr, arbitroNombre);
                } else {
                    showNotification(result.message || 'Error al actualizar disponibilidad', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión al guardar', 'error');
            });
        }
        
        function eliminarDisponibilidadDia(fecha, arbitroId, arbitroNombre, monthStr) {
            if (!confirm('¿Está seguro de eliminar la disponibilidad de este día?')) {
                return;
            }
            
            fetch('api/disponibilidad.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    arbitro_id: arbitroId,
                    fecha: fecha
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Disponibilidad eliminada correctamente', 'success');
                    document.getElementById('modalEditarDia').remove();
                    cargarCalendarioModificar(arbitroId, monthStr, arbitroNombre);
                } else {
                    showNotification(result.message || 'Error al eliminar disponibilidad', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión al eliminar', 'error');
            });
        }
        
        
        function cargarAlias(arbitroId) {
            const listaDiv = document.getElementById('listaAlias');
            const countBadge = document.getElementById('totalAliasCount');
            
            // Mostrar loading
            listaDiv.innerHTML = `
                <div class="text-center text-muted" style="padding: 20px;">
                    <i class="fas fa-spinner fa-spin"></i> Cargando alias...
                </div>
            `;
            
            fetch(`api/arbitro_alias.php?arbitro_id=${arbitroId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Alias recibidos:', data);
                    
                    if (data.success && data.alias && data.alias.length > 0) {
                        // Actualizar contador
                        countBadge.textContent = data.alias.length;
                        
                        // Mostrar lista de alias
                        let html = '<div class="list-group">';
                        data.alias.forEach(alias => {
                            html += `
                                <div class="list-group-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; border-bottom: 1px solid #e0e0e0;">
                                    <div style="flex: 1;">
                                        <i class="fas fa-tag" style="color: var(--primary-green); margin-right: 8px;"></i>
                                        <strong>${alias.alias}</strong>
                                        <small class="text-muted" style="margin-left: 10px;">
                                            <i class="fas fa-clock"></i> ${new Date(alias.fecha_creacion).toLocaleDateString('es-ES')}
                                        </small>
                                    </div>
                                    <button onclick="eliminarAlias(${alias.id}, '${alias.alias.replace(/'/g, "\\'")}')" 
                                            class="btn btn-danger btn-sm"
                                            title="Eliminar alias">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        });
                        html += '</div>';
                        listaDiv.innerHTML = html;
                    } else {
                        countBadge.textContent = '0';
                        listaDiv.innerHTML = `
                            <div class="text-center text-muted" style="padding: 30px;">
                                <i class="fas fa-inbox" style="font-size: 3em; opacity: 0.3;"></i>
                                <p style="margin-top: 10px;">No hay alias configurados para este árbitro</p>
                                <small>Agrega alias para facilitar la identificación en archivos CSV</small>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar alias:', error);
                    listaDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Error al cargar los alias. Por favor, intente de nuevo.
                        </div>
                    `;
                    countBadge.textContent = '0';
                });
        }
        
        function agregarAlias() {
            const arbitroId = document.getElementById('aliasArbitroId').value;
            const nuevoAlias = document.getElementById('nuevoAlias').value.trim();
            
            if (!nuevoAlias) {
                showNotification('Por favor, ingrese un alias', 'error');
                return;
            }
            
            // Deshabilitar botón
            const submitBtn = document.querySelector('#formAgregarAlias button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Enviar petición
            fetch('api/arbitro_alias.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    arbitro_id: parseInt(arbitroId),
                    alias: nuevoAlias
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Alias agregado correctamente', 'success');
                    document.getElementById('formAgregarAlias').reset();
                    cargarAlias(arbitroId); // Recargar lista
                } else {
                    showNotification(data.message || 'Error al agregar el alias', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión al agregar el alias', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }
        
        function eliminarAlias(aliasId, aliasNombre) {
            if (!confirm(`¿Está seguro de eliminar el alias "${aliasNombre}"?`)) {
                return;
            }
            
            fetch(`api/arbitro_alias.php?id=${aliasId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Alias eliminado correctamente', 'success');
                    const arbitroId = document.getElementById('aliasArbitroId').value;
                    cargarAlias(arbitroId); // Recargar lista
                } else {
                    showNotification(data.message || 'Error al eliminar el alias', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error de conexión al eliminar el alias', 'error');
            });
        }
        
        // ===== FUNCIONES PARA CONSULTA POR FECHA (VERSIÓN MEJORADA) =====
        
        // Registrar funciones globalmente
        window.consultarDisponibilidadFecha = function() {
            console.log('=== INICIO consultarDisponibilidadFecha ===');
            
            try {
                // Esperar a que el DOM esté completamente cargado
                if (document.readyState !== 'complete') {
                    console.log('DOM no está completamente cargado, esperando...');
                    setTimeout(window.consultarDisponibilidadFecha, 100);
                    return;
                }
                
                const fecha = document.getElementById('fechaConsulta')?.value;
                
                if (!fecha) {
                    if (typeof showNotification === 'function') {
                        showNotification('Por favor, seleccione una fecha', 'error');
                    } else {
                        alert('Por favor, seleccione una fecha');
                    }
                    return;
                }
                
                console.log('Fecha seleccionada:', fecha);
                
                // Buscar elementos de forma defensiva con espera
                const elementoIds = {
                    resultados: 'resultadosConsultaFecha',
                    bodyTabla: 'bodyArbitrosFecha',
                    infoTexto: 'textoInfoConsulta',
                    btnLimpiar: 'btnLimpiarFecha'
                };
                
                const elementos = {};
                let intentos = 0;
                const maxIntentos = 5;
                
                function buscarElementos() {
                    intentos++;
                    console.log(`Intento ${intentos} de búsqueda de elementos`);
                    
                    let todosEncontrados = true;
                    
                    for (const [nombre, id] of Object.entries(elementoIds)) {
                        // Probar múltiples métodos de búsqueda
                        elementos[nombre] = document.getElementById(id) || 
                                          document.querySelector(`#${id}`) || 
                                          document.querySelector(`[id="${id}"]`);
                        
                        if (!elementos[nombre]) {
                            console.warn(`Elemento ${nombre} (${id}) no encontrado en intento ${intentos}`);
                            
                            // Debug específico para textoInfoConsulta
                            if (id === 'textoInfoConsulta') {
                                console.log('🔍 Debug específico para textoInfoConsulta:');
                                console.log('- Buscando por getElementById:', document.getElementById('textoInfoConsulta'));
                                console.log('- Buscando por querySelector #:', document.querySelector('#textoInfoConsulta'));
                                console.log('- Buscando por querySelector [id]:', document.querySelector('[id="textoInfoConsulta"]'));
                                console.log('- Buscando por tag span:', document.querySelectorAll('span[id="textoInfoConsulta"]'));
                                
                                // Verificar si el contenedor padre existe
                                const contenedor = document.getElementById('resultadosConsultaFecha');
                                console.log('- Contenedor padre resultadosConsultaFecha:', contenedor);
                                if (contenedor) {
                                    console.log('- Hijos del contenedor:', contenedor.children.length);
                                    console.log('- HTML del contenedor (primeros 500 chars):', contenedor.innerHTML.substring(0, 500));
                                }
                            }
                            
                            todosEncontrados = false;
                        } else {
                            console.log(`Elemento ${nombre} (${id}) encontrado`);
                        }
                    }
                    
                    if (!todosEncontrados && intentos < maxIntentos) {
                        console.log('No todos los elementos encontrados, reintentando en 200ms...');
                        setTimeout(buscarElementos, 200);
                        return;
                    }
                    
                    if (!todosEncontrados) {
                        const elementosFaltantes = Object.entries(elementoIds)
                            .filter(([nombre]) => !elementos[nombre])
                            .map(([nombre, id]) => `${nombre}(${id})`);
                        
                        console.error('Elementos faltantes después de todos los intentos:', elementosFaltantes);
                        
                        // Intentar recuperación automática para textoInfoConsulta
                        if (!elementos.infoTexto && elementos.resultados) {
                            console.log('🛠️ Intentando crear elemento textoInfoConsulta automáticamente...');
                            
                            // Buscar o crear el div alert-info
                            let alertDiv = elementos.resultados.querySelector('.alert-info') || 
                                          elementos.resultados.querySelector('#infoConsultaFecha');
                            
                            if (!alertDiv) {
                                // Crear el div alert-info si no existe
                                alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-info';
                                alertDiv.id = 'infoConsultaFecha';
                                alertDiv.innerHTML = '<i class="fas fa-info-circle"></i> <span id="textoInfoConsulta"></span>';
                                elementos.resultados.insertBefore(alertDiv, elementos.resultados.firstChild);
                                console.log('✅ Creado contenedor alert-info');
                            }
                            
                            // Intentar crear el span textoInfoConsulta si no existe
                            let textoSpan = alertDiv.querySelector('#textoInfoConsulta');
                            if (!textoSpan) {
                                textoSpan = document.createElement('span');
                                textoSpan.id = 'textoInfoConsulta';
                                alertDiv.appendChild(textoSpan);
                                console.log('✅ Creado elemento textoInfoConsulta');
                            }
                            
                            // Volver a intentar la búsqueda
                            elementos.infoTexto = document.getElementById('textoInfoConsulta');
                            if (elementos.infoTexto) {
                                console.log('🎉 Elemento textoInfoConsulta recuperado exitosamente');
                                elementosFaltantes.splice(elementosFaltantes.findIndex(e => e.includes('textoInfoConsulta')), 1);
                                todosEncontrados = elementosFaltantes.length === 0;
                            }
                        }
                        
                        // Si aún faltan elementos después de la recuperación
                        if (!todosEncontrados) {
                            // Debug adicional del DOM
                            console.log('📋 Estado actual del DOM:');
                            console.log('- readyState:', document.readyState);
                            console.log('- body children:', document.body.children.length);
                            
                            // Buscar elementos por selector alternativo
                            console.log('🔍 Búsqueda alternativa de elementos:');
                            Object.entries(elementoIds).forEach(([nombre, id]) => {
                                const elemento = document.querySelector(`#${id}`);
                                console.log(`- querySelector #${id}:`, elemento ? 'ENCONTRADO' : 'NO ENCONTRADO');
                            });
                            
                            if (typeof showNotification === 'function') {
                                showNotification(`Error: No se encontraron elementos necesarios: ${elementosFaltantes.join(', ')}`, 'error');
                            } else {
                                alert(`Error: No se encontraron elementos necesarios: ${elementosFaltantes.join(', ')}`);
                            }
                            return;
                        }
                    }
                    
                    // Todos los elementos encontrados, continuar con la consulta
                    realizarConsulta();
                }
                
                function realizarConsulta() {
                    console.log('Todos los elementos encontrados correctamente, realizando consulta...');
                    
                    // Mostrar sección de resultados
                    elementos.resultados.style.display = 'block';
                    elementos.btnLimpiar.style.display = 'inline-block';
                    
                    // Mostrar loading
                    elementos.bodyTabla.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px;">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p style="margin-top: 10px;">Consultando disponibilidad...</p>
                            </td>
                        </tr>
                    `;
                    
                    elementos.infoTexto.textContent = 'Cargando...';
                    
                    console.log('Realizando fetch a API...');
                    
                    fetch(`api/disponibilidad_fecha_clean.php?fecha=${fecha}`)
                        .then(response => {
                            console.log('Respuesta recibida:', response.status, response.statusText);
                            
                            // Obtener el texto de la respuesta para debug
                            return response.text().then(text => {
                                console.log('Longitud de respuesta:', text.length);
                                console.log('Primeros 500 caracteres:', text.substring(0, 500));
                                
                                if (text.length > 1000) {
                                    console.log('Últimos 500 caracteres:', text.substring(text.length - 500));
                                }
                                
                                // Buscar posibles caracteres problemáticos
                                const problematicChars = text.match(/[^\x20-\x7E\n\r\t]/g);
                                if (problematicChars) {
                                    console.log('Caracteres problemáticos encontrados:', problematicChars);
                                }
                                
                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}\nRespuesta: ${text}`);
                                }
                                
                                // Intentar parsear el JSON directamente
                                try {
                                    const data = JSON.parse(text);
                                    return data;
                                } catch (jsonError) {
                                    console.error('Error parsing JSON:', jsonError);
                                    console.error('Texto que causó el error:', text);
                                    
                                    throw new Error(`Error parsing JSON: ${jsonError.message}\nTexto completo: ${text}`);
                                }
                            });
                        })
                        .then(data => {
                            console.log('Datos JSON recibidos:', data);
                            
                            if (!data.success) {
                                throw new Error(data.error || 'Error desconocido del servidor');
                            }
                            
                            procesarRespuestaConsulta(data, elementos);
                        })
                        .catch(error => {
                            console.error('Error en la consulta:', error);
                            manejarErrorConsulta(error, elementos);
                        });
                }
                
                // Iniciar búsqueda de elementos
                buscarElementos();
                    
            } catch (error) {
                console.error('Error general en consultarDisponibilidadFecha:', error);
                showNotification('Error interno: ' + error.message, 'error');
            }
        }
        
        function procesarRespuestaConsulta(data, elementos) {
            try {
                const stats = data.estadisticas;
                
                // Actualizar información - Solo mostrar árbitros disponibles
                elementos.infoTexto.innerHTML = `
                    Consulta para <strong>${data.dia_semana}, ${data.fecha_formateada}</strong> - 
                    <span style="color: var(--success); font-weight: bold;">${stats.total_disponibles} árbitros disponibles</span>
                    ${stats.con_observaciones > 0 ? 
                        `, <span style="color: var(--info); font-weight: bold;">${stats.con_observaciones} con observaciones</span>` : 
                        ''
                    }
                `;
                
                // Generar tabla
                let html = '';
                
                if (data.arbitros.length === 0) {
                    html = `
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--medium-gray);">
                                <i class="fas fa-info-circle fa-2x"></i>
                                <p style="margin-top: 10px;">No hay árbitros disponibles para esta fecha</p>
                            </td>
                        </tr>
                    `;
                } else {
                    data.arbitros.forEach(arbitro => {
                        const mananaFlag = arbitro.manana == 1 || arbitro.manana === '1';
                        const tardeFlag = arbitro.tarde == 1 || arbitro.tarde === '1';
                        const tieneObservaciones = (arbitro.observacion_manana && arbitro.observacion_manana.trim() !== '') || (arbitro.observacion_tarde && arbitro.observacion_tarde.trim() !== '');
                        
                        // Formatear licencia con colores y texto
                        let licenciaColor = 'var(--medium-gray)';
                        let licenciaTexto = '';
                        switch(arbitro.licencia) {
                            case 'n3a': 
                                licenciaColor = 'var(--success)'; 
                                licenciaTexto = 'NIVEL III A';
                                break;
                            case 'n3b': 
                                licenciaColor = '#28a745'; 
                                licenciaTexto = 'NIVEL III B';
                                break;
                            case 'n3c': 
                                licenciaColor = '#20c997'; 
                                licenciaTexto = 'NIVEL III C';
                                break;
                            case 'n2': 
                                licenciaColor = 'var(--info)'; 
                                licenciaTexto = 'NIVEL II';
                                break;
                            case 'n1': 
                                licenciaColor = 'var(--warning)'; 
                                licenciaTexto = 'NIVEL I';
                                break;
                            case 'anotador': 
                                licenciaColor = '#fd7e14'; 
                                licenciaTexto = 'ANOTADOR';
                                break;
                            case 'colaborador': 
                                licenciaColor = '#6c757d'; 
                                licenciaTexto = 'COLABORADOR/A';
                                break;
                            case 'habilitado_n1':
                                licenciaColor = 'var(--warning)';
                                licenciaTexto = 'HABILITADO N1';
                                break;
                            case 'habilitado_n2':
                                licenciaColor = 'var(--info)';
                                licenciaTexto = 'HABILITADO N2';
                                break;
                            case 'habilitado_n3':
                                licenciaColor = '#20c997';
                                licenciaTexto = 'HABILITADO N3';
                                break;
                            default:
                                licenciaTexto = arbitro.licencia ? arbitro.licencia.toUpperCase() : 'SIN LICENCIA';
                        }

                        const observacionesRaw = [];
                        if (arbitro.observacion_manana) observacionesRaw.push('Mañana: ' + arbitro.observacion_manana);
                        if (arbitro.observacion_tarde) observacionesRaw.push('Tarde: ' + arbitro.observacion_tarde);
                        const observacionesEscapadas = observacionesRaw.length > 0 ? observacionesRaw.join(' | ').replace(/'/g, "\\'").replace(/\"/g, '\\"') : '';
                        const observacionMananaEsc = arbitro.observacion_manana ? arbitro.observacion_manana.replace(/'/g, "\\'").replace(/\"/g, '\\"') : '';
                        const observacionTardeEsc = arbitro.observacion_tarde ? arbitro.observacion_tarde.replace(/'/g, "\\'").replace(/\"/g, '\\"') : '';

                        // Construir celda de disponibilidad con dos mitades (Mañana / Tarde) igual que el calendario
                        const mananaClass = 'half-m ' + (mananaFlag ? 'disponible' : 'nodisponible');
                        const tardeClass = 'half-t ' + (tardeFlag ? 'disponible' : 'nodisponible');
                        const iconManana = (mananaFlag && arbitro.observacion_manana) ? "<i class='fas fa-edit' style='position:absolute;right:4px;top:4px;font-size:0.7em;opacity:0.8;' title='Obs. mañana'></i>" : '';
                        const iconTarde = (tardeFlag && arbitro.observacion_tarde) ? "<i class='fas fa-edit' style='position:absolute;right:4px;top:4px;font-size:0.7em;opacity:0.8;' title='Obs. tarde'></i>" : '';

                        // Handlers por franja (si hay observación) y clase clickable
                        // Solo añadir data-attributes y clickable si la franja está disponible y tiene observación
                        const observacionMananaData = (mananaFlag && observacionMananaEsc) ? `data-observacion="${observacionMananaEsc}" data-franja="Mañana" data-nombre="${arbitro.nombre} ${arbitro.apellidos}"` : '';
                        const observacionTardeData = (tardeFlag && observacionTardeEsc) ? `data-observacion="${observacionTardeEsc}" data-franja="Tarde" data-nombre="${arbitro.nombre} ${arbitro.apellidos}"` : '';
                        const clickableMan = (mananaFlag && observacionMananaEsc) ? ' clickable' : '';
                        const clickableTar = (tardeFlag && observacionTardeEsc) ? ' clickable' : '';

                        html += `
                            <tr style="background: rgba(72, 187, 120, 0.02);">
                                <td>
                                    <strong>${arbitro.nombre} ${arbitro.apellidos}</strong>
                                    ${arbitro.numero_licencia ? `<br><small style="color: var(--medium-gray);">Nº ${arbitro.numero_licencia}</small>` : ''}
                                </td>
                                <td>${arbitro.ciudad}</td>
                                <td>
                                    <span class="badge" style="background: ${licenciaColor}; color: white;">
                                        ${licenciaTexto}
                                    </span>
                                </td>
                                <td style="width:160px;">
                                    <div style="display:flex; gap:2px; align-items:stretch;">
                                        <div class="${mananaClass}${clickableMan}" ${observacionMananaData} style="flex:1; position:relative; padding:6px 0; text-align:center; font-weight:700;">M${iconManana}</div>
                                        <div class="${tardeClass}${clickableTar}" ${observacionTardeData} style="flex:1; position:relative; padding:6px 0; text-align:center; font-weight:700;">T${iconTarde}</div>
                                    </div>
                                </td>
                                <td>
                                    <button onclick="verDisponibilidad(${arbitro.id})" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-calendar-check"></i> Ver Calendario
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                elementos.bodyTabla.innerHTML = html;
                
                // Scroll suave hasta los resultados
                elementos.resultados.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                
                
            } catch (error) {
                console.error('Error procesando respuesta:', error);
                manejarErrorConsulta(error, elementos);
            }
        }
        
        function manejarErrorConsulta(error, elementos) {
            try {
                if (elementos && elementos.bodyTabla) {
                    elementos.bodyTabla.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--danger);">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                                <p style="margin-top: 10px;">Error al consultar disponibilidad</p>
                                <small>${error.message}</small>
                            </td>
                        </tr>
                    `;
                }
                
                if (elementos && elementos.infoTexto) {
                    elementos.infoTexto.textContent = 'Error en la consulta';
                }
                
                showNotification('Error al consultar disponibilidad: ' + error.message, 'error');
            } catch (secondaryError) {
                console.error('Error manejando error:', secondaryError);
                showNotification('Error crítico en la consulta', 'error');
            }
        }
        
        window.limpiarConsultaFecha = function() {
            const resultadosDiv = document.getElementById('resultadosConsultaFecha');
            const btnLimpiar = document.getElementById('btnLimpiarFecha');
            const fechaInput = document.getElementById('fechaConsulta');
            
            if (resultadosDiv) {
                resultadosDiv.style.display = 'none';
            }
            
            if (btnLimpiar) {
                btnLimpiar.style.display = 'none';
            }
            
            if (fechaInput) {
                fechaInput.value = new Date().toISOString().split('T')[0];
            }
            
            if (typeof showNotification === 'function') {
                showNotification('Consulta limpiada', 'info');
            }
        }
        
        function mostrarObservacionesFecha(fecha, observaciones, nombreArbitro, franja) {
            if (!observaciones || observaciones.trim() === '') return;
            
            // Formatear fecha
            const fechaObj = new Date(fecha + 'T12:00:00');
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Crear modal temporal
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            modal.innerHTML = `
                <div class="modal-content" style="max-width: 600px;">
                    <div class="modal-header">
                        <h3><i class="fas fa-comment"></i> Observaciones del Árbitro ${franja ? ' - ' + franja : ''}</h3>
                        <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div style="margin-bottom: 15px; padding: 15px; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-radius: 8px; border-left: 4px solid var(--primary-green);">
                            <div style="margin-bottom: 8px;"><strong><i class="fas fa-user"></i> ${nombreArbitro}</strong></div>
                            <div><strong><i class="fas fa-calendar-alt"></i> ${fechaFormateada}</strong></div>
                        </div>
                        <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid var(--light-green);">
                            <div style="margin-bottom: 12px;">
                                <strong><i class="fas fa-check-circle" style="color: var(--success);"></i> Estado: Disponible ${franja ? '('+franja+')' : ''}</strong>
                            </div>
                            <div style="margin-bottom: 8px;">
                                <strong><i class="fas fa-comment-dots"></i> Observaciones:</strong>
                            </div>
                            <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #e0e0e0; font-style: italic; line-height: 1.5;">
                                ${observaciones.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        // Evento para permitir consulta con Enter en el campo de fecha
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INICIALIZACIÓN CONSULTA POR FECHA ===');
            
            const fechaInput = document.getElementById('fechaConsulta');
            if (fechaInput) {
                fechaInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        if (typeof window.consultarDisponibilidadFecha === 'function') {
                            window.consultarDisponibilidadFecha();
                        } else {
                            console.error('Función consultarDisponibilidadFecha no disponible');
                        }
                    }
                });
                
                console.log('✅ Event listeners para consulta por fecha configurados correctamente');
            } else {
                console.error('❌ No se encontró el campo fechaConsulta');
            }
            
            // Verificar que todos los elementos necesarios existan
            const elementosRequeridos = [
                'fechaConsulta',
                'resultadosConsultaFecha', 
                'textoInfoConsulta',
                'bodyArbitrosFecha',
                'btnLimpiarFecha'
            ];
            
            const elementosFaltantes = elementosRequeridos.filter(id => !document.getElementById(id));
            
            if (elementosFaltantes.length > 0) {
                console.error('❌ Elementos faltantes para consulta por fecha:', elementosFaltantes);
                
                // Mostrar información adicional del DOM
                console.log('Contenido del body (primeros 2000 caracteres):');
                console.log(document.body.innerHTML.substring(0, 2000));
                
                // Buscar si la sección de consulta existe
                const consultaSection = document.querySelector('.card-header');
                if (consultaSection) {
                    console.log('Sección de consulta encontrada:', consultaSection.textContent);
                } else {
                    console.error('No se encontró ninguna sección de consulta');
                }
            } else {
                console.log('✅ Todos los elementos para consulta por fecha están presentes');
            }
            
            // Verificar funciones globales
            const funcionesRequeridas = ['consultarDisponibilidadFecha', 'limpiarConsultaFecha'];
            funcionesRequeridas.forEach(funcName => {
                if (typeof window[funcName] === 'function') {
                    console.log(`✅ Función window.${funcName} disponible globalmente`);
                } else {
                    console.error(`❌ Función window.${funcName} NO disponible globalmente`);
                }
            });
            
            // Probar llamada a función
            console.log('🧪 Probando acceso a funciones...');
            if (typeof window.consultarDisponibilidadFecha === 'function') {
                console.log('✅ window.consultarDisponibilidadFecha es callable');
            }
            if (typeof window.limpiarConsultaFecha === 'function') {
                console.log('✅ window.limpiarConsultaFecha es callable');
            }
            
            console.log('=== FIN INICIALIZACIÓN ===');
            
            // Delegación: abrir modal de observaciones al pulsar sobre la mitad M/T clicable
            document.body.addEventListener('click', function(e) {
                const target = e.target.closest('.half-m.clickable, .half-t.clickable');
                if (!target) return;
                const observacion = target.getAttribute('data-observacion');
                const franja = target.getAttribute('data-franja');
                const nombre = target.getAttribute('data-nombre');
                const fecha = document.getElementById('fechaConsulta') ? document.getElementById('fechaConsulta').value : null;
                if (observacion && nombre) {
                    mostrarObservacionesFecha(fecha || '', observacion, nombre, franja || '');
                }
            });
        });
    </script>

    <style>
        .calendar-day-mini {
            background: white;
            border: 1px solid var(--light-gray);
            padding: 5px;
            text-align: center;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .calendar-day-mini.available {
            background: #e8f5e8;
            color: var(--dark-green);
        }
        
        .calendar-day-mini.unavailable {
            background: #ffebee;
            color: #c62828;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-green);
        }
        
        .stat-label {
            color: var(--medium-gray);
            font-size: 0.9rem;
        }
        
        /* Estilos para el calendario de disponibilidad del modal */
        .calendar-disponibilidad {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .calendar-header-admin {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: white;
            padding: 20px;
        }
        
        .calendar-navigation-admin {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .calendar-actions-admin {
            display: flex;
            justify-content: center;
        }
        
        .calendar-header-admin h4 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            flex: 1;
            text-align: center;
        }
        
        .btn-nav-admin {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .btn-nav-admin:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-1px);
        }
        
        .btn-today-admin {
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .btn-today-admin:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
        }
        
        .calendar-grid-admin {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-top: 1px solid #e0e0e0;
        }
        
        .calendar-day-header-admin {
            background: var(--primary-green);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .calendar-day-header-admin:last-child {
            border-right: none;
        }
        
        .calendar-day-admin {
            min-height: 60px;
            border-right: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
            background: white;
            transition: all 0.2s ease;
        }
        
        .calendar-day-admin:nth-child(7n) {
            border-right: none;
        }
        
        .calendar-day-admin.other-month {
            color: #bbb;
            background: #fafafa;
        }
        
        .calendar-day-admin.available {
            background: linear-gradient(135deg, #e8f5e8, #f1f8e9);
            color: var(--dark-green);
            border-left: 3px solid var(--light-green);
        }
        
        .calendar-day-admin.unavailable {
            background: linear-gradient(135deg, #ffebee, #fce4ec);
            color: #c62828;
            border-left: 3px solid #f44336;
        }
        
        .calendar-day-admin.today {
            border: 2px solid var(--primary-green);
            font-weight: bold;
        }
        
        .day-number-admin {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 4px;
            line-height: 1;
        }
        
        .availability-icon-admin {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .calendar-day-admin.available .availability-icon-admin {
            color: var(--light-green);
        }
        
        .calendar-day-admin.unavailable .availability-icon-admin {
            color: #f44336;
        }
        
        /* Estilo especial para días con observaciones */
        .calendar-day-admin[title*="Observaciones"]:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(46, 125, 50, 0.3);
            cursor: pointer;
        }
        
        .calendar-day-admin .fas.fa-comment {
            color: var(--primary-green);
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .legend-admin {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 15px;
            background: linear-gradient(to right, #f8f9fa, #e9ecef, #f8f9fa);
            border-top: 1px solid #e0e0e0;
        }
        
        .legend-item-admin {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .legend-color-admin {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid rgba(0,0,0,0.1);
            background: white;
        }
        
        .legend-color-admin.available {
            background: linear-gradient(135deg, #e8f5e8, #f1f8e9);
            border-left: 3px solid var(--light-green);
        }
        
        .legend-color-admin.unavailable {
            background: linear-gradient(135deg, #ffebee, #fce4ec);
            border-left: 3px solid #f44336;
        }
        
        .legend-color-admin.default {
            background: white;
            border: 1px solid #e0e0e0;
        }
        
        .info-text-admin {
            grid-column: 1 / -1;
            text-align: center;
            color: var(--medium-gray);
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
        
        /* Responsive para el modal */
        @media (max-width: 768px) {
            .calendar-navigation-admin {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .calendar-navigation-admin h4 {
                order: -1;
                margin-bottom: 10px;
            }
            
            .calendar-actions-admin {
                order: 1;
                margin-top: 10px;
            }
            
            .calendar-day-admin {
                min-height: 45px;
                padding: 4px 2px;
            }
            
            .day-number-admin {
                font-size: 0.9rem;
            }
            
            .availability-icon-admin {
                font-size: 0.8rem;
            }
            
            .legend-admin {
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
            
            .btn-nav-admin, .btn-today-admin {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .calendar-actions-admin {
                flex-direction: column;
                gap: 5px;
            }
        }
        
        /* Estilos para calendario editable */
        .calendar-day-editable {
            cursor: pointer;
            user-select: none;
        }
        
        .calendar-day-editable:hover:not(.past-day):not(.other-month) {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
            z-index: 1;
        }
        
        .calendar-day-editable.past-day {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .calendar-day-admin.available-full {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            color: var(--dark-green);
            border-left: 3px solid var(--light-green);
        }
        
        .calendar-day-admin.available-manana {
            background: linear-gradient(135deg, #fff9c4, #fff59d);
            color: #f57c00;
            border-left: 3px solid #FFA726;
        }
        
        .calendar-day-admin.available-tarde {
            background: linear-gradient(135deg, #e1f5fe, #b3e5fc);
            color: #1565c0;
            border-left: 3px solid #5C6BC0;
        }
        
        .legend-color-admin.available-manana {
            background: linear-gradient(135deg, #fff9c4, #fff59d);
            border-left: 3px solid #FFA726;
        }
        
        .legend-color-admin.available-tarde {
            background: linear-gradient(135deg, #e1f5fe, #b3e5fc);
            border-left: 3px solid #5C6BC0;
        }
        
        /* Estilos para filtros mejorados */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-black);
            margin-bottom: 5px;
            display: block;
        }
        
        .form-control:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }
        
        /* Estilos para mensajes de resultados */
        #mensajeResultados {
            border-left: 4px solid var(--primary-green);
            background: linear-gradient(135deg, #f8f9fa, #e8f5e8);
            animation: slideInDown 0.3s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Estilos para filas filtradas */
        #arbitrosTable tbody tr[style*="background: #e8f5e8"] {
            border-left: 3px solid var(--light-green);
            transition: all 0.3s ease;
        }
        
        #arbitrosTable tbody tr[style*="background: #e8f5e8"]:hover {
            background: #d4edda !important;
            transform: translateX(5px);
        }
        
        /* Mejorar botones de filtros */
        .btn-secondary:hover {
            background-color: var(--medium-gray);
            border-color: var(--medium-gray);
        }
        
        .btn-info:hover {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }
        
        /* Estilos para la información de ayuda */
        .alert-info ul {
            margin-bottom: 0;
        }
        
        .alert-info ul li {
            margin-bottom: 3px;
        }
        
        /* Responsive para filtros */
        @media (max-width: 768px) {
            .card-body > div {
                grid-template-columns: 1fr !important;
            }
            
            .form-group div {
                flex-direction: column;
                gap: 8px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        /* Estilos específicos para números de licencia */
        .badge .fas.fa-id-card {
            margin-right: 5px;
        }
        
        .badge .fas.fa-exclamation-triangle {
            margin-right: 5px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        /* Estilo para botón de asignar licencia */
        .btn-warning.btn-sm {
            margin-right: 5px;
            margin-bottom: 2px;
        }
        
        .btn-warning.btn-sm:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
        }
        
        /* Estilos para el modal de número de licencia */
        .form-control-readonly {
            background: #f8f9fa !important;
            border: 1px solid #e0e0e0 !important;
            color: #495057 !important;
            cursor: default;
        }
        
        #numeroLicencia {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        #numeroLicencia:focus {
            border-color: var(--warning);
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
        
        .alert-info ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        
        .alert-info li {
            margin-bottom: 3px;
        }
        
        /* Mejoras en la tabla */
        .table tbody tr:hover .btn-warning {
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.4);
        }
        
        /* Responsive para botones en dispositivos móviles */
        @media (max-width: 768px) {
            .btn.btn-sm {
                padding: 4px 8px;
                font-size: 0.75rem;
                margin-bottom: 3px;
                display: block;
                width: 100%;
            }
            
            .btn-warning.btn-sm {
                margin-right: 0;
                margin-bottom: 5px;
            }
        }
    </style>
    
    <!-- Estilos para consulta por fecha -->
    <style>
        /* Estilos para la sección de consulta por fecha */
        .form-group input[type="date"] {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .form-group input[type="date"]:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
            outline: none;
        }
        
        /* Mejorar la tabla de resultados */
        #tablaArbitrosFecha {
            margin-top: 0;
        }
        
        #tablaArbitrosFecha th {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: white;
            font-weight: 600;
            text-align: center;
            border: none;
            padding: 12px 8px;
        }
        
        #tablaArbitrosFecha td {
            vertical-align: middle;
            padding: 12px 8px;
            border-top: 1px solid #e9ecef;
        }
        
        #tablaArbitrosFecha tbody tr:hover {
            background-color: rgba(46, 125, 50, 0.05) !important;
            transform: translateX(3px);
            transition: all 0.2s ease;
        }
        
        /* Mejorar el alert de información */
        #infoConsultaFecha {
            border-left: 4px solid var(--primary-green);
            background: linear-gradient(135deg, #f8f9fa, #e8f5e8);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        /* Animación suave para mostrar resultados */
        #resultadosConsultaFecha {
            animation: slideInDown 0.4s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Estilos para los badges en la tabla de fecha */
        #tablaArbitrosFecha .badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        
        /* Iconos mejorados */
        #tablaArbitrosFecha .fas.fa-comment {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        #tablaArbitrosFecha .fas.fa-comment:hover {
            transform: scale(1.2);
            color: var(--primary-green) !important;
        }
        
        /* Botones en la tabla */
        #tablaArbitrosFecha .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        
        #tablaArbitrosFecha .btn-sm:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Mejorar el grid de controles */
        .card-body > div[style*="grid-template-columns"] {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        /* Estados de filas */
        #tablaArbitrosFecha tr[style*="rgba(72, 187, 120"] {
            border-left: 3px solid var(--success);
        }
        
        #tablaArbitrosFecha tr[style*="rgba(245, 101, 101"] {
            border-left: 3px solid var(--danger);
        }
        
        /* Responsive para consulta por fecha */
        @media (max-width: 768px) {
            .card-body > div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }
            
            .form-group {
                margin-bottom: 10px !important;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 5px;
            }
            
            #tablaArbitrosFecha .btn-sm {
                display: block;
                width: 100%;
                margin-bottom: 3px;
                margin-left: 0 !important;
            }
            
            #tablaArbitrosFecha th,
            #tablaArbitrosFecha td {
                padding: 8px 4px;
                font-size: 0.85rem;
            }
            
            #infoConsultaFecha {
                font-size: 0.9rem;
                text-align: center;
            }
        }
        
        /* Mejorar la experiencia de loading */
        .loading-cell {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading-shimmer 1.5s infinite;
        }
        
        @keyframes loading-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Mejorar el modal de observaciones */
        .modal .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
    
    <!-- Estilos adicionales para estadísticas completas -->
    <style>
        /* Estilos para el modal de estadísticas más grande */
        #estadisticasModal .modal-content {
            max-width: 1200px;
            width: 95%;
            max-height: 90vh;
        }
        
        #estadisticasModal .modal-body {
            max-height: 75vh;
            overflow-y: auto;
            padding: 20px;
        }
        
        /* Estilos para estadísticas completas */
        .estadisticas-completas {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .stat-card-mini {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .stat-card-mini:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .stat-card-mini .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        
        .stat-card-mini .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .stat-card-mini .stat-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
        }
        
        .stat-card-detailed {
            background: white;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }
        
        .stat-card-detailed:hover {
            border-color: var(--primary-green);
            box-shadow: 0 2px 8px rgba(46, 125, 50, 0.1);
        }
        
        .stat-card-detailed .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .stat-card-detailed .stat-label {
            font-size: 0.8rem;
            color: #666;
            font-weight: 500;
        }
        
        .tabs-estadisticas .tab-btn {
            transition: all 0.3s ease;
            border-radius: 0;
        }
        
        .tabs-estadisticas .tab-btn:hover {
            background: var(--light-green) !important;
            color: white !important;
        }
        
        .tab-content {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Mejorar la tabla de licencias */
        .table-success {
            background-color: rgba(72, 187, 120, 0.1) !important;
        }
        
        .table-danger {
            background-color: rgba(245, 101, 101, 0.1) !important;
        }
        
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        
        /* Responsive para estadísticas */
        @media (max-width: 768px) {
            .arbitro-header > div {
                grid-template-columns: 1fr !important;
                text-align: center;
            }
            
            .tabs-estadisticas .tab-btn {
                padding: 8px 4px;
                font-size: 0.8rem;
            }
            
            .stat-card-mini, .stat-card-detailed {
                padding: 10px;
            }
            
            .stat-card-mini .stat-value {
                font-size: 1.4rem;
            }
            
            .estadisticas-completas {
                max-height: 60vh;
            }
            
            #estadisticasModal .modal-content {
                width: 98%;
                max-height: 95vh;
            }
        }
    </style>
    
    <script src="../assets/js/search-bar.js"></script>
    <script>
        // Inicializar búsqueda para la tabla de árbitros
        document.addEventListener('DOMContentLoaded', function() {
            new TableSearchBar({
                searchInputId: 'searchInput',
                clearBtnId: 'searchClear',
                searchInfoId: 'searchInfo',
                searchResultsId: 'searchResults',
                totalCountId: 'total-count',
                tableSelector: 'tbody tr',
                columnsCount: 7, // Actualizado para incluir la nueva columna
                noResultsId: 'noResultsRow'
            });
        });
    </script>
</body>
</html>
