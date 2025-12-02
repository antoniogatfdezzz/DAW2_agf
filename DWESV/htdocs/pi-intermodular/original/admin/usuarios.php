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
            $message = createUser($conn, $_POST);
            break;
        case 'edit':
            $message = editUser($conn, $_POST);
            break;
        case 'delete':
            $message = deleteUser($conn, $_POST['user_id']);
            break;
        case 'reset_password':
            $message = resetPassword($conn, $_POST['user_id']);
            break;
    }
}

// Obtener usuarios
$query = "SELECT u.*, 
                 CASE 
                     WHEN u.tipo_usuario = 'administrador' THEN CONCAT(a.nombre, ' ', a.apellidos)
                     WHEN u.tipo_usuario = 'arbitro' THEN CONCAT(ar.nombre, ' ', ar.apellidos)
                 END as nombre_completo
                    FROM usuarios u
                    LEFT JOIN administradores a ON u.id = a.usuario_id
                    LEFT JOIN arbitros ar ON u.id = ar.usuario_id
                    WHERE u.activo = 1
                    ORDER BY u.tipo_usuario, nombre_completo";
$stmt = $conn->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

function createUser($conn, $data) {
    try {
        $conn->beginTransaction();
        
        $tipo = $data['tipo_usuario'];
        $usuario = sanitize_input($data['usuario']);
        $email = sanitize_input($data['email']);
        
        // Validar que el nombre de usuario sea único
        $check_query = "SELECT id FROM usuarios WHERE usuario = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute([$usuario]);
        if ($check_stmt->fetch()) {
            throw new Exception("El nombre de usuario ya está registrado");
        }
        
        // Validaciones específicas para árbitros
        if ($tipo === 'arbitro') {
            $numero_matricula = sanitize_input($data['numero_matricula']);
            $dni = sanitize_input($data['dni']);
            
            // Verificar que el DNI no exista
            if (!empty($dni)) {
                $check_query = "SELECT id FROM arbitros WHERE dni = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->execute([$dni]);
                if ($check_stmt->fetch()) {
                    throw new Exception("El DNI ya está registrado");
                }
            }
            
            // Verificar que el número de matrícula no exista
            if (!empty($numero_matricula)) {
                $check_query = "SELECT id FROM arbitros WHERE numero_matricula = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->execute([$numero_matricula]);
                if ($check_stmt->fetch()) {
                    throw new Exception("El número de matrícula ya está registrado");
                }
            }
        }
        
        $password_temporal = generate_password();
        $password_hash = password_hash($password_temporal, PASSWORD_DEFAULT);
        
        // Crear usuario base
        $query = "INSERT INTO usuarios (usuario, tipo_usuario, email, password, password_temporal) VALUES (?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario, $tipo, $email, $password_hash]);
        $usuario_id = $conn->lastInsertId();
        
        // Crear registro específico según tipo
        switch ($tipo) {
            case 'administrador':
                $query = "INSERT INTO administradores (usuario_id, nombre, apellidos) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([$usuario_id, sanitize_input($data['nombre']), sanitize_input($data['apellidos'])]);
                break;
                
            case 'arbitro':
                $query = "INSERT INTO arbitros (usuario_id, nombre, apellidos, dni, telefono, numero_matricula, iban, ciudad) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    $usuario_id,
                    sanitize_input($data['nombre']),
                    sanitize_input($data['apellidos']),
                    sanitize_input($data['dni']),
                    sanitize_input($data['telefono']),
                    sanitize_input($data['numero_matricula']),
                    sanitize_input($data['iban']),
                    sanitize_input($data['ciudad'])
                    // La licencia se asigna automáticamente como 'colaborador' por el DEFAULT de la BD
                ]);
                break;
        }
        
        $conn->commit();
        return success_message("Usuario creado correctamente. Contraseña temporal: <strong>$password_temporal</strong>");
        
    } catch (Exception $e) {
        $conn->rollback();
        return error_message('Error al crear el usuario: ' . $e->getMessage());
    }
}

function editUser($conn, $data) {
    try {
        $conn->beginTransaction();
        
        $user_id = $data['user_id'];
        $tipo = $data['tipo_usuario'];
        $email = sanitize_input($data['email']);
        
        // Validaciones específicas para árbitros
        $nombre = sanitize_input($data['nombre']);
        $apellidos = sanitize_input($data['apellidos']);
        if ($tipo === 'arbitro') {
            $numero_matricula = sanitize_input($data['numero_matricula']);
            $dni = sanitize_input($data['dni']);
            // Verificar que el DNI no exista en otro árbitro
            if (!empty($dni)) {
                $check_query = "SELECT id FROM arbitros WHERE dni = ? AND usuario_id != ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->execute([$dni, $user_id]);
                if ($check_stmt->fetch()) {
                    throw new Exception("El DNI ya está registrado por otro árbitro");
                }
            }
            // Verificar que el número de matrícula no exista en otro árbitro
            if (!empty($numero_matricula)) {
                $check_query = "SELECT id FROM arbitros WHERE numero_matricula = ? AND usuario_id != ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->execute([$numero_matricula, $user_id]);
                if ($check_stmt->fetch()) {
                    throw new Exception("El número de matrícula ya está registrado por otro árbitro");
                }
            }
        }

        // Actualizar nombre de usuario y email del usuario base
        $usuario = sanitize_input($data['usuario']);
        $query = "UPDATE usuarios SET usuario = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario, $email, $user_id]);

        // Actualizar datos específicos según tipo
        switch ($tipo) {
            case 'administrador':
                $query = "UPDATE administradores SET nombre = ?, apellidos = ? WHERE usuario_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    $nombre,
                    $apellidos,
                    $user_id
                ]);
                break;
            case 'arbitro':
                $query = "UPDATE arbitros SET nombre = ?, apellidos = ?, dni = ?, telefono = ?, numero_matricula = ?, iban = ?, ciudad = ?, licencia = ? WHERE usuario_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    $nombre,
                    $apellidos,
                    sanitize_input($data['dni']),
                    sanitize_input($data['telefono']),
                    sanitize_input($data['numero_matricula']),
                    sanitize_input($data['iban']),
                    sanitize_input($data['ciudad']),
                    $data['licencia'],
                    $user_id
                ]);
                break;
        }
        
        $conn->commit();
        return success_message('Usuario actualizado correctamente');
        
    } catch (Exception $e) {
        $conn->rollback();
        return error_message('Error al actualizar el usuario: ' . $e->getMessage());
    }
}

function deleteUser($conn, $user_id) {
    try {
        $query = "UPDATE usuarios SET activo = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$user_id]);
        
        return success_message('Usuario desactivado correctamente');
    } catch (Exception $e) {
        return error_message('Error al desactivar el usuario');
    }
}

function resetPassword($conn, $user_id) {
    try {
        $password_temporal = generate_password();
        $password_hash = password_hash($password_temporal, PASSWORD_DEFAULT);
        
        $query = "UPDATE usuarios SET password = ?, password_temporal = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$password_hash, $user_id]);
        
        return success_message("Contraseña reseteada correctamente. Nueva contraseña temporal: <strong>$password_temporal</strong>");
        
    } catch (Exception $e) {
        return error_message('Error al resetear la contraseña: ' . $e->getMessage());
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
    <title>Gestión de Usuarios - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/search-bar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-volleyball-ball"></i>
                    <span>FEDEXVB - Administrador</span>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
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
            <li><a href="usuarios.php" class="active"><i class="fas fa-users"></i> Gestión de Usuarios</a></li>
            <li><a href="partidos.php"><i class="fas fa-calendar-alt"></i> Gestión de Partidos</a></li>
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
            <h1><i class="fas fa-users"></i> Gestión de Usuarios</h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> <a href="dashboard.php">Inicio</a> / Gestión de Usuarios
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Botón crear usuario -->
        <div class="mb-3">
            <button onclick="openModal('createUserModal')" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Crear Usuario
            </button>
        </div>

        <!-- Lista de usuarios -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Lista de Usuarios
                <span class="badge" style="background: var(--primary-green); color: white; margin-left: 10px;">
                    <span id="total-count"><?php echo count($usuarios); ?></span> usuarios
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
                                   placeholder="Buscar por tipo, nombre, email o estado..." 
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
                        <span id="searchResults">0</span> usuario(s) encontrado(s)
                    </div>
                    <div class="search-help">
                        <i class="fas fa-lightbulb"></i> 
                        <em>Busca por tipo de usuario, nombre, email o estado. Usa ESC para limpiar.</em>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table data-table searchable-table" id="usersTable">
                        <thead>
                            <tr>
                                <th data-sortable>Tipo</th>
                                <th data-sortable>Nombre</th>
                                <th data-sortable>Email</th>
                                <th>Estado</th>
                                <th>Fecha Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td>
                                    <span class="badge" style="background: 
                                        <?php 
                                        echo $usuario['tipo_usuario'] == 'administrador' ? 'var(--error)' : 
                                             ($usuario['tipo_usuario'] == 'arbitro' ? 'var(--warning)' : 'var(--info)'); 
                                        ?>">
                                        <i class="fas <?php echo get_user_avatar($usuario['tipo_usuario']); ?>"></i>
                                        <?php echo ucfirst($usuario['tipo_usuario']); ?>
                                    </span>
                                </td>
                                <td><?php echo $usuario['nombre_completo']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td>
                                    <?php if ($usuario['password_temporal']): ?>
                                        <span class="badge" style="background: var(--warning);">
                                            <i class="fas fa-exclamation-triangle"></i> Temporal
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: var(--success);">
                                            <i class="fas fa-check"></i> Activo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo format_date($usuario['fecha_creacion']); ?></td>
                                <td>
                                    <button onclick="editUser(<?php echo $usuario['id']; ?>)" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="resetPassword(<?php echo $usuario['id']; ?>)" class="btn btn-warning btn-sm">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <button onclick="deleteUser(<?php echo $usuario['id']; ?>)" class="btn btn-danger btn-sm btn-delete" 
                                            data-message="¿Está seguro de desactivar este usuario?">
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

    <!-- Modal Crear Usuario -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</h2>
                <span class="close" onclick="closeModal('createUserModal')">&times;</span>
            </div>
            <form method="POST" class="validate-form">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label class="form-label">Tipo de Usuario</label>
                        <select name="tipo_usuario" class="form-control" required onchange="showUserFields(this.value)">
                            <option value="">Seleccione un tipo</option>
                            <option value="administrador">Administrador</option>
                            <option value="arbitro">Árbitro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
                        <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario único" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="example@example.com" required>
                    </div>

                    <!-- Campos Administrador -->
                    <div id="admin-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control">
                        </div>
                    </div>

                    <!-- Campos Árbitro -->
                    <div id="arbitro-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control" placeholder="Juan" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" class="form-control" placeholder="Fernández" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni" class="form-control" placeholder="12345678A">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de Teléfono</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="Ej: 653266205">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de Matrícula</label>
                            <input type="text" name="numero_matricula" class="form-control" placeholder="Ej: 1990 JJV">
                        </div>
                        <div class="form-group">
                            <label class="form-label">IBAN Bancario</label>
                            <input type="text" name="iban" class="form-control" placeholder="ES...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" placeholder="Cáceres, Badajoz, Mérida, ...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createUserModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Editar Usuario</h2>
                <span class="close" onclick="closeModal('editUserModal')">&times;</span>
            </div>
            <form method="POST" class="validate-form" id="editUserForm">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <input type="hidden" name="tipo_usuario" id="edit_tipo_usuario">
                    
                    <div class="form-group">
                        <label class="form-label">Tipo de Usuario</label>
                        <input type="text" id="edit_tipo_display" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre de usuario</label>
                        <input type="text" name="usuario" id="edit_usuario" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>

                    <!-- Campos comunes unificados -->
                    <div class="form-group">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                        <input type="text" name="apellidos" id="edit_apellidos" class="form-control" required>
                    </div>
                    <!-- Solo para árbitros -->
                    <div id="edit-arbitro-fields" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">DNI</label>
                            <input type="text" name="dni" id="edit_arbitro_dni" class="form-control" placeholder="12345678A">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de Teléfono</label>
                            <input type="tel" name="telefono" id="edit_arbitro_telefono" class="form-control" placeholder="Ej: 653266205">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Número de Matrícula</label>
                            <input type="text" name="numero_matricula" id="edit_arbitro_numero_matricula" class="form-control" placeholder="Ej: 1990 JJV">
                        </div>
                        <div class="form-group">
                            <label class="form-label">IBAN Bancario</label>
                            <input type="text" name="iban" id="edit_arbitro_iban" class="form-control" placeholder="ES...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" id="edit_arbitro_ciudad" class="form-control" placeholder="Ciudad de residencia">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Licencia de Árbitro</label>
                            <select name="licencia" id="edit_arbitro_licencia" class="form-control">
                                <option value="">Seleccione licencia</option>
                                <option value="colaborador">Colaborador/a</option>
                                <option value="anotador">Anotador</option>
                                <option value="habilitado_n1">Habilitado Nivel I</option>
                                <option value="habilitado_n2">Habilitado Nivel II</option>
                                <option value="habilitado_n3">Habilitado Nivel III</option>
                                <option value="n1">Nivel I (N1)</option>
                                <option value="n2">Nivel II (N2)</option>
                                <option value="n3c">Nivel III C (N3 C)</option>
                                <option value="n3b">Nivel III B (N3 B)</option>
                                <option value="n3a">Nivel III A (N3 A)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/app.js"></script>
    <script src="../assets/js/search-bar.js"></script>
    <script>
        // Inicializar búsqueda para la tabla de usuarios
        document.addEventListener('DOMContentLoaded', function() {
            new TableSearchBar({
                searchInputId: 'searchInput',
                clearBtnId: 'searchClear',
                searchInfoId: 'searchInfo',
                searchResultsId: 'searchResults',
                totalCountId: 'total-count',
                tableSelector: 'tbody tr',
                columnsCount: 6,
                noResultsId: 'noResultsRow'
            });
        });
        
        function showUserFields(tipo) {
            // Ocultar todos los campos y quitar 'required'
            document.getElementById('admin-fields').style.display = 'none';
            document.getElementById('arbitro-fields').style.display = 'none';
            const adminInputs = document.querySelectorAll('#admin-fields input');
            const arbitroInputs = document.querySelectorAll('#arbitro-fields input, #arbitro-fields select');
            adminInputs.forEach(input => input.removeAttribute('required'));
            arbitroInputs.forEach(input => input.removeAttribute('required'));

            // Mostrar campos relevantes y añadir 'required' solo a los visibles
            if (tipo === 'arbitro') {
                document.getElementById('arbitro-fields').style.display = 'block';
                const requiredFields = ['nombre', 'apellidos'];
                requiredFields.forEach(field => {
                    const input = document.querySelector(`#arbitro-fields input[name="${field}"]`);
                    if (input) input.setAttribute('required', 'required');
                });
            } else if (tipo === 'administrador') {
                document.getElementById('admin-fields').style.display = 'block';
                const requiredFields = ['nombre', 'apellidos'];
                requiredFields.forEach(field => {
                    const input = document.querySelector(`#admin-fields input[name="${field}"]`);
                    if (input) input.setAttribute('required', 'required');
                });
            }
        }
        
        function updateRequiredFields(tipo) {
            // Limpiar todos los campos requeridos
            const allInputs = document.querySelectorAll('#createUserModal input, #createUserModal select');
            allInputs.forEach(input => {
                if (input.name !== 'email' && input.name !== 'tipo_usuario') {
                    input.removeAttribute('required');
                }
            });
            
            // Agregar required según el tipo
            if (tipo === 'arbitro') {
                const requiredFields = ['nombre', 'apellidos'];
                requiredFields.forEach(field => {
                    const input = document.querySelector(`#arbitro-fields input[name="${field}"]`);
                    if (input) input.setAttribute('required', 'required');
                });
            } else if (tipo === 'administrador') {
                const requiredFields = ['nombre', 'apellidos'];
                requiredFields.forEach(field => {
                    const input = document.querySelector(`#admin-fields input[name="${field}"]`);
                    if (input) input.setAttribute('required', 'required');
                });
            }
        }

        function editUser(userId) {
            // Obtener datos del usuario via AJAX
            fetch(`../admin/api/usuarios.php?action=get_user&id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const user = data.user;
                        
                        // Llenar campos básicos
                        document.getElementById('edit_user_id').value = user.id;
                        document.getElementById('edit_tipo_usuario').value = user.tipo_usuario;
                        document.getElementById('edit_tipo_display').value = user.tipo_usuario.charAt(0).toUpperCase() + user.tipo_usuario.slice(1);
                        document.getElementById('edit_usuario').value = user.usuario || '';
                        document.getElementById('edit_email').value = user.email;
                        
                        // Ocultar campos específicos de árbitro y quitar 'required'
                        document.getElementById('edit-arbitro-fields').style.display = 'none';
                        const arbitroInputs = document.querySelectorAll('#edit-arbitro-fields input, #edit-arbitro-fields select');
                        arbitroInputs.forEach(input => input.removeAttribute('required'));
                        // Rellenar campos comunes
                        document.getElementById('edit_nombre').value = user.nombre || '';
                        document.getElementById('edit_apellidos').value = user.apellidos || '';
                        // Mostrar y rellenar campos de árbitro si corresponde
                        if (user.tipo_usuario === 'arbitro') {
                            document.getElementById('edit-arbitro-fields').style.display = 'block';
                            document.getElementById('edit_arbitro_dni').value = user.dni || '';
                            document.getElementById('edit_arbitro_telefono').value = user.telefono || '';
                            document.getElementById('edit_arbitro_numero_matricula').value = user.numero_matricula || '';
                            document.getElementById('edit_arbitro_iban').value = user.iban || '';
                            document.getElementById('edit_arbitro_ciudad').value = user.ciudad || '';
                            document.getElementById('edit_arbitro_licencia').value = user.licencia || '';
                            // Los campos de árbitro son opcionales, no añadimos 'required'
                        }
                        
                        openModal('editUserModal');
                    } else {
                        showNotification('Error al cargar los datos del usuario', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al conectar con el servidor', 'error');
                });
        }

        function resetPassword(userId) {
            if (confirm('¿Está seguro de resetear la contraseña de este usuario?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteUser(userId) {
            if (confirm('¿Está seguro de desactivar este usuario?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
