# Viogen (Demo, sin JS)

Pequeña demo en PHP puro (sin JavaScript) para flujo de denuncias VioGén con almacenamiento en JSON (sin base de datos real) pensada para XAMPP en macOS.

## Requisitos
- PHP 7.4+ (ideal 8.x)
- XAMPP (Apache + PHP)

## Estructura
- configuración/config.php: arranque, helpers y acceso a "BD" JSON.
- modelos/*.php: funciones para víctimas, agresores, policías (seed).
- modelos/data/*.json: ficheros JSON persistentes.
- vistas/*.php: vistas de dashboard, formularios y listados.
- assets/: estilos y uploads. (No se usa JavaScript)

## Usuario demo
Se crea automáticamente un usuario:
- Email: demo@policia.es
- Contraseña: demo1234

## Cómo ejecutar en macOS (XAMPP)
1. Copia este proyecto en: /Applications/XAMPP/xamppfiles/htdocs/viogen
2. Inicia Apache desde el panel de XAMPP.
3. Abre en el navegador: http://localhost/viogen/

## Flujo
1. Login -> Dashboard
2. + Nueva denuncia
   - form-victimas.php (crear/seleccionar víctima)
   - form-denuncia.php (datos del hecho, adjuntos)
   - form-agresor.php (crear/seleccionar agresor)
   - resumen-denuncia.php (guardar expediente)

## Notas
- No se emplea ningún script JS. La navegación entre pasos y las validaciones se realizan en el servidor con sesiones (PRG).
- Los archivos subidos se guardan en assets/uploads/.
- La persistencia es en JSON para facilitar la demo; no apto para producción.
- Para reiniciar, borra los JSON en modelos/data/.
