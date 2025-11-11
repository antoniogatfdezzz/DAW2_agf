PRAGMA foreign_keys = ON;

CREATE TABLE editorial (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL UNIQUE
);

CREATE TABLE autor (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  nombre TEXT NOT NULL
);

CREATE TABLE libro (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  titulo TEXT NOT NULL,
  editorial_id INTEGER NOT NULL,
  anio INTEGER NOT NULL,
  edicion INTEGER NOT NULL,
  autor_id INTEGER NOT NULL,
  web TEXT,
  FOREIGN KEY (editorial_id) REFERENCES editorial(id) ON DELETE RESTRICT,
  FOREIGN KEY (autor_id) REFERENCES autor(id) ON DELETE RESTRICT
);

-- Índices para búsquedas y ordenación
CREATE INDEX idx_libro_titulo ON libro(titulo);
CREATE INDEX idx_libro_anio ON libro(anio);
CREATE INDEX idx_libro_autor ON libro(autor_id);
