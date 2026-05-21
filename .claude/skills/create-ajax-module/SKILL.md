---
name: create-ajax-module
description: Crea un módulo AJAX para xmlhelper con sus comandos PHP y las llamadas JavaScript correspondientes. Usar cuando se necesite agregar funcionalidad AJAX al DMS.
argument-hint: [NombreModulo]
disable-model-invocation: true
allowed-tools: Read, Write, Edit, Bash, Grep, Glob
---

# Crear Módulo AJAX: $ARGUMENTS

Generar un módulo para el sistema de xmlhelper que maneja las peticiones AJAX del DMS.

## 1. Cómo funciona el sistema AJAX

El archivo `xml/xmlhelper.php` actúa como endpoint central. Recibe requests con un nombre de módulo y un comando, carga el módulo correspondiente desde `modules/` y ejecuta el método indicado.

## 2. Crear el archivo del módulo

Archivo: `modules/module.{nombre_minusculas}.php`

```php
<?php
class Module{$ARGUMENTS}
{
    /**
     * Descripción del comando
     * @param array $params Parámetros recibidos del request AJAX
     * @return mixed Datos que se convertirán a XML de respuesta
     */
    public function NombreComando($params)
    {
        // Acceder a parámetros
        $valor = $params['nombre_param'];

        // Usar clases de acceso a datos
        $entidades = new Entidades();
        $resultado = $entidades->GetById($valor);

        // Retornar datos (se serializan a XML automáticamente)
        return $resultado;
    }

    public function OtroComando($params)
    {
        // Otro comando del módulo
    }
}
?>
```

## 3. Convenciones del módulo

- **Nombre de archivo**: `module.{nombre_minusculas}.php` (ej: `module.clientes.php`)
- **Nombre de clase**: `Module{Nombre}` en PascalCase (ej: `ModuleClientes`)
- **Comandos**: métodos públicos con nombre descriptivo en PascalCase
- **Parámetros**: siempre reciben `$params` (array asociativo del request)
- **Retorno**: el valor retornado se convierte automáticamente a XML

## 4. Llamada desde JavaScript

```javascript
// Sintaxis: SendXMLRequest(modulo, comando, callback, parametros)
SendXMLRequest('{$ARGUMENTS}', 'NombreComando', onResultado, {
    param1: 'valor1',
    param2: 'valor2'
});

function onResultado(response) {
    var status = response.Status.Id;  // 0 = éxito
    var data = response.Data;

    if (status == 0) {
        // Procesar datos exitosos
        // Usar $j para manipular el DOM (jQuery)
        $j('#resultado').html(data);
    } else {
        // Manejar error
        alert('Error: ' + response.Status.Message);
    }
}
```

**Importante:** En el JavaScript del frontend, siempre usar `$j` para jQuery, nunca `$`.

## 5. Ejemplo de referencia

Ver [examples/module-example.md](examples/module-example.md) para un ejemplo completo con múltiples comandos.

## 6. Verificaciones finales

- [ ] El archivo está en `modules/` con nombre `module.{nombre}.php`
- [ ] La clase se llama `Module{Nombre}` (PascalCase)
- [ ] Cada comando es un método público que recibe `$params`
- [ ] Los `require_once` necesarios están incluidos si se usan clases externas
- [ ] Las llamadas JS desde el frontend usan `SendXMLRequest` con `$j` para jQuery
