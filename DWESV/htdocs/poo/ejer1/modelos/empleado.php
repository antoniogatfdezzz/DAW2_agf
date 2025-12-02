<?php

    abstract class Empleado {
        // Visibilidad privada para el nombre (no accesible en clases derivadas)
        private string $nombre;
        // Visibilidad protegida para salario (accesible en clases derivadas)
        protected float $salario;

        // Propiedad estática (uso de self:: para acceder)
        private static string $departamento = 'Informática';

        public function __construct(string $nombre, float $salario) {
            $this->nombre = $nombre;
            $this->salario = $salario;
        }

        // Getters / setters
        public function getNombre(): string {
            return $this->nombre;
        }
        public function getSalario(): float {
            return $this->salario;
        }
        public function setNombre(string $nombre){
            $this->nombre = $nombre;
        }
        public function setSalario(float $salario){
            $this->salario = $salario;
        }

        // Métodos estáticos para demostrar acceso con self::
        public static function getDepartamento(): string {
            return self::$departamento;
        }
        public static function setDepartamento(string $departamento): void {
            self::$departamento = $departamento;
        }

        // Método abstracto que deben implementar las subclases
        abstract public function calcularBono(): float;
    }

    