<?php
    require_once 'modelos/gerente.php';
    require_once 'modelos/desarrollador.php';

    // Mostrar departamento estático (acceso mediante métodos estáticos que usan self::)
    echo "<pre>Departamento inicial: " . Empleado::getDepartamento() . "</pre>";

    // Crear un gerente
    $gerente = new Gerente('Miguel', 57000, 8000);
    echo "<pre>Gerente: " . $gerente->getNombre() . " - Salario: " . $gerente->getSalario() . "</pre>";
    echo "<pre>Bono gerente (calcularBono): " . $gerente->calcularBono() . "</pre>";

    // Crear desarrolladores con lenguajes
    $dev1 = new Desarrollador('Ana', 40000, ['PHP', 'JavaScript']);
    $dev2 = new Desarrollador('Luis', 45000, ['PHP', 'JavaScript', 'Python']);

    echo "<pre>Desarrollador 1: " . $dev1->getNombre() . " - Lenguajes: " . implode(', ', $dev1->getLenguajes()) . " - Bono: " . $dev1->calcularBono() . "</pre>";
    echo "<pre>Desarrollador 2: " . $dev2->getNombre() . " - Lenguajes: " . implode(', ', $dev2->getLenguajes()) . " - Bono: " . $dev2->calcularBono() . "</pre>";

    // Demostración: cambiar el departamento (propiedad estática) usando el método estático
    Empleado::setDepartamento('Recursos Humanos');
    echo "<pre>Departamento cambiado: " . Empleado::getDepartamento() . "</pre>";

    // Mostrar estructuras
    echo "<pre>VAR_DUMP Gerente:\n";
    var_dump($gerente);
    echo "</pre>";
    echo "<pre>VAR_DUMP Desarrolladores:\n";
    var_dump($dev1, $dev2);
    echo "</pre>";
