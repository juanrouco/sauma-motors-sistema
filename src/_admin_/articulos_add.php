<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_CREATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);

/* obtiene datos del formulario */
$Codigo 				= $_REQUEST['Codigo'];
$Descripcion			= $_REQUEST['Descripcion'];
$Reemplazo				= $_REQUEST['Reemplazo'];
$PrecioCompra			= floatval($_REQUEST['PrecioCompra']);
$PrecioLista			= floatval($_REQUEST['PrecioLista']);
$PrecioOferta			= floatval($_REQUEST['PrecioOferta']);
$PrecioTerceros			= floatval($_REQUEST['PrecioTerceros']);
$IdProveedor			= $_REQUEST['IdProveedor'];
$Proveedor				= $_REQUEST['Proveedor'];
$UnidadVenta			= intval($_REQUEST['UnidadVenta']);
$ClasePieza				= $_REQUEST['ClasePieza'];
$DescCod				= $_REQUEST['DescCod'];
$CodDes					= $_REQUEST['CodDes'];
$StockMaximo			= intval($_REQUEST['StockMaximo']);
$StockMinimo			= intval($_REQUEST['StockMinimo']);
$IdIva					= intval($_REQUEST['IdIva']);
$Utilidad				= floatval($_REQUEST['Utilidad']);

$Submit					= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err			= 0;
$oArticulo	 	= new Articulo();
$Articulos		= new Articulos();
$Ivas			= new Ivas();
$arrIva			= $Ivas->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($Codigo == '')
		$err += 1;
	else
	{
		$oArticuloRepetido = $Articulos->GetByCodigo($Codigo);
		if ($oArticuloRepetido)
			$err +=64;
	}
	if ($Descripcion == '')
		$err += 2;
	if ($PrecioLista == '')
		$err += 4;
	/*if (!is_numeric($DescCod))
		$err += 128;*/
	/*if (!is_numeric($CodDes))
		$err += 256;
	
	/*if ($Ubicacion == '')
		$err += 32;*/	

	/* si no hay errores... */
	if ($err == 0)
	{		
		$PrecioCompra	= str_replace(",", ".", $PrecioCompra);
		$PrecioLista	= str_replace(",", ".", $PrecioLista);
		$PrecioOferta	= str_replace(",", ".", $PrecioOferta);
		$PrecioTerceros	= str_replace(",", ".", $PrecioTerceros);
	
		$oArticulo->Codigo 					= $Codigo;
		$oArticulo->Descripcion 			= $Descripcion;
		$oArticulo->Reemplazo				= $Reemplazo;
		$oArticulo->PrecioCompra 			= $PrecioCompra;
		$oArticulo->PrecioLista				= $PrecioLista;
		$oArticulo->PrecioOferta 			= $PrecioOferta;		
		$oArticulo->PrecioTerceros			= $PrecioTerceros;
		$oArticulo->IdProveedor 			= $IdProveedor;
		$oArticulo->UnidadVenta 			= $UnidadVenta;
		$oArticulo->DescCod 				= $DescCod;
		$oArticulo->CodDes 					= $CodDes;
		$oArticulo->StockMaximo 			= $StockMaximo;
		$oArticulo->StockMinimo				= $StockMinimo;
		$oArticulo->IdIva					= $IdIva;
		$oArticulo->Utilidad 				= $Utilidad;
		$oArticulo->ClasePieza 				= $ClasePieza;
		
		/* crea el proveedor */
		$oArticulo = $Articulos->Create($oArticulo);
		
		header("Location: articulos.php");
		exit();
	}
}
else
{
	/* determinamos como fecha de alta */
	$FechaAlta = date("Y-m-d");
	$FechaAlta = CambiarFecha($FechaAlta);
	$IdIva = 1;
	$UnidadVenta = 1;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterProveedor(IdProveedor, Nombre)
{
	if ((IdProveedor == '') && (Nombre == ''))
	{		
		Get('Proveedor').value 			= '';
		Get('IdProveedor').value 		= '';
	}

	var oProveedor = GetProveedor(IdProveedor);
	if (!(oProveedor))
		return;
	
	Get('Proveedor').value 			= oProveedor.Empresa;
	Get('IdProveedor').value 		= oProveedor.IdProveedor;
}

$j(document).ready(function() { 
	<?php
	if ($IdProveedor) {
	?>
		FilterProveedor(<?= $IdProveedor ?>, '');
	<?php
	}
	?>	
	$j('#Codigo').keypress(function(e) {
		if (e.which == 13) {			
			$j('#Descripcion').focus();
			e.cancelBubble = true;
			e.returnValue = false;

			if (e.stopPropagation) {
				e.stopPropagation();
				e.preventDefault();
			} 
		}
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
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Agregar Repuesto </span></td>
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
	  			<input type="hidden" name="Submitted" id="Submitted" value="1">				
				<input type="hidden" name="IdProveedor" id="IdProveedor" value="<?= $IdProveedor ?>">	

				<table width="75%"  border="0" align="center" cellpadding="5" cellspacing="0">
          			<tr>
            			<td class="bordeGris">
							<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0">
              					<tr>
                					<td>&nbsp;</td>
                                    <td>&nbsp;</td>
              					</tr>
								<tr>
									<td><div align="right">C&oacute;digo:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Codigo" id="Codigo" class="camporFormularioMediano" value="<?=$Codigo?>"  />
											<span style="color:#FF0000;">&nbsp;(*)</span>										
										</div>
									</td>
								</tr>
                           <?php if ($err & 1) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese el c&oacute;digo del art&iacute;culo</li></td>
                                </tr>
                           <?php } ?>
						    <?php if ($err & 64) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">El c&oacute;digo ingresado ya existe</li></td>
                                </tr>
                           <?php } ?>
																
                                <tr>
									<td><div align="right">Descripci&oacute;n:</div></td>
									<td>
										<div align="left">
											<input type="text" id="Descripcion" name="Descripcion"  class="camporFormularioSimple" maxlength="255" value="<?=$Descripcion?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>											
										</div>
									</td>
								</tr>
                           <?php if ($err & 2) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese la descripci&oacute;n del art&iacute;culo.</li></td>
                                </tr>
                           <?php } ?>
                                <tr>
									<td><div align="right">Reemplazo:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Reemplazo" id="Reemplazo" class="camporFormularioMediano" value="<?=$Reemplazo;?>" />
										</div>
									</td>
								</tr>
                                <tr>
									<td><div align="right">Precio Sugerico (c/IVA):</div></td>
									<td>
										<div align="left">
											<input type="text" name="PrecioCompra" id="PrecioCompra" class="camporFormularioChico" maxlength="16" value="<?=$PrecioCompra;?>" /> 
										</div>									
									</td>
								</tr>
								<tr>
									<td><div align="right">Precio Sugerido (s/IVA):</div></td>
									<td>
										<div align="left">
											<input type="text" name="PrecioLista" id="PrecioLista" class="camporFormularioChico" maxlength="16" value="<?=$PrecioLista;?>" /> 
											<span style="color:#FF0000;">&nbsp;(*)</span>
										</div>									
									</td>
								</tr>
								<?php if ($err & 4) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese el precio de sugerido del art&iacute;culo.</li></td>
                                </tr>
								<?php } ?><?php /*
								<tr>
									<td><div align="right">Precio De Oferta:</div></td>
									<td>
										<div align="left">
											<input type="text" name="PrecioOferta" id="PrecioOferta" class="camporFormularioChico" maxlength="16" value="<?=$PrecioOferta;?>" /> 
										</div>									
									</td>
								</tr>
								*/ ?>
								<tr>
									<td><div align="right">Precio Costo:</div></td>
									<td>
										<div align="left">
											<input type="text" name="PrecioTerceros" id="PrecioTerceros" class="camporFormularioChico" maxlength="16" value="<?=$PrecioTerceros;?>" /> 
										</div>									
									</td>
								</tr>	
								<tr>
									<td><div align="right">IVA:</div></td>
									<td>
										<div align="left">
											<select name="IdIva" id="IdIva" class="camporFormularioChico"> 
											<?php
												foreach ($arrIva as $oIva)
												{
											?>
												<option value="<?= $oIva->IdIva ?>" <?= $IdIva == $oIva->IdIva? "selected='true'" : "" ?>><?= $oIva->Nombre ?></option>
											<?php
												}
											?>
											</select>
										</div>									
									</td>
								</tr>	
              					<tr>
                					<td><div align="right">Proveedor:</div></td>
                					<td>
										<div align="left">
											<input type="text" name="Proveedor" id="Proveedor" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioSuggest" maxlength="128" value="<?=$Proveedor?>" autocomplete="off">
											<input type="button" id="btnAddProveedor" class="botonBasico"  onClick="javascript:AddProveedor();" value=" + " />
											<span style="color:#FF0000;">&nbsp;(*)</span>
											<script language="">												
												SUGGESTRequest('Proveedores', 'GetAll', 'Proveedor', 'FilterProveedor', 'IdProveedor', 'Empresa', 'Filter_Empresa', null);
											</script>
										</div>
									</td>
								</tr>
              				<?php if ($err & 8) { ?>
                            	<tr>
                					<td><div align="right"></div></td>
                					<td><li style="color:#FF0000;">Ingrese un proveedor</li></td>
								</tr>
                            <?php } ?>
								<tr>
									<td><div align="right">Unidad De Venta:</div></td>
									<td>
										<div align="left">
											<input type="text" name="UnidadVenta" id="UnidadVenta" class="camporFormularioSimple" maxlength="128" value="<?=$UnidadVenta?>" />
										</div>
									</td>
								</tr>
								<?php /*
								<tr>
									<td><div align="right">Clase De Pieza:</div></td>
									<td>
										<div align="left">
											<input type="text" name="ClasePieza" id="ClasePieza" class="camporFormularioSimple" maxlength="255" value="<?=$ClasePieza?>" />
										</div>
									</td>
								</tr>
								<tr>
									<td><div align="right">Desc Cod:</div></td>
									<td>
										<div align="left">
											<input type="text" name="DescCod" id="DescCod" class="camporFormularioSimple" maxlength="128" value="<?=$DescCod?>" />											
										</div>
									</td>
								</tr>
								<?php if ($err & 128) { ?>
                            	<tr>
                					<td><div align="right"></div></td>
                					<td><li style="color:#FF0000;">Desc Cod debe ser n&uacute;merico</li></td>
								</tr>
								<?php } ?>
								<tr>
									<td><div align="right">Cod Des:</div></td>
									<td>
										<div align="left">
											<input type="text" name="CodDes" id="CodDes" class="camporFormularioSimple" value="<?=$CodDes?>" />
										</div>									
									</td>
								</tr>
								<?php if ($err & 256) { ?>
                            	<tr>
                					<td><div align="right"></div></td>
									<td><li style="color:#FF0000;">Cod Des debe ser n&uacute;merico</li></td>
								</tr>
								<?php }  */?>
								 <tr>
									<td><div align="right">Stock M&aacute;ximo:</div></td>
									<td>
										<div align="left">
											<input type="text" name="StockMaximo" id="StockMaximo" class="camporFormularioSimple" value="<?= $StockMaximo ?>" />
										</div>									
									</td>
								</tr>
								<tr>
									<td><div align="right">Stock M&iacute;nimo:</div></td>
									<td>
										<div align="left">
											<input type="text" name="StockMinimo" id="StockMinimo" class="camporFormularioSimple" value="<?= $StockMinimo ?>" />
										</div>									
									</td>
								</tr>
								<tr>
									<td><div align="right">Utilidad:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Utilidad" id="Utilidad" class="camporFormularioSimple" value="<?= $Utilidad ?>" />
										</div>									
									</td>
								</tr>								 
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
                				<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'articulos.php<?=$strParams?>';" value="Cancelar" />
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