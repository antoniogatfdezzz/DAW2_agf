<?php
    require_once 'empleado.php';

    class Gerente extends Empleado {
        // bonus específico del gerente (privado)
        private float $bonus;

        // Sobrescribimos el constructor y llamamos al padre
        public function __construct(string $nombre, float $salario, float $bonus) {
            parent::__construct($nombre, $salario);
            $this->bonus = $bonus;
        }

        // Implementación de calcularBono: salario + bonus
        public function calcularBono(): float {
            // usamos el getter para obtener el salario (demostración)
            return $this->getSalario() + $this->bonus;
        }
    }
