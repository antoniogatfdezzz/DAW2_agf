<?php
/**
	Para configurar el acceso a SQL en XAMPP: Edita el fichero xampp/php/php.ini, descomenta la línea ;extension=sqlite3 (quitando el ;), salva el fichero y reinicia el servidor de XAMPP.
	Además, asegúrate de que hay permisos de escritura en el directorio de la base de datos para todos los usuarios y que hay permisos de escritura sobre el fichero de base de datos para cualquier usuario.
**/

	class PDO_SQLite{
		private $conexion;

		public function __construct($path_bd){
			if(!is_file($path_bd)){
				// Crear fichero si no existe
				$touch = @touch($path_bd);
				if(!$touch){
					throw new Exception("No se puede crear el fichero de base de datos en $path_bd");
				}
			}
			$this->conexion = new PDO('sqlite:'.$path_bd);
			$this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conexion->exec('CREATE TABLE IF NOT EXISTS Calificacion (id INTEGER PRIMARY KEY AUTOINCREMENT, alumno TEXT NOT NULL, calificacion INTEGER NOT NULL)');
		}

		public function listar(string $tabla, array $campos, string $orden = null){
			$listaCampos = implode(',', array_map(fn($c) => $c, $campos));
			$sql = "SELECT $listaCampos FROM $tabla";
			if($orden){
				$sql .= " ORDER BY ".$orden;
			}
			$stmt = $this->conexion->query($sql);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function insertar(string $tabla, array $campos, array $valores){
			if(count($campos) !== count($valores)){
				throw new InvalidArgumentException('Número de campos y valores no coincide');
			}
			$listaCampos = implode(',', $campos);
			$placeholders = implode(',', array_fill(0, count($valores), '?'));
			$sql = "INSERT INTO $tabla ($listaCampos) VALUES ($placeholders)";
			$stmt = $this->conexion->prepare($sql);
			$stmt->execute($valores);
		}

	}
