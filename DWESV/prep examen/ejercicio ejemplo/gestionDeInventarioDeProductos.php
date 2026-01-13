<?php

class Producto
{
    public string $nombre;
    public float $precio;
    public int $unidades;

    public function __construct(string $nombre, float $precio, int $unidades)
    {
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->unidades = $unidades;
    }
}

class Inventario
{
    /** @var Producto[] */
    private array $productos = [];

    private string $rutaFichero;

    public function __construct(string $rutaFichero)
    {
        $this->rutaFichero = $rutaFichero;
    }

    public function cargarDesdeFichero(): void
    {
        $this->productos = [];

        if (!file_exists($this->rutaFichero)) {
            return;
        }

        $lineas = file($this->rutaFichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lineas as $linea) {
            $partes = explode(';', $linea);
            if (count($partes) !== 3) {
                continue; // línea mal formada
            }

            [$nombre, $precioStr, $unidadesStr] = $partes;

            $nombre = trim($nombre);
            $precio = (float) str_replace(',', '.', trim($precioStr));
            $unidades = (int) trim($unidadesStr);

            $this->productos[] = new Producto($nombre, $precio, $unidades);
        }
    }

    public function guardarEnFichero(): void
    {
        $lineas = [];

        foreach ($this->productos as $producto) {
            $lineas[] = $producto->nombre . ';' . $producto->precio . ';' . $producto->unidades;
        }

        file_put_contents($this->rutaFichero, implode(PHP_EOL, $lineas) . PHP_EOL);
    }

    public function anadirProducto(Producto $nuevoProducto): void
    {
        foreach ($this->productos as $producto) {
            if (strcasecmp($producto->nombre, $nuevoProducto->nombre) === 0) {
                $producto->unidades += $nuevoProducto->unidades;
                $producto->precio = $nuevoProducto->precio;
                return;
            }
        }

        $this->productos[] = $nuevoProducto;
    }

    /** @return Producto[] */
    public function getProductos(): array
    {
        return $this->productos;
    }

    public function contarProductosDiferentes(): int
    {
        return count($this->productos);
    }

    public function contarUnidadesTotales(): int
    {
        $total = 0;
        foreach ($this->productos as $producto) {
            $total += $producto->unidades;
        }
        return $total;
    }

    public function calcularValorTotalInventario(): float
    {
        $total = 0.0;
        foreach ($this->productos as $producto) {
            $total += $producto->precio * $producto->unidades;
        }
        return $total;
    }

    public function obtenerProductoConMasUnidades(): ?Producto
    {
        if (empty($this->productos)) {
            return null;
        }

        $maxProducto = $this->productos[0];
        foreach ($this->productos as $producto) {
            if ($producto->unidades > $maxProducto->unidades) {
                $maxProducto = $producto;
            }
        }

        return $maxProducto;
    }
}

function gestionDeInventarioDeProductos(): void
{
    $rutaFichero = __DIR__ . DIRECTORY_SEPARATOR . 'inventario.txt';

    if (!file_exists($rutaFichero)) {
        $contenidoInicial = [
            'Manzanas;1.25;50',
            'Peras;1.50;30',
            'Leche;0.99;100',
        ];
        file_put_contents($rutaFichero, implode(PHP_EOL, $contenidoInicial) . PHP_EOL);
    }

    $inventario = new Inventario($rutaFichero);
    $inventario->cargarDesdeFichero();

    echo "=== Resumen inicial del inventario ===" . PHP_EOL;
    $totalProductos = $inventario->contarProductosDiferentes();
    $totalUnidades = $inventario->contarUnidadesTotales();
    $valorTotal = $inventario->calcularValorTotalInventario();
    $productoTop = $inventario->obtenerProductoConMasUnidades();

    echo "Productos diferentes: {$totalProductos}" . PHP_EOL;
    echo "Unidades totales: {$totalUnidades}" . PHP_EOL;
    echo "Valor total del inventario: " . number_format($valorTotal, 2, ',', '.') . " €" . PHP_EOL;

    if ($productoTop !== null) {
        echo "Producto con más unidades: {$productoTop->nombre} ({$productoTop->unidades} unidades)" . PHP_EOL;
    } else {
        echo "No hay productos en el inventario." . PHP_EOL;
    }

    echo PHP_EOL . "Añadiendo un nuevo producto al inventario..." . PHP_EOL;

    $nuevoProducto = new Producto('Huevos', 2.10, 40);
    $inventario->anadirProducto($nuevoProducto);

    $inventario->guardarEnFichero();

    $inventario->cargarDesdeFichero();

    echo PHP_EOL . "=== Inventario completo tras la actualización ===" . PHP_EOL;
    foreach ($inventario->getProductos() as $producto) {
        echo $producto->nombre . ' - Precio: ' . number_format($producto->precio, 2, ',', '.') . ' € - Unidades: ' . $producto->unidades . PHP_EOL;
    }
}

?>
