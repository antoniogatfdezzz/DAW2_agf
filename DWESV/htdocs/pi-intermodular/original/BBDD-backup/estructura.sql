-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para fedexvb_intra
CREATE DATABASE IF NOT EXISTS `fedexvb_intra` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `fedexvb_intra`;

-- Volcando estructura para tabla fedexvb_intra.administradores
CREATE TABLE IF NOT EXISTS `administradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(200) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `administradores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.arbitros
CREATE TABLE IF NOT EXISTS `arbitros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(200) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `ciudad` varchar(100) NOT NULL,
  `iban` varchar(34) DEFAULT NULL,
  `licencia` enum('colaborador','anotador','habilitado_n1','habilitado_n2','habilitado_n3','n1','n2','n3c','n3b','n3a') DEFAULT 'colaborador',
  `numero_licencia` varchar(20) DEFAULT NULL,
  `numero_matricula` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `arbitros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=712 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.arbitro_alias
CREATE TABLE IF NOT EXISTS `arbitro_alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arbitro_id` int(11) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias_unique` (`alias`),
  KEY `arbitro_id` (`arbitro_id`),
  CONSTRAINT `arbitro_alias_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=369 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para procedimiento fedexvb_intra.AutoConfirmarLiquidaciones
DELIMITER //
CREATE PROCEDURE `AutoConfirmarLiquidaciones`()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE liquidacion_id INT;
    DECLARE cur CURSOR FOR 
        SELECT id 
        FROM liquidaciones 
        WHERE estado = 'pendiente' 
        AND fecha_creacion <= DATE_SUB(NOW(), INTERVAL 14 DAY)
        AND id NOT IN (
            SELECT DISTINCT liquidacion_id 
            FROM rectificaciones_liquidaciones 
            WHERE estado = 'pendiente'
        );
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    confirmation_loop: LOOP
        FETCH cur INTO liquidacion_id;
        IF done THEN
            LEAVE confirmation_loop;
        END IF;
        
        -- Actualizar a confirmada
        UPDATE liquidaciones 
        SET estado = 'confirmada', 
            fecha_confirmacion = NOW() 
        WHERE id = liquidacion_id;
        
    END LOOP;
    
    CLOSE cur;
    
    SELECT ROW_COUNT() as liquidaciones_confirmadas;
END//
DELIMITER ;

-- Volcando estructura para tabla fedexvb_intra.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.disponibilidad_arbitros
CREATE TABLE IF NOT EXISTS `disponibilidad_arbitros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arbitro_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `manana` tinyint(1) DEFAULT 0,
  `observacion_manana` text DEFAULT NULL,
  `tarde` tinyint(1) DEFAULT 0,
  `observacion_tarde` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_disponibilidad` (`arbitro_id`,`fecha`),
  CONSTRAINT `disponibilidad_arbitros_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=729 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.licencias_arbitros
CREATE TABLE IF NOT EXISTS `licencias_arbitros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arbitro_id` int(11) NOT NULL,
  `fecha_expedicion` date NOT NULL,
  `nivel_licencia` enum('colaborador','anotador','habilitado_n1','habilitado_n2','habilitado_n3','n1','n2','n3c','n3b','n3a') NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_arbitro_licencia` (`arbitro_id`),
  CONSTRAINT `licencias_arbitros_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.liquidaciones
CREATE TABLE IF NOT EXISTS `liquidaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `arbitro_id` int(11) NOT NULL,
  `tipo_liquidacion` enum('JUDEX','SENIOR','NACIONALES') NOT NULL DEFAULT 'JUDEX',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `numero_partidos` int(11) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `estado` enum('pendiente','confirmada','pagada','cancelada','rectificacion') DEFAULT 'pendiente',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_confirmacion` timestamp NULL DEFAULT NULL COMMENT 'Fecha cuando la liquidación se confirma automáticamente',
  PRIMARY KEY (`id`),
  KEY `arbitro_id` (`arbitro_id`),
  KEY `idx_fecha_creacion_estado` (`fecha_creacion`,`estado`),
  CONSTRAINT `liquidaciones_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.liquidaciones_partidos
CREATE TABLE IF NOT EXISTS `liquidaciones_partidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) NOT NULL,
  `partido_id` int(11) NOT NULL,
  `rol_arbitro` varchar(50) NOT NULL,
  `importe_partido` decimal(10,2) DEFAULT 0.00,
  `importe_dieta` decimal(10,2) DEFAULT 0.00 COMMENT 'Importe de la dieta (14€ si aplica)',
  `importe_kilometraje` decimal(10,2) DEFAULT 0.00 COMMENT 'Importe del kilometraje (km * 0.22€)',
  `kilometros` decimal(10,2) DEFAULT 0.00 COMMENT 'Kilómetros recorridos por el árbitro',
  PRIMARY KEY (`id`),
  KEY `liquidacion_id` (`liquidacion_id`),
  KEY `partido_id` (`partido_id`),
  CONSTRAINT `liquidaciones_partidos_ibfk_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `liquidaciones_partidos_ibfk_2` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=276 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.liquidacion_detalles
CREATE TABLE IF NOT EXISTS `liquidacion_detalles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) NOT NULL,
  `partido_id` int(11) NOT NULL,
  `cantidad_partido` decimal(8,2) DEFAULT 0.00,
  `dieta` decimal(8,2) DEFAULT 0.00,
  `kilometraje` decimal(8,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `liquidacion_id` (`liquidacion_id`),
  KEY `partido_id` (`partido_id`),
  CONSTRAINT `liquidacion_detalles_ibfk_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `liquidacion_detalles_ibfk_2` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.pabellones
CREATE TABLE IF NOT EXISTS `pabellones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `direccion` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.partidos
CREATE TABLE IF NOT EXISTS `partidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ID_FEDEX` int(11) DEFAULT NULL,
  `equipo_local` varchar(100) NOT NULL,
  `equipo_visitante` varchar(100) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `grupo` varchar(50) DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `arbitro_principal_id` int(11) DEFAULT NULL,
  `arbitro_segundo_id` int(11) DEFAULT NULL,
  `anotador_id` int(11) DEFAULT NULL,
  `finalizado` tinyint(1) DEFAULT 0,
  `sets_local` int(11) DEFAULT NULL,
  `sets_visitante` int(11) DEFAULT NULL,
  `estado` enum('programado','finalizado','cancelado') DEFAULT 'programado',
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `foto_resultado` varchar(255) DEFAULT NULL,
  `pabellon_nombre` varchar(200) NOT NULL DEFAULT '',
  `dieta_arbitro1` tinyint(1) DEFAULT 0,
  `kilometraje_arbitro1` decimal(10,2) DEFAULT 0.00,
  `km_arbitro1` decimal(10,2) DEFAULT 0.00,
  `dieta_arbitro2` tinyint(1) DEFAULT 0,
  `kilometraje_arbitro2` decimal(10,2) DEFAULT 0.00,
  `km_arbitro2` decimal(10,2) DEFAULT 0.00,
  `dieta_anotador` tinyint(1) DEFAULT 0,
  `kilometraje_anotador` decimal(10,2) DEFAULT 0.00,
  `km_anotador` decimal(10,2) DEFAULT 0.00,
  `jo` varchar(50) DEFAULT NULL COMMENT 'Campo Jo del Excel',
  `jornada` varchar(50) DEFAULT NULL COMMENT 'Campo Jornada del Excel',
  `observacion_partido` text DEFAULT NULL COMMENT 'Observaciones del árbitro sobre el partido',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ID_FEDEX` (`ID_FEDEX`),
  KEY `categoria_id` (`categoria_id`),
  KEY `arbitro_principal_id` (`arbitro_principal_id`),
  KEY `arbitro_segundo_id` (`arbitro_segundo_id`),
  KEY `anotador_id` (`anotador_id`),
  KEY `idx_equipo_local` (`equipo_local`),
  KEY `idx_equipo_visitante` (`equipo_visitante`),
  CONSTRAINT `partidos_ibfk_3` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `partidos_ibfk_5` FOREIGN KEY (`arbitro_principal_id`) REFERENCES `arbitros` (`id`),
  CONSTRAINT `partidos_ibfk_6` FOREIGN KEY (`arbitro_segundo_id`) REFERENCES `arbitros` (`id`),
  CONSTRAINT `partidos_ibfk_7` FOREIGN KEY (`anotador_id`) REFERENCES `arbitros` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para función fedexvb_intra.PuedeConfirmarse
DELIMITER //
CREATE FUNCTION `PuedeConfirmarse`(liquidacion_id INT) RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE puede_confirmarse BOOLEAN DEFAULT FALSE;
    DECLARE dias_transcurridos INT;
    DECLARE tiene_rectificaciones_pendientes INT DEFAULT 0;
    
    -- Calcular días transcurridos desde creación
    SELECT DATEDIFF(NOW(), fecha_creacion) INTO dias_transcurridos
    FROM liquidaciones 
    WHERE id = liquidacion_id;
    
    -- Verificar si tiene rectificaciones pendientes
    SELECT COUNT(*) INTO tiene_rectificaciones_pendientes
    FROM rectificaciones_liquidaciones 
    WHERE liquidacion_id = liquidacion_id 
    AND estado = 'pendiente';
    
    -- Puede confirmarse si han pasado 14 días y no tiene rectificaciones pendientes
    IF dias_transcurridos >= 14 AND tiene_rectificaciones_pendientes = 0 THEN
        SET puede_confirmarse = TRUE;
    END IF;
    
    RETURN puede_confirmarse;
END//
DELIMITER ;

-- Volcando estructura para tabla fedexvb_intra.rectificaciones_liquidaciones
CREATE TABLE IF NOT EXISTS `rectificaciones_liquidaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `liquidacion_id` int(11) NOT NULL,
  `partido_id` int(11) DEFAULT NULL,
  `arbitro_id` int(11) NOT NULL,
  `motivo` varchar(100) NOT NULL,
  `observaciones` text NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
  `respuesta_admin` text DEFAULT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `importe_partido_solicitado` decimal(10,2) DEFAULT NULL,
  `importe_dieta_solicitado` decimal(10,2) DEFAULT NULL,
  `importe_kilometraje_solicitado` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `liquidacion_id` (`liquidacion_id`),
  KEY `arbitro_id` (`arbitro_id`),
  KEY `idx_rectificacion_partido` (`partido_id`),
  KEY `idx_rectificacion_estado` (`estado`),
  CONSTRAINT `fk_rectificacion_partido` FOREIGN KEY (`partido_id`) REFERENCES `liquidaciones_partidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rectificaciones_liquidaciones_ibfk_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rectificaciones_liquidaciones_ibfk_2` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.sets_partidos
CREATE TABLE IF NOT EXISTS `sets_partidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partido_id` int(11) NOT NULL,
  `numero_set` int(11) NOT NULL,
  `puntos_local` int(11) NOT NULL,
  `puntos_visitante` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_partido` (`partido_id`),
  CONSTRAINT `sets_partidos_ibfk_1` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para tabla fedexvb_intra.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_usuario` enum('administrador','arbitro') NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_temporal` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=748 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La exportación de datos fue deseleccionada.

-- Volcando estructura para vista fedexvb_intra.v_liquidaciones_pendientes_confirmacion
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `v_liquidaciones_pendientes_confirmacion` (
	`id` INT(11) NOT NULL,
	`arbitro_id` INT(11) NOT NULL,
	`arbitro_nombre` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo_liquidacion` ENUM('JUDEX','SENIOR','NACIONALES') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`fecha_inicio` DATE NOT NULL,
	`fecha_fin` DATE NOT NULL,
	`fecha_creacion` TIMESTAMP NOT NULL,
	`dias_transcurridos` INT(7) NULL,
	`rectificaciones_pendientes` BIGINT(21) NOT NULL,
	`puede_confirmarse` TINYINT(1) NULL
) ENGINE=MyISAM;

-- Volcando estructura para disparador fedexvb_intra.tr_liquidaciones_estado_confirmada
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `tr_liquidaciones_estado_confirmada` 
BEFORE UPDATE ON `liquidaciones`
FOR EACH ROW
BEGIN
    -- Si el estado cambia a confirmada y no tenía fecha de confirmación
    IF NEW.estado = 'confirmada' AND OLD.estado != 'confirmada' AND NEW.fecha_confirmacion IS NULL THEN
        SET NEW.fecha_confirmacion = NOW();
    END IF;
    
    -- Si el estado cambia de confirmada a otro, limpiar fecha de confirmación
    IF NEW.estado != 'confirmada' AND OLD.estado = 'confirmada' THEN
        SET NEW.fecha_confirmacion = NULL;
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `v_liquidaciones_pendientes_confirmacion`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_liquidaciones_pendientes_confirmacion` AS SELECT 
    l.id,
    l.arbitro_id,
    CONCAT(a.nombre, ' ', a.apellidos) as arbitro_nombre,
    l.tipo_liquidacion,
    l.fecha_inicio,
    l.fecha_fin,
    l.fecha_creacion,
    DATEDIFF(NOW(), l.fecha_creacion) as dias_transcurridos,
    COUNT(r.id) as rectificaciones_pendientes,
    PuedeConfirmarse(l.id) as puede_confirmarse
FROM liquidaciones l
LEFT JOIN arbitros a ON l.arbitro_id = a.id
LEFT JOIN rectificaciones_liquidaciones r ON l.id = r.liquidacion_id AND r.estado = 'pendiente'
WHERE l.estado = 'pendiente'
GROUP BY l.id, l.arbitro_id, a.nombre, a.apellidos, l.tipo_liquidacion, l.fecha_inicio, l.fecha_fin, l.fecha_creacion
HAVING dias_transcurridos >= 14 AND rectificaciones_pendientes = 0 ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
