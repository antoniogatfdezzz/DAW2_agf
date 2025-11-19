-- Datos iniciales para la base de datos viogen
USE viogen;

-- Usuario de aplicación (según requisitos)
INSERT INTO Usuario(nombre, clave) VALUES ('abcd', '1234');

-- Datos de ejemplo para Victima y Agresion (opcionales)
INSERT INTO Victima(nombre, apellidos, tipo_documento, documento, telefono, observaciones)
VALUES
('Ana', 'García', 'NIF', '12345678A', '600111222', 'Víctima de prueba'),
('María', NULL, NULL, NULL, NULL, 'Observaciones solo');

INSERT INTO Agresion(id_victima, agresor, tipo_agresion, fecha_hora, observaciones)
VALUES
(1, 'Juan Pérez', 'física', '2025-11-01 14:30:00', 'Agresión en la calle'),
(2, NULL, 'psicológica', '2025-10-20 09:00:00', 'Comentario de ejemplo');
