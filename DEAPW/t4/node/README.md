## URL del contenedor

https://node.antoniogatfdezpruebas.es/

## Pasos para reproducirlo

1. Asegurarse de tener el contenedor de Caddy encendido.
2. Entra en este directorio:
   cd node
3. Construye y levanta el contenedor de la aplicación Node.js:
   docker compose up -d --build
4. Accede en un navegador a:
   https://node.antoniogatfdezpruebas.es/

## Comando para ejecutar el Docker Compose

Desde el directorio node:

docker compose up -d --build

## Estructura de carpetas

- Dockerfile
- README.md
- docker-compose.yml
- package.json
- package-lock.json
- server.js
- node_modules/
