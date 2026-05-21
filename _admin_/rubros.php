<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RUBR_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;

/* armamos el filtro */
$filter = array();
$filter['Nombre'] = $_REQUEST['FilterNombre'];

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if (!IsEmptyArray($filter))
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}

/* declaracion de variables */
$arr 			= array();
$Rubros 		= new Rubros();
$oPage 			= new Page($Page);
$oPage->Size 	= 20;
/* SOLUCION TEMPORAL PARA EL PAGINADOR */
if ($Page > $Rubros->GetPagesCount($oPage, $filter))
	$Page = $Rubros->GetPagesCount($oPage, $filter);

$oPage 			= new Page($Page);
$oPage->Size 	= 20;
$arr 			= $Rubros->GetAll($filter, $oPage);
$CountRows		= $Rubros->GetCountRows($filter);
$Paginado		= Pageable::PrintPaginator($oPage, $Rubros->GetPagesCount($oPage, $filter), $CountRows);

$strParams = '';
$strParams.= '?Page=' . $Page;
$strParams.= '&FilterNombre=' . $filter['Nombre'];

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

/*function ClearCampos()
{
	var frmData = Get('frmData');
	frmData.FilterNombre.value = '';
}*/

function ClearFilter()
{	
	window.location.href = 'rubros.php';
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

<?php include('include/head.inc.php'); ?>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Rubros</span></td>
      			</tr>
    		</table>		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      			<tr>
        		<?php if (Session::CheckPerm(PERM_RUBR_CREATE)){ ?>
                	<td width="30" height="40"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" title="Agregar" border="0"></div></td>
        			<td height="40"><a title="Agregar" href="rubros_add.php<?=$strParams?>">Agregar</a></td>
                <?php } ?>
                
      			    <td width="10">&nbsp;</td>
      			</tr>
	  </table>		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>">
				<input type="hidden" name="MainAction" id="MainAction">
				<input type="hidden" name="Id" id="Id">
				<input type="hidden" name="filtroActivo" id="filtroActivo" value="1">
				
				<!-- Aca van los filtros -->				
				<div id="ShownFilter" class="bordeGrisFondo" style="<?=$filterMostrar;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td>[+] <a href="#bottom" class="linkMenu" onClick="javascript: ShowFilter();"> <b> Mostrar b&uacute;squeda y filtros</b></a></td>
                            <td><div align="right"><a href="#" onClick="javascript: ClearFilter();" class="linkMenu">[Volver al listado general]</a></div></td>
                          </tr>
                        </table>
				</div>
				<div id="HiddenFilter" style="<?=$filterStyle;?> padding-right: 10px; padding-left: 10px; padding-top: 10px; padding-bottom: 10px;" class="bordeGrisFondo" >
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
						  <td class="tituloMenu"><table border="0" cellspacing="0" cellpadding="0">
                            <tr>                              
                              <td class="tituloMenu"><div align="right">Nombre:</div></td>
                              <td><input name="FilterNombre" id="FilterNombre" type="text" class="camporFormularioSimple" value="<?=$filter['Nombre']?>" maxlength="128"></td>
                              <td><input type="submit" name="button" id="button" class="botonBasico" value="Buscar"></td>
                            </tr>
                          </table></td>
					  </tr>
					</table>
                </div>
				</div>				
        	</form>      	</td>
  	</tr>
	<tr>
		<td>&nbsp;
		</td>
	</tr>
	  
<?php if ($arr != NULL) { ?>
  		
  	<tr>
  	  <td>&nbsp;</td>
  </tr>
  	<tr>
    	<td>
			<div align="right"><? print ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
			  <tr class="bordeGrisFondo">
                <td height="25" class="bordeGrisTitulo"><div id="margen"><strong>Rubro</strong></div></td>
		  		<td width="100" height="25"  class="bordeGrisTitulo"><div align="center" ><strong>Acciones</strong></div></td>
   			  </tr>
      
	  		<?php foreach ($arr as $oRubro) { ?>
      
            <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
	            <td height="25"><div id="margen"><?=$oRubro->Nombre?></div></td>
	            <td width="100" height="25" valign="middle">
                	<div align="center">
                    <?php if (Session::CheckPerm(PERM_RUBR_UPDATE)){ ?>
                    	<a href="rubros_mod.php<?=$strParams?>&IdRubro=<?=$oRubro->IdRubro?>">
                        	<img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a> - 
                    <?php } ?>
                    <?php if (Session::CheckPerm(PERM_RUBR_DELETE)){ ?>
			            <a href="rubros_del.php<?=$strParams?>&IdRubro=<?=$oRubro->IdRubro?>">
				            <img src="images/iconos/del.gif" alt="Eliminar" title="Eliminar" border="0" /></a>
                    <?php } ?>
		            </div>
	            </td>
            </tr>
            <tr>
	            <td colspan="5"><div align="center">
    	    	    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        	   	 		<tr>
            				<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            			</tr>
            		</table>
            	</div></td>
            </tr>
      
	  		<?php } ?>      
	  </table>		</td>
  	</tr>
  	<tr>
    	<td>
			<br>
        	<div align="right"><? print ($Paginado) ?></div>
      		<br>		</td>
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
        	</table>		</td>
  	</tr>
      
<?php } ?>
</table>

</body>

<script language="javascript">
	//HideFilter();
</script>

</html>