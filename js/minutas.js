
function FilterUsuario(IdUsuario, Nombre)
{
	if ((IdUsuario == '') && (Nombre == ''))
	{
		Get('IdUsuario').value 	= '';
		Get('Usuario').value 	= '';
	}

	var oUsuario = GetUsuario(IdUsuario);
	if (!(oUsuario))
		return;

	Get('IdUsuario').value 	= oUsuario.IdUsuario;
	Get('Usuario').value 	= (oUsuario.Nombre + ' ' + oUsuario.Apellido);
}

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	
	if (oCliente.IdEstadoCivil == '2')
	{
		ShowSection('trClienteCondominio_Conyugue');
	}
	VerificarCliente();
	
	/* si posee vendedor asignado, entonces levsntamos los datos */
	if (oCliente.IdVendedor != '')
	{
		//FilterUsuario(oCliente.IdVendedor, '');
	}
}

function FilterClienteReventa(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdClienteReventa').value 	= '';
		Get('Reventa').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdClienteReventa').value 	= oCliente.IdCliente;
	Get('Reventa').value 	= oCliente.RazonSocial;
}

function FilterClienteCondominio(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdClienteCondominio').value 	= '';
		Get('ClienteCondominio').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdClienteCondominio').value 	= oCliente.IdCliente;
	Get('ClienteCondominio').value 	= oCliente.RazonSocial;	
}

function FilterUsadoMarca(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('UsadoMarcaCodigo').value 	= '';
		Get('UsadoMarca').value 		= '';
		Get('UsadoIdMarca').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('UsadoMarcaCodigo').value 	= oMarca.Codigo;
	Get('UsadoMarca').value 		= oMarca.Nombre;
	Get('UsadoIdMarca').value 		= oMarca.IdMarca;
}

function FilterUsadoMarca2(IdMarca, Nombre)
{
	if ((IdMarca == '') && (Nombre == ''))
	{
		Get('UsadoMarcaCodigo2').value 	= '';
		Get('UsadoMarca2').value 		= '';
		Get('UsadoIdMarca2').value 		= '';
	}

	var oMarca = GetMarca(IdMarca);
	if (!(oMarca))
		return;
	
	Get('UsadoMarcaCodigo2').value 	= oMarca.Codigo;
	Get('UsadoMarca2').value 		= oMarca.Nombre;
	Get('UsadoIdMarca2').value 		= oMarca.IdMarca;
}

function FilterUsadoColor(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('UsadoColorCodigo').value 	= '';
		Get('UsadoColor').value 		= '';
		Get('UsadoIdColor').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('UsadoColorCodigo').value 	= oColor.Codigo;
	Get('UsadoColor').value 		= oColor.Nombre;
	Get('UsadoIdColor').value 		= oColor.IdColor;
}

function FilterUsadoColor2(IdColor, Nombre)
{
	if ((IdColor == '') && (Nombre == ''))
	{
		Get('UsadoColorCodigo2').value 	= '';
		Get('UsadoColor2').value 		= '';
		Get('UsadoIdColor2').value 		= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;
		
	Get('UsadoColorCodigo2').value 	= oColor.Codigo;
	Get('UsadoColor2').value 		= oColor.Nombre;
	Get('UsadoIdColor2').value 		= oColor.IdColor;
}

function VerificarEntregaUsado(value)
{
	HideSection('trDatosUsadoTitulo');
	HideSection('trDatosUsado');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trDatosUsadoTitulo');
		ShowSection('trDatosUsado');
	}
}

function VerificarFinanciacion(value)
{
	/*HideSection('trFinanciacionCapital');
	HideSection('trFinanciacionCapitalError');
	HideSection('trPlazoPrenda');
	HideSection('trPlazoPrendaError');
	HideSection('trQuebranto');
	HideSection('trQuebrantoError');
	HideSection('trAcreedor');
	HideSection('trAcreedorError');*/
	HideSection('trFinanciacionTitulo');
	HideSection('trFinanciacionItems');
	HideSection('trFinanciacionLink');
	
	if ((value == '1') || (value == true))
	{
		/*ShowSection('trFinanciacionCapital');
		ShowSection('trFinanciacionCapitalError');
		ShowSection('trPlazoPrenda');
		ShowSection('trPlazoPrendaError');
		ShowSection('trQuebranto');
		ShowSection('trQuebrantoError');
		ShowSection('trAcreedor');
		ShowSection('trAcreedorError');*/
		ShowSection('trFinanciacionTitulo');
		ShowSection('trFinanciacionItems');
		ShowSection('trFinanciacionLink');
	}
}

function VerificarCliente()
{
	var IdCliente = Get('IdCliente').value;

	HideSection('trModificarCliente');
	
	if (IdCliente != '')
	{
		ShowSection('trModificarCliente');
	}
}
function VerificarClienteReventa()
{
	var IdCliente = Get('IdClienteReventa').value;

	HideSection('trModificarClienteReventa');
	
	if (IdCliente != '')
	{
		ShowSection('trModificarClienteReventa');
	}
}

function VerificarClienteCondominio()
{
	var IdCliente = Get('IdClienteCondiminio').value;

	HideSection('trModificarClienteCondominio');
	
	if (IdClienteCondominio != '')
	{
		ShowSection('trModificarClienteCondominio');
	}
}



function VerificarCondominio(value)
{
	HideSection('trClienteCondominio');
	HideSection('trClienteCondominio_white');
		
	if ((value == '1') || (value == true))
	{
		ShowSection('trClienteCondominio');
		ShowSection('trClienteCondominio_white');
	}
}

function ModCliente()
{
	var IdCliente = Get('IdCliente').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function ModClienteReventa()
{
	var IdCliente = Get('IdClienteReventa').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function ModClienteCondominio()
{
	var IdCliente = Get('IdClienteCondominio').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}


$j(document).ready(function() {
	$j('#calcular-cuotas').click(function(e) {
		e.preventDefault();
		var IdAcreedor = $j('#IdAcreedor').val();
		if (!IdAcreedor)
		{
			alert('Seleccione el acreedor');
			return;
		}
		var FinanciacionCapital = parseFloat($j('#FinanciacionCapital').val());
		if (!FinanciacionCapital)
		{
			alert('Ingrese el capital a financiar');
			return;
		}
		var PlazoPrenda = parseInt($j('#PlazoPrenda').val());
		if (!PlazoPrenda)
		{
			alert('Ingrese el plazo');
			return;
		}
		$j.ajax('ssi_cuotas.php?IdAcreedor=' + IdAcreedor + '&FinanciacionCapital=' + FinanciacionCapital + '&Cuotas=' + PlazoPrenda, {
			success: function (data, textStatus, jqXHR) {
				$j('#cuotas-container').html(data);
			}
		});
	});
	$j('#PrecioVentaTotal').on('input',function() {
		var PrecioTotal = parseFloat($j('#PrecioVentaTotal').val());
		var PrecioFact = parseFloat($j('#PrecioVenta').val());
		var GastosPatentamiento = parseFloat($j('#GastosPatentamiento').val());
		var Interes = parseFloat($j('#Interes').val());
		if (!PrecioTotal)
			PrecioTotal = 0;
		if (!PrecioFact)
			PrecioFact = 0;
		if (!GastosPatentamiento)
			GastosPatentamiento = 0;
		if (!Interes)
			Interes = 0;
		if (PrecioFact == 0 || !PrecioFact)
			$j('#PrecioVenta').val(PrecioTotal)
		$j('#GastosOtorgamiento').val((PrecioTotal - PrecioFact - GastosPatentamiento - Interes).toFixed(2));
	});
	$j('#PrecioVenta').on('input',function() {
		var PrecioTotal = parseFloat($j('#PrecioVentaTotal').val());
		var PrecioFact = parseFloat($j('#PrecioVenta').val());
		var GastosPatentamiento = parseFloat($j('#GastosPatentamiento').val());
		var Interes = parseFloat($j('#Interes').val());
		if (!PrecioTotal)
			PrecioTotal = 0;
		if (!PrecioFact)
			PrecioFact = 0;
		if (!GastosPatentamiento)
			GastosPatentamiento = 0;
		if (!Interes)
			Interes = 0;
		$j('#GastosOtorgamiento').val((PrecioTotal - PrecioFact - GastosPatentamiento - Interes).toFixed(2));
	});
	$j('#GastosPatentamiento, #Interes').on('input',function() {
		var PrecioTotal = parseFloat($j('#PrecioVentaTotal').val());
		var PrecioFact = parseFloat($j('#PrecioVenta').val());
		var GastosPatentamiento = parseFloat($j('#GastosPatentamiento').val());
		var Interes  = parseFloat($j('#Interes').val());
		if (!PrecioTotal)
			PrecioTotal = 0;
		if (!PrecioFact)
			PrecioFact = 0;
		if (!GastosPatentamiento)
			GastosPatentamiento = 0;
		if (!Interes)
			Interes = 0;
		$j('#GastosOtorgamiento').val((PrecioTotal - PrecioFact - GastosPatentamiento - Interes).toFixed(2));
	});
});