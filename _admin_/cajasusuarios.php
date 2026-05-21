<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_USUA_UPDATE))
	Session::NoPerm();

$filter 				= array();
$IdUsuario				= trim($_REQUEST['IdUsuario']);
$filter['IdUsuario']	= $IdUsuario;
$Submit					= (isset($_REQUEST['Submitted']));

$arrCajas 				= array();
$arrCajasUsuario		= array();
$oUsuarios				= new Usuarios();
$oCajasDetalles			= new CajasDetalles();
$oCajasDetallesUsuarios	= new CajasDetallesUsuarios();
$oCajaDetalleUsuario	= new CajaDetalleUsuario();

$oUsuario = $oUsuarios->GetById($IdUsuario);

if ($Submit)
{
	if (isset($IdUsuario) && !empty($IdUsuario))
	{
		$oCajasDetallesUsuarios->Begin();
		$oCajasDetallesUsuarios->Delete($IdUsuario);	
		if (isset($_REQUEST['asociacionCajas']))
		{
			foreach($_REQUEST['asociacionCajas'] as $IdCajaDetalle) 
			{
				$oCajaDetalleUsuario->IdUsuario 	= $IdUsuario;
				$oCajaDetalleUsuario->IdCajaDetalle = $IdCajaDetalle;
				$oCajasDetallesUsuarios->Create($oCajaDetalleUsuario);
			}
		}
		$oCajasDetallesUsuarios->Commit();
		header("Location: usuarios.php" . $strParams);
		exit();
	}
}
else
{
	if (!isset($IdUsuario) || empty($IdUsuario))
	{	
		$arrCajas = null;
	}
	else
	{
		$arrCajas 			= $oCajasDetalles->GetAll();
		$arrCajasUsuario	= $oCajasDetallesUsuarios->GetAll($filter);
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

$j(document).ready(function() {
	
	$j('#frmBusquedaCajas').ajaxForm({
		success: function(responseText, statusText, xhr, $form) {
			$j('#tr_resultado').html(responseText);
		}
	});
	
	$j('#FilterUsuario').change(function(){
		$j('#frmBusquedaCajas').attr("action", "cajasusuarios.php?IdUsuario=" + this.value);
		$j('#frmBusquedaCajas').submit();
	});
});

</script>

</head>
<body>
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Cajas autorizadas para <?=$oUsuario->Nombre . " " . $oUsuario->Apellido?></span></td>
                    </tr>
                </table>		
            </td>
        </tr>
    
        <tr>
            <td>&nbsp;</td>
        </tr>
		<tr id="tr_resultado">
			<?php if ($arrCajas != NULL) { ?>
  	
    	<td>
			<form id="frmData" method="post">
			<input type="hidden" name="Submitted" id="Submitted" value="1" />
			<input type="hidden" name="IdUsuario" id="IdUsuario" value="<?=$IdUsuario?>" />
			<table width="100%" align="center" cellpadding="0" cellspacing="0" >
				<tr>
					<td>
						<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
							<tr class="bordeGrisFondo">
								<td width="75%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Caja</strong></div></td>
								<td width="25%" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Asociada</strong></div></td>  
							</tr>
				  
						<?php foreach ($arrCajas as $oCajaDetalle) { 
						?>          
							<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
								<td height="25"><div id="margen"><?= $oCajaDetalle->Nombre ?></div></td>
								<?php
								$checked = '';
								foreach($arrCajasUsuario as $oCajaDetalleUsuario) {
									if ($oCajaDetalle->IdCajaDetalle == $oCajaDetalleUsuario->IdCajaDetalle) {
										$checked = 'checked';
									}
								}
								?>
								<td height="25" align="center"><input type="checkbox" name="asociacionCajas[]" value="<?=$oCajaDetalle->IdCajaDetalle?>" <?=$checked?>></td>
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
				<tr height="5"></tr>				
				<tr>
                    <td align="center">
						<input type="submit" name="button" id="button" class="botonBasico" value="Aceptar">
						<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'usuarios.php<?=$strParams?>';" value="Cancelar" />
					</td>
				</tr>
			</table>
			</form>
	  </td>
  	

<?php } else { ?>  

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
      
<?php } ?>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
    </table>

</body>
</html>