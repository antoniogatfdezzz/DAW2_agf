<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

$auth = new Auth();
$auth->requireUserType('arbitro');

$database = new Database();
$conn = $database->getConnection();
$message = '';

// Obtener ID del árbitro
$query = "SELECT id FROM arbitros WHERE usuario_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$arbitro_id = $stmt->fetchColumn();

if (!$arbitro_id) {
    die('Error: No se encontró el árbitro asociado a este usuario. Contacte al administrador.');
}

// Procesar cambio de disponibilidad
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fecha'])) {
    $fecha = $_POST['fecha'];
    $manana = isset($_POST['manana']) ? 1 : 0;
    $tarde = isset($_POST['tarde']) ? 1 : 0;
    $observacion_manana = isset($_POST['observacion_manana']) ? sanitize_input($_POST['observacion_manana']) : null;
    $observacion_tarde = isset($_POST['observacion_tarde']) ? sanitize_input($_POST['observacion_tarde']) : null;

    // Validar que solo se pueda dar disponibilidad los lunes hasta las 23:00
    $fechaSeleccionada = new DateTime($fecha);
    $ahora = new DateTime();
    $hoy = new DateTime($ahora->format('Y-m-d'));
    $proximoLunes = clone $hoy;
    $diaSemanaActual = (int)$ahora->format('N');
    if ($diaSemanaActual == 1) {
        $horaActual = (int)$ahora->format('H');
        if ($horaActual >= 23) {
            $proximoLunes->add(new DateInterval('P7D'));
        }
    } else {
        $diasHastaLunes = (8 - $diaSemanaActual) % 7;
        if ($diasHastaLunes == 0) $diasHastaLunes = 7;
        $proximoLunes->add(new DateInterval('P' . $diasHastaLunes . 'D'));
    }
    if ($fechaSeleccionada < $proximoLunes) {
        echo json_encode([
            'success' => false,
            'message' => 'Solo puedes gestionar disponibilidad los lunes hasta las 23:00. Próxima fecha disponible: ' . $proximoLunes->format('d/m/Y')
        ]);
        exit();
    }

    error_log("=== DEBUG DISPONIBILIDAD ===");
    error_log("POST datos recibidos:");
    error_log("- fecha: $fecha");
    error_log("- mañana: $manana");
    error_log("- tarde: $tarde");
    error_log("- observacion_manana: $observacion_manana");
    error_log("- observacion_tarde: $observacion_tarde");
    error_log("- arbitro_id: $arbitro_id");
    error_log("- Fecha de hoy: " . date('Y-m-d H:i:s'));
    error_log("- Próximo lunes disponible: " . $proximoLunes->format('Y-m-d'));
    error_log("- Día de la semana actual: " . $diaSemanaActual);
    error_log("- Hora actual: " . $ahora->format('H:i'));
    error_log("=============================");

    try {
        $query = "INSERT INTO disponibilidad_arbitros (arbitro_id, fecha, manana, observacion_manana, tarde, observacion_tarde)
                  VALUES (?, ?, ?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE manana = VALUES(manana), observacion_manana = VALUES(observacion_manana), tarde = VALUES(tarde), observacion_tarde = VALUES(observacion_tarde)";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$arbitro_id, $fecha, $manana, $observacion_manana, $tarde, $observacion_tarde]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Disponibilidad actualizada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar disponibilidad']);
        }
        exit();
    } catch (Exception $e) {
        error_log("Error en disponibilidad: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}

// Obtener disponibilidad actual del mes
$currentMonth = $_GET['month'] ?? date('Y-m');
$firstDay = $currentMonth . '-01';
$lastDay = date('Y-m-t', strtotime($firstDay));



$query = "SELECT fecha, manana, observacion_manana, tarde, observacion_tarde FROM disponibilidad_arbitros 
          WHERE arbitro_id = ? AND fecha BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->execute([$arbitro_id, $firstDay, $lastDay]);
$disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Mi Disponibilidad - FEDEXVB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .availability-calendar {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            margin: 20px 0;
        }
        
        .calendar-header {
            background: linear-gradient(135deg, var(--primary-green), var(--light-green));
            color: white;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .calendar-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .calendar-navigation {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .calendar-navigation .btn {
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .calendar-navigation .btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-1px);
        }
        
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            border-top: 1px solid #e0e0e0;
        }
        
        #calendar-days {
            display: contents;
        }
        
        .calendar-day-header {
            background: var(--primary-green);
            color: white;
            padding: 18px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        
        .calendar-day-header:last-child {
            border-right: none;
        }
        
        .calendar-day {
            min-height: 90px;
            border-right: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
            position: relative;
            background: #fff;
        }
        
        .calendar-day:nth-child(7n) {
            border-right: none;
        }
        
        .calendar-day:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1;
        }
        
        .calendar-day.other-month {
            color: #bbb;
            background: #fafafa;
            cursor: not-allowed;
        }
        
        .calendar-day.other-month:hover {
            transform: none;
            box-shadow: none;
            background: #fafafa;
        }
        
        .calendar-day.disabled {
            color: #bbb;
            background: #f5f5f5;
            cursor: not-allowed;
            position: relative;
        }
        
        .calendar-day.disabled:hover {
            transform: none;
            box-shadow: none;
            background: #f5f5f5;
        }
        
        .calendar-day.disabled::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
            pointer-events: none;
        }
        
        .calendar-day.available {
            background: linear-gradient(135deg, #e8f5e8, #f1f8e9);
            color: var(--dark-green);
            border-left: 4px solid var(--light-green);
        }
        
        .calendar-day.available:hover {
            background: linear-gradient(135deg, #dcedc8, #e8f5e8);
            transform: translateY(-2px);
        }
        
        .calendar-day.unavailable {
            background: linear-gradient(135deg, #ffebee, #fce4ec);
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        
        .calendar-day.unavailable:hover {
            background: linear-gradient(135deg, #ffcdd2, #ffebee);
            transform: translateY(-2px);
        }
        
        .calendar-day.today {
            border: 2px solid var(--primary-green);
            font-weight: bold;
        }
        
        .day-number {
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            background: rgba(255,255,255,0.95);
            border-radius: 50%;
            width: 2.2em;
            height: 2.2em;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .availability-icon {
            width: 100%;
            display: flex;
            flex-direction: row;
            height: 100%;
            min-height: 60px;
        }
        .half-m, .half-t {
            flex: 1 1 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            position: relative;
            font-size: 1.1em;
            font-weight: bold;
            background: #fff;
            color: #888;
            transition: background 0.2s, color 0.2s;
        }
        .half-m {
            border-radius: 8px 0 0 8px;
            border-right: 1px solid #e0e0e0;
        }
        .half-t {
            border-radius: 0 8px 8px 0;
            border-left: 1px solid #e0e0e0;
        }
        .half-m.disponible, .half-t.disponible {
            background: #4caf50;
            color: #fff;
        }
        .half-m.nodisponible, .half-t.nodisponible {
            background: #f44336;
            color: #fff;
        }
        
        .calendar-day.available .availability-icon {
            color: var(--light-green);
        }
        
        .calendar-day.unavailable .availability-icon {
            color: #f44336;
        }
        
        /* Estilo para el icono de observaciones */
        .calendar-day .fas.fa-edit {
            color: var(--primary-green);
            opacity: 0.8;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .legend {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 25px;
            background: linear-gradient(to right, #f8f9fa, #e9ecef, #f8f9fa);
            border-top: 1px solid #e0e0e0;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
            .legend-color {
                width: 24px;
                height: 24px;
                border-radius: 6px;
                border: 2px solid rgba(0,0,0,0.1);
            }
            
            /* Estilos para modal de observaciones */
            .modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .modal-content {
                background: white;
                border-radius: 8px;
                width: 90%;
                max-width: 500px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            }
            
            .modal-header {
                background: var(--primary-green);
                color: white;
                padding: 20px;
                border-radius: 8px 8px 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .modal-header h3 {
                margin: 0;
                font-size: 1.2rem;
            }
            
            .modal-header .close {
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 5px;
                border-radius: 4px;
                transition: background 0.2s;
            }
            
            .modal-header .close:hover {
                background: rgba(255,255,255,0.2);
            }
            
            .modal-body {
                padding: 20px;
            }
            
            .modal-body p {
                margin-bottom: 15px;
                color: #666;
            }
            
            .modal-body textarea {
                width: 100%;
                border: 2px solid #e0e0e0;
                border-radius: 6px;
                padding: 12px;
                font-size: 14px;
                line-height: 1.4;
                resize: vertical;
                min-height: 100px;
            }
            
            .modal-body textarea:focus {
                outline: none;
                border-color: var(--primary-green);
                box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
            }
            
            .modal-footer {
                padding: 15px 20px;
                border-top: 1px solid #e0e0e0;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
            }
            
            .form-control {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            
            /* Estilos para modal de advertencia */
            .alert {
                border-radius: 6px;
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .alert-warning {
                background-color: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
            }
            
            .alert-warning ul {
                margin: 10px 0;
                padding-left: 20px;
            }
            
            .alert-warning a {
                color: var(--primary-green);
                text-decoration: none;
                font-weight: 600;
            }
            
            .alert-warning a:hover {
                text-decoration: underline;
            }        /* Responsive */
        @media (max-width: 768px) {
            .calendar-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .calendar-navigation {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .calendar-day {
                min-height: 70px;
                padding: 8px 4px;
            }
            
            .day-number {
                font-size: 0.8rem;
                width: 1.6em;
                height: 1.6em;
            }
            
            .availability-icon {
                font-size: 1.3rem;
            }
            
            .legend {
                flex-direction: column;
                gap: 15px;
                align-items: center;
            }
        }
        
        @media (max-width: 480px) {
            .calendar-day-header {
                padding: 12px 5px;
                font-size: 0.8rem;
            }
            
            .calendar-day {
                min-height: 60px;
            }
            
            .day-number {
                font-size: 0.8rem;
                width: 1.6em;
                height: 1.6em;
            }
            
            .availability-icon {
                font-size: 1.1rem;
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
            <li><a href="disponibilidad.php" class="active"><i class="fas fa-calendar-check"></i> Mi Disponibilidad</a></li>
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
            <h1><i class="fas fa-calendar-check"></i> Mi Disponibilidad</h1>
            <div class="breadcrumb">
                <i class="fas fa-home"></i> <a href="dashboard.php">Inicio</a> / Mi Disponibilidad
            </div>
        </div>

        <!--
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Instrucciones
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <p><strong>Cómo gestionar tu disponibilidad:</strong></p>
                    <ul class="mb-0">
                        <li>Haz clic en cualquier día del calendario para cambiar tu disponibilidad</li>
                        <li><span style="color: var(--success);"><i class="fas fa-check"></i> Verde</span> = Disponible para arbitrar</li>
                        <li><span style="color: var(--error);"><i class="fas fa-times"></i> Rojo</span> = No disponible</li>
                        <li>Por defecto, todos los días están marcados como no disponibles</li>
                        <li>Es importante mantener actualizada tu disponibilidad para recibir asignaciones</li>
                    </ul>
                </div>
            </div>
        </div>
        -->

        <div class="card">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle"></i> Importante - Horario de Gestión de Disponibilidad
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <p><strong><i class="fas fa-clock"></i> Restricción de horario:</strong></p>
                    <ul class="mb-0">
                        <li>Solo puedes gestionar tu disponibilidad <strong>los lunes hasta las 23:00</strong></li>
                        <li>Las fechas aparecen bloqueadas fuera de este horario y no se pueden modificar</li>
                        <li>Esta medida asegura que la federación tenga tiempo suficiente para organizar los partidos</li>
                        <li>Si necesitas hacer cambios urgentes fuera de horario, contacta directamente con la administración</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="availability-calendar">
            <div class="calendar-header">
                <h2 id="month-year"></h2>
                <div class="calendar-navigation">
                    <button onclick="previousMonth()" class="btn btn-secondary">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    <button onclick="currentMonth()" class="btn btn-secondary">
                        <i class="fas fa-calendar-day"></i> Hoy
                    </button>
                    <button onclick="nextMonth()" class="btn btn-secondary">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            
            <div class="calendar-grid">
                <!-- Headers de días -->
                <div class="calendar-day-header">Lun</div>
                <div class="calendar-day-header">Mar</div>
                <div class="calendar-day-header">Mié</div>
                <div class="calendar-day-header">Jue</div>
                <div class="calendar-day-header">Vie</div>
                <div class="calendar-day-header">Sáb</div>
                <div class="calendar-day-header">Dom</div>
                
                <!-- Días del calendario -->
                <div id="calendar-days"></div>
            </div>
            
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #e8f5e8, #f1f8e9); border-left: 4px solid var(--light-green);"></div>
                    <span><i class="fas fa-check" style="color: var(--light-green);"></i> Disponible</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #ffebee, #fce4ec); border-left: 4px solid #f44336;"></div>
                    <span><i class="fas fa-times" style="color: #f44336;"></i> No disponible</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: white; border: 2px solid #e0e0e0;"></div>
                    <span><i class="fas fa-question" style="color: #999;"></i> Sin definir (no disponible por defecto)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f5f5f5; border: 2px solid #bbb;"></div>
                    <span><i class="fas fa-lock" style="color: #bbb;"></i> No modificable (solo lunes hasta 23:00)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: white; border: 2px solid var(--primary-green);"></div>
                    <span><i class="fas fa-calendar-day" style="color: var(--primary-green);"></i> Día actual</span>
                </div>
            </div>
        </div>

        <!-- Acciones rápidas 
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-lightning-bolt"></i> Acciones Rápidas
            </div>
            <div class="card-body">
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button onclick="setAllAvailable()" class="btn btn-success">
                        <i class="fas fa-check-double"></i> Marcar todo como disponible
                    </button>
                    <button onclick="setWeekendsUnavailable()" class="btn btn-warning">
                        <i class="fas fa-calendar-minus"></i> Fines de semana no disponibles
                    </button>
                    <button onclick="setWeekdaysUnavailable()" class="btn btn-warning">
                        <i class="fas fa-calendar-times"></i> Entre semana no disponible
                    </button>
                    <button onclick="clearMonth()" class="btn btn-secondary">
                        <i class="fas fa-eraser"></i> Limpiar mes
                    </button>
                </div>
            </div>
        </div>-->
    </main>

    <script src="../assets/js/app.js"></script>
    <script>
        // Sincronizar fecha JavaScript con el mes cargado por PHP
        <?php 
        echo "let currentDate = new Date('$currentMonth-01');";
        ?>
        let disponibilidadData = {};
        
        // Convertir los datos PHP a formato JavaScript
        <?php 
        echo "disponibilidadData = {";
        foreach($disponibilidad as $disp) {
            echo "'{$disp['fecha']}': {manana: '{$disp['manana']}', observacion_manana: ".json_encode($disp['observacion_manana']).", tarde: '{$disp['tarde']}', observacion_tarde: ".json_encode($disp['observacion_tarde'])."},";
        }
        echo "};";
        ?>
        
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        // Función auxiliar para formatear fecha en formato YYYY-MM-DD sin problemas de zona horaria
        function formatDateString(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

    function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();
            
            // Encontrar el próximo lunes disponible para gestionar disponibilidad
            const now = new Date();
            let nextAvailableMonday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const currentDayOfWeek = now.getDay(); // 0=Domingo, 1=Lunes, etc.
            const currentHour = now.getHours();
            
            if (currentDayOfWeek === 1) { // Si hoy es lunes
                if (currentHour >= 23) {
                    // Si es lunes después de las 23:00, el próximo lunes disponible es el siguiente
                    nextAvailableMonday.setDate(nextAvailableMonday.getDate() + 7);
                }
                // Si es lunes antes de las 23:00, se puede usar el lunes actual
            } else {
                // Si no es lunes, calcular días hasta el próximo lunes
                let daysUntilMonday = (1 - currentDayOfWeek + 7) % 7;
                if (daysUntilMonday === 0) daysUntilMonday = 7;
                nextAvailableMonday.setDate(nextAvailableMonday.getDate() + daysUntilMonday);
            }
            
            document.getElementById('month-year').textContent = `${monthNames[month]} ${year}`;
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            
            // Ajustar para que la semana empiece en Lunes (1) en lugar de Domingo (0)
            let dayOfWeek = firstDay.getDay();
            dayOfWeek = dayOfWeek === 0 ? 6 : dayOfWeek - 1; // Convertir domingo (0) a 6, y el resto restar 1
            startDate.setDate(startDate.getDate() - dayOfWeek);
            
            const calendarDays = document.getElementById('calendar-days');
            calendarDays.innerHTML = '';
            
            const currentDateObj = new Date(startDate);
            
            for (let i = 0; i < 42; i++) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day';
                
                const dayNumber = currentDateObj.getDate();
                const isCurrentMonth = currentDateObj.getMonth() === month;
                const dateStr = formatDateString(currentDateObj);
                const isToday = currentDateObj.toDateString() === today.toDateString();
                const isDisabled = currentDateObj < nextAvailableMonday && isCurrentMonth;
                
                dayDiv.setAttribute('data-date', dateStr);
                
                if (!isCurrentMonth) {
                    dayDiv.classList.add('other-month');
                } else if (isDisabled) {
                    dayDiv.classList.add('disabled');
                }
                
                if (isToday) {
                    dayDiv.classList.add('today');
                }
                
                const disponibilidadInfo = disponibilidadData[dateStr] || {};
                const manana = disponibilidadInfo.manana === '1';
                const tarde = disponibilidadInfo.tarde === '1';
                const observacion_manana = disponibilidadInfo.observacion_manana || '';
                const observacion_tarde = disponibilidadInfo.observacion_tarde || '';

                let mClass = 'half-m';
                let tClass = 'half-t';
                if (disponibilidadInfo.manana === '1') {
                    mClass += ' disponible';
                } else if (disponibilidadInfo.manana === '0') {
                    mClass += ' nodisponible';
                }
                if (disponibilidadInfo.tarde === '1') {
                    tClass += ' disponible';
                } else if (disponibilidadInfo.tarde === '0') {
                    tClass += ' nodisponible';
                }
                let iconHtml = '';
                iconHtml += `<div class="${mClass}" onclick='event.stopPropagation();toggleFranja("${dateStr}","manana",this)'>M${manana && observacion_manana ? "<i class='fas fa-edit' style='margin-left:2px;font-size:0.8em;opacity:0.7;position:absolute;right:2px;top:2px;' title='Obs. mañana'></i>" : ""}</div>`;
                iconHtml += `<div class="${tClass}" onclick='event.stopPropagation();toggleFranja("${dateStr}","tarde",this)'>T${tarde && observacion_tarde ? "<i class='fas fa-edit' style='margin-left:2px;font-size:0.8em;opacity:0.7;position:absolute;right:2px;top:2px;' title='Obs. tarde'></i>" : ""}</div>`;

                dayDiv.innerHTML = `
                    <div class="day-number">${dayNumber}</div>
                    <div class="availability-icon">${iconHtml}</div>
                `;
                if (isCurrentMonth && !isDisabled) {
                    let obsText = '';
                    if (manana && observacion_manana) obsText += ' - Obs. mañana: ' + observacion_manana;
                    if (tarde && observacion_tarde) obsText += ' - Obs. tarde: ' + observacion_tarde;
                    dayDiv.title = `${dayNumber} de ${monthNames[month]} - Mañana: ${manana ? 'Sí' : 'No'} | Tarde: ${tarde ? 'Sí' : 'No'}${obsText}`;
                } else if (isDisabled && isCurrentMonth) {
                    dayDiv.title = `${dayNumber} de ${monthNames[month]} - No modificable (solo se puede gestionar disponibilidad los lunes hasta las 23:00)`;
                } else {
                    dayDiv.title = 'Día de otro mes';
                }
                
                calendarDays.appendChild(dayDiv);
                currentDateObj.setDate(currentDateObj.getDate() + 1);
            }
        }



        function toggleFranja(dateStr, franja, btn) {
            const dayDiv = btn.closest('.calendar-day');
            const selectedDate = new Date(dateStr + 'T12:00:00');
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            let nextAvailableMonday = new Date(today);
            const currentDayOfWeek = now.getDay();
            const currentHour = now.getHours();
            if (currentDayOfWeek === 1) {
                if (currentHour >= 23) {
                    nextAvailableMonday.setDate(nextAvailableMonday.getDate() + 7);
                }
            } else {
                let daysUntilMonday = (1 - currentDayOfWeek + 7) % 7;
                if (daysUntilMonday === 0) daysUntilMonday = 7;
                nextAvailableMonday.setDate(nextAvailableMonday.getDate() + daysUntilMonday);
            }
            if (selectedDate < nextAvailableMonday) {
                const nextMondayStr = nextAvailableMonday.toLocaleDateString('es-ES');
                showNotification(`Solo puedes gestionar disponibilidad los lunes hasta las 23:00. Próxima fecha disponible: ${nextMondayStr}`, 'error');
                return;
            }
            const info = disponibilidadData[dateStr] || {manana:'0',observacion_manana:'',tarde:'0',observacion_tarde:''};
            let manana = info.manana === '1';
            let tarde = info.tarde === '1';
            if (franja === 'manana') {
                if (!manana) {
                    showObservacionFranjaModal(dateStr, dayDiv, franja, info.observacion_manana || '');
                    return;
                } else {
                    manana = false;
                    updateAvailabilityFranjas(dateStr, dayDiv, manana, tarde, '', info.observacion_tarde || '');
                }
            } else if (franja === 'tarde') {
                if (!tarde) {
                    showObservacionFranjaModal(dateStr, dayDiv, franja, info.observacion_tarde || '');
                    return;
                } else {
                    tarde = false;
                    updateAvailabilityFranjas(dateStr, dayDiv, manana, tarde, info.observacion_manana || '', '');
                }
            }
        }

        function showObservacionFranjaModal(dateStr, dayDiv, franja, currentObs) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.style.display = 'block';
            modal.innerHTML = `
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3><i class="fas fa-edit"></i> Observación para ${franja === 'manana' ? 'mañana' : 'tarde'}<br>${formatDate(dateStr)}</h3>
                        <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <p>Puedes agregar una observación opcional para esta franja:</p>
                        <textarea id="obsFranjaText" class="form-control" rows="3" placeholder="Ejemplo: Solo disponible hasta las 12:00">${currentObs || ''}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancelar</button>
                        <button type="button" class="btn btn-success" onclick="saveObsFranja('${dateStr}','${franja}', this)"><i class="fas fa-check"></i> Guardar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            document.getElementById('obsFranjaText').focus();
        }

        function saveObsFranja(dateStr, franja, btn) {
            const obs = document.getElementById('obsFranjaText').value.trim();
            const dayDiv = btn.closest('.modal').parentNode.querySelector('.calendar-day') || document.querySelector(`.calendar-day[data-date='${dateStr}']`);
            const info = disponibilidadData[dateStr] || {manana:'0',observacion_manana:'',tarde:'0',observacion_tarde:''};
            let manana = info.manana === '1';
            let tarde = info.tarde === '1';
            if (franja === 'manana') manana = true;
            if (franja === 'tarde') tarde = true;
            let obsManana = franja === 'manana' ? obs : info.observacion_manana || '';
            let obsTarde = franja === 'tarde' ? obs : info.observacion_tarde || '';
            updateAvailabilityFranjas(dateStr, dayDiv, manana, tarde, obsManana, obsTarde);
            btn.closest('.modal').remove();
        }

        function updateAvailabilityFranjas(dateStr, dayDiv, manana, tarde, observacion_manana, observacion_tarde) {
            const formData = new FormData();
            formData.append('fecha', dateStr);
            if (manana) formData.append('manana', '1');
            if (tarde) formData.append('tarde', '1');
            // Siempre enviar ambos campos, aunque estén vacíos
            formData.append('observacion_manana', observacion_manana !== undefined ? observacion_manana : '');
            formData.append('observacion_tarde', observacion_tarde !== undefined ? observacion_tarde : '');
            const icon = dayDiv.querySelector('.availability-icon');
            if (icon) icon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    disponibilidadData[dateStr] = {
                        manana: manana ? '1' : '0',
                        observacion_manana: observacion_manana || '',
                        tarde: tarde ? '1' : '0',
                        observacion_tarde: observacion_tarde || ''
                    };
                    renderCalendar();
                    showNotification('Disponibilidad actualizada', 'success');
                } else {
                    showNotification('Error al actualizar disponibilidad', 'error');
                    renderCalendar();
                }
            })
            .catch(() => {
                showNotification('Error de conexión', 'error');
                renderCalendar();
            });
        }


        // (Eliminada función duplicada updateAvailabilityFranjas)
        
        
        function formatDate(dateStr) {
            const date = new Date(dateStr + 'T12:00:00'); // Usar mediodía para evitar problemas de zona horaria
            const day = date.getDate();
            const month = monthNames[date.getMonth()];
            return `${day} de ${month}`;
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadMonth();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadMonth();
        }

        function currentMonth() {
            currentDate = new Date();
            loadMonth();
        }

        function loadMonth() {
            const year = currentDate.getFullYear();
            const month = String(currentDate.getMonth() + 1).padStart(2, '0');
            const monthStr = `${year}-${month}`;
            window.location.href = `?month=${monthStr}`;
        }

        function setAllAvailable() {
            if (confirm('¿Marcar todos los días del mes como disponibles?')) {
                updateMonthAvailability(true);
            }
        }

        function setWeekendsUnavailable() {
            if (confirm('¿Marcar todos los fines de semana como no disponibles?')) {
                updateWeekendsAvailability(false);
            }
        }

        function setWeekdaysUnavailable() {
            if (confirm('¿Marcar todos los días entre semana como no disponibles?')) {
                updateWeekdaysAvailability(false);
            }
        }

        function clearMonth() {
            if (confirm('¿Limpiar toda la disponibilidad del mes?')) {
                updateMonthAvailability(null);
            }
        }

        function updateMonthAvailability(available) {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Calcular el próximo lunes disponible
            const now = new Date();
            let nextAvailableMonday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const currentDayOfWeek = now.getDay();
            const currentHour = now.getHours();
            
            if (currentDayOfWeek === 1) {
                if (currentHour >= 23) {
                    nextAvailableMonday.setDate(nextAvailableMonday.getDate() + 7);
                }
            } else {
                let daysUntilMonday = (1 - currentDayOfWeek + 7) % 7;
                if (daysUntilMonday === 0) daysUntilMonday = 7;
                nextAvailableMonday.setDate(nextAvailableMonday.getDate() + daysUntilMonday);
            }
            
            showNotification('Actualizando disponibilidad del mes...', 'info');
            
            let updatedDays = 0;
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                
                // Solo actualizar fechas que cumplan con la nueva restricción
                if (date >= nextAvailableMonday) {
                    const dateStr = formatDateString(date);
                    
                    const formData = new FormData();
                    formData.append('fecha', dateStr);
                    if (available !== null && available) {
                        formData.append('disponible', '1');
                    }
                    // No enviamos observaciones en las acciones masivas
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    });
                    updatedDays++;
                }
            }
            
            if (updatedDays === 0) {
                showNotification('No hay fechas válidas para actualizar (solo se puede gestionar disponibilidad los lunes hasta las 23:00)', 'warning');
                return;
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        function updateWeekendsAvailability(available) {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Calcular el próximo lunes disponible
            const now = new Date();
            let nextAvailableMonday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const currentDayOfWeek = now.getDay();
            const currentHour = now.getHours();
            
            if (currentDayOfWeek === 1) {
                if (currentHour >= 23) {
                    nextAvailableMonday.setDate(nextAvailableMonday.getDate() + 7);
                }
            } else {
                let daysUntilMonday = (1 - currentDayOfWeek + 7) % 7;
                if (daysUntilMonday === 0) daysUntilMonday = 7;
                nextAvailableMonday.setDate(nextAvailableMonday.getDate() + daysUntilMonday);
            }
            
            showNotification('Actualizando fines de semana...', 'info');
            
            let updatedDays = 0;
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayOfWeek = date.getDay();
                
                if ((dayOfWeek === 0 || dayOfWeek === 6) && date >= nextAvailableMonday) { // Domingo (0) o Sábado (6) y dentro del plazo
                    const dateStr = formatDateString(date);
                    
                    const formData = new FormData();
                    formData.append('fecha', dateStr);
                    if (available) {
                        formData.append('disponible', '1');
                    }
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    });
                    updatedDays++;
                }
            }
            
            if (updatedDays === 0) {
                showNotification('No hay fines de semana válidos para actualizar (solo se puede gestionar disponibilidad los lunes hasta las 23:00)', 'warning');
                return;
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        function updateWeekdaysAvailability(available) {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Calcular el próximo lunes disponible
            const now = new Date();
            let nextAvailableMonday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const currentDayOfWeek = now.getDay();
            const currentHour = now.getHours();
            
            if (currentDayOfWeek === 1) {
                if (currentHour >= 23) {
                    nextAvailableMonday.setDate(nextAvailableMonday.getDate() + 7);
                }
            } else {
                let daysUntilMonday = (1 - currentDayOfWeek + 7) % 7;
                if (daysUntilMonday === 0) daysUntilMonday = 7;
                nextAvailableMonday.setDate(nextAvailableMonday.getDate() + daysUntilMonday);
            }
            
            showNotification('Actualizando días entre semana...', 'info');
            
            let updatedDays = 0;
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayOfWeek = date.getDay();
                
                if (dayOfWeek >= 1 && dayOfWeek <= 5 && date >= nextAvailableMonday) { // Lunes a Viernes y dentro del plazo
                    const dateStr = formatDateString(date);
                    
                    const formData = new FormData();
                    formData.append('fecha', dateStr);
                    if (available) {
                        formData.append('disponible', '1');
                    }
                    
                    fetch('', {
                        method: 'POST',
                        body: formData
                    });
                    updatedDays++;
                }
            }
            
            if (updatedDays === 0) {
                showNotification('No hay días entre semana válidos para actualizar (solo se puede gestionar disponibilidad los lunes hasta las 23:00)', 'warning');
                return;
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Inicializar calendario
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar();
        });
    </script>
</body>
</html>
