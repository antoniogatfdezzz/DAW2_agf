-- Datos iniciales para la base de datos viogen
USE viogen;

-- Usuario de aplicación con contraseña '1234' (SHA1)
INSERT INTO Usuario(nombre, clave) 
VALUES ('abcd', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220'),
       ('abcde', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220'),
       ('abcdef', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220');

-- Datos de ejemplo para Victima
INSERT INTO Victima(nombre, apellidos, tipo_documento, documento, telefono, observaciones)
VALUES
('Ana', 'García', 'NIF', '12345678A', '600111222', 'Víctima de prueba'),
('María', NULL, NULL, NULL, NULL, 'Observaciones solo');

-- Datos de ejemplo para Agresion
INSERT INTO Agresion(id_victima, agresor, tipo_agresion, fecha_hora, observaciones)
VALUES
(1, 'Juan Pérez', 'física', '2025-11-01 14:30:00', 'Agresión en la calle'),
(2, NULL, 'psicológica', '2025-10-20 09:00:00', 'Comentario de ejemplo');
