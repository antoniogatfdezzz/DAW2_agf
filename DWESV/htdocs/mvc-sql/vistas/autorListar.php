<?php

class AutorListar {
	private $config;

	public function __construct($config) {
		$this->config = $config;
	}

	public function mostrar($autores) {
		$ruta = $this->config['dir_html'] . 'autor_listar.html';
		if (file_exists($ruta)) {
			include $ruta;
			return;
		}
		echo 'Listado de autores';
	}
}

