DROP DATABASE IF EXISTS viogen;
CREATE DATABASE viogen;
CREATE USER IF NOT EXISTS uviogeo@localhost IDENTIFIED BY 'cviogen';
USE viogen;
CREATE TABLE Usuario(
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(64) NOT NULL,
	clave VARCHAR(128) NOT NULL,
	CHECK (LENGTH(nombre) >= 4 AND LENGTH(clave) >= 4)
);
CREATE TABLE Victima(
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(128),
	apellidos VARCHAR(128),
	tipo_documento ENUM('NIF', 'NIE', 'Pasaporte'),
	documento VARCHAR(64),
	telefono VARCHAR(128),
	observaciones TEXT,
	CHECK (nombre IS NOT NULL OR observaciones IS NOT NULL)
);
CREATE TABLE Agresion(
	id INT AUTO_INCREMENT PRIMARY KEY,
	id_victima INT NOT NULL,
	agresor TEXT,
	tipo_agresion ENUM('física', 'psicológica', 'sexual', 'vicaria') NOT NULL,
	fecha_hora DATETIME NOT NULL,
	observaciones TEXT,
	FOREIGN KEY (id_victima) REFERENCES Victima(id)
);



