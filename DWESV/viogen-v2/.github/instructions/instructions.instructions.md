---
applyTo: '**'
---
- Sigue los pasos analizando el proyecto lentamente.

- La Estructura del Proyecto debe ser la siguiente:
  -sql
    - viogen.sql - script de la base de datos
    - datos.sql - script con datos de ejemplo
  - www
    - controladores - todos los controladores del proyecto
    - modelos - todos los modelos del proyecto
    - vistas - todas las vistas del proyecto (php)
      - html - todas las vistas en html
    - index.html
    - config.html
    - .htaccess

- La aplicación se organizará a través de un menú principal al que se accederá tras el login.
- Ninguna vista será accesible si previamente no se ha realizado un login válido. En caso de intento de acceso inválido se generará el error 401.
- Todos los campos de registros deben ser sanitizados antes de ser almacenados en base de datos.
- Tras las operaciones de registro, si no se ha producido error, se volverá al menú principal con un mensaje de confirmación. En caso de error, se permanecerá en la vista de registro con un mensaje informativo.
- Todas las constantes de configuración del sistema deben estar especificadas en un fichero de configuración (config.php).
- Debe generarse la documentación técnica de la aplicación (no son necesarios los manuales de instalación ni de usuario).
- Es OBLIGATORIO utilizar el patrón MVC visto en clase así como la misma estructura de clases y directorios.
- PRHIBIDO el uso de URLs absolutas.
- No podrá utilizarse ninguna tecnología no vista en este módulo (incluida JavaScript)
