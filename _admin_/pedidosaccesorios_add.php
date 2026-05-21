<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PACC_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta		= intval($_REQUEST['IdMinuta']);
$Fecha			= strval($_REQUEST['Fecha']);
$Accesorios		= strval($_REQUEST['Accesorios']);
$arrDetalles 	= $_REQUEST['Detalle'];
$arrImportes 	= $_REQUEST['Importe'];
$Submit			= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err						= 0;
$oMinutas 					= new Minutas();
$oPedidoAccesorios 			= new PedidoAccesorios();
$oPedidosAccesorios			= new PedidosAccesorios();
$oPedidosAccesoriosItems	= new PedidosAccesoriosItems();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado */
if ($Submit)
{
	/* validaciones... */
	if (($IdMinuta == '') || (!$oMinutas->GetById($IdMinuta)))
		$err |= 1;
	if ($Fecha == '')
		$err |= 2;
	if ($Accesorios == '')
		$err |= 4;

	/* si no hay errores... */
	if ($err == 0)
	{
		$oPedidoAccesorios->IdMinuta 	= $IdMinuta;
		$oPedidoAccesorios->Fecha 		= $Fecha;
		$oPedidoAccesorios->Accesorios 	= $Accesorios;

		if ($oPedidoAccesorios = $oPedidosAccesorios->Create($oPedidoAccesorios))
		{
			for ($i = 0; $i < count($arrDetalles); $i++)
			{
				$Detalle = $arrDetalles[$i];
				$Importe = $arrImportes[$i];
				$Importe = str_replace(',', '.', $Importe);
				
				if ($Detalle && $Importe != '')
				{
					$oPedidoAccesorioItem = new PedidoAccesorioItem();
					$oPedidoAccesorioItem->Detalle = $Detalle;
					$oPedidoAccesorioItem->Importe = $Importe;
					$oPedidoAccesorioItem->IdPedidoAccesorio = $oPedidoAccesorios->IdPedido;
					
					$oPedidosAccesoriosItems->Create($oPedidoAccesorioItem);
				}
			}
		}

		header("Location: pedidosaccesorios.php" . $strParams);
		exit();
	}
}
else
{
	/* determinamos como fecha a la fecha de mañana */
	$Fecha = date("Y-m-d", strtotime(date("Y-m-d") . " + 1 days"));
	$Fecha = CambiarFecha($Fecha);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function SelectMinuta()
{
	var Url = 'minutas.php?MainAction=Select';
	
	window.open(Url, this.target, 'width=1000,height=600'); 
}

function SetMinuta(IdMinuta)
{
	var oMinuta;
	var oUnidad;
	var oModelo;
	var oTipoModelo;
	var oColor;
	var oMarca;
	var oMarcaMotor;
	var oMarcaChasis;
	var oCliente;
	var oTipoiva;
	var oLocalidad;
	var oProvincia;

	if (!(oMinuta = GetMinuta(IdMinuta)))
	{
		ShowSection('IdMinutaError');
		return;
	}

	if (!(oUnidad = GetUnidad(oMinuta.IdUnidad)))
		return;

	if (!(oModelo = GetModelo(oUnidad.IdModelo)))
		return;

	if (!(oTipoModelo = GetTipoModelo(oModelo.IdTipoModelo)))
		return;

	if (!(oColor = GetColor(oUnidad.IdColor)))
		return;

	if (!(oMarca = GetMarca(oModelo.IdMarcaVehiculo)))
		return;

	if (!(oMarcaMotor = GetMarca(oModelo.IdMarcaMotor)))
		return;

	if (!(oMarcaChasis = GetMarca(oModelo.IdMarcaChasis)))
		return;

	if (!(oCliente = GetCliente(oMinuta.IdCliente)))
		return;

	if (!(oLocalidad = GetLocalidad(oCliente.DomicilioIdLocalidad)))
		return;

	if (!(oTipoIva = GetTipoIva(oCliente.IdTipoIva)))
		return;

	HideSection('IdMinutaError');

	Get('MinutaFecha').innerHTML 					= oMinuta.FechaMinuta;
	Get('ClienteRazonSocial').innerHTML 			= oCliente.RazonSocial;
	Get('ClienteDomicilio').innerHTML 				= oCliente.DomicilioCalle + ' ' + oCliente.DomicilioNumero;
	Get('ClienteLocalidad').innerHTML 				= oLocalidad.Nombre;
	Get('ClienteCodigoPostal').innerHTML 			= oLocalidad.CodigoPostal;
	Get('ClienteTelefono').innerHTML 				= oCliente.TelefonoCodigoArea + ' ' + oCliente.Telefono;
	Get('ClienteCondicionIva').innerHTML 			= oTipoIva.Nombre;
	Get('ClienteCuit').innerHTML 					= oCliente.ClaveFiscalNumero;
	Get('VehiculoMarca').innerHTML 					= oMarca.Nombre;
	Get('VehiculoTipo').innerHTML 					= oTipoModelo.Nombre;
	Get('VehiculoAnio').innerHTML 					= oModelo.Anio;
	Get('VehiculoModelo').innerHTML 				= oModelo.DenominacionModelo;
	Get('VehiculoNumeroMotor').innerHTML 			= oUnidad.NumeroMotor + ' - ' + oMarcaMotor.Nombre;
	Get('VehiculoNumeroChasis').innerHTML 			= oUnidad.NumeroChasisPrefijo + oUnidad.NumeroChasis + ' - ' + oMarcaChasis.Nombre;
	Get('VehiculoColor').innerHTML					= oColor.Nombre;
	Get('VehiculoDenominacionComercial').innerHTML 	= oModelo.DenominacionComercial;
	Get('VehiculoCodigoLlaves').innerHTML 			= oUnidad.CodigoLlaves;
	
	/* guardamos el numero de operacion */
	Get('IdMinuta').value = IdMinuta;
	
	ShowSection('trDatosPedidoAccesoriosTitulo');
	ShowSection('trDatosPedidoAccesorios');
}

</script>
<script type="text/javascript">
	function AgregarItem() {
		var html = '<tr class="bordeGrisFondo">';
			html+= '	<td height="30"><div id="margen"><input type="text" id="Detalle[]" name="Detalle[]" class="camporFormularioSimple" /></div></td>';
			html+= '	<td width="10">&nbsp;</td>';
			html+= '	<td width="200"><div id="margen" align="center">$<input type="text" id="Importe[]" name="Importe[]" class="camporFormularioChico" /></div></td>';
			html+= '	<td width="75"><div id="margen" align="center"><a href="#" id="quitar-item"><img src="images/iconos/del.gif" /></a></div></td>';
			html+= '</tr>';
		
			var element = $j(html);
			element.find('#quitar-item').click(function(e) {
				e.preventDefault();
				element.remove();
			});
			$j('#contenedor-items').append(element);
	}
	
	function QuitarItem(id) {
		$j('#row_' + id).remove();
	}
	
	$j(document).ready(function() {
		<?php
		if (!$arrDetalles || count($arrDetalles) == 0)
		{
		?>
		AgregarItem();
		<?php
		}
		?>
		$j('#agregar-item').click(function(e) {
			e.preventDefault();
			
			AgregarItem();
		});
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
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Pedidos de Accesorios - Agregar</span></td>
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
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td>
                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td><div align="right">Nro. Carpeta:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="IdMinuta" id="IdMinuta" class="camporFormularioChico" maxlength="10" onblur="javascript: SetMinuta(this.value);" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$IdMinuta?>" />
	                                                        &nbsp;<input type="button" id="btnSelectMinuta" class="botonBasico"  onClick="javascript:SelectMinuta();" value=" ? " />
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 1) { ?><li style="color:#FF0000;">Seleccione o ingrese un nro. de carpeta v&aacute;lido</li><?php } else { ?><li style="color:#FF0000; display:none;" id="IdMinutaError">El nro. ingresado no existe</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Fecha:</div></td>
                                                    <td>
                                                        <div align="left">
                                                            <input name="Fecha" type="text" class="camporFormularioChico" id="Fecha" value="<?=$Fecha?>" size="12" maxlength="12" />
                                                            <script language="javascript">
                                                            new tcal({'formname': 'frmData', 'controlname': 'Fecha'});
                                                            </script>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese la fecha</li><?php } ?></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="right">Comentarios:</div></td>
                                                    <td>
                                                        <div align="left">
                                                        	<textarea name="Accesorios" id="Accesorios" class="camporFormularioMultilineGrande" onkeyup="javascript: StrToUpper(this.id);"><?=$Accesorios?></textarea>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                	<td height="25">&nbsp;</td>
                                                	<td height="25"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese los accesorios a agregar en la unidad</li><?php } ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td>
											<div align="center">
												<table id="contenedor-items" width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
													<tr class="bordeGrisFondo">
														<td height="30"><div id="margen"><strong>Item</strong></div></td>
														<td width="10">&nbsp;</td>
														<td width="200"><div id="margen" align="center"><strong>Importe</strong></div></td>
														<td width="75"><div id="margen" align="center"><strong>Acciones</strong></div></td>
													</tr>
													<?php
													if ($arrDetalles && count($arrDetalles) > 0)
													{
														for ($i = 0; $i < count($arrDetalles); $i++)
														{
													?>
													<tr id="row_<?= $i ?>" class="bordeGris">
														<td height="30"><div id="margen"><input type="text" id="Detalle[]" name="Detalle[]" class="camporFormularioSimple" value="<?= $arrDetalles[$i] ?>" /></div></td>
														<td width="10">&nbsp;</td>
														<td width="200"><div id="margen" align="center">$<input type="text" id="Importe[]" name="Importe[]" class="camporFormularioChico" value="<?= $arrImportes[$i] ?>" /></div></td>
														<td width="75"><div id="margen" align="center"><a href="javascript: QuitarItem('<?= $i ?>');" id="quitar-item"><img src="images/iconos/del.gif" /></a></div></td>
													</tr>
													<?php
														}
													}
													
													?>
												</table>
											</div>
										</td>
									</tr>
									<tr>
										<td align="right"><a href="#" id="agregar-item" style="margin-right: 35px">Agregar Item</a></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trDatosPedidoAccesoriosTitulo" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Operaci&oacute;n</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trDatosPedidoAccesorios" style="display:none;">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Fecha Venta:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="MinutaFecha"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Cliente:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="ClienteRazonSocial"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Domicilio:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="36%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteDomicilio"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="11%"><div align="left">Localidad:</div></td>
                                                                            	<td width="31%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteLocalidad"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="5%"><div align="left">CP:</div></td>
                                                                            	<td width="17%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCodigoPostal"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tel&eacute;fono:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="ClienteTelefono"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Condici&oacute;n IVA:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="43%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCondicionIva"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="8%"><div align="left">CUIT:</div></td>
                                                                            	<td width="49%">
                                                                                    <div align="left">
                                                                                        <label id="ClienteCuit"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                        <td><div align="center">
                                                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                </tr>
                                                            </table>
                                                        </div></td>
                                                    </tr>
                                                    <tr>
                                                    	<td valign="top">
                                                            <table width="100%" border="0" align="left" cellpadding="3" cellspacing="3">
                                                                <tr>
                                                                    <td width="20%"><div id="margen" align="left">Marca:</div></td>
                                                                    <td width="80%">
                                                                        <div align="left">
                                                                        	<label id="VehiculoMarca"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Tipo:</div></td>
                                                                    <td>
                                                                    	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                        	<tr>
                                                                            	<td width="53%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoTipo"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td width="7%"><div align="left">A&ntilde;o:</div></td>
                                                                            	<td width="40%">
                                                                                    <div align="left">
                                                                                        <label id="VehiculoAnio"></label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Modelo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoModelo"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Motor:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroMotor"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Nro. Chasis:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoNumeroChasis"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Color:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoColor"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">Equipo:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoDenominacionComercial"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><div id="margen" align="left">C&oacute;digo de llaves:</div></td>
                                                                    <td>
                                                                        <div align="left">
                                                                        	<label id="VehiculoCodigoLlaves"></label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr> 
                                                    <tr>
                                                    	<td>&nbsp;</td>
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
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'pedidosaccesorios.php<?=$strParams?>';" value="Cancelar" />
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

<script language="javascript">
<?php if ($IdMinuta != '') { ?>
SetMinuta('<?=$IdMinuta?>');
<?php } ?>
</script>

</body>
</html>