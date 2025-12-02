-- Creación y Estructura de la base de datos (MySQL)

-- Ajusta el nombre de la base de datos si lo deseas
CREATE DATABASE IF NOT EXISTS `pi_intermodular` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pi_intermodular`;

-- Tabla de usuarios
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de administradores
CREATE TABLE IF NOT EXISTS `administradores` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`usuario_id` int(11) NOT NULL,
	`nombre` varchar(100) NOT NULL,
	`apellidos` varchar(200) NOT NULL,
	`telefono` varchar(20) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `usuario_id` (`usuario_id`),
	CONSTRAINT `administradores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de árbitros
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS `categorias` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nombre` varchar(100) NOT NULL,
	`descripcion` text DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de partidos
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
	`jo` varchar(50) DEFAULT NULL,
	`jornada` varchar(50) DEFAULT NULL,
	`observacion_partido` text DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `ID_FEDEX` (`ID_FEDEX`),
	KEY `categoria_id` (`categoria_id`),
	KEY `arbitro_principal_id` (`arbitro_principal_id`),
	KEY `arbitro_segundo_id` (`arbitro_segundo_id`),
	KEY `anotador_id` (`anotador_id`),
	CONSTRAINT `partidos_ibfk_3` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
	CONSTRAINT `partidos_ibfk_5` FOREIGN KEY (`arbitro_principal_id`) REFERENCES `arbitros` (`id`),
	CONSTRAINT `partidos_ibfk_6` FOREIGN KEY (`arbitro_segundo_id`) REFERENCES `arbitros` (`id`),
	CONSTRAINT `partidos_ibfk_7` FOREIGN KEY (`anotador_id`) REFERENCES `arbitros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de liquidaciones
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
	`fecha_confirmacion` timestamp NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `arbitro_id` (`arbitro_id`),
	CONSTRAINT `liquidaciones_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de relación liquidaciones-partidos
CREATE TABLE IF NOT EXISTS `liquidaciones_partidos` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`liquidacion_id` int(11) NOT NULL,
	`partido_id` int(11) NOT NULL,
	`rol_arbitro` varchar(50) NOT NULL,
	`importe_partido` decimal(10,2) DEFAULT 0.00,
	`importe_dieta` decimal(10,2) DEFAULT 0.00,
	`importe_kilometraje` decimal(10,2) DEFAULT 0.00,
	`kilometros` decimal(10,2) DEFAULT 0.00,
	PRIMARY KEY (`id`),
	KEY `liquidacion_id` (`liquidacion_id`),
	KEY `partido_id` (`partido_id`),
	CONSTRAINT `liquidaciones_partidos_ibfk_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
	CONSTRAINT `liquidaciones_partidos_ibfk_2` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Tabla alias de árbitros
CREATE TABLE IF NOT EXISTS `arbitro_alias` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`arbitro_id` int(11) NOT NULL,
	`alias` varchar(100) NOT NULL,
	`fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (`id`),
	UNIQUE KEY `alias` (`alias`),
	KEY `arbitro_id` (`arbitro_id`),
	CONSTRAINT `arbitro_alias_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla disponibilidad de árbitros
CREATE TABLE IF NOT EXISTS `disponibilidad_arbitros` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`arbitro_id` int(11) NOT NULL,
	`fecha` date NOT NULL,
	`manana` tinyint(1) DEFAULT 0,
	`tarde` tinyint(1) DEFAULT 0,
	`observacion_manana` varchar(255) DEFAULT NULL,
	`observacion_tarde` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `arbitro_fecha` (`arbitro_id`,`fecha`),
	KEY `arbitro_id` (`arbitro_id`),
	CONSTRAINT `disponibilidad_arbitros_ibfk_1` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla sets de partidos
CREATE TABLE IF NOT EXISTS `sets_partidos` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`partido_id` int(11) NOT NULL,
	`numero_set` tinyint(1) NOT NULL,
	`puntos_local` int(11) NOT NULL,
	`puntos_visitante` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `partido_set` (`partido_id`,`numero_set`),
	KEY `partido_id` (`partido_id`),
	CONSTRAINT `sets_partidos_ibfk_1` FOREIGN KEY (`partido_id`) REFERENCES `partidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla rectificaciones de liquidaciones
CREATE TABLE IF NOT EXISTS `rectificaciones_liquidaciones` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`liquidacion_id` int(11) NOT NULL,
	`arbitro_id` int(11) NOT NULL,
	`motivo` text NOT NULL,
	`respuesta` text DEFAULT NULL,
	`estado` enum('pendiente','respondida') DEFAULT 'pendiente',
	`fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
	`fecha_respuesta` timestamp NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `liquidacion_id` (`liquidacion_id`),
	KEY `arbitro_id` (`arbitro_id`),
	CONSTRAINT `rect_liq_ibfk_1` FOREIGN KEY (`liquidacion_id`) REFERENCES `liquidaciones` (`id`) ON DELETE CASCADE,
	CONSTRAINT `rect_liq_ibfk_2` FOREIGN KEY (`arbitro_id`) REFERENCES `arbitros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;