# Proyecto Intermodular - 2º DAW
## Antonio Gat Fernández - agatf02@educarex.es

Aquí se realizarán las entregas (Splits) de la asignatura "Proyecto Intermodular".

# Intranet Arbitral

## Resumen breve

Este proyecto plantea el desarrollo de una Intranet Arbitral: una aplicación web interna destinada a gestionar los procesos, la comunicación y la documentación relacionados con la actividad arbitral (usuarios, partidos, incidencias, informes y estadísticas).

El objetivo principal es proporcionar una plataforma centralizada para que administradores y árbitros gestionen partidos, notificaciones, actuaciones y registros, facilitando trazabilidad y generación de informes.

## Funcionalidades

- Autenticación y autorización con roles (administrador, árbitro, gestor, invitado).
- Gestión de usuarios: alta/baja/edición de árbitros y otros perfiles, asignación de roles y permisos.
- Gestión de partidos: crear, editar y programar (fechas, lugares, participantes).
- Calendario y planificación: vista calendario con filtros por árbitro y estado.
- Notificaciones y comunicaciones: avisos por correo sobre designaciones o cambios.
- Panel de administración
- Exportación de liquidaciones: generación de PDFs.

## Requisitos y supuestos

- Base de datos relacional para persistencia de datos.
- Autenticación basada en sesiones según requisitos de despliegue.
- Interfaz web responsiva para uso desde escritorio y dispositivos móviles.

## Arranque rápido (MVC en `src/www`)

- Requisitos: PHP >= 7.4, MySQL 8.x o MariaDB 10.x, servidor con mod_rewrite (Apache recomendado).
- Crear base de datos y tablas:
	1. Importa `src/sql/bbdd.sql` (estructura)
	2. Importa `src/sql/datos.sql` (datos iniciales)
	3. Opcional: `src/sql/pruebas.sql` (datos de prueba)
- Configura conexión en `src/www/config.php` (BBDD_HOST, BBDD_NOMBRE, etc.).
- Sirve el directorio `src/www/` como raíz del sitio (Apache: VirtualHost apuntando a `src/www`).

Rutas principales:
- `/auth/login` Iniciar sesión (admin por defecto: email `admin@local`, contraseña `password`).
- `/usuarios/crear` Crear usuarios (administrador/árbitro) [solo admin].
- `/partidos/crear` Crear partidos [solo admin].
- `/liquidaciones/crear` Crear liquidaciones (admin o árbitro para sí mismo).
- `/liquidaciones/consultar` Listado de liquidaciones.
- `/liquidaciones/detalle?id=ID` Detalle de una liquidación.
- `/partidos/mis_designaciones` Partidos asignados al árbitro logueado.
- `/perfil/mi_perfil` Ver y actualizar perfil.

