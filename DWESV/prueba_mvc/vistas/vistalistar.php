<?php

	class VistaListar{
		private $path_html;

		public function __construct($path_html){
			$this->path_html = $path_html;
		}

		public function mostrar($calificaciones){
			require_once($this->path_html.'listar.html');

			if (!empty($resultados)) {
				echo "
				<table>
                <thead>
                    <tr>
                        <th>Nombre alumno</th>
                        <th>calificacion</th>
                    </tr>
                </thead>";
                echo 
                "<tbody>";
                    foreach ($resultados as $r)
                        echo "<tr>";
                        echo "<td>"; htmlspecialchars(($r['alumno'] ?? '') echo"</td>";
                        echo "<td>"; htmlspecialchars($r['calificacion'] ?? '') echo"</td>";
                        echo "</tr>";
                    endforeach;
                echo "</tbody>";
            echo "</table>";
			}
	}