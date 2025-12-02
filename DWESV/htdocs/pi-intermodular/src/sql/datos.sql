-- Datos iniciales para la base de datos (MySQL)
USE `pi_intermodular`;

-- Categorías básicas
INSERT INTO categorias (nombre, descripcion) VALUES
	('Senior Masculino', 'Categoría senior masculina'),
	('Senior Femenino', 'Categoría senior femenina'),
	('Juvenil', 'Categoría juvenil'),
	('Cadete', 'Categoría cadete')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Usuarios iniciales
-- NOTA: Sustituye <HASH_ADMIN> y <HASH_ARBITRO> por hashes generados con password_hash en PHP.
-- Ejemplo para generar hash: php -r "echo password_hash('admin123', PASSWORD_DEFAULT), PHP_EOL;"
INSERT INTO usuarios (tipo_usuario, usuario, email, password, password_temporal, activo)
VALUES
	('administrador', 'admin', 'admin@example.com', '<HASH_ADMIN>', 1, 1),
	('arbitro', 'arbitro1', 'arbitro1@example.com', '<HASH_ARBITRO>', 1, 1)
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

-- Datos de administradores y árbitros enlazados a los usuarios anteriores
INSERT INTO administradores (usuario_id, nombre, apellidos, telefono)
SELECT u.id, 'Admin', 'Principal', '600000000' FROM usuarios u WHERE u.usuario = 'admin'
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellidos = VALUES(apellidos), telefono = VALUES(telefono);

INSERT INTO arbitros (usuario_id, nombre, apellidos, dni, telefono, ciudad, licencia, numero_licencia)
SELECT u.id, 'Álvaro', 'Pérez', '00000000A', '600000001', 'Badajoz', 'habilitado_n2', 'LIC-0001'
FROM usuarios u WHERE u.usuario = 'arbitro1'
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellidos = VALUES(apellidos), ciudad = VALUES(ciudad), licencia = VALUES(licencia);

-- Partidos de ejemplo (sin asignación de árbitros)
INSERT INTO partidos (ID_FEDEX, equipo_local, equipo_visitante, categoria_id, `grupo`, fecha, estado, pabellon_nombre)
VALUES
	(1001, 'CV Badajoz', 'CV Mérida', (SELECT id FROM categorias WHERE nombre='Senior Masculino'), 'A', DATE_ADD(NOW(), INTERVAL 3 DAY), 'programado', 'Pabellón Badajoz'),
	(1002, 'CV Cáceres', 'CV Don Benito', (SELECT id FROM categorias WHERE nombre='Senior Femenino'), 'B', DATE_ADD(NOW(), INTERVAL 5 DAY), 'programado', 'Pabellón Cáceres')
ON DUPLICATE KEY UPDATE fecha = VALUES(fecha), estado = VALUES(estado);
