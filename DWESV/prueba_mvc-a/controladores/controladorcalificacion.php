<?php
	class ControladorCalificacion{
		private $config;
		private $modelo;
		
		public function __construct($config){
			$this->config = $config;
			require_once($this->config['path_modelos'].'calificacion.php');
			$this->modelo = new Calificacion($this->config['path_servicios'], $this->config['path_bd']);
		}

		public function listar(){
			$calificaciones = $this->modelo->listar();
			require_once($this->config['path_vistas'].'vistalistar.php');
			$vista = new VistaListar($this->config['path_html']);
			$vista->mostrar($calificaciones);
		}

		public function registrar(){
			//Sanitización de parámetros
			//$sanitize = []
			//	if (isset($_POST['nombre'])) {
			//		$_POST['nombre'] = htmlspecialchars($_POST['nombre']);
			//	}
			//	if (isset($_POST['calificacion'])) {
			//		$_POST['calificacion'] = htmlspecialchars($_POST['calificacion']);
			//	}

			//Validación de parámetros

			//	if (string($_POST['alumno']) <= 2) {
			//		$sanitize[] = "El nombre del alumno tiene que tener almenos 2 letras."
			//	}
			//	if (is_int($_POST['calificacion']) >= 1 ?? ($_POST['calificacion']) <= 10) {
			//		$sanitize[] = "La calificacion tiene que ser entre 1 y 10."
			//	}

			$alumno = $_POST['nombre'];
			$calificacion = $_POST['calificacion'];

			$this->modelo->registrar($alumno, $calificacion);
			$this->verRegistrar("El registro de la calificación se realizó con éxito");
		}

		public function verRegistrar($mensaje = null){
			require_once($this->config['path_vistas'].'vistaregistrar.php');
			$vista = new VistaRegistrar($this->config['path_html']);
			$vista->mostrar();
		}
	}
