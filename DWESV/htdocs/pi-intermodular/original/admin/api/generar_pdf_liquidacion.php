<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/database.php';

$auth = new Auth();
$auth->requireUserType('administrador');

$database = new Database();
$conn = $database->getConnection();

$liquidacion_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$liquidacion_id) {
    die('ID de liquidación no válido');
}

// Obtener datos de la liquidación
$query = "SELECT l.*, 
                 CONCAT(a.nombre, ' ', a.apellidos) as arbitro_nombre,
                 a.dni as arbitro_dni,
                 a.ciudad as arbitro_ciudad,
                 a.telefono as arbitro_telefono,
                 a.iban as arbitro_iban,
                 u.email as arbitro_email,
                 l.numero_partidos as partidos_contabilizados
          FROM liquidaciones l
          LEFT JOIN arbitros a ON l.arbitro_id = a.id
          LEFT JOIN usuarios u ON a.usuario_id = u.id
          WHERE l.id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$liquidacion_id]);
$liquidacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$liquidacion) {
    die('Liquidación no encontrada');
}

// Obtener partidos de la liquidación
$query = "SELECT lp.*, 
                 p.fecha,
                 p.equipo_local,
                 p.equipo_visitante,
                 c.nombre as categoria_nombre
          FROM liquidaciones_partidos lp
          LEFT JOIN partidos p ON lp.partido_id = p.id
          LEFT JOIN categorias c ON p.categoria_id = c.id
          WHERE lp.liquidacion_id = ?
          ORDER BY p.fecha";
$stmt = $conn->prepare($query);
$stmt->execute([$liquidacion_id]);
$partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totales
$total_partido = 0;
$total_dieta = 0;
$total_kilometraje = 0;
$total_general = 0;

foreach ($partidos as $partido) {
    $total_partido += floatval($partido['importe_partido']);
    $total_dieta += floatval($partido['importe_dieta']);
    $total_kilometraje += floatval($partido['importe_kilometraje']);
    $total_general += floatval($partido['importe_partido']) + floatval($partido['importe_dieta']) + floatval($partido['importe_kilometraje']);
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
    <title>Liquidación <?php echo htmlspecialchars($liquidacion['tipo_liquidacion']); ?> - <?php echo htmlspecialchars($liquidacion['arbitro_nombre']); ?></title>
    <style>
        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .header {
            border-bottom: 3px solid #2E7D32;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .header-logos {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .header-logos img {
            max-height: 60px;
            width: auto;
            object-fit: contain;
        }
        
        .header-logos .logo-badajoz {
            max-height: 70px;
        }
        
        .header-logos .logo-caceres {
            max-height: 90px;
        }
        
        .header-logos .logo-fedexvb {
            max-height: 65px;
        }
        
        .header-text {
            margin-left: 30px;
            flex: 1;
            min-width: 300px;
        }
        
        .header h1 {
            color: #2E7D32;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #666;
            font-size: 18px;
            font-weight: normal;
        }
        
        .federacion-info {
            background: #f9f9f9;
            border-left: 4px solid #2E7D32;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .federacion-info h3 {
            color: #2E7D32;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .federacion-info p {
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 3px 0;
        }
        
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #2E7D32;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 5px;
        }
        
        .info-box p {
            font-size: 12px;
            line-height: 1.8;
            color: #333;
            margin: 5px 0;
        }
        
        .info-box strong {
            color: #000;
            display: inline-block;
            width: 120px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        table thead {
            background: #2E7D32;
            color: white;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
        }
        
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        table tbody tr:hover {
            background: #f5f5f5;
        }
        
        .totals {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        
        .totals table {
            margin: 0;
        }
        
        .totals td {
            border: none;
            padding: 8px;
            font-weight: bold;
        }
        
        .totals .total-final {
            background: #2E7D32;
            color: white;
            font-size: 14px;
        }
        
        .firma {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .firma-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            margin: 20px 2.5%;
        }
        
        .firma-linea {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-size: 12px;
            color: #666;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #2E7D32;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2E7D32;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .btn-print:hover {
            background: #1B5E20;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <button class="btn-print no-print" onclick="window.print()">
        Imprimir / Guardar como PDF
    </button>
    
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="header-logos">
                <img src="../../assets/logos/logo-dip-badajoz.png" alt="Diputación de Badajoz" class="logo-badajoz">
                <img src="../../assets/logos/logo-dip-caceres.png" alt="Diputación de Cáceres" class="logo-caceres">
                <img src="../../assets/logos/logo-fedexvb.png" alt="FEDEXVB" class="logo-fedexvb">
            </div>
            <div class="header-text">
                <h1>LIQUIDACIÓN DE ÁRBITRO</h1>
                <h2>Tipo: <?php echo htmlspecialchars($liquidacion['tipo_liquidacion']); ?></h2>
            </div>
        </div>
        
        <!-- Información de la Federación -->
        <div class="federacion-info">
            <h3>Federación Extremeña de Voleibol</h3>
            <p><strong>CIF/NIF:</strong> G10047769</p>
            <p><strong>Actividad Principal:</strong> Federación Deportiva</p>
            <p><strong>Dirección:</strong> Avenida Pierre de Coubertin S/N, Pabellón Multiusos, CP 10005, Cáceres (Cáceres)</p>
            <p><strong>Teléfono:</strong> 670 85 61 34</p>
            <p><strong>Email:</strong> fedexvoleibol@gmail.com</p>
            <p><strong>Web:</strong> fedexvoleibol.com</p>
        </div>
        
        <!-- Información del Árbitro y Liquidación -->
        <div class="info-section">
            <div class="info-box">
                <h3>Datos del Árbitro</h3>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($liquidacion['arbitro_nombre']); ?></p>
                <p><strong>DNI:</strong> <?php echo htmlspecialchars($liquidacion['arbitro_dni'] ?? 'No disponible'); ?></p>
                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($liquidacion['arbitro_ciudad'] ?? 'No disponible'); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($liquidacion['arbitro_telefono'] ?? 'No disponible'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($liquidacion['arbitro_email'] ?? 'No disponible'); ?></p>
                <p><strong>IBAN:</strong> <?php echo htmlspecialchars($liquidacion['arbitro_iban'] ?? 'No disponible'); ?></p>
            </div>
            
            <div class="info-box">
                <h3>Datos de la Liquidación</h3>
                <p><strong>ID Liquidación:</strong> #<?php echo $liquidacion['id']; ?></p>
                <p><strong>Tipo:</strong> <?php echo htmlspecialchars($liquidacion['tipo_liquidacion']); ?></p>
                <p><strong>Período:</strong> <?php echo date('d/m/Y', strtotime($liquidacion['fecha_inicio'])); ?> al <?php echo date('d/m/Y', strtotime($liquidacion['fecha_fin'])); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($liquidacion['estado']); ?></p>
                <p><strong>Fecha emisión:</strong> <?php echo date('d/m/Y'); ?></p>
                <p><strong>Nº Partidos:</strong> <?php echo count($partidos); ?></p>
            </div>
        </div>
        
        <!-- Tabla de Partidos -->
        <h3 style="color: #2E7D32; margin-bottom: 15px; font-size: 16px;">Detalle de Partidos</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    
                    <th>Equipos</th>
                    <th>Categoría</th>
                    <th>Rol</th>
                    <th class="text-right">Partido</th>
                    <th class="text-right">Dieta</th>
                    <th class="text-right">Km</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($partidos) > 0): ?>
                    <?php foreach ($partidos as $index => $partido): ?>
                        <?php 
                        $importe_partido = floatval($partido['importe_partido']);
                        $importe_dieta = floatval($partido['importe_dieta']);
                        $importe_km = floatval($partido['importe_kilometraje']);
                        $total_partido = $importe_partido + $importe_dieta + $importe_km;
                        ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($partido['fecha'])); ?></td>
                            
                            <td><?php echo htmlspecialchars($partido['equipo_local'] . ' vs ' . $partido['equipo_visitante']); ?></td>
                            <td><?php echo htmlspecialchars($partido['categoria_nombre']); ?></td>
                            <td><?php echo htmlspecialchars($partido['rol_arbitro']); ?></td>
                            <td class="text-right"><?php echo number_format($importe_partido, 2, ',', '.'); ?> €</td>
                            <td class="text-right"><?php echo number_format($importe_dieta, 2, ',', '.'); ?> €</td>
                            <td class="text-right"><?php echo number_format($importe_km, 2, ',', '.'); ?> €</td>
                            <td class="text-right"><strong><?php echo number_format($total_partido, 2, ',', '.'); ?> €</strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No hay partidos registrados en esta liquidación</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Resumen de Totales -->
        <div class="totals">
            <table>
                <tr>
                    <td>Total Importe Partidos:</td>
                    <td class="text-right"><?php echo number_format($total_partido, 2, ',', '.'); ?> €</td>
                </tr>
                <tr>
                    <td>Total Dietas:</td>
                    <td class="text-right"><?php echo number_format($total_dieta, 2, ',', '.'); ?> €</td>
                </tr>
                <tr>
                    <td>Total Kilometraje:</td>
                    <td class="text-right"><?php echo number_format($total_kilometraje, 2, ',', '.'); ?> €</td>
                </tr>
                <tr class="total-final">
                    <td style="font-size: 16px;">TOTAL A LIQUIDAR:</td>
                    <td class="text-right" style="font-size: 16px;"><?php echo number_format($total_general, 2, ',', '.'); ?> €</td>
                </tr>
            </table>
        </div>
        
        <?php if (!empty($liquidacion['observaciones'])): ?>
        <div class="info-box" style="margin-top: 20px;">
            <h3>Observaciones</h3>
            <p><?php echo nl2br(htmlspecialchars($liquidacion['observaciones'])); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Firmas -->
        <div class="firma">
            <div class="firma-box">
                <div class="firma-linea">
                    Firma del Árbitro<br>
                    <?php echo htmlspecialchars($liquidacion['arbitro_nombre']); ?>
                </div>
            </div>
            <div class="firma-box">
                <div class="firma-linea">
                    Firma y Sello de la Federación<br>
                    FEDEXVB
                </div>
            </div>
        </div>
        
        <!-- Pie de página -->
        <div class="footer">
            <p>Documento generado el <?php echo date('d/m/Y H:i'); ?> - Federación Extremeña de Voleibol</p>
            <p>Este documento tiene carácter informativo y debe ser validado por la federación</p>
        </div>
    </div>
    
    <script>
        // Auto-abrir el diálogo de impresión al cargar la página (opcional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
