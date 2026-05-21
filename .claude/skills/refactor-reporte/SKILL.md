---
name: refactor-reporte
description: Auditar y refactorizar un reporte existente del DMS legacy. Revisa seguridad, performance, bugs, mantenibilidad y genera un plan de accion con las correcciones necesarias.
---

# Refactor de reporte existente

Audita un archivo de reporte contra las reglas del sistema y genera un plan de correcciones.

## Input

El usuario debe indicar el archivo a auditar. Ejemplo: `/refactor-reporte src/_admin_/ordenestrabajo_reporte.php`

Si no indica archivo, preguntar cual reporte quiere auditar.

## Proceso

### Paso 1: Leer el reporte y su backend

1. Leer el archivo de reporte indicado.
2. Identificar la clase de acceso a datos que usa (buscar las instancias `new XxxEntidades()`).
3. Leer los metodos de consulta relevantes en `src/library/class.{entidades}.php` (buscar `GetTotalReporte`, `GetAllReporte`, `ParseFilter`, etc.).
4. Si existe archivo de exportacion asociado (`*_exportar.php`), leerlo tambien.

### Paso 2: Ejecutar checklist de auditoria

Revisar cada punto del checklist (ver seccion abajo). Para cada item, clasificar como:

- **CRITICO** — Bug o vulnerabilidad que puede causar errores en produccion
- **ALTO** — Problema de seguridad o performance con impacto real
- **MEDIO** — Mejora de mantenibilidad o limpieza significativa
- **BAJO** — Limpieza cosmetica o buena practica menor

### Paso 3: Presentar hallazgos

Mostrar al usuario una tabla resumen con todos los hallazgos, mostrando para cada uno: prioridad, descripcion corta, linea(s) afectada(s), y ejemplo del codigo actual vs propuesto.

### Paso 4: Generar plan de accion

Agrupar las correcciones en pasos ordenados por prioridad (critico primero) y presentar el plan completo con ejemplos de codigo, similar al formato de `.planning/REFACTOR-STOCKMOVIMIENTOS-REPORTE.md`.

Preguntar al usuario si quiere ejecutar las correcciones.

## Checklist de auditoria

### Seguridad

| # | Check | Que buscar | Prioridad si falla |
|---|-------|-----------|-------------------|
| S1 | `$_REQUEST` con `isset()` | Todo acceso a `$_REQUEST` debe tener `isset()` con valor default | ALTO |
| S2 | `intval()` en IDs numericos | Campos como `IdUbicacion`, `IdArticulo`, `Page` deben pasar por `intval()` | ALTO |
| S3 | `htmlspecialchars()` en HTML | Todo valor impreso en atributos `value=""`, URLs, o contenido de celdas que venga del usuario | ALTO |
| S4 | Sanitizacion en queries | Verificar que se use `DB::Number()`, `DB::Date()`, `DB::String()` en queries. Buscar concatenacion directa de `$_REQUEST` en SQL | CRITICO |
| S5 | XSS en JavaScript inline | Valores PHP dentro de `<script>` deben estar escapados | ALTO |

### Bugs

| # | Check | Que buscar | Prioridad si falla |
|---|-------|-----------|-------------------|
| B1 | Division por cero | Toda division debe verificar denominador != 0 antes de dividir | CRITICO |
| B2 | URLs incorrectas | Funciones como `ClearFilter()` que redirigen a otro archivo en vez del actual | MEDIO |
| B3 | Side-effects en filtro | El array `$filter` se modifica entre llamadas sin resetear claves previas | ALTO |
| B4 | Funciones JS invocadas | Verificar que toda funcion JS definida en `<script>` se invoque en el HTML. Si no se invoca, es codigo muerto | MEDIO |
| B5 | `onSubmit` sin funcion | Si el form tiene `onSubmit="Funcion();"`, verificar que la funcion exista | MEDIO |

### Performance

| # | Check | Que buscar | Prioridad si falla |
|---|-------|-----------|-------------------|
| P1 | `NOT IN (SELECT ...)` | Subconsultas `NOT IN` deben reemplazarse por `LEFT JOIN ... IS NULL` (~20% mejora medida) | ALTO |
| P2 | Objetos no usados | Clases instanciadas con `new` que nunca se usan en el archivo. Cada una puede abrir conexion a BD | MEDIO |
| P3 | Queries repetitivos | Multiples llamadas al mismo metodo de consulta que podrian agruparse o encapsularse en un wrapper | MEDIO |
| P4 | Variables no usadas | Variables asignadas (`$arr = array()`, `$oPage`, `$filterStyle`) que nunca se leen | BAJO |

### Mantenibilidad

| # | Check | Que buscar | Prioridad si falla |
|---|-------|-----------|-------------------|
| M1 | HTML duplicado | Bloques HTML repetidos que solo varian en la variable de datos. Deben refactorizarse con loop `foreach` | MEDIO |
| M2 | IDs hardcodeados | Numeros magicos en queries (ej: `IdCliente NOT IN (827, 703, 716)`). Deben ser constantes en `Config` | MEDIO |
| M3 | Codigo muerto PHP | Bloques comentados (`/* ... */`, `//`), variables asignadas y no usadas, `print_r` de debug | BAJO |
| M4 | Codigo muerto JS | Funciones JS definidas pero nunca invocadas en el HTML del archivo | MEDIO |
| M5 | Paginador comentado | Bloques de paginacion comentados que confunden sobre si el reporte pagina o no | BAJO |

### HTML / Formato

| # | Check | Que buscar | Prioridad si falla |
|---|-------|-----------|-------------------|
| H1 | `</head>` antes de `<body>` | Falta la etiqueta de cierre `</head>` | BAJO |
| H2 | `<div>` fuera de `<td>` | Elementos `<div>` directamente dentro de `<table>` sin estar en una celda | BAJO |
| H3 | Formato de montos | Montos deben usar `number_format($monto, 2, ',', '.')` (formato argentino) | MEDIO |
| H4 | jQuery usa `$j` | JavaScript debe usar `$j` para jQuery, nunca `$` (reservado para Prototype.js) | ALTO |
| H5 | Clases CSS del sistema | Verificar uso de clases estandar: `bordeGris`, `bordeGrisFondo`, `bordeGrisTitulo`, `camporFormularioSuggest`, `botonBasico` | BAJO |

## Formato de salida del plan

Generar un documento `.planning/REFACTOR-{ENTIDAD}-REPORTE.md` con esta estructura:

```markdown
# Refactoring: {archivo}

**Archivo principal:** `src/_admin_/{archivo}`
**Archivo backend:** `src/library/class.{entidades}.php`
**Fecha:** {fecha}

---

## Resumen

{descripcion breve del refactoring}

---

## Paso N — {categoria} ({nivel de riesgo})

### [ ] Tarea N: {descripcion}

**Archivo:** `{path}` (lineas X-Y)

**Problema:** {explicacion}

**Actual:**
{codigo actual}

**Cambiar a:**
{codigo propuesto}

**Justificacion:** {por que}

---

## Resumen de progreso

| Paso | Tareas | Estado |
|------|--------|--------|
| 1. ... | T1, T2 | [ ] Pendiente |
```

## Ejemplo de referencia

Ver `.planning/REFACTOR-STOCKMOVIMIENTOS-REPORTE.md` para un ejemplo completo de plan de refactoring ejecutado exitosamente.
