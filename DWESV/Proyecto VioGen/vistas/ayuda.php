<?php
/**
 * PÁGINA DE AYUDA Y DOCUMENTACIÓN
 * Guía completa del sistema para usuarios
 */

require_once __DIR__ . '/../config.php';

// Verificar autenticación
if (!estaAutenticado()) {
    header('Location: login.html?error=Debe iniciar sesión');
    exit;
}

$es_policia = tieneRol(ROL_POLICIA) || tieneRol(ROL_ADMIN);
$es_victima = tieneRol(ROL_VICTIMA);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VioGén - Ayuda y Documentación</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .help-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .help-section h3 {
            color: #667eea;
            margin-top: 0;
            border-bottom: 2px solid #E0E0E0;
            padding-bottom: 0.5rem;
        }
        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .help-card {
            background: #F5F5F5;
            padding: 1.5rem;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }
        .help-card h4 {
            margin: 0 0 1rem 0;
            color: #333;
        }
        .help-card ul {
            margin: 0;
            padding-left: 1.5rem;
            line-height: 1.8;
        }
        .nivel-ejemplo {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: bold;
            margin: 0.25rem;
        }
        .indicador-ejemplo {
            background: #E3F2FD;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border-left: 3px solid #2196F3;
            border-radius: 4px;
        }
        .faq-item {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            border-left: 4px solid #4CAF50;
        }
        .faq-item h4 {
            margin: 0 0 0.5rem 0;
            color: #333;
        }
        .toc {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .toc h3 {
            margin: 0 0 1rem 0;
            color: white;
        }
        .toc ul {
            margin: 0;
            padding-left: 1.5rem;
            line-height: 2;
        }
        .toc a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>🛡️ VioGén</h2>
            <nav>
                <ul>
                    <?php if ($es_policia): ?>
                    <li><a href="dashboard_policia.php">📊 Dashboard</a></li>
                    <li><a href="victimas_lista.php">👥 Víctimas</a></li>
                    <li><a href="agresores_lista.php">⚠️ Agresores</a></li>
                    <li><a href="valoraciones_lista.php">📋 Valoraciones</a></li>
                    <li><a href="pendientes.php">⏰ Casos Pendientes</a></li>
                    <li><a href="nueva_valoracion.php">➕ Nueva Valoración VPR</a></li>
                    <li><a href="registrar_victima.php">✏️ Registrar Víctima</a></li>
                    <li><a href="registrar_agresor.php">✏️ Registrar Agresor</a></li>
                    <?php elseif ($es_victima): ?>
                    <li><a href="dashboard_victima.php">🏠 Mi Panel</a></li>
                    <?php endif; ?>
                    <li><a href="ayuda.php" class="active">❓ Ayuda</a></li>
                    <li><a href="../cerrar_sesion.php" style="margin-top: 2rem; background: rgba(255,255,255,0.2);">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header-bar">
                <h1>❓ Ayuda y Documentación</h1>
                <a href="<?php echo $es_policia ? 'dashboard_policia.php' : 'dashboard_victima.php'; ?>" class="btn btn-secondary">← Volver</a>
            </div>

            <!-- ÍNDICE -->
            <div class="toc">
                <h3>📑 Índice de Contenidos</h3>
                <ul>
                    <li><a href="#que-es">¿Qué es el Sistema VioGén?</a></li>
                    <li><a href="#niveles">Niveles de Riesgo</a></li>
                    <li><a href="#vpr">Valoración Policial del Riesgo (VPR)</a></li>
                    <?php if ($es_policia): ?>
                    <li><a href="#uso-policia">Guía para Policías</a></li>
                    <li><a href="#indicadores">Los 35 Indicadores</a></li>
                    <li><a href="#vper">Valoración de Evolución (VPER)</a></li>
                    <?php endif; ?>
                    <?php if ($es_victima): ?>
                    <li><a href="#uso-victima">Información para Víctimas</a></li>
                    <?php endif; ?>
                    <li><a href="#recursos">Recursos de Ayuda</a></li>
                    <li><a href="#faq">Preguntas Frecuentes</a></li>
                </ul>
            </div>

            <!-- ¿QUÉ ES? -->
            <div class="help-section" id="que-es">
                <h3>¿Qué es el Sistema VioGén?</h3>
                <p>
                    El <strong>Sistema VioGén</strong> (Sistema de Seguimiento Integral en los casos de Violencia de Género) 
                    es una herramienta profesional utilizada por las Fuerzas y Cuerpos de Seguridad del Estado español 
                    para gestionar y hacer seguimiento de casos de violencia de género.
                </p>
                <p>
                    Su principal función es realizar una <strong>valoración objetiva del riesgo</strong> que sufren las víctimas 
                    mediante el protocolo oficial <strong>VPR 5.0-H</strong> (Valoración Policial del Riesgo), 
                    que utiliza 35 indicadores especializados para determinar el nivel de peligro.
                </p>
            </div>

            <!-- NIVELES DE RIESGO -->
            <div class="help-section" id="niveles">
                <h3>📊 Niveles de Riesgo</h3>
                <p>El sistema clasifica el riesgo en 5 niveles según la puntuación obtenida:</p>
                
                <div style="margin: 2rem 0;">
                    <div class="nivel-ejemplo" style="background: #4CAF50; color: white;">
                        <strong>NO APRECIADO</strong> (0-9 puntos)
                    </div>
                    <p style="margin: 0.5rem 0 1.5rem 0;">
                        No se detectan indicadores significativos de riesgo. No se requieren medidas especiales de protección.
                    </p>

                    <div class="nivel-ejemplo" style="background: #8BC34A; color: white;">
                        <strong>BAJO</strong> (10-19 puntos)
                    </div>
                    <p style="margin: 0.5rem 0 1.5rem 0;">
                        Presencia de algunos indicadores de riesgo leve. Se establece un seguimiento periódico.
                    </p>

                    <div class="nivel-ejemplo" style="background: #FFC107; color: #333;">
                        <strong>MEDIO</strong> (20-29 puntos)
                    </div>
                    <p style="margin: 0.5rem 0 1.5rem 0;">
                        Riesgo moderado que requiere medidas de protección. Seguimiento mensual obligatorio.
                    </p>

                    <div class="nivel-ejemplo" style="background: #FF5722; color: white;">
                        <strong>ALTO</strong> (30-44 puntos)
                    </div>
                    <p style="margin: 0.5rem 0 1.5rem 0;">
                        Riesgo importante con probabilidad de violencia grave. Requiere medidas urgentes de protección 
                        (orden de alejamiento, teleasistencia). Seguimiento quincenal.
                    </p>

                    <div class="nivel-ejemplo" style="background: #D32F2F; color: white;">
                        <strong>EXTREMO</strong> (45+ puntos)
                    </div>
                    <p style="margin: 0.5rem 0 1.5rem 0;">
                        Riesgo extremo de violencia grave o letal. Requiere protección policial inmediata, 
                        medidas cautelares reforzadas y seguimiento semanal o diario.
                    </p>
                </div>
            </div>

            <!-- VPR -->
            <div class="help-section" id="vpr">
                <h3>📋 Valoración Policial del Riesgo (VPR)</h3>
                <p>
                    La <strong>VPR</strong> es la valoración inicial que se realiza cuando una víctima denuncia 
                    o entra en el sistema. Se basa en el protocolo oficial VPR 5.0-H que incluye:
                </p>
                <ul>
                    <li><strong>35 indicadores especializados</strong> organizados en 5 factores</li>
                    <li><strong>Pesos diferenciados:</strong> CRÍTICO (8pts), ALTO (6pts), MEDIO (4pts), BAJO (2pts), POSITIVO (-4pts)</li>
                    <li><strong>Algoritmo actuarial</strong> que calcula automáticamente el nivel de riesgo</li>
                    <li><strong>Evaluación de 5 factores:</strong>
                        <ol>
                            <li>Gravedad del episodio actual</li>
                            <li>Historial de violencia</li>
                            <li>Perfil del agresor</li>
                            <li>Vulnerabilidad de la víctima</li>
                            <li>Factores protectores</li>
                        </ol>
                    </li>
                </ul>
            </div>

            <?php if ($es_policia): ?>
            <!-- GUÍA PARA POLICÍAS -->
            <div class="help-section" id="uso-policia">
                <h3>👮 Guía de Uso para Policías</h3>
                
                <div class="help-grid">
                    <div class="help-card">
                        <h4>1️⃣ Registrar Víctima</h4>
                        <ul>
                            <li>Completar datos personales obligatorios</li>
                            <li>Información de contacto y domicilio</li>
                            <li>Situación social y económica</li>
                            <li>Datos de salud relevantes</li>
                            <li>Información sobre menores a cargo</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h4>2️⃣ Registrar Agresor</h4>
                        <ul>
                            <li>Datos de identificación</li>
                            <li>Antecedentes penales (CRÍTICO)</li>
                            <li>Historial de agresiones</li>
                            <li>Posesión de armas (LETALIDAD)</li>
                            <li>Adicciones y salud mental</li>
                            <li>Medidas judiciales activas</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h4>3️⃣ Realizar Valoración VPR</h4>
                        <ul>
                            <li>Seleccionar víctima y agresor</li>
                            <li>Evaluar los 35 indicadores</li>
                            <li>El sistema calcula automáticamente</li>
                            <li>Añadir observaciones</li>
                            <li>Especificar medidas recomendadas</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h4>4️⃣ Seguimiento (VPER)</h4>
                        <ul>
                            <li>VPER cada 30-60 días</li>
                            <li>Más frecuente si riesgo alto</li>
                            <li>Evaluar cambios en indicadores</li>
                            <li>Ajustar medidas de protección</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h4>5️⃣ Casos Pendientes</h4>
                        <ul>
                            <li>Revisar VPER pendientes (>30 días)</li>
                            <li>Seguimiento casos alto riesgo</li>
                            <li>Víctimas sin valoración inicial</li>
                            <li>Priorizar según urgencia</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h4>6️⃣ Documentación</h4>
                        <ul>
                            <li>Todas las fichas se pueden imprimir</li>
                            <li>Informes de valoración completos</li>
                            <li>Historial de valoraciones</li>
                            <li>Datos actualizados en tiempo real</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- INDICADORES -->
            <div class="help-section" id="indicadores">
                <h3>📊 Los 35 Indicadores del VPR 5.0-H</h3>
                <p>Ejemplos de indicadores por factor:</p>

                <h4 style="color: #D32F2F; margin-top: 1.5rem;">Factor 1: Gravedad del Episodio Actual</h4>
                <div class="indicador-ejemplo">
                    <strong>Indicador 1 (CRÍTICO):</strong> Ha existido violencia física grave o uso de armas
                </div>
                <div class="indicador-ejemplo">
                    <strong>Indicador 3 (ALTO):</strong> Amenazas graves o de muerte
                </div>

                <h4 style="color: #FF5722; margin-top: 1.5rem;">Factor 2: Historial de Violencia</h4>
                <div class="indicador-ejemplo">
                    <strong>Indicador 9 (CRÍTICO):</strong> Historial de agresiones físicas previas
                </div>

                <h4 style="color: #FF9800; margin-top: 1.5rem;">Factor 3: Perfil del Agresor</h4>
                <div class="indicador-ejemplo">
                    <strong>Indicador 15 (CRÍTICO):</strong> El agresor posee armas o tiene acceso a ellas
                </div>
                <div class="indicador-ejemplo">
                    <strong>Indicador 16 (ALTO):</strong> Consumo de alcohol o drogas
                </div>

                <h4 style="color: #FFC107; margin-top: 1.5rem;">Factor 4: Vulnerabilidad de la Víctima</h4>
                <div class="indicador-ejemplo">
                    <strong>Indicador 24 (MEDIO):</strong> La víctima está embarazada o tiene hijos menores
                </div>

                <h4 style="color: #4CAF50; margin-top: 1.5rem;">Factor 5: Factores Protectores</h4>
                <div class="indicador-ejemplo">
                    <strong>Indicador 31 (POSITIVO):</strong> La víctima cuenta con apoyo familiar/social (-4 puntos)
                </div>
            </div>

            <!-- VPER -->
            <div class="help-section" id="vper">
                <h3>🔄 Valoración de Evolución del Riesgo (VPER)</h3>
                <p>
                    La <strong>VPER</strong> es una revaloración periódica que evalúa si el riesgo ha aumentado, 
                    disminuido o se mantiene. Se debe realizar:
                </p>
                <ul>
                    <li><strong>Cada 30-60 días</strong> para casos de riesgo medio</li>
                    <li><strong>Cada 15-30 días</strong> para casos de riesgo alto</li>
                    <li><strong>Semanal o quincenalmente</strong> para casos de riesgo extremo</li>
                    <li><strong>Siempre que cambian las circunstancias</strong> (nuevas agresiones, quebrantamientos, etc.)</li>
                </ul>
                <p>
                    El proceso es idéntico al VPR: se evalúan nuevamente los 35 indicadores y se compara 
                    con la valoración anterior para ver la evolución.
                </p>
            </div>
            <?php endif; ?>

            <?php if ($es_victima): ?>
            <!-- INFORMACIÓN PARA VÍCTIMAS -->
            <div class="help-section" id="uso-victima">
                <h3>👤 Información para Víctimas</h3>
                
                <h4>¿Qué información puedo ver?</h4>
                <ul>
                    <li>Tu <strong>nivel de riesgo actual</strong> según la última valoración</li>
                    <li>Historial completo de valoraciones realizadas</li>
                    <li>Medidas de protección recomendadas por los evaluadores</li>
                    <li>Tus datos personales registrados en el sistema</li>
                </ul>

                <h4>¿Qué significa mi nivel de riesgo?</h4>
                <p>
                    El nivel de riesgo indica la probabilidad de que sufras nuevas agresiones según una evaluación 
                    profesional. Un nivel alto o extremo requiere que sigas estrictamente todas las medidas de protección 
                    y mantengas contacto regular con las autoridades.
                </p>

                <h4>¿Puedo hacer algo para reducir el riesgo?</h4>
                <ul>
                    <li>Sigue todas las <strong>medidas de protección</strong> establecidas</li>
                    <li>No contactes con el agresor si hay orden de alejamiento</li>
                    <li>Informa inmediatamente si el agresor te contacta o incumple medidas</li>
                    <li>Mantén comunicación regular con tu contacto policial</li>
                    <li>Utiliza los recursos de ayuda disponibles (016, servicios sociales)</li>
                    <li>No compartas tu ubicación en redes sociales</li>
                </ul>
            </div>
            <?php endif; ?>

            <!-- RECURSOS -->
            <div class="help-section" id="recursos">
                <h3>📞 Recursos de Ayuda y Emergencia</h3>
                
                <div style="background: #FFEBEE; padding: 1.5rem; border-radius: 6px; border-left: 4px solid #D32F2F; margin-bottom: 1.5rem;">
                    <h4 style="margin: 0 0 1rem 0; color: #D32F2F;">🚨 EN CASO DE EMERGENCIA</h4>
                    <p style="font-size: 1.25rem; margin: 0;"><strong>112</strong> - Emergencias (24h)</p>
                    <p style="font-size: 1.25rem; margin: 0;"><strong>091</strong> - Policía Nacional</p>
                    <p style="font-size: 1.25rem; margin: 0;"><strong>062</strong> - Guardia Civil</p>
                </div>

                <div style="background: #E3F2FD; padding: 1.5rem; border-radius: 6px; border-left: 4px solid #2196F3;">
                    <h4 style="margin: 0 0 1rem 0; color: #1976D2;">📞 Atención Especializada</h4>
                    <ul style="margin: 0; line-height: 2;">
                        <li><strong>016</strong> - Teléfono contra la violencia de género (24h, gratuito, no deja rastro)</li>
                        <li><strong>WhatsApp: 600 000 016</strong></li>
                        <li><strong>Email: 016-online@igualdad.gob.es</strong></li>
                        <li><strong>Oficinas de Atención a las Víctimas</strong> (OAVD)</li>
                        <li><strong>Servicios Sociales</strong> municipales</li>
                        <li><strong>Centros de la Mujer</strong></li>
                        <li><strong>Casas de Acogida</strong></li>
                    </ul>
                </div>
            </div>

            <!-- FAQ -->
            <div class="help-section" id="faq">
                <h3>❓ Preguntas Frecuentes</h3>

                <div class="faq-item">
                    <h4>¿Quién puede acceder al sistema?</h4>
                    <p>
                        El sistema tiene dos tipos de usuarios:
                        <strong>Policías/evaluadores</strong> (acceso completo) y 
                        <strong>víctimas</strong> (acceso a su información personal).
                    </p>
                </div>

                <div class="faq-item">
                    <h4>¿Es confidencial la información?</h4>
                    <p>
                        Sí, toda la información está protegida y solo es accesible por personal autorizado. 
                        Todas las acciones quedan registradas en auditoría.
                    </p>
                </div>

                <div class="faq-item">
                    <h4>¿Con qué frecuencia se actualizan las valoraciones?</h4>
                    <p>
                        La frecuencia depende del nivel de riesgo: entre 7-60 días. 
                        También se actualiza cuando cambian las circunstancias del caso.
                    </p>
                </div>

                <div class="faq-item">
                    <h4>¿Qué pasa si el agresor incumple las medidas?</h4>
                    <p>
                        Debes denunciarlo inmediatamente llamando al 112, 091 o 062. 
                        El quebrantamiento de medidas es un delito grave.
                    </p>
                </div>

                <div class="faq-item">
                    <h4>¿Puedo solicitar cambios en mi nivel de riesgo?</h4>
                    <p>
                        El nivel de riesgo lo determinan los evaluadores profesionales según el protocolo VPR. 
                        Sin embargo, si percibes un aumento del peligro, debes informar inmediatamente para 
                        que se realice una nueva valoración.
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
