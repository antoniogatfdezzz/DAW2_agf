## URL del contenedor

https://laravel.antoniogatfdezpruebas.es

## Pasos para reproducirlo

1. Asegurarse de tener el contenedor de Caddy encendido.
2. Entra en este directorio:
    cd laravel
3. Construye y levanta el contenedor de Laravel:
    docker compose up -d --build
4. Accede en un navegador a:
    https://laravel.antoniogatfdezpruebas.es

## Comando para ejecutar el Docker Compose

Desde el directorio laravel:

docker compose up -d --build

## Estructura de carpetas

- Dockerfile
- README.md
- docker-compose.yml
- src/
  - routes/
     - web.php
