## URL del contenedor

https://silver.antoniogatfdezpruebas.es/

## Pasos para reproducirlo

1. Crea (si no existen) las redes Docker externas necesarias:
   docker network create caddy
   docker network create interna
2. Desde la raíz del proyecto, levanta el proxy Caddy (si no está ya en marcha):
   docker compose -f caddy/docker-compose.yml up -d
3. Entra en este directorio:
   cd silver
4. Levanta el contenedor de SilverBullet:
   docker compose up
5. Accede en el navegador a:
   https://silver.antoniogatfdezpruebas.es/

## Comando para ejecutar el Docker Compose

Desde el directorio silver:

docker compose up

## Estructura de carpetas

- Caddyfile
- docker-compose.yml
- space/
  - CONFIG.md
  - index.md
