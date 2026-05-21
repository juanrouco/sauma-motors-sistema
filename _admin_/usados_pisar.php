<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para clientes autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_USADOS_PISAR))
	Session::NoPerm();

/* obtiene datos enviados */
$IdUsado				= intval($_REQUEST['IdUsado']);

$Pisado					= intval($_REQUEST['Pisado']);
$Comentarios			= strval($_REQUEST['Comentarios']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err			= 0;
$oUsados		= new Usados();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* verifica si existe el registro */
if (!$oUsado = $oUsados->GetById($IdUsado))
{	
	header("Location: usados.php" . $strParams);
	exit();
}

if ($Submit)
{
	/* validaciones... */
	if ($Pisado == '1'  && $Comentarios == '')
		$err |= 1;
	

	/* si no hay errores... */
	if ($err == 0)
	{
		
		$oUsado->Pisado			= $Pisado;
		$oUsado->Comentarios	= $Comentarios;
		
		
		$oUsado = $oUsados->Update($oUsado);

		header("Location: usados.php" . $strParams);
		exit();
	}
}
else
{
	
	$Pisado			= $oUsado->Pisado;
	$Comentarios	= $oUsado->Comentarios;
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>



</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Usados - Pisar Usado</span></td>
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

            <form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
                <input type="hidden" name="Submitted" id="Submitted" value="1" />
                <input type="hidden" name="IdUsado" id="IdUsado" value="<?=$IdUsado?>" />
                
                <table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
                    <tr>
                        <td class="bordeGris">
                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
									<td>Pisar Usado:</td>
									<td width="10">&nbsp;</td>
									<td><input type="checkbox" name="Pisado" id="Pisado" value="1" <?=($Pisado) ? 'checked="checked"' : '';?> /></td>
								</tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
								<tr>
									<td width="65">Comentarios:</td>
								<td width="10">&nbsp;</td>
                                    <td>
                                        <textarea id="Comentarios" name="Comentarios" onkeyup="javascript: StrToUpper(this.id);" class="camporFormularioMultiline"><?= $Comentarios ?></textarea>
                                    </td>
                                </tr>
                                <tr>
									<td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td><?php if ($err & 1) { ?><li style="color: red">Debe ingresar uun comentario para pisar el usado.<?php } ?>&nbsp;</li></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td><div align="center"></div></td>
                    </tr>
                </table>
  				<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td height="30">
							<div align="center">
								<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
								<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'usados.php<?=$strParams?>';" value="Cancelar" />
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
</body>
</html>