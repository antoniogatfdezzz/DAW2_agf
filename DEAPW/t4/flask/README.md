## URL del contenedor

https://flask.antoniogatfdezpruebas.es/

## Pasos para reproducirlo

1. Asegurarse de tener el contenedor de Caddy encendido.
2. Entra en este directorio:
   cd flask
3. Construye y levanta el contenedor de la aplicación Flask:
   docker compose up -d --build
4. Accede en el navegador a:
   https://flask.antoniogatfdezpruebas.es/

## Comando para ejecutar el Docker Compose

Desde el directorio flask:

docker compose up -d --build

## Estructura de carpetas

- Dockerfile
- app.py
- docker-compose.yml
- requirements.txt
