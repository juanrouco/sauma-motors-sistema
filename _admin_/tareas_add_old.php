<?php
require_once('../inc_library.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_TAREAS_CREATE))
	Session::NoPerm();

$oUsuario = Session::GetCurrentUser();

/* obtiene datos enviados */
$Page			= intval($_REQUEST['Page']);

/* obtiene datos del formulario */

$Nombre 				= $_REQUEST['Nombre'];
$IdTipo			 		= $_REQUEST['IdTipo'];
$IdEstado				= $_REQUEST['IdEstado'];
$FechaFin				= $_REQUEST['FechaFin'];
$FechaInicio			= $_REQUEST['FechaInicio'];
$Descripcion			= $_REQUEST['Descripcion'];
$IdUsuarioFrom			= $_REQUEST['IdUsuarioFrom'];
$IdUsuarioTo			= $_REQUEST['IdUsuarioTo'];
$IdCliente				= $_REQUEST['IdCliente'];
$Cliente				= strval($_REQUEST['Cliente']);
$ClienteTelefono		= strval($_REQUEST['ClienteTelefono']);
$ClienteEmail			= strval($_REQUEST['ClienteEmail']);
$HoraInicio				= strval($_REQUEST['HoraInicio']);
$MinutoInicio			= strval($_REQUEST['MinutoInicio']);

$Submit					= $_REQUEST['Submitted'];

/* declaracion de variables */
$err					= 0;
$oTarea					= new Tarea();	
$oTareas				= new Tareas();
$oTiposTareas			= new TiposTareas();
$oUsuarios				= new Usuarios();
$oClientes				= new Clientes();

$arrTiposTareas			= $oTiposTareas->GetAll();
$arrUsuarios			= $oUsuarios->GetAll();

/* armamos cadena con parametros a mandar */
$strParams = '';
$strParams.= '?Page=' 							. $Page;
$strParams.= '&FilterIdTipo='					. $_REQUEST['FilterIdTipo'];
$strParams.= '&FilterFechaInicio='				. $_REQUEST['FilterFechaInicio'];
$strParams.= '&FilterFechaFin='					. $_REQUEST['FilterFechaFin'];
$strParams.= '&FilterNombre='					. $_REQUEST['FilterNombre'];
$strParams.= '&FilterIdUsuarioFrom='			. $_REQUEST['FilterIdUsuarioFrom'];
$strParams.= '&FilterIdUsuarioTo='				. $_REQUEST['FilterIdUsuarioTo'];
$strParams.= '&FilterIdEstado='					. $_REQUEST['FilterIdEstado'];
$strParams.= '&FilterDescripcion='				. $_REQUEST['FilterDescripcion'];

if ($Submit)
{
	/* validaciones... */
	if ($Nombre == '')
		$err += 1;
	if ($Descripcion == '')
		$err += 2;
	if ($IdTipo == '')
		$err += 4;
	if ($IdUsuarioFrom == '')
		$err += 8;
	if ($IdUsuarioTo == '')
		$err += 16;
	if ($FechaInicio == '')
		$err += 32;
	if ($FechaFin == '')
		$err += 64;
	if ($IdEstado == '')
		$err += 128;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		if (!$IdCliente && $Cliente)
		{
			$oCliente = new Cliente();
			$oCliente->IdTipoPersona = ClienteTipos::PersonaFisica;
			$oCliente->RazonSocial = $Cliente;
			$oCliente->Email = $ClienteEmail;
			$oCliente->Telefono = $ClienteTelefono;
			$oCliente->IdTipoIva = TipoIva::CF;
			$oCliente = $oClientes->Create($oCliente);
			$IdCliente = $oCliente->IdCliente;
		}
		elseif ($IdCliente)
		{
			$oCliente = $oClientes->GetById($IdCliente);
			$oCliente->Email = $ClienteEmail;
			$oCliente->Telefono = $ClienteTelefono;
			$oClientes->Update($oCliente);
		}
		
		$oTarea->Nombre	 				= $Nombre;		
		$oTarea->IdTipo		 			= $IdTipo;
		$oTarea->IdUsuarioTo			= $IdUsuarioTo;	
		$oTarea->IdUsuarioFrom			= $IdUsuarioFrom;		
		$oTarea->FechaInicio	 		= $FechaInicio;
		$oTarea->FechaFin 				= $FechaFin;
		$oTarea->Descripcion 			= $Descripcion;
		$oTarea->IdEstado 				= $IdEstado;
		$oTarea->IdCliente 				= $IdCliente;
		$oTarea->Hora					= str_pad($HoraInicio, 2, 0, STR_PAD_LEFT) . ':' . str_pad($MinutoInicio, 2, 0, STR_PAD_LEFT);
		
		/* crea el cliente */
		$oTarea= $oTareas->Create($oTarea);
			
		/* envia mail de nueva llamada*/
/*		$oClienteContactos 		= new ClienteContactos();
		$oProveedorContactos 	= new ProveedorContactos();
		$oClientes				= new Clientes();
		$oProveedores			= new Proveedores();
		$oUsuarios			= new Usuarios();
		
		$oUsuarioAsignado	= $oUsuarios->GetById($oTarea->IdUsuarioTo);
		$oUsuarioRecibio 	= $oUsuarios->GetById($oTarea->IdUsuarioFrom);
		$oTareaTipo 			= $oTiposTareas->GetById($oTarea->IdTipo);
		
		$Headers = "From: " . $oDatosEmpresa->RazonSocial . "\r\n";
		$Headers.= " <" . Config::MailNoReply . "> \r\n";
		$Headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
	
		$Content.= "<b>Asignado a: </b> " 	. $oUsuarioAsignado->Nombre . ' ' . $oUsuarioAsignado->Apellido . "<br><br>";
		$Content.= "<b>Recibido por: </b> " . $oUsuarioRecibio->Nombre . ' ' . $oUsuarioRecibio->Apellido . "<br><br>";
		$Content.= "<b>Tipo Tarea: </b> " 	. $oTareaTipo->Nombre . "<br><br>";
		$Content.= "<b>Descripcion: </b> " 	. $oTarea->Descripcion . "<br><br>";
		
		/*envia mail*/
		//mail($oUsuarioAux->Email, "Nueva Llamada de " . $Empresa . ' - ' . $Contacto, $Content, $Headers);
		
		header("Location: tareas.php" . $strParams);
		exit();
	}
}
else
{
	$IdUsuarioFrom	= $oUsuario->IdUsuario;
	$IdUsuarioTo	= $oUsuario->IdUsuario;
	
	$IdEstado			= 1;
	if (!$FechaInicio)
		$FechaInicio 		= date('d-m-Y');
	$FechaFin	 		= date('d-m-Y');
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php 
	include('include/head.inc.php');
	require_once('../library/suggest/include.php'); 
	/* incluimkos funcion para armar suggest */
	IncludeSUGGEST();
?>
<script type="text/javascript">
function FilterUsuarioFrom(IdUsuario, Nombre)
{
	if ((IdUsuario == '') && (Nombre == ''))
	{
		Get('IdUsuarioFrom').value 	= '';
		Get('UsuarioFrom').value 	= '';
	}

	var oUsuario = GetUsuario(IdUsuario);
	if (!(oUsuario))
		return;

	Get('IdUsuarioFrom').value 	= oUsuario.IdUsuario;
	Get('UsuarioFrom').value 	= (oUsuario.Nombre + ' ' + oUsuario.Apellido);
}

function ClearCliente()
{
	Get('IdCliente').value 	= '';
}

function FilterUsuarioTo(IdUsuario, Nombre)
{
	if ((IdUsuario == '') && (Nombre == ''))
	{
		Get('IdUsuarioTo').value 	= '';
		Get('UsuarioTo').value 	= '';
	}

	var oUsuario = GetUsuario(IdUsuario);
	if (!(oUsuario))
		return;

	Get('IdUsuarioTo').value 	= oUsuario.IdUsuario;
	Get('UsuarioTo').value 	= (oUsuario.Nombre + ' ' + oUsuario.Apellido);
}

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
		Get('ClienteTelefono').value 	= '';
		Get('ClienteEmail').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	Get('ClienteTelefono').value 	= oCliente.Telefono;
	Get('ClienteEmail').value 	= oCliente.Email;
	
}

</script>

</head>
<body>
<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloDepartamento">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Agregar Tarea</span></td>
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
      		<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" enctype="multipart/form-data">
	  			<input type="hidden" name="Submitted" id="Submitted" value="1">
				<input type="hidden" name="IdCliente" id="IdCliente" value="<?=$IdCliente?>" />
                    
			<table width="65%"  border="0" align="center" cellpadding="5" cellspacing="0">
   			  <tr>
            			<td class="bordeGris">
							<table width="72%"  border="0" align="center" cellpadding="0" cellspacing="0">
       					  <tr>
                					<td width="129">&nbsp;</td>
                                  <td>&nbsp;</td>
              					</tr>			
                                <tr>
									<td><div align="right">Nombre:</div></td>
									<td>
										<div align="left">
										<input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" value="<?=$Nombre?>" >
								  		<span style="color:#FF0000;">&nbsp;(*)</span>										</div>
																			</td>
								</tr>
                          
                                <tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese nombre </li><?php } ?></td>
                                </tr>
                           
                                								
                                <tr>
									<td><div align="right">Tipo:</div></td>
									<td>
										<div align="left">
										  <select name="IdTipo" id="IdTipo" class="camporFormularioSimple">
                                    
                                    <?php foreach ($arrTiposTareas as $oTareaTipo) { ?>
                                    <option value="<?=$oTareaTipo->IdTipoTarea?>" <?php echo ($oTareaTipo->IdTipoTarea == $IdTipo) ? "selected='selected'" : "" ?> >
                                    <?=$oTareaTipo->Nombre;?>
                                    </option>
                                    <?php } ?>
                                    </select>
									   <span style="color:#FF0000;">&nbsp; (*)</span>	</div>
									</td>
								</tr>
								<tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 4) { ?><li style="color:#FF0000;">Seleccione el Tipo</li><?php } ?></td>
                                </tr>
                                <tr>    
									<td><div align="right">Generada Por:</div></td>
                                  <td>
									<table border="0" align="left" cellpadding="0" cellspacing="0">
										
										<tr>
											<td>
												<div align="left">
													<input type="text" name="UsuarioFrom" id="UsuarioFrom" class="camporFormularioSuggest" maxlength="128" value="<?=$UsuarioFrom?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
													<script language="javascript">
														var arrParams = new Array();
														SUGGESTRequest('Usuarios', 'GetAllSuggest', 'UsuarioFrom', 'FilterUsuarioFrom', 'IdUsuario', 'Nombre', 'FilterNombre', arrParams);
													</script>
												</div>
											</td>
											<td>
												<div align="left">
													<input type="text" name="IdUsuarioFrom" id="IdUsuarioFrom" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUsuarioFrom?>" readonly="readonly" />
												</div>
											</td>
										</tr>
									</table>
								  
                                    <span style="color:#FF0000;">&nbsp;(*)</span>	
                                  </td>
                              </tr>
							  <tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 8) { ?><li style="color:#FF0000;">Ingrese usuario que genera la tarea </li><?php } ?></td>
                                </tr>
                                <tr>    
                                    <td><div align="right">Asignada a:</div></td>
                                    <td width="286">
									<table border="0" align="left" cellpadding="0" cellspacing="0">
										
										<tr>
											<td>
												<div align="left">
													<input type="text" name="UsuarioTo" id="UsuarioTo" class="camporFormularioSuggest" maxlength="128" value="<?=$UsuarioTo?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
													<script language="javascript">
														var arrParams = new Array();
														SUGGESTRequest('Usuarios', 'GetAllSuggest', 'UsuarioTo', 'FilterUsuarioTo', 'IdUsuario', 'Nombre', 'FilterNombre', arrParams);
													</script>
												</div>
											</td>
											<td>
												<div align="left">
													<input type="text" name="IdUsuarioTo" id="IdUsuarioTo" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUsuarioTo?>" readonly="readonly" />
												</div>
											</td>
										</tr>
									</table>
								  
                                    <span style="color:#FF0000;">&nbsp;(*)</span>	
                                  </td>
                              </tr>
							   <tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese usuario al que se le asigna la tarea </li><?php } ?></td>
                                </tr>
								<tr>    
                                    <td><div align="right">Cliente:</div></td>
                                    <td width="286">
									<table border="0" align="left" cellpadding="0" cellspacing="0">
										
										<tr>
											<td>
												<div align="left">
													<input type="text" name="Cliente" id="Cliente" class="camporFormularioSimple" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);  ClearCliente();"  autocomplete="off" />
													<script language="javascript">
														var arrParams = new Array();
														SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
													</script>
												</div>
											</td>
										</tr>
										
									</table>
								  
                                  </td>
                              </tr>
							  <tr><td>&nbsp;</td></tr>
							  <tr>    
                                    <td><div align="right">Cliente Tel&eacute;fono:</div></td>
                                    <td width="286">
									<table border="0" align="left" cellpadding="0" cellspacing="0">
										
										<tr>
											<td>
												<div align="left">
													<input type="text" name="ClienteTelefono" id="ClienteTelefono" class="camporFormularioSimple" maxlength="128" value="<?=$ClienteTelefono?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
												</div>
											</td>
										</tr>
										
									</table>
								  
                                  </td>
                              </tr>
							  <tr><td>&nbsp;</td></tr>
							  <tr>    
                                    <td><div align="right">Cliente Email:</div></td>
                                    <td width="286">
									<table border="0" align="left" cellpadding="0" cellspacing="0">
										
										<tr>
											<td>
												<div align="left">
													<input type="text" name="ClienteEmail" id="ClienteEmail" class="camporFormularioSimple" maxlength="128" value="<?=$ClienteEmail?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="off" />
												</div>
											</td>
										</tr>
										
									</table>
								  
                                  </td>
                              </tr>
							   <tr>
                                	<td>&nbsp;</td>
                                	<td>&nbsp;</td>
                                </tr>
								<tr>
                                  <td><div align="right">Fecha Inicio:</div></td>
								  <td><div align="left">
                                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td><input name="FechaInicio" type="text" class="camporFormularioMediano" id="FechaInicio" value="<?=$FechaInicio?>" size="12" maxlength="12" />
										<script language="javascript">
											new tcal({'formname': 'frmData', 'controlname': 'FechaInicio'});
										</script>                 <span style="color:#FF0000;">  &nbsp;(*)</span>	                               </td>
										
                                        </tr>
                                      </table>                                  </td>
							 	</tr>
								<tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese la fecha de inicio<li><?php } ?></td>
                                </tr>
								<tr>
									<td><div align="right">Hora:</div></td>
									<td><div align="left">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>
											<div align="left">
												<select class="camporFormularioMediano" style="width: 50px" id="HoraInicio" name="HoraInicio">
												<?php
													for ($Hora = 0; $Hora < 24; $Hora++)
													{
														$selected = '';
														if ($Hora == intval($HoraInicio))
															$selected = 'selected="selected"';					
												?>
													<option value="<?= $Hora ?>" <?= $selected ?>><?= str_pad($Hora, 2, 0, STR_PAD_LEFT) ?></option>
												<?php
													}
												?>
												</select> : <select class="camporFormularioMediano" style="width: 50px" id="MinutoInicio" name="MinutoInicio">
												<?php
													for ($Minuto = 0; $Minuto < 60; $Minuto++)
													{
														$selected = '';
														if ($Minuto == intval($MinutoInicio))
															$selected = 'selected="selected"';
												?>
													<option value="<?= $Minuto ?>" <?= $selected ?>><?= str_pad($Minuto, 2, 0, STR_PAD_LEFT) ?></option>
												<?php
													}
												?>
												</select>
											</div>
										</td>
											</tr>
										</table>
									</div>
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
                                  <td><div align="right">Fecha Fin:</div></td>
								  <td><div align="left">
                                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                          <td><input name="FechaFin" type="text" class="camporFormularioMediano" id="FechaFin" value="<?=$FechaFin?>" size="12" maxlength="12" />
										<script language="javascript">
											new tcal({'formname': 'frmData', 'controlname': 'FechaFin'});
										</script> <span style="color:#FF0000;">  &nbsp;(*)</span>                                               </td>
                                        </tr>
                                      </table>                                  </td>
							 	</tr>
								<tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese la fecha de fin<li><?php } ?></td>
                                </tr>
                                <tr>
									<td><div align="right">Estado:</div></td>
									<td>
										<div align="left">
										  <select name="IdEstado" id="IdEstado" class="camporFormularioSimple">
											
											<?php foreach (TareaEstados::GetAll() as $oTareaEstado) { ?>
											<option value="<?=$oTareaEstado['IdEstado']?>" <?php echo ($oTareaEstado['IdEstado'] == $IdEstado) ? "selected='selected'" : "" ?> > <?=$oTareaEstado['Descripcion']?></option>
											<?php } ?>
                                          </select>
																						                                        <span style="color:#FF0000;"> &nbsp;(*)</span>	</div>									</td>
								</tr>
								<tr>
                                	<td>&nbsp;</td>
                                    <td> <?php if ($err & 128) { ?><li style="color:#FF0000;">Seleccione un estado<li><?php } ?></td>
                                </tr>
                                <tr>
									<td><div align="right">Descripci&oacute;n:</div></td>
									<td>
										<div align="left">
										<textarea name="Descripcion" id="Descripcion" class="camporFormularioMultiline"><?=$Descripcion?></textarea>
										<span style="color:#FF0000;">&nbsp;(*)</span>	</div>									</td>
								</tr>
                        		<?php if ($err & 2) { ?>
                                <tr>
                                	<td>&nbsp;</td>
                                    <td><li style="color:#FF0000;">Ingrese descripci&oacute;n </li></td>
                                </tr>
                           		<?php } ?>
                                <tr>
									<td>&nbsp;</td>
                                    <td>&nbsp;</td>
								</tr>

            				</table>						</td>
          			</tr>
        		</table>
				
            <table width="65%" border="0" cellspacing="0" cellpadding="0">
<tr>
                    <td height="1"><div align="center"></div></td>
                  </tr>
                </table>
  <table width="65%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
<tr>
            			<td height="30">
              				<div align="center">
                				<input type="submit" name="btnAceptar" id="btnAceptar" class="botonBasico" value="Aceptar" />
                				<input type="button" name="btnCancelar" id="btnCancelar" class="botonBasico" onclick="javascript: window.location.href = 'tareas.php<?=$strParams?>';" value="Cancelar" />
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

<script language="javascript">
<?php
if ($IdUsuarioFrom)
{
?>
FilterUsuarioFrom('<?= $IdUsuarioFrom ?>', '');
<?php
}
if ($IdUsuarioTo)
{
?>
FilterUsuarioTo('<?= $IdUsuarioTo ?>', '');
<?php
}
if ($IdCliente)
{
?>
FilterCliente('<?= $IdCliente ?>', '');
<?php
}
?>


</script>

</body>
</html>