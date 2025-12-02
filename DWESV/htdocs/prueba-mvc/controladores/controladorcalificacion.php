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
			// Sanitización de parámetros
			$alumnoOriginal = $_POST['nombre'] ?? '';
			$calificacionOriginal = $_POST['calificacion'] ?? '';
			$alumno = htmlspecialchars(trim($alumnoOriginal));
			$calificacionStr = htmlspecialchars(trim($calificacionOriginal));

			// Validación de parámetros
			$errores = [];
			if(strlen($alumno) <= 2){
				$errores[] = 'El nombre del alumno debe tener más de 2 caracteres.';
			}
			if($calificacionStr === '' || !ctype_digit($calificacionStr)){
				$errores[] = 'La calificación debe ser un número entero.';
			}else{
				$calificacion = (int)$calificacionStr;
				if($calificacion < 1 || $calificacion > 10){
					$errores[] = 'La calificación debe estar entre 1 y 10.';
				}
			}

			if(!empty($errores)){
				$this->verRegistrar(implode('<br>', $errores));
				return;
			}

			try{
				$this->modelo->registrar($alumno, $calificacion);
				$this->verRegistrar('El registro de la calificación se realizó con éxito.');
			}catch(Throwable $e){
				$this->verRegistrar('Error al registrar en la base de datos: '.htmlspecialchars($e->getMessage()));
			}
		}

		public function verRegistrar($mensaje = null){
			require_once($this->config['path_vistas'].'vistaregistrar.php');
			$vista = new VistaRegistrar($this->config['path_html']);
			$vista->mostrar($mensaje);
		}
	}
