## URL del contenedor

https://apache.antoniogatfdezpruebas.es/

## Pasos para reproducirlo

1. Asegurarse de tener el contenedor de Caddy encendido.
2. Entra en este directorio:
   cd apache
3. Levanta el contenedor Apache que sirve el contenido estático de la carpeta `www`:
   docker compose up
4. Accede en el navegador a:
   https://apache.antoniogatfdezpruebas.es/

## Comando para ejecutar el Docker Compose

Desde el directorio apache:

docker compose up

## Estructura de carpetas

- docker-compose.yml
- www/
  - index.html
  - assets/
    - css/
      - style.css
    - images/
    - js/
      - script.js
      - tasks.js
      - tresenraya.js
