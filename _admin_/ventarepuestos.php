<?php 

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_LIST))
	Session::NoPerm();


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['FechaCargaDesde'] 		= $_REQUEST['FilterFechaCargaDesde'];
$filter['FechaCargaHasta']		= $_REQUEST['FilterFechaCargaHasta'];
$filter['IdCliente']			= $_REQUEST['FilterIdCliente'];
$filter['IdFactura']			= $_REQUEST['FilterIdFactura'];
$filter['IdRemito']				= $_REQUEST['FilterIdRemito'];
$filter['CodigoRepuesto']		= $_REQUEST['FilterCodigoRepuesto'];
$filter['Facturado']			= trim($_REQUEST['FilterFacturado']);


/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if (!IsEmptyArray($filter))
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}

$filter['TipoOperacion']		= '' . TipoVenta::Mostrador;
$filter['IdTipoMovimiento']		= '' . TipoMovimiento::Venta;

/* declaracion de variables */
$arr = array();

$Compras				= new Compras();
$Clientes				= new Clientes();
$Comprobantes			= new Comprobantes();
$oNotasCredito			= new NotasCredito();
$Ubicaciones 			= new Ubicaciones();
$TallerUnidades			= new TallerUnidades();
$oFacturasPostVentas 	= new FacturasPostVentas();
$oPage 					= new Page($Page);
$oPage->Size 			= 20;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $Compras->GetPagesCount($oPage, $filter))
	$Page = $Compras->GetPagesCount($oPage, $filter);

$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$arr 			= $Compras->GetAll($filter, $oPage);
$CountRows		= $Compras->GetCountRows($filter);

$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, true);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterFechaCargaDesde=' 		. $filter['FechaCargaDesde'];
$strParams.= '&FilterFechaCargaHasta=' 	. $filter['FechaCargaHasta'];
$strParams.= '&FilterIdCliente='	. $filter['IdCliente'];
$strParams.= '&FilterIdFactura='	. $filter['IdFactura'];
$strParams.= '&FilterIdFactura='	. $filter['IdFactura'];
$strParams.= '&FilterCodigoRepuesto='	. $filter['CodigoRepuesto'];
/* incluimkos funcion para armar suggest */
IncludeSUGGEST();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
<script language="javascript" src="../js/jquery.tooltip.js"></script>
<link rel="stylesheet" href="../css/jquery.tooltip.css" />
<script language="javascript">
var	IdTipoComprobanteRemito = '<?=ComprobanteTipos::Remito?>';
var IdTipoComprobante = '';
var arrParams = new Array();
var arrParamsRemito = new Array();

function SetNumeroComprobante(IdComprobante, NumeroComprobante)
{
	Get('FilterIdFactura').value 		= IdComprobante;
	Get('Factura').value 	= NumeroComprobante;
}

function SetNumeroComprobanteRemito(IdComprobante, NumeroComprobante)
{
	Get('FilterIdRemito').value 		= IdComprobante;
	Get('Remito').value 	= NumeroComprobante;
}

function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = Page;		
	frmData.submit();
}

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = 0;
	frmData.submit();
}

function ClearCampos()
{
	var frmData = Get('frmData');
	
	frmData.FilterFechaCargaDesde.value 		= '';
	frmData.FilterFechaCargaHasta.value = '';
	frmData.FilterIdCliente.value = '';
	frmData.FilterIdFactura.value 	= '';
	frmData.FilterIdRemito.value 	= '';
	
	return true;
}

function ClearFilter()
{
	window.location.href='ventarepuestos.php';
}								

function ShowFilter()
{
	HideSection('ShownFilter');
	ShowSection('HiddenFilter');
	ShowSection('FilterMain');
}

function HideFilter()
{
	ShowSection('ShownFilter');
	HideSection('HiddenFilter');
	HideSection('FilterMain');
}

function FilterCliente(IdCliente, Nombre)
{
	if ((IdCliente == '') && (Nombre == ''))
	{		
		$j('#Cliente').val('');
		$j('#IdCliente').val('');
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;
	
	$j('#Cliente').val(oCliente.RazonSocial);
	$j('#FilterIdCliente').val(oCliente.IdCliente);
}

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Ventas de Repuestos</span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
              <?php if (Session::CheckPerm(PERM_ARTI_CREATE)){ ?>
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                  <td><a title="Agregar" href="ventarepuestos_add.php<?=$strParams?>">Agregar</a></td>
                </tr>
              <?php } ?>
              </table></td>
             
          </tr>
        </table>
	  </td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
				<input type="hidden" name="MainAction" id="MainAction">
				<input type="hidden" name="Id" id="Id">
				<input type="hidden" name="filtroActivo" id="filtroActivo" value="1">
				
				<div class="bordeGrisFondo" id="ShownFilter" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px; ">
			   		<table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
				</div>
				<div class="bordeGrisFondo" id="HiddenFilter" style="<?=$filterStyle;?> padding-left: 10px; padding-bottom: 10px; padding-right: 10px; padding-top: 10px; " >
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>[-] <a href="#bottom" class="linkMenu" onClick="javascript: HideFilter();"> <b>Ocultar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                        </tr>
                    </table>
				</div>
                <div align="center">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="1"><div align="center"></div></td>
                    </tr>
                  </table>
                </div>
				<div id="FilterMain" style="<?=$filterStyle;?>" class="">
				<div id="Filter" >
					<table border="0"  class="bordeGrisFondo" align="left" cellpadding="2" cellspacing="2" width="100%" >
						<tr>
						  <td class="tituloMenu"><table border="0" align="left" cellpadding="0" cellspacing="0">
							<tr>
                              <td class="tituloMenu">Fecha Carga Desde:</td>
                              <td width="270">
								<input type="text" name="FilterFechaCargaDesde" id="FilterFechaCargaDesde" class="camporFormularioMediano" value="<?=$filter['FechaCargaDesde']?>" />
								<script language="javascript">
								new tcal({'formname': 'frmData', 'controlname': 'FilterFechaCargaDesde'});
								</script>
							</td>
							
										<td>&nbsp;</td>
                              <td class="tituloMenu">Fecha Carga Hasta:</td>
                              <td width="270">
							  <input type="text" name="FilterFechaCargaHasta" id="FilterFechaCargaHasta" class="camporFormularioMediano" value="<?=$filter['FechaCargaHasta']?>" />
							<script language="javascript">
							new tcal({'formname': 'frmData', 'controlname': 'FilterFechaCargaHasta'});
							</script>
							  </td>
                            </tr>
                            <tr>
                              <td class="tituloMenu">Cliente:</td>
                              <td width="270">
							  <input type="hidden" name="FilterIdCliente" id="FilterIdCliente" value="<?=$filter['IdCliente']?>" />
							  <input type="text" name="Cliente" id="Cliente" class="camporFormularioSimple"  value="<?=$_REQUEST['Cliente']?>" autocomplete="Off" />
							  <script language="">												
									SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
								</script>
							  </td>
										<td>&nbsp;</td>
										<td class="tituloMenu"><div align="right">Facturado:</div></td>
                                        <td>
											<select id="FilterFacturado" name="FilterFacturado" class="camporFormularioSimple">
												<option value>INDISTINTO</option>
												<option value="1" <?= $filter['Facturado'] == '1' ? 'selected="selected"' : '' ?>>SI</option>
												<option value="0" <?= $filter['Facturado'] == '0' ? 'selected="selected"' : '' ?>>NO</option>
											</select>
										</td>
                              <td>&nbsp;</td>
							  <td valign="middle">
									<div align="left">
									</div>
								</td>
                            </tr>
                            <tr>
                              <td class="tituloMenu">C&oacute;digo Repuesto:</td>
                              <td width="270">
							  <input type="text" name="FilterCodigoRepuesto" id="FilterCodigoRepuesto" class="camporFormularioSimple"  value="<?=$filter['CodigoRepuesto']?>" autocomplete="Off" />
							 
							  </td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
                              <td>&nbsp;</td>
							  <td valign="middle">
									<div align="left">
										<input type="submit" name="button" id="button" class="botonBasico" value="Buscar" />
									</div>
								</td>
                            </tr>
                          </table></td>
					  </tr>
					</table>
				</div>
				</div>
        	</form>
      	</td>
  	</tr>	
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
  
<?php if ($arr != NULL) { ?>
  	
	<tr>
    	<td>
			<div align="right"><?php print_r ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Estado</strong></div></td>
					<td width="230" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>					
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Factura</strong></div></td>	
					<td width="100" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oCompra) 
			{ 
				$oCliente = $Clientes->GetById($oCompra->IdCliente);
				$oTallerUnidad = $TallerUnidades->GetById($oCompra->IdTallerUnidad);
				$oCompra->LoadAllDetalles();
				$oTipoVenta	= TipoVenta::GetById($oCompra->TipoOperacion);
				$oFacturaPostVenta = $oFacturasPostVentas->GetByCompra($oCompra);
				$oFactura = $Comprobantes->GetById($oFacturaPostVenta[0]->IdComprobante);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=cambiarFecha($oCompra->FechaCarga)?></div></td>
					<td height="25">
						<div id="margen">
							<?php
							if ($oCompra->IdFactura == -1)
							{
							?>
							Anulada
							<?php
							}
							elseif (!$oFacturaPostVenta)
							{
							?>
							Sin Facturar
							<?php
							}
							elseif ($oFactura->IdEstado !=  ComprobanteEstados::Anulado)
							{
							?>
							Facturada
							<?php
							}
							else
							{
							?>
							Anulada
							<?php
							}
							?>
						</div>
					</td>
	                <td height="25"><div id="margen"><?=$oCliente ? $oCliente->GetUsuario() : $oTallerUnidad->Dominio ?></div></td>
	                <td height="25"><div id="margen">$<?= number_format($oCompra->Total(), 2)?></div></td>
					<td height="25"><div id="margen"><?= ComprobanteTipos::GetLetraById($oFactura->IdTipoComprobante) ?><?=$oFactura->Prefijo?> - <?=$oFactura->Numero?></div></td>					
	                <td width="100" height="25">
						<div align="center">                         
							<a href="ventarepuestos_details.php<?=$strParams?>&IdCompra=<?=$oCompra->IdCompra?>">
								<img src="images/iconos/preview.gif" alt="Ver Detalles" border="0" /></a>
							<?php
							if ($oFactura->IdEstado != ComprobanteEstados::Anulado && $oCompra->IdFactura != -1)
							{
								if (!$oFacturaPostVenta)
								{
							?> - <a href="ventasrepuestos_factura_add.php<?=$strParams?>&IdCompra=<?=$oCompra->IdCompra?>">
								<img src="images/iconos/facturacion.png" alt="Facturar" border="0" /></a> - <a href="ventasrepuestos_factura_add_1.php<?=$strParams?>&IdCompra=<?=$oCompra->IdCompra?>">
								<img src="images/iconos/alerta.gif" width="16" title="Agregar Factura Talonario" alt="Facturar" border="0" /></a>
							<?php
								}
								elseif (!$oFactura->Numero || $oFactura->Numero == '00000000'){
							?>
											 - <form action="ventasrepuestos_factura_afip.php" style="display: inline">
												<input type="hidden" name="IdFactura" id="IdFactura" value="<?= $oFacturaPostVenta[0]->IdFacturaPostVenta ?>" />
												<input type="image" src="images/iconos/refresh.gif" alt="Enviar AFIP" title="Enviar AFIP" border="0" />
											</form>
							<?php
								}
								elseif ($oFactura)
								{
							?> - <a target="_blank" href="facturaspostventas_pdf.php<?=$strParams?>&IdFacturaPostVenta=<?= $oFacturaPostVenta[0]->IdFacturaPostVenta ?>">
										<img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a> - 
									<a href="facturaspostventas_pagos.php<?=$strParams?>&IdFacturaPostVenta=<?=$oFacturaPostVenta[0]->IdFacturaPostVenta?>">
										<img src="images/iconos/currency.gif" width="16" alt="Ver Pagos" border="0" /></a>
									<?php
}
?>									- <a href="ventarepuestos_anular.php<?=$strParams?>&IdCompra=<?=$oCompra->IdCompra?>">
								<img src="images/iconos/permisos.gif" alt="Anular" border="0" /></a>
							<?php
							}
							else
							{
									if ($oCompra->IdFactura != -1)
									{
										$oNotaCredito = $oNotasCredito->GetByIdFactura($oFacturaPostVenta[0]->IdComprobante);
										
										$oComprobanteNC = $Comprobantes->GetById($oNotaCredito->IdComprobante);
										if (!$oComprobanteNC->Numero || $oComprobanteNC->Numero == '00000000'){
										?>
											<form action="ventarepuestos_factura_notascredito_afip.php" style="display: inline">
												<input type="hidden" name="IdFactura" id="IdFactura" value="<?= $oFacturaPostVenta[0]->IdFacturaPostVenta ?>" />
												<input type="image" src="images/iconos/refresh.gif" alt="Enviar AFIP" title="Enviar AFIP" border="0" />
											</form> - 
										<?php
										}
										else
										{
							?>
								 - <a target="_blank" href="facturaspostventas_notacredito_imprimir.php<?=$strParams?>&IdFacturaPostVenta=<?= $oFacturaPostVenta[0]->IdFacturaPostVenta ?>">
										<img src="images/iconos/pdf.png" alt="Imprimir" border="0" /></a>
							<?php
										}
									}
							}
							?>
		                </div>
                    </td>
              </tr>
	  			<tr>
        			<td colspan="9"><div align="center">
          				<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            				<tr>
              					<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            				</tr>
          				</table>
        			</div></td>
      			</tr>
      
	  		<?php } ?>      
			</table>
	  </td>
  	</tr>
  	<tr>
    	<td>
			<br>
        	<div align="right"><?php print ($Paginado) ?></div>
      		<br>    
		</td>
  	</tr>

<?php } else { ?>  

	<tr>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
          		<tr>
            		<td>&nbsp;</td>
          		</tr>
          		<tr>
            		<td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
          		</tr>
          		<tr>
            		<td><div align="center"><strong>No hay registros disponibles.</strong></div></td>
          		</tr>
          		<tr>
            		<td>&nbsp;</td>
          		</tr>
        	</table>
		</td>
  	</tr>
      
<?php } ?>

</table>
</body>
</html>