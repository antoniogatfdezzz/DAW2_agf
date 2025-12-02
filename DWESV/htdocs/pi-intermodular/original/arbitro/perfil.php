<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireUserType('arbitro');

$database = new Database();
$conn = $database->getConnection();

// Obtener información del árbitro
$query = "SELECT a.*, u.email, u.password_temporal 
          FROM arbitros a 
          JOIN usuarios u ON a.usuario_id = u.id 
          WHERE a.usuario_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$arbitro = $stmt->fetch(PDO::FETCH_ASSOC);

// Ejecutar limpieza automática de licencias vencidas
desactivar_licencias_vencidas($conn);


// Obtener licencias del árbitro
$query = "SELECT * FROM licencias_arbitros 
          WHERE arbitro_id = ? 
          ORDER BY fecha_expedicion DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$arbitro['id']]);
$licencias_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Adaptar los datos para evitar warnings por campos que no existen
$licencias = array_map(function($lic) {
    // Añadir campos faltantes con valores por defecto
    $lic['activa'] = $lic['activa'] ?? 1; // Por compatibilidad, todas activas
    $lic['fecha_vencimiento'] = $lic['fecha_vencimiento'] ?? null;
    $lic['fecha_curso'] = $lic['fecha_curso'] ?? $lic['fecha_expedicion'] ?? null;
    $lic['lugar_curso'] = $lic['lugar_curso'] ?? '';
    $lic['fecha_inicio'] = $lic['fecha_inicio'] ?? $lic['fecha_expedicion'] ?? null;
    $lic['observaciones'] = $lic['observaciones'] ?? '';
    return $lic;
}, $licencias_raw);

$message = '';

// Procesar formulario de actualización de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $conn->beginTransaction();
        
        if ($_POST['action'] === 'actualizar_datos') {
            $nombre = sanitize_input($_POST['nombre']);
            $apellidos = sanitize_input($_POST['apellidos']);
            $dni = sanitize_input($_POST['dni'] ?? '');
            $ciudad = sanitize_input($_POST['ciudad']);
            $telefono = sanitize_input($_POST['telefono'] ?? '');
            $iban = sanitize_input($_POST['iban']);
            $email = sanitize_input($_POST['email']);
            $numero_matricula = sanitize_input($_POST['matricula']);
            // Validar DNI
            if (empty($dni)) {
                throw new Exception('El campo DNI es obligatorio');
            }
            if (!preg_match('/^[0-9]{8}[A-Za-z]$/', $dni)) {
                throw new Exception('El formato del DNI no es válido');
            }


            // Validaciones
            if (empty($nombre) || empty($apellidos) || empty($ciudad)) {
                throw new Exception('Los campos nombre, apellidos y ciudad son obligatorios');
            }

            if (!validate_email($email)) {
                throw new Exception('Email no válido');
            }

            // Verificar si el email ya existe (excepto el actual)
            $query = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                throw new Exception('Este email ya está registrado por otro usuario');
            }

            // Validar IBAN si se proporciona
            if (!empty($iban)) {
                $iban = strtoupper(str_replace(' ', '', $iban));
                if (strlen($iban) < 15 || strlen($iban) > 34) {
                    throw new Exception('IBAN no válido');
                }
            }

            // Validar matrícula si se proporciona
            if (!empty($numero_matricula) && strlen($numero_matricula) > 20) {
                throw new Exception('La matrícula no puede superar los 20 caracteres');
            }

            // Validar teléfono si se proporciona
            if (!empty($telefono) && !preg_match('/^[0-9\s\+\-\(\)]{9,15}$/', $telefono)) {
                throw new Exception('El formato del teléfono no es válido');
            }

            // Actualizar datos del árbitro (incluye número_matricula, dni y telefono)
            $query = "UPDATE arbitros SET nombre = ?, apellidos = ?, dni = ?, ciudad = ?, telefono = ?, iban = ?, numero_matricula = ? WHERE usuario_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$nombre, $apellidos, $dni, $ciudad, $telefono, $iban, $numero_matricula, $_SESSION['user_id']]);

            // Actualizar email del usuario
            $query = "UPDATE usuarios SET email = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$email, $_SESSION['user_id']]);

            // Actualizar variables de sesión
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_lastname'] = $apellidos;

            $conn->commit();
            $message = success_message('Datos actualizados correctamente');

            // Recargar datos del árbitro
            $query = "SELECT a.*, u.email, u.password_temporal 
                      FROM arbitros a 
                      JOIN usuarios u ON a.usuario_id = u.id 
                      WHERE a.usuario_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
            $arbitro = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $message = error_message($e->getMessage());
    }
}

// Función para determinar el estado de una licencia
function getLicenciaEstado($fecha_vencimiento, $activa) {
    if (!$activa) {
        return ['estado' => 'Inactiva', 'clase' => 'danger'];
    }
    
    // Evitar pasar null a DateTime (PHP 8.1+ depreca esto)
    if (empty($fecha_vencimiento)) {
        // Puedes ajustar la fecha por defecto según la lógica de tu app
        $fecha_venc = new DateTime('2225-11-25');
    } else {
        $fecha_venc = new DateTime($fecha_vencimiento);
    }
    $fecha_actual = new DateTime();
    $fecha_preaviso = clone $fecha_actual;
    $fecha_preaviso->add(new DateInterval('P30D')); // 30 días
    
    // Evaluamos el estado basado en la fecha, pero NO cambiamos automáticamente el estado activo
    if ($fecha_venc < $fecha_actual) {
        return ['estado' => 'Vencida', 'clase' => 'danger'];
    } elseif ($fecha_venc <= $fecha_preaviso) {
        return ['estado' => 'Próxima a vencer', 'clase' => 'warning'];
    } else {
        return ['estado' => 'Vigente', 'clase' => 'success'];
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
    <title>Mi Perfil - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
                    <span>FEDEXVB - Árbitro</span>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <div class="user-name"><?php echo $_SESSION['user_name'] . ' ' . $_SESSION['user_lastname']; ?></div>
                        <div class="user-role">Árbitro</div>
                    </div>
                    <div class="user-actions">
                        <a href="../cambiar-password.php" class="btn btn-secondary btn-sm" title="Cambiar Contraseña">
                            <i class="fas fa-key"></i>
                        </a>
                        <a href="../includes/logout.php" class="btn btn-secondary btn-sm" title="Cerrar Sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="disponibilidad.php"><i class="fas fa-calendar-check"></i> Mi Disponibilidad</a></li>
            <li><a href="partidos.php"><i class="fa-solid fa-globe"></i> Mis Partidos</a></li>
            <li><a href="liquidaciones.php"><i class="fas fa-file-invoice-dollar"></i> Mis Liquidaciones</a></li>
            <li><a href="arbitros.php"><i class="fas fa-users"></i> Lista de Árbitros</a></li>
            <li><a href="perfil.php" class="active"><i class="fas fa-user-cog"></i> Mi Perfil</a></li>
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
            <h1><i class="fas fa-user-cog"></i> Mi Perfil</h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> Inicio / Mi Perfil
            </div>
        </div>

        <!-- Información adicional -->
        <div class="card">
            <div class="card-header" style="background: var(--info);">
                <i class="fas fa-info-circle"></i> Información Importante
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h5><i class="fas fa-lightbulb"></i> Recordatorios:</h5>
                    <ul class="mb-0">
                        <li>Mantén actualizados tus datos personales para recibir comunicaciones</li>
                        <li>Es obligatorio tener configurado el IBAN para recibir liquidaciones</li>
                        <li>Revisa regularmente el estado de tus licencias</li>
                        <li>Si tu contraseña es temporal, cámbiala cuanto antes por seguridad</li>
                        <li>Para cualquier cambio en las licencias, contacta con la administración</li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <div style="width: 100%;">
            <!-- Información Personal -->
            <div class="card" style="width: 100%;">
                <div class="card-header">
                    <i class="fas fa-user"></i> Información Personal
                </div>
                <div class="card-body">
                    <form method="POST" id="formPerfil">
                        <input type="hidden" name="action" value="actualizar_datos">
                        
                        <div class="form-group">
                            <label for="nombre">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo htmlspecialchars($arbitro['nombre']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" 
                                   value="<?php echo htmlspecialchars($arbitro['apellidos']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($arbitro['email']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="ciudad">Ciudad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad" 
                                   value="<?php echo htmlspecialchars($arbitro['ciudad']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="dni">DNI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="dni" name="dni" 
                                   value="<?php echo htmlspecialchars($arbitro['dni'] ?? ''); ?>" maxlength="9" placeholder="Ej: 12345678A" required>
                            <small class="form-text text-muted">
                                Introduce tu DNI (formato: 8 números y una letra)
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo htmlspecialchars($arbitro['telefono'] ?? ''); ?>" 
                                   placeholder="Ej: 600123456 o +34 600123456" maxlength="15">
                            <small class="form-text text-muted">
                                Introduce tu número de teléfono (móvil o fijo)
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="matricula">Matrícula</label>
                            <input type="text" class="form-control" id="matricula" name="matricula" 
                                   value="<?php echo htmlspecialchars($arbitro['numero_matricula'] ?? ''); ?>" maxlength="9" placeholder="Ej: 5555 JJJ">
                            <small class="form-text text-muted">
                                Puedes modificar tu matrícula. Máx. 9 caracteres.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="iban">IBAN (para liquidaciones)</label>
                            <input type="text" class="form-control" id="iban" name="iban" 
                                   value="<?php echo htmlspecialchars($arbitro['iban'] ?? ''); ?>" 
                                   placeholder="ES00 0000 0000 0000 0000 0000">
                            <small class="form-text text-muted">
                                Necesario para recibir las liquidaciones de arbitraje
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Licencia Principal</label>
                            <input type="text" class="form-control" 
                                   value="<?php echo formatLicenciaNivel($arbitro['licencia']); ?>" 
                                   readonly>
                            <small class="form-text text-muted">
                                Contacta con la administración para cambios en la licencia
                            </small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="../cambiar-password.php" class="btn btn-secondary">
                                <i class="fas fa-key"></i> Cambiar Contraseña
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            
        </div>

        <!-- Licencias -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-certificate"></i> Mis Licencias
                <div class="card-actions">
                    <span class="badge" style="background: var(--info);">
                        <?php echo count($licencias); ?> Total
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($licencias) > 0): ?>
                    <!-- Mostrar licencias activas destacadas -->
                    <?php 
                    // Obtener licencias activas (independientemente de si están vencidas o no)
                    $licencias_activas_sistema = array_filter($licencias, function($lic) {
                        return $lic['activa'] == 1;
                    });
                    
                    // Separar en vigentes y vencidas pero activas
                    $licencias_vigentes = array_filter($licencias_activas_sistema, function($lic) {
                        $estado = getLicenciaEstado($lic['fecha_vencimiento'], $lic['activa']);
                        return $estado['estado'] === 'Vigente' || $estado['estado'] === 'Próxima a vencer';
                    });
                    
                    $licencias_vencidas_activas = array_filter($licencias_activas_sistema, function($lic) {
                        $estado = getLicenciaEstado($lic['fecha_vencimiento'], $lic['activa']);
                        return $estado['estado'] === 'Vencida';
                    });
                    ?>
                    
                    <?php if (count($licencias_vigentes) > 0): ?>
                        <div class="alert alert-success" style="margin-bottom: 20px;">
                            <h5><i class="fas fa-certificate"></i> Licencias Vigentes Activas</h5>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                                <?php foreach ($licencias_vigentes as $licencia): ?>
                                    <?php $estado = getLicenciaEstado($licencia['fecha_vencimiento'], $licencia['activa']); ?>
                                    <div style="background: rgba(255,255,255,0.9); padding: 15px; border-radius: 8px; border-left: 4px solid var(--success);">
                                        <div style="font-weight: bold; font-size: 1.1rem; color: var(--success);">
                                            <?php echo formatLicenciaNivel($licencia['nivel_licencia']); ?>
                                        </div>
                                        <div style="font-size: 0.9rem; margin-top: 5px;">
                                            <strong>Vence:</strong> <?php echo format_date($licencia['fecha_vencimiento']); ?>
                                        </div>
                                        <div style="margin-top: 8px;">
                                            <span class="badge badge-<?php echo $estado['clase']; ?>" style="font-size: 0.8rem;">
                                                <?php echo $estado['estado']; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Bloque de licencias vencidas eliminado: ya no aplica para el modelo actual sin fecha de vencimiento -->

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nivel</th>
                                    <th>Fecha de Expedición</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($licencias as $licencia): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo formatLicenciaNivel($licencia['nivel_licencia']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo format_date($licencia['fecha_expedicion']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bloque de estadísticas de licencias eliminado, solo se muestra el listado -->

                <?php else: ?>
                    <div class="text-center p-4">
                        <i class="fas fa-certificate" style="font-size: 3rem; color: var(--medium-gray);"></i>
                        <h4 class="mt-3">Sin licencias registradas</h4>
                        <p class="text-muted">
                            No tienes licencias registradas en el sistema. Contacta con la administración 
                            si necesitas que se añadan tus licencias.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
    </main>

    <script src="../assets/js/app.js"></script>
    
    <script>
        // Formatear IBAN automáticamente
        document.getElementById('iban').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').toUpperCase();
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Validación del formulario
        document.getElementById('formPerfil').addEventListener('submit', function(e) {
            const iban = document.getElementById('iban').value.replace(/\s/g, '');
            
            if (iban && (iban.length < 15 || iban.length > 34)) {
                e.preventDefault();
                alert('El IBAN debe tener entre 15 y 34 caracteres');
                return false;
            }
        });

        // Mostrar notificación si hay contraseña temporal
        <?php if ($arbitro['password_temporal']): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-warning alert-dismissible';
            alert.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Atención:</strong> Tu contraseña es temporal. Te recomendamos cambiarla por una personalizada.
                <a href="../cambiar-password.php" class="btn btn-warning btn-sm ml-2">
                    <i class="fas fa-key"></i> Cambiar Ahora
                </a>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            
            const mainContent = document.querySelector('.main-content');
            const contentHeader = mainContent.querySelector('.content-header');
            contentHeader.parentNode.insertBefore(alert, contentHeader.nextSibling);
        });
        <?php endif; ?>
    </script>

    <style>
        .status-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-icon {
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .status-content h5 {
            margin: 0 0 5px 0;
            color: var(--primary-black);
        }

        .status-content p {
            margin: 0;
            font-weight: 500;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .stat-card {
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .badge-success {
            background-color: var(--success);
        }

        .badge-warning {
            background-color: var(--warning);
        }

        .badge-danger {
            background-color: var(--danger);
        }

        .alert-dismissible .close {
            position: absolute;
            top: 0;
            right: 0;
            padding: .75rem 1.25rem;
            color: inherit;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .main-content > div:first-of-type {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
