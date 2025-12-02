<?php
    require_once 'empleado.php';

    class Desarrollador extends Empleado {
        // Lista de lenguajes que domina el desarrollador
        private array $lenguajes;

        // Constructor: heredamos nombre y salario del padre y añadimos lenguajes
        public function __construct(string $nombre, float $salario, array $lenguajes = []) {
            parent::__construct($nombre, $salario);
            $this->lenguajes = $lenguajes;
        }

        public function addLenguaje(string $lenguaje): void {
            $this->lenguajes[] = $lenguaje;
        }
        public function getLenguajes(): array {
            return $this->lenguajes;
        }

        // Implementación de calcularBono: salario + 10% por cada lenguaje
        public function calcularBono(): float {
            // NOTA: accedemos directamente a $this->salario para demostrar protected
            $cantidad = count($this->lenguajes);
            $incremento = $this->salario * 0.10 * $cantidad;
            return $this->salario + $incremento;
        }
    }
