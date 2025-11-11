INSERT INTO editorial (nombre) VALUES
 ('Alfaguara'),
 ('Planeta'),
 ('Anagrama'),
 ('Tusquets');

INSERT INTO autor (nombre) VALUES
 ('Gabriel García Márquez'),
 ('Isabel Allende'),
 ('Jorge Luis Borges'),
 ('Miguel de Cervantes');

INSERT INTO libro (titulo, editorial_id, anio, edicion, autor_id, web) VALUES
 ('Cien Años de Soledad', 1, 1967, 1, 1, 'https://ejemplo.com/cien-anos'),
 ('La Casa de los Espíritus', 2, 1982, 1, 2, 'https://ejemplo.com/casa-espiritus'),
 ('El Aleph', 3, 1945, 3, 3, 'https://ejemplo.com/aleph'),
 ('Don Quijote de la Mancha', 4, 1605, 10, 4, 'https://ejemplo.com/quijote');
