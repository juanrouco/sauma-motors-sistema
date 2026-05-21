<?php 

require_once('../inc_library.php'); 


/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ARTI_LIST))
	Session::NoPerm();


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['IdArticulo'] 	= $_REQUEST['FilterIdArticulo'];
$filter['IdUbicacion']	= $_REQUEST['FilterIdUbicacion'];

$IdArticulo = $filter['IdArticulo'] ;

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if ($filter['IdUbicacion'] != '' && $filter['IdUbicacion'] != null)
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}

/* declaracion de variables */
$arr = array();

$Ubicaciones 	= new Ubicaciones();
$Articulos	 	= new Articulos();
$ArticuloStocks = new ArticuloStocks();
$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$oArticulo	 	= $Articulos->GetById($IdArticulo);
$arrUbicaciones = $Ubicaciones->GetAll();

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $ArticuloStocks->GetPagesCount($oPage, $filter))
	$Page = $ArticuloStocks->GetPagesCount($oPage, $filter);

$oPage 		= new Page($Page);
$oPage->Size = 20;
$arr 			= $ArticuloStocks->GetAll($filter, $oPage);
$CountRows		= $ArticuloStocks->GetCountRows($filter);
$Paginado		= Pageable::PrintPaginator($oPage, $ArticuloStocks->GetPagesCount($oPage, $filter), $CountRows);


/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterIdArticulo=' 	. $filter['IdArticulo'];
$strParams.= '&FilterIdUbicacion=' 	. $filter['IdUbicacion'];


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<script language="javascript">
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

	frmData.FilterIdUbicacion.value = '';	
	
	return true;
}

function ClearFilter()
{
	window.location.href='proveedores.php?FilterIdArticulo=' + $filter['IdArticulo'] ;
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

</script>

<?php include('include/head.inc.php'); ?></head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina"><?= $oArticulo->Descripcion ?> (<?= $oArticulo->Codigo ?>) - Stocks Actual </span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td height="40">
						<table border="0" align="left" cellpadding="0" cellspacing="0">
							<?php if (Session::CheckPerm(PERM_STOCK_CREATE)){ ?>
								<tr>
									<td width="30">
										<div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div>
									</td>
									<td>
										<a title="Agregar" href="articulostocks_add.php<?=$strParams?>&IdArticulo=<?= $IdArticulo ?>">Agregar</a>
									</td>
								</tr>
							<?php } ?>
						</table>
						<table border="0" align="right" cellpadding="0" cellspacing="0">							
							<tr>
								<td>
									<a class="linkMenu" href="articulos.php<?=$strParams?>">[Volver a Articulos]</a>
								</td>
							</tr>							
						</table>
					</td>             
				</tr>
			</table>
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
					<td width="103" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Sucursal</strong></div></td>
					<td width="103" height="25" class="bordeGrisTitulo"><strong>Ubicaci&oacute;n</strong></td>
					<td width="158" height="25" class="bordeGrisTitulo"><strong>Stock Inicial</strong></td>
					<td width="158" height="25" class="bordeGrisTitulo"><strong>Stock Actual</strong></td>					
					<td width="80" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
				</tr>
      
	  		<?php foreach ($arr as $oArticuloStock) { 
				$oUbicacion = $Ubicaciones->GetById($oArticuloStock->IdUbicacion);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oUbicacion->Nombre?></div></td>
	                <td height="25"><?=$oArticuloStock->Ubicacion?></td>
	                <td height="25"><?=$oArticuloStock->StockInicial?></td>
   	                <td height="25"><?=$oArticuloStock->StockActual?></td>	                
	                <td width="80" height="25">
                   <div align="center">                         
						<?php if (Session::CheckPerm(PERM_STOCK_UPDATE)){ ?>
			                <a href="articulostocks_mod.php<?=$strParams?>&IdArticuloStock=<?=$oArticuloStock->IdArticuloStock?>&IdArticulo=<?=$oArticulo->IdArticulo?>">
				                <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a>
                        <?php } ?>
                        <?php /*if (Session::CheckPerm(PERM_STOCK_DELETE)){ ?>
			                <a href="articulostocks_del.php<?=$strParams?>&IdArticuloStock=<?=$oArticuloStock->IdArticuloStock?>">
				                <img src="images/iconos/del.gif" alt="Eliminar" title="Eliminar" border="0" /></a>
                        <?php } */?>
		                </div>
                    </td>
              </tr>
	  			<tr>
        			<td colspan="6"><div align="center">
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