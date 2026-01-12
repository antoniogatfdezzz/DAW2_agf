## URL del contenedor

https://rust.antoniogatfdezpruebas.es/

## Pasos para reproducirlo

1. Asegurarse de tener el contenedor de Caddy encendido.
2. Entra en este directorio:
   cd rust
3. Levanta el contenedor Rust que ejecutará la aplicación `holaMundo`:
   docker compose up
4. Accede en el navegador a:
   https://rust.antoniogatfdezpruebas.es/

## Comando para ejecutar el Docker Compose

Desde el directorio rust:

docker compose up

## Estructura de carpetas

- docker-compose.yml
- holaMundo/
  - Dockerfile
  - Cargo.toml
  - Cargo.lock
  - src/
