<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENTUS_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinuta	= intval($_REQUEST['IdMinuta']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err		= 0;
$oMinutasUsados	= new MinutasUsados();
$oClientes 	= new Clientes();
$oUsados	= new Usados();
$oPagos		= new Pagos();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oMinuta = $oMinutasUsados->GetById($IdMinuta))
{
	header("Location: minutasusados.php" . $strParams);
	exit;
}

/* si el formulario fue enviado... */
if ($Submit)
{
	$IdUsado = $oMinuta->IdUsado;
	$oMinuta->IdEstado = EstadoUnidad::Stock;
	if ($oMinuta->EntregaUsado)
	{
		$arrUsados = $oUsados->GetAllByIdMinutaUsado($oMinuta->IdMinuta);
		$oUsadoTomado = $arrUsados[0];
		if ($oUsadoTomado) 
			$oUsados->Delete($oUsadoTomado->IdUsado);
		if (count($arrUsados) > 1)
		{
			$oUsado2 = $arrUsados[1];
			if ($oUsado2) 
				$oUsados->Delete($oUsado2->IdUsado);
		}
	}
		
	$arrPagos = $oPagos->GetByIdMinutaUsado($oMinuta->IdMinuta);
	
	if ($arrPagos)
	{
		foreach ($arrPagos as $oPago)
		{
			$oPagos->Delete($oPago->IdPago);
		}
	}
	
	if ($oMinuta = $oMinutas->Update($oMinuta))
	{
		/* obtenemos los datos de la unidad */
		$oUsado = $oUsados->GetById($oMinuta->IdUsado);
		
		/* actualizamos el estado del vehiculo, volviendolo a stock */
		$oUsado->IdEstado = EstadoUnidad::Stock;
		
		$oUsados->Update($oUsado);
	}

	header("Location: minutasusados.php" . $strParams);
	exit;
}

/* obtenemos ciertos datos asociados a la venta */
$oUsado 	= $oUsados->GetById($oMinuta->IdUsado);
$oCliente 	= $oClientes->GetById($oMinuta->IdCliente);

$Detalle = '';
$Detalle.= 'Carpeta Nro. ' . $oMinuta->IdMinuta;
$Detalle.= ' || ';
$Detalle.= 'Cliente ' . $oCliente->RazonSocial;
$Detalle.= ' || ';
$Detalle.= 'Nro. Vin ' . $oUsado->NumeroVin;
$Detalle.= ' || ';
$Detalle.= 'Modelo ' . $oUsado->Modelo;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Minutas de Usados - Anular</span></td>
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
            
			
				<table width="60%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center"><strong>&iquest;Esta seguro que desea anular el siguiente registro?</strong></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar"><?=$Detalle?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="center" class="campoEliminar">La unidad volver&aacute; a tener estado en 'stock'</div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
						  	</table>						
                      	</td>
					</tr>
				</table>
                <table width="60%" border="0" cellspacing="0" cellpadding="0">
                  	<tr>
                    	<td height="1"><div align="center"></div></td>
                  	</tr>
                </table>
          		<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
						<input type="hidden" name="Submitted" id="Submitted" value="1" />
						<input type="hidden" name="IdMinuta" id="IdMinuta" value="<?=$IdMinuta?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'minutasusados.php<?=$strParams?>';">
								</div>
							</td>
						</tr>
					</form>
				</table>

	    
			</div>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

</body>
</html>