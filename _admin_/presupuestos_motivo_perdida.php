<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PRESUP_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPresupuesto			= intval($_REQUEST['IdPresupuesto']);
$IdCausaPerdida			= intval($_REQUEST['IdCausaPerdida']);

$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oPresupuestos	= new Presupuestos();
$oModelos	= new Modelos();
$oColores	= new Colores();
$oClientes = new Clientes();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oPresupuesto = $oPresupuestos->GetById($IdPresupuesto))
{
	header('Location: presupuestos.php');
	exit;
}

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if ($IdCausaPerdida == '')
		$err |= 1;
	

	/* si no hay errores... */
	if ($err == 0)
	{
		$oPresupuesto->IdCausaPerdida		= $IdCausaPerdida;
		
		if ($oPresupuesto = $oPresupuestos->Update($oPresupuesto))
		{
			header("Location: presupuestos.php" . $strParams);
			exit();
		}
	}
}
else
{
	$IdCausaPerdida			= $oPresupuesto->IdCausaPerdida;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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

function ClearCliente()
{
	Get('IdCliente').value 	= '';
}

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
		Get('ClienteTelefono').value 	= '';
		Get('ClienteEmail').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	Get('ClienteTelefono').value 	= oCliente.Telefono;
	Get('ClienteEmail').value 	= oCliente.Email;
	
	/* si posee vendedor asignado, entonces levsntamos los datos */
	if (oCliente.IdVendedor != '')
	{
		FilterUsuario(oCliente.IdVendedor, '');
	}
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
	HideSection('trFinanciacionCapital');
	HideSection('trFinanciacionCapitalError');
	HideSection('trFinanciacionAcreedor');
	HideSection('trFinanciacionAcreedorError');
	HideSection('trFinanciacionCuotas');
	HideSection('trFinanciacionCuotasError');
	HideSection('trFinanciacionValorCuota');
	HideSection('trFinanciacionValorCuotaError');
	
	if ((value == '1') || (value == true))
	{
		ShowSection('trFinanciacionCapital');
		ShowSection('trFinanciacionCapitalError');
		ShowSection('trFinanciacionAcreedor');
		ShowSection('trFinanciacionAcreedorError');
		ShowSection('trFinanciacionCuotas');
		ShowSection('trFinanciacionCuotasError');
		ShowSection('trFinanciacionValorCuota');
		ShowSection('trFinanciacionValorCuotaError');
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

function ModCliente()
{
	var IdCliente = Get('IdCliente').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
}

function FilterCodigoComercial(IdModelo, NumeroVinPrefijo)
{
	if (IdModelo == '')
	{
		Get('VehiculoModelo').value 		= '';		
		Get('CodigoComercial').value 		= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;
	Get('IdModelo').value 				= oModelo.IdModelo;
	Get('CodigoComercial').value 		= oModelo.CodigoComercial;	
	Get('VehiculoModelo').value 		= oModelo.DenominacionModelo;
}

function FilterColor(IdColor, Nombre)
{
	if ((IdModelo == ''))
	{
		Get('IdColor').value 	= '';
		Get('Color').value 	= '';
		Get('ColorCodigo').value 	= '';
	}

	var oColor = GetColor(IdColor);
	if (!(oColor))
		return;

	Get('IdColor').value 	= oColor.IdColor;
	Get('Color').value 	= oColor.Nombre;	
	Get('ColorCodigo').value 	= oColor.Codigo;	
}

function FilterDenominacionComercial(IdModelo, Nombre)
{
	if ((IdModelo == ''))
	{
		Get('IdModelo').value 	= '';
		Get('VehiculoModelo').value 	= '';
	}

	var oModelo = GetModelo(IdModelo);
	if (!(oModelo))
		return;

	Get('IdModelo').value 	= oModelo.IdModelo;
	Get('VehiculoModelo').value 	= oModelo.DenominacionModelo;	
}

$j(document).ready(function() {
<?php
if ($IdModelo && !$Submit)
{
?>
	FilterDenominacionComercial('<?= $IdModelo ?>', '');
<?php
}
if ($IdUsuario && !$Submit)
{
?>
	FilterUsuario('<?= $IdUsuario ?>', '');
<?php
}
if ($IdColor && !$Submit)
{
?>
	FilterColor('<?= $IdColor ?>', '');
<?php
}
if ($UsadoIdMarca && !$Submit)
{
?>
	FilterUsadoMarca('<?= $UsadoIdMarca ?>', '');
<?php
}
?>

});
</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Facturas Proforma - Causa de Perdida</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div align="center">
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="IdPresupuesto" id="IdPresupuesto" value="<?=$IdPresupuesto?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    
													<tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                        
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                               <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Causa de Perdida:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select  name="IdCausaPerdida" id="IdCausaPerdida" class="camporFormularioSimple">
																							<option value="">[Selecciona una causa de perdida]</option>
																							<option value="1" <?= $IdCausaPerdida == '1' ? 'selected="selected"' : '' ?>>Pospone la compra</option>
																							<option value="2" <?= $IdCausaPerdida == '2' ? 'selected="selected"' : '' ?>>Compro en otra concesionaria</option>
																							<option value="3" <?= $IdCausaPerdida == '3' ? 'selected="selected"' : '' ?>>Compro otra marca</option>
																						</select>
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione una causa</li><?php } ?></td>
                                                                </tr>
																
                                                            </table>
                                                        </td>
                                                    </tr>
													
                                            	</table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'presupuestos.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

</body>
</html>