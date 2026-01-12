## URL del contenedor

https://tomcat.antoniogatfdezpruebas.es/

## Pasos para reproducirlo

1. Asegurarse de tener el contenedor de Caddy encendido.
2. Entra en este directorio:
   cd tomcat
3. Levanta el contenedor Tomcat que desplegará las aplicaciones de la carpeta `aplicaciones`:
   docker compose up
4. Accede en el navegador a:
   https://tomcat.antoniogatfdezpruebas.es/

## Comando para ejecutar el Docker Compose

Desde el directorio tomcat:

docker compose up

## Estructura de carpetas

- docker-compose.yml
- aplicaciones/
  - ROOT/
