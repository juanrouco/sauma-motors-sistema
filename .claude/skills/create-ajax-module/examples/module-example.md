# Ejemplo: Módulo AJAX de Clientes

Ejemplo completo de un módulo con múltiples comandos.

## Archivo PHP (`modules/module.clientes.php`)

```php
<?php
class ModuleClientes
{
    public function BuscarPorCuit($params)
    {
        $cuit = $params['cuit'];
        $clientes = new Clientes();
        $resultado = $clientes->GetByCuit($cuit);
        return $resultado;
    }

    public function BuscarPorNombre($params)
    {
        $nombre = $params['nombre'];
        $clientes = new Clientes();
        $filtro = new Filtro();
        $filtro->Add("RazonSocial LIKE '%{$nombre}%'");
        return $clientes->GetAll($filtro);
    }

    public function GetDetalle($params)
    {
        $id = $params['id'];
        $clientes = new Clientes();
        return $clientes->GetById($id);
    }
}
?>
```

## Llamadas desde JavaScript

```javascript
// Búsqueda por CUIT
$j('#btnBuscarCuit').click(function() {
    var cuit = $j('#txtCuit').val();
    SendXMLRequest('Clientes', 'BuscarPorCuit', onClienteEncontrado, {cuit: cuit});
});

function onClienteEncontrado(response) {
    if (response.Status.Id == 0) {
        var data = response.Data;
        $j('#nombreCliente').text(data.RazonSocial);
        $j('#domicilioCliente').text(data.DomicilioCalle);
    }
}

// Búsqueda por nombre (autocomplete)
$j('#txtNombre').keyup(function() {
    var nombre = $j(this).val();
    if (nombre.length >= 3) {
        SendXMLRequest('Clientes', 'BuscarPorNombre', onResultadosBusqueda, {nombre: nombre});
    }
});

function onResultadosBusqueda(response) {
    if (response.Status.Id == 0) {
        // Poblar lista de resultados
        var html = '';
        // ... construir HTML con los resultados
        $j('#listaResultados').html(html);
    }
}
```

## Notas

- Cada método público de la clase es un comando invocable desde `SendXMLRequest`
- El primer parámetro de `SendXMLRequest` debe coincidir con el nombre del módulo (sin el prefijo `Module`)
- El segundo parámetro debe coincidir exactamente con el nombre del método
- Siempre usar `$j` para jQuery en el frontend
