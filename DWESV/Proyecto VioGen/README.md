# 🛡️ Proyecto VioGén - Sistema de Valoración Policial del Riesgo

Sistema completo de gestión y valoración de riesgo para víctimas de violencia de género, implementando el protocolo VPR 5.0-H (Valoración Policial del Riesgo) y VPER (Valoración Policial de Evolución del Riesgo).

## 📋 Tabla de Contenidos

1. [Descripción](#descripción)
2. [Características](#características)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Instalación](#instalación)
5. [Uso](#uso)
6. [Algoritmo de Valoración](#algoritmo-de-valoración)
7. [35 Indicadores VPR](#35-indicadores-vpr)
8. [Usuarios de Prueba](#usuarios-de-prueba)
9. [Tecnologías](#tecnologías)

---

## 🎯 Descripción

El **Proyecto VioGén** es un sistema integral diseñado para:

- Registrar y gestionar víctimas y agresores de violencia de género
- Realizar valoraciones de riesgo mediante el protocolo VPR 5.0-H
- Realizar seguimientos mediante VPER
- Calcular automáticamente el nivel de riesgo usando un algoritmo actuarial
- Gestionar medidas de protección
- Registrar evidencias y documentación
- Realizar auditoría completa de todas las acciones

## ✨ Características

### Para Policías/Evaluadores:
- ✅ Dashboard con estadísticas en tiempo real
- ✅ Registro completo de víctimas con todos los campos requeridos
- ✅ Registro completo de agresores con historial
- ✅ Formulario VPR con 35 indicadores oficiales
- ✅ Cálculo automático de riesgo (5 niveles)
- ✅ Ajuste manual del nivel (actuarial ajustado)
- ✅ Programación automática de próxima valoración VPER
- ✅ Alertas de casos pendientes de revisión
- ✅ Sistema de auditoría completo

### Para Víctimas:
- ✅ Acceso a su información personal
- ✅ Consulta de valoraciones
- ✅ Información de medidas de protección activas
- ✅ Acceso a recursos de ayuda

### Sistema:
- ✅ Autenticación segura con roles (Admin, Policía, Víctima)
- ✅ Base de datos simulada con arrays PHP (persistencia en JSON)
- ✅ Sistema de auditoría completo
- ✅ Interfaz responsive
- ✅ Protección de datos

## 📁 Estructura del Proyecto

```
Proyecto VioGén/
│
├── config.php                      # Configuración global, constantes, funciones auxiliares
├── procesar_login.php              # Autenticación de usuarios
├── cerrar_sesion.php               # Cierre de sesión
│
├── modelos/                        # Capa de datos (simulación BD con arrays)
│   ├── victimas.php                # CRUD de víctimas
│   ├── agresores.php               # CRUD de agresores
│   ├── policias.php                # CRUD de policías/evaluadores
│   ├── valoraciones.php            # Sistema VPR/VPER con algoritmo de riesgo
│   ├── data_victimas.json          # Persistencia víctimas
│   ├── data_agresores.json         # Persistencia agresores
│   ├── data_policias.json          # Persistencia policías
│   └── data_valoraciones.json      # Persistencia valoraciones
│
├── vistas/                         # Interfaz de usuario (HTML/CSS/PHP)
│   ├── login.html                  # Página de inicio de sesión
│   ├── estilos.css                 # Estilos globales
│   ├── dashboard_policia.php       # Panel principal policía
│   ├── dashboard_victima.php       # Panel principal víctima
│   ├── registrar_victima.php       # Formulario registro víctima
│   ├── registrar_agresor.php       # Formulario registro agresor
│   ├── nueva_valoracion.php        # Formulario VPR (35 indicadores)
│   ├── ver_valoracion.php          # Detalle de valoración
│   ├── victimas_lista.php          # Lista de víctimas
│   ├── agresores_lista.php         # Lista de agresores
│   ├── valoraciones_lista.php      # Lista de valoraciones
│   └── pendientes.php              # Casos pendientes VPER
│
├── logs/                           # Registros de auditoría
│   └── auditoria_YYYY-MM.log       # Logs mensuales en JSON
│
├── uploads/                        # Archivos adjuntos
│   ├── evidencias/                 # Evidencias (fotos, capturas)
│   └── partes_medicos/             # Partes médicos
│
└── README.md                       # Esta documentación
```

## 🚀 Instalación

### Requisitos Previos
- PHP 7.4 o superior
- Servidor web (Apache, Nginx) o PHP built-in server
- Permisos de escritura en carpetas: `logs/`, `uploads/`, `modelos/`

### Pasos de Instalación

1. **Clonar o copiar el proyecto** en tu directorio web:
```bash
cd /ruta/a/tu/servidor/web
# Copiar la carpeta "Proyecto VioGen"
```

2. **Verificar permisos**:
```bash
chmod -R 755 "Proyecto VioGen"
chmod -R 777 "Proyecto VioGen/logs"
chmod -R 777 "Proyecto VioGen/uploads"
chmod -R 777 "Proyecto VioGen/modelos"
```

3. **Iniciar servidor** (si usas PHP built-in):
```bash
cd "Proyecto VioGen"
php -S localhost:8000
```

4. **Acceder a la aplicación**:
```
http://localhost:8000/vistas/login.html
```

## 👤 Usuarios de Prueba

### Policía 1:
- **Usuario:** `mgarcia`
- **Contraseña:** `policia123`
- **Rol:** Policía
- **Unidad:** Unidad de Violencia de Género - Madrid Centro

### Policía 2:
- **Usuario:** `cmartinez`
- **Contraseña:** `policia123`
- **Rol:** Policía
- **Unidad:** Unidad de Violencia de Género - Madrid Norte

### Administrador:
- **Usuario:** `admin`
- **Contraseña:** `admin123`
- **Rol:** Administrador
- **Unidad:** Administración Central

## 🎓 Uso del Sistema

### 1. Inicio de Sesión
- Acceder a `login.html`
- Seleccionar tipo de usuario (Policía / Víctima)
- Introducir credenciales
- El sistema redirige al dashboard correspondiente

### 2. Registrar una Nueva Víctima
1. Dashboard → "Registrar Víctima"
2. Completar formulario con datos obligatorios:
   - Nombre, apellidos, documento
   - Fecha de nacimiento
   - Domicilio
   - Teléfono
3. Campos opcionales: salud, situación económica, menores, etc.
4. Guardar

### 3. Crear una Valoración VPR
1. Dashboard → "Nueva Valoración VPR"
2. Seleccionar víctima y agresor (o crear nuevos)
3. **Sección A: Metadatos**
   - Fuentes de información
   - Nivel de confianza
   - Limitaciones
4. **Sección B-C: Relación y contexto**
   - Duración relación
   - Fecha ruptura
   - Convivencia
5. **Sección E: Datos del hecho**
   - Tipo de violencia
   - Fecha y lugar
   - Descripción detallada
   - Evidencias
6. **Sección F: 35 Indicadores VPR**
   - Marcar todos los indicadores presentes (sí/no)
   - Proporcionar detalles para cada uno
7. **Resultado automático:**
   - Sistema calcula puntuación
   - Asigna nivel de riesgo
   - Programa próxima VPER

### 4. Ajuste Manual del Nivel
- Si el evaluador considera que el nivel automático no refleja la realidad
- Puede ajustarlo manualmente
- Debe proporcionar una razón
- Queda registrado en auditoría

## 🧮 Algoritmo de Valoración

### Sistema de Puntuación

El algoritmo asigna **pesos** a cada indicador:

| Categoría | Peso | Ejemplos |
|-----------|------|----------|
| **CRÍTICO / LETALIDAD** | 8 pts | Sexo forzado, Uso de armas, Amenazas de muerte, Estrangulamiento |
| **ALTO** | 6 pts | Violencia física, Escalada 6 meses, Quebrantamientos, Antecedentes |
| **MEDIO** | 4 pts | Celos, Control, Acoso, Adicciones |
| **BAJO** | 2 pts | Carencia apoyo social, Agresor <24 años |
| **POSITIVO** | -4 pts | Distanciamiento, Colaboración, Cumplimiento medidas |

### Umbrales de Nivel

| Puntuación | Nivel | Color | Próxima VPER |
|------------|-------|-------|--------------|
| 0 - 9 pts | **No Apreciado** | 🟢 Verde | 90 días |
| 10 - 19 pts | **Bajo** | 🟡 Verde claro | 60 días |
| 20 - 29 pts | **Medio** | 🟠 Amarillo | 30 días |
| 30 - 44 pts | **Alto** | 🔴 Naranja | 7 días |
| ≥ 45 pts | **Extremo** | 🔴 Rojo | 72 horas |

### Fórmula

```
Puntuación Total = Σ (Indicador_presente × Peso)
                  + Ajustes especiales (percepción víctima, etc.)
                  - Indicadores positivos

Nivel = calcularNivelRiesgo(Puntuación Total)
```

## 📊 35 Indicadores VPR

### Factor 1: Historia de Violencia (I-1 a I-6)
1. **I1** - Violencia psicológica (insultos, humillaciones)
2. **I2** - Violencia física ⚠️ CRÍTICO
3. **I3** - Sexo forzado ⚠️ LETALIDAD
4. **I4** - Uso de armas/objetos ⚠️ LETALIDAD
5. **I5** - Amenazas/planes de daño ⚠️ LETALIDAD
6. **I6** - Escalada en últimos 6 meses ⚠️ CRÍTICO

### Factor 2: Características del Agresor (I-7 a I-23)
7. **I7** - Celos exagerados
8. **I8** - Conductas de control
9. **I9** - Acoso persistente
10. **I10** - Problemas personales recientes
11. **I11** - Daños materiales
12. **I12** - Faltas de respeto a autoridad
13. **I13** - Agresiones a terceros/animales
14. **I14** - Amenazas a terceros
15. **I15** - Antecedentes penales ⚠️ CRÍTICO
16. **I16** - Quebrantamientos previos ⚠️ CRÍTICO
17. **I17** - Antecedentes agresiones físicas/sexuales ⚠️ CRÍTICO
18. **I18** - Violencia con otra pareja
19. **I19** - Trastorno mental
20. **I20** - Intentos/ideas suicidas agresor
21. **I21** - Adicciones
22. **I22** - Antecedentes familiares violencia
23. **I23** - Agresor menor de 24 años

### Factor 3: Vulnerabilidades Víctima (I-24 a I-28)
24. **I24** - Discapacidad víctima
25. **I25** - Intentos/ideas suicidas víctima
26. **I26** - Adicciones víctima
27. **I27** - Carencia de apoyo social
28. **I28** - Víctima extranjera

### Factor 4: Menores (I-29 a I-31)
29. **I29** - Víctima tiene menores
30. **I30** - Violencia/amenaza a menores
31. **I31** - Víctima teme por menores

### Factor 5: Circunstancias Agravantes (I-32 a I-35)
32. **I32** - Ha denunciado a otros agresores
33. **I33** - Violencia lateral/recíproca
34. **I34** - Ruptura reciente (<6 meses)
35. **I35** - Víctima cree que agresor puede matarla ⚠️ LETALIDAD

### Indicadores Adicionales
- **I36** - Percepción de riesgo de la víctima (+6 pts si alto)
- **I37** - Evaluador acuerda con víctima
- **Escala-H** - Estrangulamiento, Intento homicidio previo, Amenaza muerte explícita

## 🔒 Seguridad y Auditoría

### Sistema de Auditoría
Todas las acciones quedan registradas en logs JSON:

```json
{
    "fecha_hora": "2025-10-16 14:30:00",
    "usuario": "mgarcia",
    "accion": "VPR_CREADA",
    "detalles": "Nueva valoración VPR: VPR20251016143012345 - Nivel: Alto",
    "ip": "192.168.1.100"
}
```

### Acciones Auditadas:
- Login exitoso/fallido
- Creación/modificación de víctimas
- Creación/modificación de agresores
- Creación de valoraciones VPR/VPER
- Ajustes manuales de nivel
- Acceso a información sensible
- Cierre de sesión

### Protección de Datos:
- Contraseñas hasheadas con `password_hash()`
- Sanitización de entradas con `htmlspecialchars()`
- Control de acceso por roles
- Sesiones seguras PHP

## 🛠️ Tecnologías

- **Backend:** PHP 7.4+
- **Frontend:** HTML5, CSS3, JavaScript vanilla
- **Almacenamiento:** Arrays PHP + JSON (persistencia)
- **Servidor:** Apache / Nginx / PHP built-in server
- **Seguridad:** password_hash, sessions, sanitización

## 📞 Contacto de Emergencia

### 🚨 NÚMEROS DE EMERGENCIA
- **016** - Atención a víctimas de violencia de género (no deja rastro en factura)
- **112** - Emergencias
- **091** - Policía Nacional
- **062** - Guardia Civil

## 📝 Notas Técnicas

### Persistencia de Datos
Los datos se guardan automáticamente en archivos JSON en la carpeta `modelos/`:
- `data_victimas.json`
- `data_agresores.json`
- `data_policias.json`
- `data_valoraciones.json`

### Estructura de Datos
Cada modelo tiene funciones CRUD completas:
- `crear*()`
- `buscar*PorId()`
- `actualizar*()`
- `desactivar*()`
- `obtenerTodos*()`

### Extensibilidad
El sistema está preparado para:
- Migrar a base de datos MySQL/PostgreSQL
- Añadir más indicadores VPER
- Integrar con sistemas externos
- Generar informes PDF
- Enviar notificaciones automáticas

## 🎓 Cumplimiento Normativo

Este proyecto implementa:
- ✅ VPR 5.0-H (35 indicadores oficiales)
- ✅ VPER (indicadores de evolución)
- ✅ Escala-H (letalidad)
- ✅ Algoritmo actuarial ajustado
- ✅ Plazos de revaloración según nivel
- ✅ Auditoría completa
- ✅ Protección de datos

## 📄 Licencia

Proyecto educativo - Desarrollo Web en Entorno Servidor (DWES)

---

**Desarrollado por:** Antonio Gat Fernández  
**Fecha:** Octubre 2025  
**Curso:** DAW2 - DWES  
**Centro:** [Tu Centro Educativo]

🛡️ **VioGén - Protegiendo vidas, gestionando riesgos**
