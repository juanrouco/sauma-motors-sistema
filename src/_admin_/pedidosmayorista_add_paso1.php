<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PEDMAY_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCliente				= intval($_REQUEST['IdCliente']);
$Cliente				= strval($_REQUEST['Cliente']);
$FechaPedidoMayorista	= strval($_REQUEST['FechaPedidoMayorista']);
$Observaciones			= strval($_REQUEST['Observaciones']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oPedidoMayorista 	= new PedidoMayorista();
$oPedidosMayorista	= new PedidosMayorista();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado... */
if ($Submit)
{
	/* validaciones... */
	if ($IdCliente == '')
		$err |= 1;
	if ($FechaPedidoMayorista == '')
		$err |= 4;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		$oPedidoMayorista->IdCliente			= $IdCliente;
		$oPedidoMayorista->FechaPedidoMayorista	= $FechaPedidoMayorista;
		$oPedidoMayorista->Observaciones		= $Observaciones;
		$oPedidoMayorista->IdEstado				= PedidosMayoristaEstados::Pendiente;

		if ($oPedidoMayorista = $oPedidosMayorista->Create($oPedidoMayorista))
		{
			/* enviamos el id generado */
			$strParams.= '&IdPedidoMayorista=' . $oPedidoMayorista->IdPedidoMayorista;
			
			header('Location: pedidosmayorista_seleccionar_minuta.php' . $strParams);
			exit;
		}
	}
}
else
{
	/* determinamos como fecha de recepcion a la fecha de ayer */
	$FechaPedidoMayorista = date("Y-m-d");
	$FechaPedidoMayorista = CambiarFecha($FechaPedidoMayorista);
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('include/head.inc.php'); ?>
<script type="text/javascript">
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
}
</script>
</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de PEdidos Mayoristas - Agregar</span></td>
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
				<form name="frmData" id="frmData" method="post" enctype="multipart/form-data" action="pedidosmayorista_add_paso1.php<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="IdCliente" id="IdCliente" value="<?= $IdCliente ?>" />
					<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                    
					<table width="80%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table  border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">N&uacute;mero Pedido:</div></td>
										<td>
											<input type="text" class="camporFormularioChico" maxlength="128" disabled="disabled" value="<?=$oPedidosMayorista->GetNextId()?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><div align="right">Cliente:</div></td>
										<td>
											<input type="text" name="Cliente" id="Cliente" class="camporFormularioSimple" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
											<span style="color:#FF0000;">&nbsp;(*)</span>
											<script language="javascript">
                                                                                        SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                                                                        </script>
										</td>
									</tr>
								
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } if ($err & 2) { ?><li style="color:#FF0000;">Ya existe registrado el nro. de carta porte</li><?php } ?></td>
                                    </tr>
									<tr>
										<td><div align="right">Fecha de Pedido:</div></td>
                                        <td>
                                            <div align="left">
                                                <input name="FechaPedidoMayorista" type="text" class="camporFormularioChico" id="FechaPedidoMayorista" value="<?=$FechaPedidoMayorista?>" size="12" maxlength="12" />
                                                <script language="javascript">
                                                new tcal({'formname': 'frmData', 'controlname': 'FechaPedidoMayorista'});
                                                </script>
                                            </div>
                                        </td>
									</tr>
								
                                	<tr>
										<td>&nbsp;</td>
										<td align="left"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese la fecha de pedido</li><?php } ?></td>
                                    </tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="80%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Siguiente" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'pedidosmayorista.php<?=$strParams?>';" value="Cancelar" />
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