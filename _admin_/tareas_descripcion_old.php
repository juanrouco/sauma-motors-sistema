<?php

require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TAREAS_UPDATE))
	Session::NoPerm();


$CurrentUser = Session::GetCurrentUser();

/* obtiene datos del formulario */
$IdTarea				= $_REQUEST['IdTarea'];
$Submit1				= $_REQUEST['Submitted1'];
$FechaSeg				= $_REQUEST['FechaSeg'];
$IdUsuarioSeg			= ($_REQUEST['IdUsuarioSeg'] != '') ? $_REQUEST['IdUsuarioSeg'] : $CurrentUser->IdUsuario;
$IdAccionSeg			= $_REQUEST['IdAccionSeg'];
$DetalleSeg				= $_REQUEST['DetalleSeg'];

/* declaracion de variables */
$oTareas 				= new Tareas();
$oTiposTareas 			= new TiposTareas();
$oTareaSeguimientos		= new TareaSeguimientos();
$oTareaSeguimiento		= new TareaSeguimiento();
$oUsuarios				= new Usuarios();

$filtro 				= array();
$filtro['IdTarea']		= $IdTarea; 
$arrSeguimientos		= $oTareaSeguimientos->GetAll($filtro);


/* verifica si existe el registro */
if (!$oTarea = $oTareas->GetById($IdTarea))
	exit;

$oUsuarioTo			= $oUsuarios->GetById($oTarea->IdUsuarioTo);
$oUsuarioFrom		= $oUsuarios->GetById($oTarea->IdUsuarioFrom);

$oTareaTipo 		= $oTiposTareas->GetById($oTarea->IdTipo);

$arrUsuarios	= $oUsuarios->GetAll();

if ($Submit1)
{			
	
	if ($DetalleSeg == '')
		$err += 8;
	
	/* si no hay errores... */
	if ($err == 0)
	{
		$oTareaSeguimiento	 				= new TareaSeguimiento();
		
		$oTareaSeguimiento->IdTarea			= $IdTarea;
		$oTareaSeguimiento->IdUsuario		= $IdUsuarioSeg;
		$oTareaSeguimiento->IdAccion	 	= $IdAccionSeg;
		$oTareaSeguimiento->Fecha	 		= $FechaSeg;
		$oTareaSeguimiento->Detalle		 	= $DetalleSeg;
			
		
		/* crea el cliente */
		$oTareaSeguimiento = $oTareaSeguimientos->Create($oTareaSeguimiento);
		
		header("Location: tareas_descripcion.php?IdTarea=" . $IdTarea);
		exit();
	}
}
else
{
	$FechaSeg = date('d-m-Y');
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<script language="javascript">
function frmDataSegSubmit()
{
	var frmDataSeg = Get('frmDataSeg');
	
	if (frmDataSeg == undefined)
		return false;
	
	frmDataSeg.submit();
	return true;
}
</script>

<?php include('include/head.inc.php'); ?>
</head>
<body>

<table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
		<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
				<tr>
					<td width="20" height="40" class="TituloRubro">&nbsp;</td>
					<td height="40"><span class="tituloPagina">Datos Tarea: <?=$oTarea->Nombre?></span></td>
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
				<table width="98%"  border="0" align="center" cellpadding="4" cellspacing="0">
					<tr>
						<td class="bordeGris">
							<table width="70%"  border="0" align="center" cellpadding="0" cellspacing="0">
								<tr>
									<td width="48%">&nbsp;</td>
									<td width="4%">&nbsp;</td>
									<td width="48%">&nbsp;</td>
								</tr>
								<tr>
									<td height="20"><div align="right"><strong>Fecha Inicio:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oTarea->FechaInicio?></div>
									</td>
								</tr>
								<tr>
									<td height="20"><div align="right"><strong>HORA:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oTarea->Hora?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Fecha Fin:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oTarea->FechaFin?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Nombre:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oTarea->Nombre?></div>
									</td>
								</tr>
                                <tr>
									<td height="25"><div align="right"><strong>Generada por:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oUsuarioFrom->Nombre . ' ' . $oUsuarioFrom->Apellido?></div>
									</td>
								</tr>
                                <tr>
									<td height="25"><div align="right"><strong>Asignada a:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oUsuarioTo->Nombre . ' ' . $oUsuarioTo->Apellido?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Tipo:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=$oTareaTipo->Nombre?></div>
									</td>
								</tr>
								<tr>
									<td height="25"><div align="right"><strong>Estado:</strong></div></td>
									<td>&nbsp;</td>
									<td>
										<div align="left"><?=TareaEstados::GetColorById($oTarea->IdEstado)?></div>
									</td>
								</tr>
								<tr>
									<td height="25" valign="top"><div align="right"><strong>Descripci&oacute;n:</strong></div></td>
									<td>&nbsp;</td>
									<td><div align="left"><?=$oTarea->Descripcion?></div></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
						  	</table>						
						</td>
					</tr>
				</table>
                 <form name="frmDataSeg" id="frmDataSeg" method="post" action="tareas_descripcion.php<?=$strParams?>">
                                <input type="hidden" name="Submitted1" id="Submitted1" value="1">	
                                <input type="hidden" name="IdTarea" id="IdTarea" value="<?=$IdTarea?>">
                					<table width="98%" cellpadding="0" cellspacing="0">
                	        			<tr>
                                        	<td colspan="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="10">
                                                <table width="98%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td><strong style="font-size:16px"><b>&nbsp;<img src="images/iconos/clock.png"  />&nbsp;&nbsp;Seguimientos:</b></strong></td>
                                                    </tr>    
                                                </table>
                                            </td>        
                                        </tr>
                                        <tr>
                                        	<td colspan="10">&nbsp;</td>
                                        </tr>
                                        <tr>
                                        	<td><div align="left">
                                            	<input name="FechaSeg" type="text" class="camporFormularioChico" id="FechaSeg" value="<?=$FechaSeg?>" size="12" maxlength="12" onKeyDown="javascript: return false;"/> 
												<script language="JavaScript" type="text/javascript">
                                                    new tcal
                                                    ({
                                                        'formname': 'frmDataSeg',
                                                        'controlname': 'FechaSeg'
                                                    });
                                                </script></div></td>
                                            <td>
                                                <select name="IdUsuarioSeg" id="IdUsuarioSeg" class="camporFormularioSimple">													<?php foreach ($arrUsuarios as $oUsuarioAux) { ?>
                                                          <option value="<?=$oUsuarioAux->IdUsuario?>" <?php echo ($oUsuarioAux->IdUsuario == $IdUsuarioSeg) ? "selected='selected'" : "" ?> >
                                                            <?=$oUsuarioAux->Apellido;?>
                                                            ,
                                                            <?=$oUsuarioAux->Nombre;?>
                                                          </option>
                                                          <?php } ?>
                                                        </select></td>
                                                        <td><select name="IdAccionSeg" id="IdAccionSeg" class="camporFormularioSimple">
                                                          <?php foreach (SeguimientoEstados::GetAll() as $oEstado) { ?>
                                                          <option value="<?=$oEstado['IdAccion']?>" <?php echo ($oEstado['IdAccion'] == $IdAccion) ? "selected='selected'" : "" ?> >
                                                            <?=$oEstado['Descripcion']?>
                                                          </option>
                                                          <?php } ?>
                                                        </select></td>
                                                        <td valign="middle"><input name="DetalleSeg" id="DetalleSeg" class="camporFormularioSimple" value="<?=$DetalleSeg?>" /> <a onclick="javascript: frmDataSegSubmit()" href="<?=$_SERVER['REQUEST_URI']?>#m"><img src="images/iconos/add.gif" alt="Agregar Seguimiento" title="Agregar Seguimiento" border="0" /></a> </td>
                                      	</tr> 
                					</table>
                </form>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                   <?php if ($arrSeguimientos != NULL) { ?>
								<tr>
                                   		<td>
                                            <table width="98%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                <tr class="bordeGrisFondo">
                                                              <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha</strong></div></td>
                                                              <td width="129" height="25" class="bordeGrisTitulo"><strong>Vendedor</strong></td>
                                                              <td width="119" height="25" class="bordeGrisTitulo"><strong>Acci&oacute;n</strong></td>
                                                              <td width="354" height="25" class="bordeGrisTitulo"><strong>Comentario</strong></td>
                                                </tr>

                                                        <?php foreach ($arrSeguimientos as $oTareaSeguimiento) { 
                                                        
                                                            $oUsuarioAux = $oUsuarios->GetById($oTareaSeguimiento->IdUsuario);
                                                            
                                                        ?>
                                                      
                                                <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                    <td height="25"><div id="margen"><?=CambiarFecha($oTareaSeguimiento->Fecha)?></div></td>
                                                    <td height="25"><?=$oUsuarioAux->Apellido?>, <?=$oUsuarioAux->Nombre?></td>
                                                    <td height="25"><?=SeguimientoEstados::GetById($oTareaSeguimiento->IdAccion)?></td>
                                                    <td height="25"><?=$oTareaSeguimiento->Detalle?></td>         
                                                 </tr>
                                                <tr>
                                                    <td colspan="8"><div align="center">
                                                        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                            <tr>
                                                                <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                            </tr>
                                                        </table></div>
                                                    </td>
                                                </tr>
                                                
                                                             <?php } ?>      
                                            </table>
                                   		</td>
                                </tr>
                                <?php } else { ?>  
                                
                                    <tr>
                                        <td>
                                            <table width="98%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                                                </tr>
                                                <tr>
                                                    <td><div align="center"><strong>No hay seguimientos para esta tarea.</strong></div></td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                      
                                <?php } ?>
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
