---
name: create-abm-pages
description: Genera las páginas ABM (Alta, Baja, Modificación) del panel de administración para una entidad existente del DMS. Crear cuando se necesiten listados, formularios de alta/edición y eliminación en _admin_.
argument-hint: [NombreEntidadPlural]
disable-model-invocation: true
allowed-tools: Read, Write, Edit, Bash, Grep, Glob
---

# Crear ABM: $ARGUMENTS

Generar las páginas de administración para la entidad `$ARGUMENTS` en `_admin_/`.

## 1. Prerequisitos

Antes de generar las páginas, verificar que existan:

- La clase entidad en `library/class.{singular}.php`
- La clase de acceso a datos en `library/class.{plural}.php`
- La tabla `TB_{Plural}` en la base de datos

Si no existen, sugerir al usuario que ejecute primero `/create-entity`.

## 2. Archivos a generar

Todos en `_admin_/` con nombres en minúsculas usando el plural de la entidad:

| Archivo | Función | Obligatorio |
|---------|---------|:-----------:|
| `{plural}.php` | Listado/grid con todos los registros | Sí |
| `{plural}_add.php` | Formulario de alta | Sí |
| `{plural}_mod.php` | Formulario de modificación | Sí |
| `{plural}_del.php` | Confirmación y eliminación | Sí |
| `{plural}_detail.php` | Vista de solo lectura | Opcional |
| `{plural}_exportar.php` | Exportación a Excel | Opcional |
| `{plural}_pdf.php` | Generación de PDF | Opcional |

## 3. Página de listado (`{plural}.php`)

```php
<?php
require_once('../inc_library.php');

// Verificar permisos si aplica
// Session::CheckPermission('PERM_{PLURAL}');

$entidades = new {Plural}();
$filtro = new Filtro();
// Construir filtro desde $_GET si hay parámetros de búsqueda

$registros = $entidades->GetAll($filtro);
?>
```

Incluir:
- Tabla HTML con las columnas principales de la entidad
- Links a `_add.php`, `_mod.php?id=X`, `_del.php?id=X` por cada fila
- Filtros de búsqueda si la entidad tiene campos filtrables
- Usar `$j` para cualquier JavaScript (jQuery), nunca `$`

## 4. Página de alta (`{plural}_add.php`)

```php
<?php
require_once('../inc_library.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entity = new {Singular}();
    $entity->Campo1 = $_POST['Campo1'];
    // ... mapear todos los campos del POST

    $entidades = new {Plural}();
    $entidades->Insert($entity);

    header('Location: {plural}.php');
    exit;
}
?>
```

Incluir:
- Formulario HTML con `method="POST"`
- Un input por cada campo editable de la entidad
- Selects/dropdowns para foreign keys (cargar opciones desde la tabla relacionada)
- Botón submit y botón cancelar (link al listado)

## 5. Página de modificación (`{plural}_mod.php`)

```php
<?php
require_once('../inc_library.php');

$id = $_GET['id'];
$entidades = new {Plural}();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entity = new {Singular}();
    $entity->Id{Singular} = $id;
    $entity->Campo1 = $_POST['Campo1'];
    // ... mapear campos

    $entidades->Update($entity);
    header('Location: {plural}.php');
    exit;
}

$registro = $entidades->GetById($id);
?>
```

Incluir:
- Mismo formulario que alta pero con valores precargados del registro
- Hidden input con el ID de la entidad

## 6. Página de eliminación (`{plural}_del.php`)

```php
<?php
require_once('../inc_library.php');

$id = $_GET['id'];
$entidades = new {Plural}();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entidades->Delete($id);
    header('Location: {plural}.php');
    exit;
}

$registro = $entidades->GetById($id);
?>
```

Incluir:
- Mostrar datos del registro a eliminar (solo lectura)
- Formulario de confirmación con `method="POST"`
- Botón "Confirmar eliminación" y botón cancelar

## 7. Permisos y menú

- Si la entidad requiere permisos, agregar la constante en `inc_perms.php`
- Si corresponde, agregar la entrada al menú de navegación

## 8. Verificaciones finales

- [ ] Todos los archivos PHP inician con `require_once('../inc_library.php')`
- [ ] JavaScript usa `$j` para jQuery, nunca `$`
- [ ] Los formularios usan `method="POST"` para escrituras
- [ ] Los redirects post-acción usan `header('Location: ...')` + `exit`
- [ ] Los links de navegación entre páginas son consistentes
- [ ] Los selects de FK cargan sus opciones desde las tablas relacionadas

Ver [templates/abm-layout.md](templates/abm-layout.md) para la estructura HTML base del panel.
