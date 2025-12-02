<?php
require_once __DIR__ . '/../configuración/config.php';

function policias_all(): array {
    return [
        [
            'id' => 1,
            'nombre' => 'Agente Prueba',
            'email' => 'demo@policia.es',
            'password' => password_hash('demo1234', PASSWORD_DEFAULT),
            'placa' => 'P-001',
            'rango' => 'Oficial'
        ],
        [
            'id' => 2,
            'nombre' => 'Inspector García',
            'email' => 'garcia@policia.es',
            'password' => password_hash('garcia123', PASSWORD_DEFAULT),
            'placa' => 'P-105',
            'rango' => 'Inspector'
        ],
        [
            'id' => 3,
            'nombre' => 'Subinspector Martínez',
            'email' => 'martinez@policia.es',
            'password' => password_hash('martinez123', PASSWORD_DEFAULT),
            'placa' => 'P-089',
            'rango' => 'Subinspector'
        ]
    ];
}

function policia_find_by_email(string $email): ?array {
    foreach (policias_all() as $p) {
        if ($p['email'] === $email) return $p;
    }
    return null;
}
