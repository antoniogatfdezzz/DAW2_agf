# Viogen - Antonio Gat Fernández

## Estructura
- configuración/config.php: arranque, helpers y acceso a "BD" JSON.
- modelos/*.php: funciones para víctimas, agresores, policías (seed).
- modelos/data/*.json: ficheros JSON persistentes.
- vistas/*.php: vistas de dashboard, formularios y listados.
- assets/: estilos y uploads.

## Usuario demo
Se crea automáticamente un usuario:
- Email: demo@policia.es
- Contraseña: demo1234

## Flujo
1. Login -> Dashboard
2. + Nueva denuncia
   - form-victimas.php (crear/seleccionar víctima)
   - form-denuncia.php (datos del hecho, adjuntos)
   - form-agresor.php (crear/seleccionar agresor)
   - resumen-denuncia.php (guardar expediente)

## Notas
- Los archivos subidos se guardan en assets/uploads/.
- La persistencia es en JSON para facilitar la demo
- Para reiniciar, borra los JSON en modelos/data/.
