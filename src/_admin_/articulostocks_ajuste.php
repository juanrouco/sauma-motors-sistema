<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para proveedores autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_STOCK_AJUSTE))
	Session::NoPerm();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);

/* obtiene datos del formulario */
$TipoOperacion	= $_REQUEST['TipoOperacion'];	
$Codigo			= $_REQUEST['Codigo'];
$IdArticulo 	= $_REQUEST['IdArticulo'];
$IdUbicacion	= $_REQUEST['IdUbicacion'];
$Ubicacion		= $_REQUEST['Ubicacion'];
$Cantidad		= $_REQUEST['Cantidad'];
$Observaciones	= $_REQUEST['Observaciones'];

$Submit			= isset($_REQUEST['Submitted']);

/* declaracion de variables */
$err				= 0;
$oStockMovimiento	= new StockMovimiento();
$StockMovimientos	= new StockMovimientos();
$ArticuloStocks		= new ArticuloStocks();
$Articulos			= new Articulos();
$oArticulo		 	= $Articulos->GetById($IdArticulo);

/* armamos cadena con parametros a mandar */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if ($Submit)
{
	/* validaciones... */
	if ($IdUbicacion == '')
		$err += 1;
		
	if ($IdArticulo == '')
		$err += 16;
			
	if ($Cantidad == '')
		$err += 8;

	/* si no hay errores... */
	if ($err == 0)
	{
		$Fecha = date("Y-m-d");
		$Fecha = CambiarFecha($Fecha);
		
		$oStockMovimiento->IdArticulo 		= $IdArticulo;
		$oStockMovimiento->IdUbicacion 		= $IdUbicacion;
		$oStockMovimiento->Remito			= $Remito;
		$oStockMovimiento->Fecha			= $Fecha;
		$oStockMovimiento->Cantidad			= $Cantidad;
		$oStockMovimiento->Observaciones	= $Observaciones;
		
		$oArticuloStocks = $ArticuloStocks->GetByArticuloAndUbicacion($IdArticulo, $IdUbicacion);
		if (!$oArticuloStocks)
		{
			$oArticuloStocks = new ArticuloStock();
			$oArticuloStocks->IdArticulo = $IdArticulo;
			$oArticuloStocks->IdUbicacion = $IdUbicacion;
			$oArticuloStocks->StockInicial = 0;
			$oArticuloStocks->StockActual = 0;
		}
		
		$oArticuloStocks->AumentarStock($Cantidad);
		
		if ($oArticuloStocks->IdArticuloStock)
			$oArticuloStocks = $ArticuloStocks->Update($oArticuloStocks);
		else
			$oArticuloStocks = $ArticuloStocks->Create($oArticuloStocks);
		
		/* crea el proveedor */
		$oStockMovimiento = $StockMovimientos->Create($oStockMovimiento);
		
		header("Location: articulostocks.php?FilterIdArticulo=" . $IdArticulo);
		exit();
	}
}
else
{
	/* determinamos como fecha de alta */
	$IdUbicacion = Ubicacion::Liberador;
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
		$j('#Ubicacion').val('');
		$j('#IdUbicacion').val('');
	}

	var oUbicacion = GetUbicacion(IdUbicacion);
	if (!(oUbicacion))
		return;
	
	$j('#Ubicacion').val(oUbicacion.Nombre);
	$j('#IdUbicacion').val(oUbicacion.IdUbicacion);
}

function BuscarArticulo()
{
	realizarBusqueda(1);
}

function SetPage(page)
	{
		realizarBusqueda(page);
	}

function SetArticulo(IdArticulo)
{
	if (IdArticulo == '')
	{		
		$j('#lblArticulo').html('');
		$j('#IdArticulo').val('');
	}

	var oArticulo = GetArticulo(IdArticulo);
	if (!(oArticulo))
		return;
	
	$j('#lblArticulo').html(oArticulo.Descripcion);
	$j('#IdArticulo').val(oArticulo.IdArticulo);
	$j('#modal-popup').dialog('close');
}

function realizarBusqueda(page) {
	if ($j('#Codigo').val() != '') {
		var urlAjax = 'articulos_buscar_popup.php?FilterIdUbicacion=&FilterCodigo=' + $j('#Codigo').val() + '&FilterDescripcion=&Page=' + page;
		$j('body').addClass("loading"); 
		$j.ajax(urlAjax,{
			success: function(data) {
				$j('#modal-popup').html(data);	
				$j('body').removeClass("loading"); 
				$j('.agregar').click(function() {
					var idArticulo = $j(this).attr('id').split('_')[1];							
					SetArticulo(idArticulo);
				});						
				
				$j('#modal-popup').dialog({
					closeOnEscape: true,
					title: 'Repuestos encontrados',
					width: 700,
					height: 550,
					modal: true
				});
			}
		});				
	}
}

$j(document).ready(function() { 
	<?php
	if ($IdUbicacion) {
	?>
		FilterUbicacion(<?= $IdUbicacion ?>, '');
	<?php
	}
	?>	
	<?php
	if ($IdArticulo) {
	?>
		SetArticulo('<?= $oArticulo->Codigo ?>');
	<?php
	}
	?>	
	$j('#Codigo').keypress(function(e) {
		if (e.which == 13) {			
			BuscarArticulo();
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
        			<td height="40"><span class="tituloPagina">Ajuste de Stock</span></td>
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
									<td><div align="right">C&oacute;digo Art&iacute;culo:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Codigo" id="Codigo" class="camporFormularioSimple" value="<?=$Codigo?>" />
											<img src="images/iconos/lupa.jpg" alt="Buscar" title="Buscar" class="buscar" onClick="javascript:BuscarArticulo();" />											
											<span style="color:#FF0000;">&nbsp;(*)</span>																		
										</div>
									</td>
								</tr>
								<tr>
									<td><div align="right">Descripci&oacute;n Art&iacute;culo:</div></td>
									<td height="25">
										<div align="left">
											&nbsp;&nbsp;<label id="lblArticulo">Seleccione un art&iacute;culo</label>
										</div>
									</td>
								</tr>
								<?php if ($err & 16) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Debe ingresar un art&iacute;culo</li></td>
                                </tr>
								<?php } ?>
								<tr>
                                	<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
								<tr>
									<td><div align="right">Sucursal:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Ubicacion" id="Ubicacion" class="camporFormularioSuggest" value="<?=$Ubicacion?>" autocomplete="off" />
											<input type="button" id="btnAddUbicacion" class="botonBasico"  onClick="javascript:AddUbicacion();" value=" + " />
											<span style="color:#FF0000;">&nbsp;(*)</span>
											<script language="">												
												SUGGESTRequest('Ubicaciones', 'GetAll', 'Ubicacion', 'FilterUbicacion', 'IdUbicacion', 'Nombre', 'FilterNombre', null);
											</script>								
										</div>
									</td>
								</tr>
                           <?php if ($err & 1) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese la ubicaci&oacute;n</li></td>
                                </tr>
                           <?php } ?>
								<tr>
									<td><div align="right">Cantidad:</div></td>
									<td>
										<div align="left">
											<input type="text" name="Cantidad" id="Cantidad" class="camporFormularioMediano" value="<?=$Cantidad;?>" />
											<span style="color:#FF0000;">&nbsp;(*)</span>											
										</div>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td height="25">
										<div align="left">
											&nbsp;&nbsp;Para agregar art&iacute;culos al stock ingrese un número positivo.<br />&nbsp;&nbsp;Para quitar del stock, ingrese uno negativo.
										</div>
									</td>
								</tr>
								<?php if ($err & 8) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Debe ingresar una cantidad</li></td>
                                </tr>
								<?php } ?>
								<tr>
                                	<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
								<tr>
									<td><div align="right">Observaciones:</div></td>
									<td>
										<div align="left">
											<textarea name="Observaciones" id="Observaciones" class="camporFormularioSuggest" style="height: 45px"><?= $Observaciones ?></textarea>
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
<div id="modal-popup" style="display:none">
</div>
</body>
</html>