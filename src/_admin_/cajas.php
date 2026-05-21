<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST) && $currentUser->IdUsuario != 21 && $currentUser->IdUsuario != 24 && $currentUser->IdPerfil != Perfil::Tesorero && !($currentUser->IdPerfil == Perfil::Vendedor && ($currentUser->IdUsuario == 24 || $currentUser->IdUsuario == 10)))
	Session::NoPerm();

/* obtenemos datos enviados */
$filter		= ReceiveArray($_REQUEST['filter']);
$Page 		= intval($_REQUEST['Page']);
$PageSize 	= intval($_REQUEST['PageSize']);

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$arrData 			= array();
$oCajas 			= new Cajas();
$oCajasDetalles		= new CajasDetalles();
$oPage 				= new Page($Page, $PageSize);

/* armamos el filtro en caso de que no este armado */
if ((!isset($filter)) || (IsEmptyArray($filter)) || ($Submit))
{
	$filter = array();
}

/* si el filtro esta aplicado mantiene el filtro */
$filterStyle 	= (IsEmptyArray($filter)) 	? "display:none;" : "";
$filterMostrar 	= (!IsEmptyArray($filter)) 	? "display:none;" : "";

$Paginado					= Pageable::PrintPaginator($oPage, $oCajasDetalles->GetCountRows($filter), true);
$oUsuarioActivo 			= Session::GetCurrentUser();
//$filter['PermisoUsuario'] 	= $oUsuarioActivo->IdUsuario;

$arrData 					= $oCajasDetalles->GetAll($filter, $oPage);

$oCaja = $oCajas->GetById(1);

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 		. $Page;
$strParams.= '&PageSize=' 	. $PageSize;
$strParams.= '&filter=' 	. SendArray($filter);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>

<script language="javascript">

function SetPage(Page)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.Page.value = Page;		
	frmData.submit();
}

function SetPageSize(PageSize)
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	if (frmData.PageSize == undefined)
		return false;

	frmData.PageSize.value = PageSize;
	frmData.submit();
}

function Filtrar()
{
	var frmData = Get('frmData');
	
	if (frmData == undefined)
		return false;

	frmData.MainActino.value = '';
	frmData.Page.value = 0;
	
	return true;
}

function ClearFilter()
{
	var frmData = Get('frmData');

	if (frmData == undefined)
		return false;

	frmData.FilterUsuario.value 	= '';

	frmData.Page.value = 0;

	frmData.submit();	
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

<form name="frmData" id="frmData" method="post" action="<?=$_SEVER['PHP_SELF']?>" onSubmit="Filtrar();">
    <input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
	<input type="hidden" name="PageSize" id="PageSize" value="<?=$PageSize?>" />
    <input type="hidden" name="filtroActivo" id="filtroActivo" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Disponibilidades</span></td>
                    </tr>
                </table>
            </td>
        </tr>	
        <tr>
            <td></td>
            <td>&nbsp;</td>
        </tr>
      
    <?php if ($arrData != NULL) { ?>
        
        <tr>
            <td>
                <div align="right"><? print ($Paginado) ?></div>
                <br>
                <table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="50%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Disponibilidades</strong></div></td>
                        <td width="20%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Ultimo Movimiento</strong></div></td>
                        <td width="20%" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Total</strong></div></td>
                        <td width="10%" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php 
					foreach ($arrData as $oCajaDetalle) 
					{
						if ($currentUser->IdPerfil == 11 || ($currentUser->IdUsuario == 39 && ($oCajaDetalle->IdCajaDetalle == 4 || $oCajaDetalle->IdCajaDetalle == 20)) || (Session::CheckPerm(PERM_CAJA_LIST) || $currentUser->IdUsuario == 21) && 
							(($currentUser->IdUsuario == 25 || $currentUser->IdUsuario == 27) || $oCajaDetalle->IdCajaDetalle != 22) &&
							($currentUser->IdUsuario == 21 && ($oCajaDetalle->IdCajaDetalle == 8 || $oCajaDetalle->IdCajaDetalle == 12 || $oCajaDetalle->IdCajaDetalle == 11)) &&
							($currentUser->IdUsuario != 26 || $oCajaDetalle->IdCajaDetalle == 1) || ($currentUser->IdPerfil == 1)
							|| ($currentUser->IdPerfil == Perfil::Vendedor && ($currentUser->IdUsuario == 24 || $currentUser->IdUsuario == 10) && $oCajaDetalle->IdCajaDetalle == 6) || 
							($currentUser->IdUsuario == 29 && ( $oCajaDetalle->IdCajaDetalle != 6)) || 
							($currentUser->IdPerfil == Perfil::Tesorero && ($currentUser->IdUsuario == 25 || $currentUser->IdUsuario == 29  || $currentUser->IdUsuario == 17 || $currentUser->IdUsuario == 23) &&  (($oCajaDetalle->IdCajaDetalle == CajaDetalle::CajaCheques && ($currentUser->IdUsuario != 23 && $currentUser->IdUsuario != 29 && $currentUser->IdUsuario != 17)) || ($oCajaDetalle->IdCajaDetalle == 6 && $currentUser->IdUsuario == 23) || ($oCajaDetalle->IdCajaDetalle == 3 && $currentUser->IdUsuario != 23) || ($oCajaDetalle->IdCajaDetalle == 2 && $currentUser->IdUsuario != 23) || ($oCajaDetalle->IdCajaDetalle == 1 && $currentUser->IdUsuario != 23) || ($oCajaDetalle->IdCajaDetalle == 8 && $currentUser->IdUsuario != 29 && $currentUser->IdUsuario != 17 && $currentUser->IdUsuario != 25) || ($oCajaDetalle->IdCajaDetalle == 11 && $currentUser->IdUsuario == 29 && $currentUser->IdUsuario == 17) || ($oCajaDetalle->IdCajaDetalle == 3 && $currentUser->IdUsuario != 23 && $currentUser->IdUsuario != 29 && $currentUser->IdUsuario != 17) || ($oCajaDetalle->IdCajaDetalle == 10) || ($oCajaDetalle->IdCajaDetalle == 12 && $currentUser->IdUsuario != 29 && $currentUser->IdUsuario != 17 && $currentUser->IdUsuario != 25) || ($oCajaDetalle->IdCajaDetalle == 11 && $currentUser->IdUsuario != 29 && $currentUser->IdUsuario != 17))) || 
							($currentUser->IdPerfil == Perfil::Tesorero && $currentUser->IdUsuario == 8 &&  $oCajaDetalle->IdCajaDetalle == 1))
						{
							if ($oCajaDetalle->IdCajaDetalle == 22 && ($currentUser->IdUsuario != 25 && $currentUser->IdUsuario != 27 && $currentUser->IdPerfil != 1))
								continue;
							if ($currentUser->IdPerfil == 11 && ($oCajaDetalle->IdCajaDetalle != 20 && $oCajaDetalle->IdCajaDetalle != 4))
								continue;
				?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25">
							<div id="margen">
								<?=$oCajaDetalle->Nombre?> 
								<?php
								if ($oCajaDetalle->IdCajaDetalle == CajaDetalle::CajaCheques)
								{
								?>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="pagos_listado_cheque.php?IdTipoPago=4">[Ver Cartera]</a>
								<?php
								}
								?>
							</div>
						</td>
                        <td height="25"><div id="margen" align="center"><?=CambiarFechaHora($oCajaDetalle->FechaUltimoMovimiento)?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?=$oCajaDetalle->Total?></div></td>
                        <td width="77" height="25">
							<div align="center">
								<a href="cajas_detalle<?= $oCajaDetalle->IdCajaDetalle == CajaDetalle::CajaCheques ? '_cheque' : '' ?>.php<?=$strParams?>&IdCajaDetalle=<?=$oCajaDetalle->IdCajaDetalle?>"><img src="images/iconos/preview.gif" alt="Ver Detalle" title="Ver Detalle" border="0" /></a>
							</div>
						</td>
                    </tr>
                    <tr>
                        <td colspan="7">
                        	<div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                        	</div>
                      	</td>
                    </tr>
          
                <?php 
						}
					} 
					if (!($currentUser->IdPerfil == Perfil::Vendedor && $currentUser->IdUsuario == 10) && $currentUser->IdPerfil != Perfil::Tesorero)
					{
				?> 
				
                    <tr class="bordeGrisFondo">
                        <td width="50%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Total</strong></div></td>
                        <td width="20%" height="25" class="bordeGrisTitulo">&nbsp;</td>
                        <td width="20%" height="25"><div id="margen" align="center"><strong>$<?= $oCaja->TotalRendir ?></strong></div></td>
                        <td width="10%"><div align="center">&nbsp;</div></td>
                    </tr>   
				<?php
					}
					?>
                
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
</form>

</body>
</html>