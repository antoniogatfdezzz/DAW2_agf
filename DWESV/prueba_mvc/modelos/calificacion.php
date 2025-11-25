<?php

	class Calificacion{
		private $path_bd;
		private $path_servicios;
		private $bd;

		private const TABLA = 'Calificacion';

		public function __construct($path_servicios, $path_bd){
			$this->path_servicios = $path_servicios;
			$this->path_bd = $path_bd;
			require_once($this->path_servicios.'pdo.php');
			$this->bd = new PDO_SQLite($this->path_bd); 
		}

		public function listar(){
			return $this->bd->listar(self::TABLA, ['id', 'alumno', 'calificacion'], 'alumno');
		}

		public function registrar(string $alumno, int $calificacion){
			$this->bd->insertar(self::TABLA, ['alumno', 'calificacion'], [$alumno, $calificacion]);
		}

	}
