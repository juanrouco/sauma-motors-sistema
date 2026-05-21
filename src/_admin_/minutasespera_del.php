<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_VENT_DELETE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdMinutaEspera	= intval($_REQUEST['IdMinutaEspera']);
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oMinutasEspera	= new MinutasEspera();
$oModelos 		= new Modelos();
$oClientes 		= new Clientes();
$oUnidades		= new Unidades();
$oUsados		= new Usados();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oMinutaEspera = $oMinutasEspera->GetById($IdMinutaEspera))
{
	header("Location: minutasespera.php" . $strParams);
	exit;
}

/* si el formulario fue enviado... */
if ($Submit)
{
	
	if ($oMinutaEspera->EntregaUsado)
	{
		$arrUsados = $oUsados->GetAllByIdMinutaEspera($oMinutaEspera->IdMinutaEspera);
		$oUsado = $arrUsados[0];
		if ($oUsado) 
			$oUsados->Delete($oUsado->IdUsado);
		if (count($arrUsados) > 1)
		{
			$oUsado2 = $arrUsados[1];
			if ($oUsado2) 
				$oUsados->Delete($oUsado2->IdUsado);
		}
	}
	
	$oMinutaEspera = $oMinutasEspera->Delete($oMinutaEspera->IdMinutaEspera);
	
	header("Location: minutasespera.php" . $strParams);
	exit;
}

/* obtenemos ciertos datos asociados a la venta */
$oModelo 	= $oModelos->GetById($oMinutaEspera->IdModelo);
$oCliente 	= $oClientes->GetById($oMinutaEspera->IdCliente);

$Detalle = '';
$Detalle.= 'Minuta Nro. ' . $oMinutaEspera->IdMinutaEspera;
$Detalle.= ' || ';
$Detalle.= 'Cliente ' . $oCliente->RazonSocial;
$Detalle.= ' || ';
$Detalle.= 'Modelo ' . $oModelo->DenominacionComercial;

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
					<td height="40"><span class="tituloPagina">Administraci&oacute;n de Preventa - Eliminar</span></td>
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
									<td><div align="center"><strong>&iquest;Esta seguro que desea eliminar el siguiente registro?</strong></div></td>
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
						<input type="hidden" name="IdMinutaEspera" id="IdMinutaEspera" value="<?=$IdMinutaEspera?>" />
						<tr>
						  	<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar">
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'minutasespera.php<?=$strParams?>';">
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