-- Datos de prueba adicionales
USE `pi_intermodular`;

-- Asignar árbitro a partidos existentes
UPDATE partidos p
JOIN arbitros a ON a.numero_licencia = 'LIC-0001'
SET p.arbitro_principal_id = a.id
WHERE p.ID_FEDEX IN (1001, 1002);

-- Insertar más partidos con resultados
INSERT INTO partidos (ID_FEDEX, equipo_local, equipo_visitante, categoria_id, `grupo`, fecha, estado, sets_local, sets_visitante, pabellon_nombre)
VALUES
	(2001, 'CV Plasencia', 'CV Almendralejo', (SELECT id FROM categorias WHERE nombre='Juvenil'), 'C', DATE_SUB(NOW(), INTERVAL 7 DAY), 'finalizado', 3, 1, 'Pabellón Plasencia'),
	(2002, 'CV Zafra', 'CV Villanueva', (SELECT id FROM categorias WHERE nombre='Cadete'), 'D', DATE_SUB(NOW(), INTERVAL 2 DAY), 'finalizado', 3, 2, 'Pabellón Zafra')
ON DUPLICATE KEY UPDATE estado = VALUES(estado), sets_local = VALUES(sets_local), sets_visitante = VALUES(sets_visitante);