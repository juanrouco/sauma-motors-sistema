# Layout base para páginas ABM

Estructura HTML estándar que deben seguir todas las páginas del panel `src/_admin_/`.

## Estructura general

```php
<?php
require_once('../inc_library.php');

// Verificar sesión activa
Session::Initialize();

// Lógica PHP (queries, procesamiento POST, etc.)
// ...
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="iso-8859-1">
    <title>DMS - {Título de la página}</title>
    <!-- CSS del proyecto -->
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Contenido principal -->

    <!-- Scripts al final -->
    <script src="../js/prototype.js"></script>
    <script src="../js/jquery.js"></script>
    <script>
        var $j = jQuery.noConflict();
        // JavaScript de la página usando $j para jQuery
    </script>
</body>
</html>
```

## Notas importantes

- El charset del HTML es `iso-8859-1` (consistente con `latin1` de la BD)
- Prototype.js se carga antes que jQuery
- jQuery se usa siempre via `$j`, nunca `$`
- Revisar las páginas ABM existentes en `src/_admin_/` para copiar la estructura de includes y header/footer que ya use el proyecto
