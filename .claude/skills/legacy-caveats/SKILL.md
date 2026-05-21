---
name: legacy-caveats
description: Gotchas y restricciones críticas del stack legacy PHP 5.6 del DMS. Cargar automáticamente al trabajar con código PHP o JavaScript del proyecto para evitar errores comunes.
user-invocable: false
paths:
  - "**/*.php"
  - "**/*.js"
---

# Restricciones del Stack Legacy DMS

Estas reglas son críticas y deben respetarse **siempre** al escribir o modificar código en este proyecto.

## PHP 5.6

- **Sin Composer**: no hay autoload PSR-4. Las dependencias se cargan con `require_once`
- **Sin type hints modernos**: no usar return types (`: string`), nullable types (`?int`), ni typed properties
- **Sin null coalescing**: no usar `??`. Usar `isset($x) ? $x : $default`
- **Sin spaceship operator**: no usar `<=>`. Usar comparaciones tradicionales
- **Sin array destructuring**: no usar `[$a, $b] = $arr`. Usar `list($a, $b) = $arr`
- **Sin anonymous classes**: no existen en PHP 5.6
- **Sin `strict_types`**: no usar `declare(strict_types=1)`
- **Autoload legacy**: la función `__autoload` en `inc_library.php` carga clases desde `library/`

## MySQL y SQL

- **Sin prepared statements**: no hay PDO ni mysqli con bindings en este proyecto. Los valores se pasan directamente en las queries
- **Escapar valores**: usar los métodos de la clase `DB` para escapar, o escapar manualmente
- **Charset `latin1`**: la base de datos usa `latin1`, no `utf8`. Cuidado con acentos, ñ y caracteres especiales en strings literales SQL
- **Case-insensitive**: `lower_case_table_names=1` está activo, pero respetar siempre la convención PascalCase en el código
- **`ONLY_FULL_GROUP_BY` deshabilitado**: las queries con GROUP BY no requieren que todos los campos estén en el GROUP BY o en funciones de agregación. No cambiar este comportamiento

## JavaScript: jQuery + Prototype

- **`$j` para jQuery**: SIEMPRE usar `$j` para jQuery. El operador `$` está reservado para Prototype.js
- **`$` para Prototype**: `$('id')` es Prototype (equivale a `document.getElementById`). `$$('.clase')` es el selector CSS de Prototype
- **No mezclar**: no usar métodos de jQuery sobre elementos Prototype ni viceversa
- **Orden de carga**: Prototype.js se carga primero, luego jQuery con `jQuery.noConflict()`
- **AJAX propio**: para requests al backend, usar `SendXMLRequest()` en lugar de `$j.ajax()` directamente

## Archivos y encoding

- **Archivos PHP**: sin BOM, sin espacios antes de `<?php`
- **Charset en HTML**: usar `charset="iso-8859-1"` (consistente con `latin1`)
- **Paths**: todos los includes son relativos. Los archivos en `_admin_/` usan `../` para subir a la raíz del repo
- **Iniciar siempre con**: `require_once('../inc_library.php')` en páginas de `_admin_/`, o `require_once('inc_library.php')` desde la raíz

## Errores comunes a evitar

1. Usar `$` en lugar de `$j` para jQuery (rompe Prototype)
2. Usar sintaxis PHP 7+ (short closures, null coalescing, typed properties)
3. Olvidar `ENGINE=InnoDB DEFAULT CHARSET=latin1` en CREATE TABLE
4. Usar UTF-8 en archivos cuando la BD es latin1
5. Hacer `require_once` con path incorrecto (no hay autoload PSR-4)
6. Olvidar `exit` después de `header('Location: ...')`
