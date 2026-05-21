<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page				= intval($_REQUEST['Page']);
$IdArticulo			= intval($_REQUEST['IdArticulo']);
$IdArticuloStock	= intval($_REQUEST['IdArticuloStock']);

/* obtiene datos del formulario */
$IdArticulo 			= $_REQUEST['IdArticulo'];
$IdUbicacion			= $_REQUEST['IdUbicacion'];
$Sucursal				= $_REQUEST['Sucursal'];
$Ubicacion				= $_REQUEST['Ubicacion'];
$StockInicial			= intval($_REQUEST['StockInicial']);
//$StockActual			= $StockInicial;

$Submit					= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err			= 0;
$Articulos		= new Articulos();
$ArticuloStocks	= new ArticuloStocks();

/* armamos cadena con parametros a mandar */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
$oArticulo = $Articulos->GetById($IdArticulo);
$oArticuloStock = $ArticuloStocks->GetById($IdArticuloStock);

if (!$oArticulo || !$oArticuloStock)
	$err += 128;

/* si los datos fueron enviados... */
if ($Submit)
{
	/* validaciones... */
	if ($IdUbicacion == '')
		$err += 1;
	
	/*if ($Ubicacion == '')
		$err += 32;*/	
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oArticuloStock->IdArticulo 	= $IdArticulo;
		$oArticuloStock->IdUbicacion 	= $IdUbicacion;
		$oArticuloStock->Ubicacion		= $Ubicacion;
		//$oArticuloStock->StockInicial 	= $StockInicial;
		//$oArticuloStock->StockActual	= $StockActual;	

		/* modifica el proveedor */
		$oArticuloStock = $ArticuloStocks->Update($oArticuloStock);		
		
		header("Location: articulostocks.php" . $strParams);
		exit();
	}
}
else
{		
	$IdArticulo 	= $oArticuloStock->IdArticulo;
	$IdUbicacion 	= $oArticuloStock->IdUbicacion;
	$Ubicacion		= $oArticuloStock->Ubicacion;
	$StockInicial 	= $oArticuloStock->StockInicial;	
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterUbicacion(IdUbicacion, Nombre)
{
	if ((IdUbicacion == '') && (Nombre == ''))
	{		
		Get('Sucursal').value 			= '';
		Get('IdUbicacion').value 		= '';
	}

	var oUbicacion = GetUbicacion(IdUbicacion);
	if (!(oUbicacion))
		return;
	
	Get('Sucursal').value 			= oUbicacion.Nombre;
	Get('IdUbicacion').value 		= oUbicacion.IdUbicacion;
}

$j(document).ready(function() { 
	<?php
	if ($IdUbicacion) {
	?>
		FilterUbicacion(<?= $IdUbicacion ?>, '');
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
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina"><?= $oArticulo->Descripcion ?> - Modificar Stock</span></td>
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
      		<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" >
	  			<input type="hidden" name="Submitted" id="Submitted" value="1" />
				<input type="hidden" name="IdArticulo" id="IdArticulo" value="<?= $IdArticulo ?>" />
				<input type="hidden" name="IdArticuloStock" id="IdArticuloStock" value="<?= $IdArticuloStock ?>" />
				<input type="hidden" name="IdUbicacion" id="IdUbicacion" value="<?= $IdUbicacion ?>" />

				<table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
          			<tr>
            			<td class="bordeGris">
							<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
              					<tr>
                					<td>&nbsp;</td>
                                    <td>&nbsp;</td>
              					</tr>
								<tr>
									<td><div align="right">Sucursal:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Sucursal" id="Sucursal" class="camporFormularioSuggest" value="<?=$Sucursal?>" disabled="true" />											
											<span style="color:#FF0000;">&nbsp;(*)</span>											
										</div>
									</td>
								</tr>
                           <?php if ($err & 1) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese la sucursal</li></td>
                                </tr>
                           <?php } ?>
						    <?php if ($err & 2) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">El stock para esta sucursal ya fue creado</li></td>
                                </tr>
                           <?php } ?>
                                <tr>
									<td><div align="right">Ubicaci&oacute;n:</div></td>
									<td>
										<div align="left">
											<input type="text" id="Ubicacion" name="Ubicacion"  class="camporFormularioMediano" maxlength="255" value="<?=$Ubicacion?>" />																						
										</div>
									</td>
								</tr>                           
                                <tr>
									<td><div align="right">Stock Inicial:</div></td>
									<td>
										<div align="left">
											<input type="text" name="StockInicial" id="StockInicial" class="camporFormularioMediano" value="<?=$StockInicial;?>" disabled="true" />
										</div>
									</td>
								</tr>
								<?php if ($err & 4) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Debe ingresar un stock inicial</li></td>
                                </tr>
								<?php } ?>
                        		<tr>
									<td>&nbsp;</td>
                                    <td>&nbsp;</td>
								</tr>

            				</table>						</td>
          			</tr>
        		</table>
				
   		        <table width="75%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="1"><div align="center"></div></td>
                  </tr>
                </table>
  <table width="75%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
          			<tr>
            			<td height="30">
              				<div align="center">
                				<input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
                				<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'articulostocks.php<?=$strParams?>';" value="Cancelar" />
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