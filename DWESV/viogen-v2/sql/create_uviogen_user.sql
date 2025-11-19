-- Crea el usuario de base de datos requerido (si no existe)
-- Nota: el archivo `viogen.sql` adjunto contiene 'uviogeo' (posible errata).
-- Este script crea el usuario correcto 'uviogen' con la contraseña requerida.

CREATE USER IF NOT EXISTS 'uviogen'@'localhost' IDENTIFIED BY 'cviogen';
GRANT ALL PRIVILEGES ON viogen.* TO 'uviogen'@'localhost';
FLUSH PRIVILEGES;
