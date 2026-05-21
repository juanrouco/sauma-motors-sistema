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


/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if (!IsEmptyArray($filter))
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}

$filter['TipoOperacion']		= '' . TipoVenta::Mostrador;
$filter['IdTipoMovimiento']		= '' . TipoMovimiento::Devolucion;

/* declaracion de variables */
$arr = array();

$Compras		= new Compras();
$Clientes		= new Clientes();
$Comprobantes	= new Comprobantes();
$Ubicaciones 	= new Ubicaciones();
$TallerUnidades	= new TallerUnidades();
$oNotasCredito	= new NotasCredito();
$oPage 			= new Page($Page);
$oPage->Size 	= 20;

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $Compras->GetPagesCount($oPage, $filter))
	$Page = $Compras->GetPagesCount($oPage, $filter);

$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$arr 			= $Compras->GetAll($filter, $oPage);
$CountRows		= $Compras->GetCountRows($filter);
$Paginado		= Pageable::PrintPaginator($oPage, $CountRows, $Compras->GetPagesCount($oPage, $filter));


/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterFechaCargaDesde=' 		. $filter['FechaCargaDesde'];
$strParams.= '&FilterFechaCargaHasta=' 	. $filter['FechaCargaHasta'];
$strParams.= '&FilterIdCliente='	. $filter['IdCliente'];
$strParams.= '&FilterIdFactura='	. $filter['IdFactura'];
$strParams.= '&FilterIdRemito='	. $filter['IdRemito'];
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
	window.location.href='devolucionrepuestos.php';
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
        			<td height="40"><span class="tituloPagina">Devoluciones de Repuestos</span></td>
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
                  <td><a title="Agregar" href="devolucionrepuestos_add.php<?=$strParams?>">Agregar</a></td>
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
			<div align="right"><? print ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">      			
				<tr class="bordeGrisFondo">					
					<td width="75" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Tipo Venta</strong></div></td>
					<td width="230" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Cliente/Unidad</strong></div></td>
					<td width="85" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>					
					<td width="100" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Nota Credito</strong></div></td>	
					<td width="100" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oCompra) 
			{ 
				$oCliente = $Clientes->GetById($oCompra->IdCliente);
				$oTallerUnidad = $TallerUnidades->GetById($oCompra->IdTallerUnidad);
				$oCompra->LoadAllDetalles();
				$oNotaCredito = $oNotasCredito->GetById($oCompra->IdNotaCredito);
				$oComprobante = $Comprobantes->GetById($oNotaCredito->IdComprobante);
				$oTipoVenta	= TipoVenta::GetById($oCompra->TipoOperacion);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=cambiarFecha($oCompra->FechaCarga)?></div></td>
					<td height="25"><div id="margen"><?=$oTipoVenta['Nombre']?></div></td>
	                <td height="25"><div id="margen"><?=$oCliente ? $oCliente->GetUsuario() : $oTallerUnidad->Dominio ?></div></td>
	                <td height="25"><div id="margen">$<?= number_format($oCompra->Total(), 2)?></div></td>
					<td height="25"><div id="margen"><?=$oComprobante->Prefijo?> - <?=$oComprobante->Numero?></div></td>					
	                <td width="100" height="25">
						<div align="center">                         
							<a href="devolucionrepuestos_details.php<?=$strParams?>&IdCompra=<?=$oCompra->IdCompra?>">
								<img src="images/iconos/preview.gif" alt="Ver Detalles" border="0" /></a>
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
        	<div align="right"><? print ($Paginado) ?></div>
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