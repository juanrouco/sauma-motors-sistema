<?php 

require_once('../inc_library.php'); 


/* sección exclusiva para s autentificados */
Session::ForceLogin();


/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PROVE_LIST))
	Session::NoPerm();


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$filter['Empresa'] 		= $_REQUEST['FilterEmpresa'];
$filter['Email'] 		= $_REQUEST['FilterEmail'];
$filter['IdTipo']		= $_REQUEST['FilterTipo'];
$filter['IdRubro']		= $_REQUEST['FilterIdRubro'];
$filter['IdPais']		= $_REQUEST['FilterPais'];
$filter['IdProvincia']	= $_REQUEST['FilterProvincia'];

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle = "display:none;";
$filterMostrar = "";
if (!IsEmptyArray($filter))
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}



/* declaracion de variables */
$arr = array();

$Paises			= new Paises();
$Provincias		= new Provincias();
$Proveedores 	= new Proveedores();
$oPage 			= new Page($Page);
$Rubros			= new Rubros();
$oPage->Size 	= 20;
$arrPaises 		= $Paises->GetAll();

/* SOLUCION TEMPORAL PARA EL PAGINADOR */

if ($Page > $Proveedores->GetPagesCount($oPage, $filter))
	$Page = $Proveedores->GetPagesCount($oPage, $filter);

$oPage 		= new Page($Page);
$oPage->Size = 20;
$arr 			= $Proveedores->GetAll($filter, $oPage);
$Paginado		= Pageable::PrintPaginator($oPage, $Proveedores->GetCountRows($filter), true);

$arrRubros  			= $Rubros->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 				. $Page;
$strParams.= '&FilterEmpresa=' 		. $filter['Empresa'];
$strParams.= '&FilterEmail=' 		. $filter['Email'];
$strParams.= '&FilterTipo='			. $filter['IdTipo'];
$strParams.= '&FilterIdRubro='		. $filter['IdRubro'];
$strParams.= '&FilterPais='			. $filter['IdPais'];
$strParams.= '&FilterProvincia='	. $filter['IdProvincia'];


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

	frmData.FilterEmpresa.value 		= '';
	frmData.FilterEmail.value 			= '';

																														
	
	return true;
}

function ClearFilter()
{
	window.location.href='proveedores.php';
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

function LoadProvincias(Element, IdPais, IdSelected)
{
	var arr 	= new Array();
	var opts 	= Get(Element).options;
	var obj;
	var opt;
	var oProvincias;
				
	opts.length = 0;
	opts.add(new Option('Indistinto', ''));

	if (IdPais == '')
		IdPais = 0;

	arr['IdPais'] = IdPais;
				
	obj = SendXMLRequest('Provincias', 'GetAll', null, arr);
	if (obj.Status.Id != 0)
	{
		alert(obj.Status.Description);
		return;
	}
	
	oProvincias = obj.Response.Rows;
	
	for (var i=0; oProvincias && i<oProvincias.length; i++)
	{
		var oProvincia = oProvincias[i];
	
		opt = new Option(oProvincia.Nombre, oProvincia.IdProvincia);
		opt.selected = (oProvincia.IdProvincia == IdSelected);
		opts.add(opt);
	}	
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
        			<td height="40"><span class="tituloPagina">Proveedores </span></td>
   			  </tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td height="30" valign="top">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		    <tr>
              <td height="40"><table border="0" align="left" cellpadding="0" cellspacing="0">
              <?php if (Session::CheckPerm(PERM_PROVE_CREATE)){ ?>
                <tr>
                  <td width="30"><div align="center"><img src="images/iconos/add.gif" alt="Agregar" border="0"></div></td>
                  <td><a title="Agregar" href="proveedores_add.php<?=$strParams?>">Agregar</a></td>
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
                              <td class="tituloMenu">Pa&iacute;s:</td>
                              <td width="270"><select name="FilterPais" id="FilterPais" class="camporFormularioSimple" onChange="javascript:LoadProvincias('FilterProvincia', this.value, '');">
                                <option value="" >Indistinto</option>
                                <?php if ($arrPaises){ ?>
                                <?php foreach ($arrPaises as $oPais) { ?>
                                <option value="<?=$oPais->IdPais?>" <?php echo ($oPais->IdPais == $filter['IdPais']) ? "selected='selected'" : "" ?> >
                                <?=$oPais->Nombre;?>
                                </option>
                                <?php } ?>
                                <?php } ?>
                              </select></td>
                              <td class="tituloMenu">Empresa:</td>
                              <td>
							  	<input name="FilterEmpresa" id="FilterEmpresa" type="text" class="camporFormularioSimple" value="<?=$filter['Empresa']?>" maxlength="128">
															  </td>
                              <td>&nbsp;</td>
                            </tr>
                            <tr>
                              <td class="tituloMenu">Provincia:</td>
                              <td width="270"><select name="FilterProvincia" id="FilterProvincia" class="camporFormularioSimple">
                                <option value="">Indistinto</option>
                              </select></td>
                               <td class="tituloMenu">Rubro:</td>
                              <td width="270"><select name="FilterIdRubro" id="FilterIdRubro" class="camporFormularioSimple">
                                <option value="" >Indistinto</option>
                                <?php foreach ($arrRubros as $oRubro) { ?>
                                <option value="<?=$oRubro->IdRubro?>" <?php echo ($oRubro->IdRubro == $filter['IdRubro']) ? "selected='selected'" : "" ?> >
                                <?=$oRubro->Nombre;?>
                                </option>
                                <?php } ?>
                              </select></td>
							  <td>&nbsp;</td>
                                                          <td valign="middle"><div align="left">
                                <input type="submit" name="button" id="button" class="botonBasico" value="Buscar">
                              </div></td>
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
			<div align="right"><?php print ($Paginado) ?></div>
        	<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
      			<tr class="bordeGrisFondo">
				  <td width="231" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Proveedor</strong></div></td>
				  <td width="103" height="25" class="bordeGrisTitulo"><strong>Cuit</strong></td>
                  <td width="103" height="25" class="bordeGrisTitulo"><strong>Rubro</strong></td>
                  <td width="158" height="25" class="bordeGrisTitulo"><strong>Teléfono 1</strong></td>
                  <td width="158" height="25" class="bordeGrisTitulo"><strong>Teléfono 2</strong></td>                
                  <td width="107" height="25" class="bordeGrisTitulo"><strong>E-mail</strong></td>                  
       			  <td width="140" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
   			  </tr>
      
	  		<?php foreach ($arr as $oProveedor) { 
							
			$oPais 		= $Paises->GetById($oProveedor->IdPais);
			$oProvincia = $Provincias->GetById($oProveedor->IdProvincia);
			$oRubro 	= $Rubros->GetById($oProveedor->IdRubro);
			?>
          
          <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                	<td height="25"><div id="margen"><?=$oProveedor->Empresa?></div></td>
					<td height="25"><?=$oProveedor->Cuit?></td>
	                <td height="25"><?=$oRubro->Nombre?></td>
	                <td height="25"><?=$oProveedor->TelefonoCodigoArea?> <?=$oProveedor->Telefono?></td>
   	                <td height="25"><?=$oProveedor->TelefonoCodigoArea2?> <?=$oProveedor->Telefono2?></td>
	                <td height="25"><?=$oProveedor->Email?></td>
	                <td width="140" height="25">
                   <div align="center">
                         <?php if (Session::CheckPerm(PERM_PROVE_EDIT)){ ?>
			                <a href="proveedor_contactos.php<?=$strParams?>&IdProveedor=<?=$oProveedor->IdProveedor?>">
				                <img src="images/iconos/usuarios.png" alt="Contactos" title="Contactos" border="0" /></a> - 
                        <?php } ?>
						<?php if (Session::CheckPerm(PERM_PROVE_UPDATE)){ ?>
			                <a href="proveedores_mod.php<?=$strParams?>&IdProveedor=<?=$oProveedor->IdProveedor?>">
				                <img src="images/iconos/mod.gif" alt="Modificar" title="Modificar" border="0" /></a> - 
                        <?php } ?>

                        <?php if (Session::CheckPerm(PERM_PROVE_DELETE)){ ?>
			                <a href="proveedores_del.php<?=$strParams?>&IdProveedor=<?=$oProveedor->IdProveedor?>">
				                <img src="images/iconos/del.gif" alt="Eliminar" title="Eliminar" border="0" /></a>
                        <?php } ?>
		                </div>
                    </td>
              </tr>
	  			<tr>
        			<td colspan="7"><div align="center">
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


<script language="javascript">

LoadProvincias('FilterProvincia', '<?=$filter['IdPais']?>', '<?=$filter['IdProvincia']?>');

</script>

<script language="javascript">
	//HideFilter();
</script>

</html>