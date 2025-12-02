<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

$message = '';
$error = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'create':
                    $arbitro_id = sanitize_input($_POST['arbitro_id']);
                    $fecha_expedicion = sanitize_input($_POST['fecha_expedicion']);
                    $nivel_licencia = sanitize_input($_POST['nivel_licencia']);

                    // Validaciones
                    if (empty($arbitro_id) || empty($fecha_expedicion) || empty($nivel_licencia)) {
                        throw new Exception('Todos los campos obligatorios deben ser completados');
                    }

                    // Insertar nueva licencia
                    $query = "INSERT INTO licencias_arbitros 
                             (arbitro_id, fecha_expedicion, nivel_licencia) 
                             VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$arbitro_id, $fecha_expedicion, $nivel_licencia]);

                    // Actualizar nivel de licencia en tabla árbitros con la licencia de mayor nivel activa
                    $licencia_principal = obtener_licencia_principal_arbitro($conn, $arbitro_id);
                    if ($licencia_principal) {
                        $updateArbitroQuery = "UPDATE arbitros SET licencia = ? WHERE id = ?";
                        $updateArbitroStmt = $conn->prepare($updateArbitroQuery);
                        $updateArbitroStmt->execute([$licencia_principal['nivel_licencia'], $arbitro_id]);
                    }

                    $message = success_message('Licencia registrada correctamente');
                    break;

                case 'edit':
                    $id = sanitize_input($_POST['id']);
                    $arbitro_id = sanitize_input($_POST['arbitro_id']);
                    $fecha_expedicion = sanitize_input($_POST['fecha_expedicion']);
                    $nivel_licencia = sanitize_input($_POST['nivel_licencia']);

                    $query = "UPDATE licencias_arbitros 
                             SET fecha_expedicion = ?, nivel_licencia = ?
                             WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$fecha_expedicion, $nivel_licencia, $id]);

                    // Actualizar nivel de licencia en tabla árbitros con la licencia de mayor nivel activa
                    $licencia_principal = obtener_licencia_principal_arbitro($conn, $arbitro_id);
                    if ($licencia_principal) {
                        $updateArbitroQuery = "UPDATE arbitros SET licencia = ? WHERE id = ?";
                        $updateArbitroStmt = $conn->prepare($updateArbitroQuery);
                        $updateArbitroStmt->execute([$licencia_principal['nivel_licencia'], $arbitro_id]);
                    }

                    $message = success_message('Licencia actualizada correctamente');
                    break;

                case 'delete':
                    $id = sanitize_input($_POST['id']);
                    
                    // Obtener el arbitro_id antes de eliminar la licencia
                    $getArbitroQuery = "SELECT arbitro_id FROM licencias_arbitros WHERE id = ?";
                    $getArbitroStmt = $conn->prepare($getArbitroQuery);
                    $getArbitroStmt->execute([$id]);
                    $arbitro_id = $getArbitroStmt->fetchColumn();
                    
                    $query = "DELETE FROM licencias_arbitros WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->execute([$id]);

                    // Actualizar nivel de licencia en tabla árbitros con la licencia de mayor nivel activa
                    if ($arbitro_id) {
                        $licencia_principal = obtener_licencia_principal_arbitro($conn, $arbitro_id);
                        if ($licencia_principal) {
                            $updateArbitroQuery = "UPDATE arbitros SET licencia = ? WHERE id = ?";
                            $updateArbitroStmt = $conn->prepare($updateArbitroQuery);
                            $updateArbitroStmt->execute([$licencia_principal['nivel_licencia'], $arbitro_id]);
                        } else {
                            // Si no tiene licencias activas, establecer un valor por defecto
                            $updateArbitroQuery = "UPDATE arbitros SET licencia = 'anotador' WHERE id = ?";
                            $updateArbitroStmt = $conn->prepare($updateArbitroQuery);
                            $updateArbitroStmt->execute([$arbitro_id]);
                        }
                    }

                    $message = success_message('Licencia eliminada correctamente');
                    break;
            }
        }
    } catch (Exception $e) {
        $error = error_message($e->getMessage());
    }
}

// Obtener licencias
$query = "SELECT l.*, a.nombre, a.apellidos, a.ciudad
          FROM licencias_arbitros l
          JOIN arbitros a ON l.arbitro_id = a.id
          ORDER BY l.fecha_creacion DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$licencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener árbitros para el formulario
$arbitrosQuery = "SELECT a.id, a.nombre, a.apellidos, a.ciudad, a.licencia 
                  FROM arbitros a 
                  JOIN usuarios u ON a.usuario_id = u.id 
                  WHERE u.activo = 1 
                  ORDER BY a.nombre, a.apellidos";
$arbitrosStmt = $conn->prepare($arbitrosQuery);
$arbitrosStmt->execute();
$arbitros = $arbitrosStmt->fetchAll(PDO::FETCH_ASSOC);

// Licencia para editar
$licenciaEdit = null;
if (isset($_GET['edit'])) {
    $editId = sanitize_input($_GET['edit']);
    $editQuery = "SELECT * FROM licencias_arbitros WHERE id = ?";
    $editStmt = $conn->prepare($editQuery);
    $editStmt->execute([$editId]);
    $licenciaEdit = $editStmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Gestión de Licencias - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            <li><a href="arbitros.php"><i class="fa-solid fa-person"></i> Gestión de Árbitros</a></li>
            <li><a href="licencias.php" class="active"><i class="fas fa-id-card"></i> Gestión de Licencias</a></li>
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
            <h1><i class="fas fa-id-card"></i> Gestión de Licencias de Árbitros</h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> Inicio / Gestión de Licencias
            </div>
        </div>

        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <?php echo $error; ?>
        <?php endif; ?>

        <!-- Formulario de licencia -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus"></i> <?php echo $licenciaEdit ? 'Editar Licencia' : 'Nueva Licencia'; ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $licenciaEdit ? 'edit' : 'create'; ?>">
                    <?php if ($licenciaEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $licenciaEdit['id']; ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="arbitro_id">Árbitro *</label>
                            <select name="arbitro_id" id="arbitro_id" required class="form-control">
                                <option value="">Seleccionar árbitro</option>
                                <?php foreach ($arbitros as $arbitro): ?>
                                    <option value="<?php echo $arbitro['id']; ?>" 
                                            <?php echo ($licenciaEdit && $licenciaEdit['arbitro_id'] == $arbitro['id']) ? 'selected' : ''; ?>>
                                        <?php echo $arbitro['nombre'] . ' ' . $arbitro['apellidos'] . ' (' . $arbitro['ciudad'] . ')'; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nivel_licencia">Nivel de Licencia *</label>
                            <select name="nivel_licencia" id="nivel_licencia" required class="form-control">
                                <option value="">Seleccionar nivel</option>
                                <option value="colaborador" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'colaborador') ? 'selected' : ''; ?>>Colaborador/a</option>
                                <option value="anotador" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'anotador') ? 'selected' : ''; ?>>Anotador</option>
                                <option value="habilitado_n1" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'habilitado_n1') ? 'selected' : ''; ?>>Habilitado Nivel I</option>
                                <option value="habilitado_n2" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'habilitado_n2') ? 'selected' : ''; ?>>Habilitado Nivel II</option>
                                <option value="habilitado_n3" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'habilitado_n3') ? 'selected' : ''; ?>>Habilitado Nivel III</option>
                                <option value="n1" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'n1') ? 'selected' : ''; ?>>Nivel I (N1)</option>
                                <option value="n2" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'n2') ? 'selected' : ''; ?>>Nivel II (N2)</option>
                                <option value="n3c" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'n3c') ? 'selected' : ''; ?>>Nivel III C (N3 C)</option>
                                <option value="n3b" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'n3b') ? 'selected' : ''; ?>>Nivel III B (N3 B)</option>
                                <option value="n3a" <?php echo ($licenciaEdit && $licenciaEdit['nivel_licencia'] == 'n3a') ? 'selected' : ''; ?>>Nivel III A (N3 A)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_expedicion">Fecha de Expedición *</label>
                            <input type="date" name="fecha_expedicion" id="fecha_expedicion" required class="form-control"
                                   value="<?php echo $licenciaEdit ? $licenciaEdit['fecha_expedicion'] : ''; ?>">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $licenciaEdit ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <?php if ($licenciaEdit): ?>
                            <a href="licencias.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado de licencias -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Licencias Registradas
                <span class="badge" style="background: var(--primary-green); color: white; margin-left: 10px;">
                    <span id="total-count"><?php echo count($licencias); ?></span> licencias
                </span>
            </div>
            <div class="card-body">
                                <div class="search-container">
                    <div class="search-input-group">
                        <div class="search-input-icon">
                            <i class="fas fa-search"></i>
                            <input type="text" 
                                   id="searchInput" 
                                   placeholder="Buscar por árbitro, nivel..." 
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
                        <span id="searchResults">0</span> licencia(s) encontrada(s)
                    </div>
                    <div class="search-help">
                        <i class="fas fa-lightbulb"></i> 
                        <em>Busca por nombre del árbitro o nivel de licencia. Usa ESC para limpiar.</em>
                    </div>
                </div>
                
                <?php if (count($licencias) > 0): ?>
                    <div class="table-responsive">
                        <table class="table data-table searchable-table" id="licenciasTable">
                            <thead>
                                <tr>
                                    <th data-sortable>Árbitro</th>
                                    <th data-sortable>Nivel</th>
                                    <th data-sortable>Fecha Expedición</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($licencias as $licencia): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $licencia['nombre'] . ' ' . $licencia['apellidos']; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $licencia['ciudad']; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: 
                                                <?php 
                                                switch($licencia['nivel_licencia']) {
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
                                                switch($licencia['nivel_licencia']) {
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
                                                        echo strtoupper($licencia['nivel_licencia']);
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo format_date($licencia['fecha_expedicion']); ?></td>
                                        <td>
                                            <a href="licencias.php?edit=<?php echo $licencia['id']; ?>" 
                                               class="btn btn-primary btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmDelete(<?php echo $licencia['id']; ?>)" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center p-4">
                        <i class="fas fa-id-card" style="font-size: 3rem; color: var(--medium-gray);"></i>
                        <h4 class="mt-3">No hay licencias registradas</h4>
                        <p class="text-muted">Comience registrando la primera licencia de árbitro</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal de confirmación -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h3>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <p>¿Está seguro de que desea eliminar esta licencia?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">
                        Cancelar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script>
        function confirmDelete(id) {
            document.getElementById('deleteId').value = id;
            openModal('deleteModal');
        }
    </script>
    
    <script src="../assets/js/search-bar.js"></script>
    <script>
        // Inicializar búsqueda para la tabla de licencias
        document.addEventListener('DOMContentLoaded', function() {
            new TableSearchBar({
                searchInputId: 'searchInput',
                clearBtnId: 'searchClear',
                searchInfoId: 'searchInfo',
                searchResultsId: 'searchResults',
                totalCountId: 'total-count',
                tableSelector: 'tbody tr',
                columnsCount: 4,
                noResultsId: 'noResultsRow'
            });
        });
    </script>
</body>
</html>
