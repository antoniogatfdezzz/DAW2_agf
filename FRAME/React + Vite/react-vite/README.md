# CinemaFAW (React + Vite + Bootstrap)

Recreación de la actividad Flask en un frontend SPA con React, Vite y Bootstrap. Incluye listado de películas, búsqueda por título, filtros por género/año y página de detalle.

## Requisitos

- Node.js 18+ (macOS con zsh)

## Instalar dependencias

```bash
npm install
```

Esto instalará React, Vite, Bootstrap y `react-router-dom`.

## Ejecutar en desarrollo

```bash
npm run dev
```

Abre la URL que aparece en la terminal. HMR estará activo.

## Compilar para producción

```bash
npm run build
```

Los artefactos quedarán en `dist/`.

## Estructura relevante

- `src/components/` Navbar, Footer, Layout, y componentes de películas (SearchBar, FilterBar, MovieCard, MovieList).
- `src/pages/` Páginas: Home, Movies, MovieDetail, About, Contact, NotFound.
- `src/data/mockMovies.js` Datos mock de películas usados por la app.

## Notas

Esta versión es solo frontend (no hay backend Flask). Si más adelante quieres conectar un backend, se pueden sustituir los mocks por llamadas a API.
