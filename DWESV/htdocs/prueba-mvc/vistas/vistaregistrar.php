<?php

	class VistaRegistrar{
		private $path_html;

		public function __construct($path_html){
			$this->path_html = $path_html;
		}

		public function mostrar(){
			$mensaje = func_num_args() > 0 ? func_get_arg(0) : null;
			require_once($this->path_html.'registrar.html');
		}
	}
