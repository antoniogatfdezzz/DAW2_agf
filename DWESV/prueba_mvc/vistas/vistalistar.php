<?php

	class VistaListar{
		private $path_html;

		public function __construct($path_html){
			$this->path_html = $path_html;
		}

		public function mostrar($calificaciones){
			if(is_array($calificaciones) && count($calificaciones) > 0){
				$html = '<table border="1" cellpadding="5" cellspacing="0">';
				$html .= '<thead><tr><th>ID</th><th>Alumno</th><th>Calificación</th></tr></thead><tbody>';
				foreach($calificaciones as $c){
					$html .= '<tr>';
					$html .= '<td>'.htmlspecialchars($c['id']).'</td>';
					$html .= '<td>'.htmlspecialchars($c['alumno']).'</td>';
					$html .= '<td>'.htmlspecialchars($c['calificacion']).'</td>';
					$html .= '</tr>';
				}
				$html .= '</tbody></table>';
			}else{
				$html = '<p>No se han encontrado resultados.</p>';
			}

			require_once($this->path_html.'listar.html');
		}
	}
