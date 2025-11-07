# Proyecto Halloween - DIINW

## Autores
- Mario Cerro
- Antonio Gat  
- Alberto Jiménez

## Descripción
Web educativa sobre Halloween creada para el módulo de Desarrollo de Interfaces Web. El proyecto explora la historia, tradiciones y elementos característicos de esta festividad.

## Estructura del Proyecto

### Páginas HTML
1. **index.html** - Página principal con introducción a Halloween
2. **historia.html** - Historia y orígenes desde Samhain hasta la actualidad
3. **tradiciones.html** - Tradiciones y costumbres (Jack-o'-Lantern, truco o trato, etc.)
4. **dulces.html** - Tabla comparativa de dulces típicos
5. **bibliografia.html** - Referencias y fuentes consultadas

### Hojas de Estilo
- **estilos-generales.css** - Estilos base compartidos
- **flexbox-layout.css** - Layouts con Flexbox
- **grid-layout.css** - Layouts con CSS Grid
- **tablas.css** - Estilos para tablas
- **responsive.css** - Media queries para diseño responsive

## Elementos Implementados

### ✅ CSS Grid
- Implementado en `historia.html`
- Layout tipo revista con áreas diferenciadas
- Grid responsive que se adapta al tamaño de pantalla

### ✅ Flexbox
- Navegación principal en todas las páginas
- Layout de tarjetas en `index.html`
- Sistema de tarjetas en `tradiciones.html`

### ✅ Tablas
- Tabla completa en `dulces.html` con:
  - `<caption>` para el título
  - `<thead>`, `<tbody>`, `<tfoot>` para estructura semántica
  - `<th scope="col">` para accesibilidad
  - Estilos hover y alternancia de filas

### ✅ @media Queries
- En `responsive.css` con breakpoints para:
  - Móviles pequeños (max-width: 480px)
  - Tablets (max-width: 768px)
  - Tablets grandes (max-width: 1024px)
  - Pantallas grandes (min-width: 1400px)
  - Impresión (@media print)

### ✅ Etiquetas META
Incluidas en todas las páginas:
- `charset="UTF-8"`
- `viewport` para responsive
- `description` con descripción de cada página
- `keywords` con palabras clave
- `authors` con nuestros nombres

### ✅ Z-Index
- Header sticky con z-index: 100
- Tarjetas con z-index que cambia en hover
- Sidebar con z-index: 20 para mantenerse visible
- Sistema de capas en elementos interactivos

### ✅ Semántica HTML
- `<header>`, `<nav>`, `<main>`, `<footer>`
- `<article>`, `<section>`, `<aside>`
- Uso correcto de headings (h1-h3)
- Estructura lógica y accesible

### ✅ Aplicación de Estilos
He aplicado los tres tipos de estilos:
1. **En línea**: `style="..."` en el h1 de index.html
2. **Incrustados**: `<style>` en bibliografia.html
3. **Externos**: Múltiples hojas CSS vinculadas

## Características Adicionales

- **Diseño coherente**: Paleta de colores naranja/marrón oscuro temática
- **Contenido real**: Sin Lorem Ipsum, información verificada
- **Bibliografía completa**: Enlaces a fuentes reales consultadas
- **Navegación clara**: Menú en todas las páginas
- **Transiciones suaves**: Efectos hover en elementos interactivos
- **Accesibilidad**: Uso de atributos semánticos y estructura lógica

## Cómo Visualizar

1. Abrir `index.html` en un navegador
2. Navegar entre las diferentes secciones usando el menú
3. Probar la responsividad redimensionando la ventana del navegador

## Documentación de Elementos

### Página Index (index.html)
- Flexbox para layout de tarjetas
- Estilo en línea en h1
- Navegación con Flexbox
- Sección hero con gradientes

### Página Historia (historia.html)
- CSS Grid con layout tipo revista
- Z-index en sidebar sticky
- Grid responsive de 1-3 columnas
- Áreas del grid diferenciadas con clases

### Página Tradiciones (tradiciones.html)
- Flexbox para sistema de tarjetas
- Tarjetas con iconos grandes
- Z-index en hover de tarjetas
- Layout flexible que se adapta

### Página Dulces (dulces.html)
- Tabla completa con caption, thead, tbody, tfoot
- Estilos para filas alternadas
- Responsive: oculta columnas en móvil
- Sistema de popularidad visual

### Página Bibliografía (bibliografia.html)
- Estilos incrustados en `<style>`
- Referencias con enlaces externos
- target="_blank" y rel="noopener"
- Formato académico

## Tecnologías Utilizadas
- HTML5
- CSS3
- Diseño Mobile First
- Accesibilidad Web

## Fuentes de Imágenes

Todas las imágenes utilizadas en este proyecto provienen de **Unsplash**, una plataforma que ofrece fotografías de alta calidad libres de derechos de autor bajo la licencia Unsplash (uso comercial y no comercial permitido sin necesidad de atribución, aunque es apreciado).

### Lista de Imágenes a Descargar:

Las imágenes deben guardarse en la carpeta `images/` con los siguientes nombres:

#### index.html
1. **hero-calabazas.jpg** - Calabazas de Halloween iluminadas
   - Descargar de: https://unsplash.com/photos/fBdlytm6Hp8
   - URL directa: https://images.unsplash.com/photo-1509557965875-b88c97052f0e?w=1200&h=400&fit=crop
   - Fotógrafo: Iva Muškić

2. **calabaza-tallada.jpg** - Calabaza tallada clásica
   - Descargar de: https://unsplash.com/photos/Qf92nnoJ-Sw
   - URL directa: https://images.unsplash.com/photo-1477268568614-d543e0d9d5e6?w=400&h=250&fit=crop
   - Fotógrafo: Dave Hoefler

3. **ninos-disfrazados.jpg** - Niños disfrazados
   - Descargar de: https://unsplash.com/photos/KjA4nmYbrZM
   - URL directa: https://images.unsplash.com/photo-1570472324770-23a049888cf8?w=400&h=250&fit=crop
   - Fotógrafo: Jordan Rowland

4. **decoraciones-halloween.jpg** - Decoraciones de Halloween
   - Descargar de: https://unsplash.com/photos/6JR4ojH1HZ4
   - URL directa: https://images.unsplash.com/photo-1508280756091-9bdd7ef1f463?w=400&h=250&fit=crop
   - Fotógrafo: Jamie Street

#### historia.html
5. **hoguera-samhain.jpg** - Hoguera antigua
   - Descargar de: https://unsplash.com/photos/1rBg5YSi00c
   - URL directa: https://images.unsplash.com/photo-1541339907592-7c2b2f42c6b5?w=600&h=300&fit=crop
   - Fotógrafo: Mark Duffel

6. **ruinas-romanas.jpg** - Ruinas romanas antiguas
   - Descargar de: https://unsplash.com/photos/bKhETeDV1WM
   - URL directa: https://images.unsplash.com/photo-1604881991720-f91add269bed?w=400&h=250&fit=crop
   - Fotógrafo: Spencer Davis

7. **iglesia-antigua.jpg** - Iglesia antigua
   - Descargar de: https://unsplash.com/photos/c77OkdwslYE
   - URL directa: https://images.unsplash.com/photo-1509718443690-d8e2fb3474b7?w=400&h=250&fit=crop
   - Fotógrafo: Pedro Lastra

8. **puerto-inmigracion.jpg** - Puerto antiguo - inmigración
   - Descargar de: https://unsplash.com/photos/V75YEqJp4pE
   - URL directa: https://images.unsplash.com/photo-1543511791-e062815e3a46?w=400&h=250&fit=crop
   - Fotógrafo: Pedro Lastra

9. **casa-moderna-halloween.jpg** - Casa decorada para Halloween moderno
   - Descargar de: https://unsplash.com/photos/WNoLnJo7tS8
   - URL directa: https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=250&fit=crop
   - Fotógrafo: Michael Dam

#### tradiciones.html
10. **jack-o-lanterns.jpg** - Jack-o'-Lanterns iluminadas
    - Descargar de: https://unsplash.com/photos/Eg2awKSsMGs
    - URL directa: https://images.unsplash.com/photo-1569173112611-52a7cd38bea9?w=400&h=300&fit=crop
    - Fotógrafo: Toni Cuenca

11. **truco-o-trato.jpg** - Niños haciendo truco o trato
    - Descargar de: https://unsplash.com/photos/eHUMDkv4q1w
    - URL directa: https://images.unsplash.com/photo-1603056674785-d0a97a81a295?w=400&h=300&fit=crop
    - Fotógrafo: Kate Darmody

12. **disfraces-halloween.jpg** - Calabazas y ambiente Halloween
    - Descargar de: https://unsplash.com/photos/fBdlytm6Hp8
    - URL directa: https://images.unsplash.com/photo-1509557965875-b88c97052f0e?w=400&h=300&fit=crop
    - Fotógrafo: Iva Muškić

13. **decoraciones-casa.jpg** - Casa decorada para Halloween
    - Descargar de: https://unsplash.com/photos/XoCCUnqwPc8
    - URL directa: https://images.unsplash.com/photo-1542042161784-26ab9e041e89?w=400&h=300&fit=crop
    - Fotógrafo: Charles DeLoye

14. **gato-negro.jpg** - Gato negro
    - Descargar de: https://unsplash.com/photos/KbkofviRS7M
    - URL directa: https://images.unsplash.com/photo-1587318587508-33f6f2652b6f?w=400&h=300&fit=crop
    - Fotógrafo: Rémi Müller

15. **manzanas-rojas.jpg** - Manzanas rojas
    - Descargar de: https://unsplash.com/photos/mvLyHPRGLCs
    - URL directa: https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400&h=300&fit=crop
    - Fotógrafo: Priscilla Du Preez

#### dulces.html
16. **dulces-variados.jpg** - Variedad de dulces de Halloween
    - Descargar de: https://unsplash.com/photos/jJZ9Khzn6YY
    - URL directa: https://images.unsplash.com/photo-1603052875821-999fcccc2b2b?w=800&h=300&fit=crop
    - Fotógrafo: Tamas Pap

### Licencia de Imágenes
Todas las imágenes están bajo la **Licencia Unsplash** (https://unsplash.com/license):
- ✅ Uso gratuito para fines comerciales y no comerciales
- ✅ No se requiere atribución (aunque es apreciada)
- ✅ Se pueden modificar y redistribuir
- ❌ No se pueden vender las imágenes sin modificar
- ❌ No se pueden usar para crear servicios similares a Unsplash

**Fuente principal**: https://unsplash.com/
**Acerca de Unsplash**: Plataforma de fotografía gratuita con millones de imágenes de alta calidad contribuidas por una comunidad de fotógrafos.

## Fecha de Creación
31 de octubre de 2025
