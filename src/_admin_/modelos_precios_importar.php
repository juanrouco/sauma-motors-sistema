<?php

require_once('../inc_library.php'); 

set_time_limit(0);

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MODE_IMPORT))
	Session::NoPerm();

/* obtiene datos enviados */
$Archivo	= $_FILES['ArchivoCsv'];
$Submit		= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err 		= 0;
$errUpload 	= 0;
$Mensaje 	= '';

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

$oModelos = new Modelos();

if ($Submit)
{
	/* validaciones... */
	if ($Archivo['name'] == '')
		$err |= 1;
		
	/* si no hay errores... */
	if ($err == 0)
	{
		$oUpload = new Up
		(
			$Archivo['name'], 
			$Archivo['tmp_name'], 
			$Archivo['size'], 
			$Archivo['type'], 
			Modelo::PathCsvImportBack,
			array('xls')
		);

		if ($oUpload->UploadFile())
		{
			/* importamos los usuarios */	
			$Mensaje = $oModelos->ImportPrecios($oUpload->GetNombre());

			/* eliminamos el archivo */
			Up::DeleteFile(Modelo::PathCsvImportBack, $oUpload->GetNombre());
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>" enctype="multipart/form-data">
    <input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Modelos - Actualizar Precios</span></td>
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
                    
                <?php if (empty($Mensaje)) { ?>
                
                    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="bordeGris">
                                <table  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Archivo XLS:</div></td>
                                        <td>
                                            <input type="file" name="ArchivoCsv" id="ArchivoCsv" class="camporFormularioSimple">									
                                       	</td>
                                    </tr>
                                <?php if ($err & 1) { ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td align="left"><span style="color:#FF0000;">Debe cargar un archivo para poder importarlo.</span></td>
                                    </tr>								
                                <?php } ?>									
                                <?php if ((is_object($oUpload)) && !(empty($oUpload->strError))) { ?>
                                    <tr>
                                        <td><div align="right"></div></td>
                                        <td><li style="color:#FF0000;"><?=$oUpload->strError?></li></td>
                                    </tr>
                                <?php } ?>
                                    <tr>
                                      	<td colspan="2">&nbsp;</td>
                                  	</tr>								
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td height="1"><div align="center"></div></td>
                      	</tr>
                    </table>
      				<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      					<tr>
                            <td height="30">
                                <div align="center">
                                	<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
                                	<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'unidades_modificar_precios.php<?=$strParams?>';" value="Cancelar" />
                                </div>
                            </td>
                        </tr>
                    </table>
                    
              <?php } else{ ?>							
              
                    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
          				<tr>
                            <td class="bordeGris">						
                                <table  border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>							
                                    <tr>
                                        <td colspan="2"><span style="color:#FF0000;"><?=$Mensaje?></span></td>
                                    </tr>
                                    <tr>
                                      <td colspan="2">&nbsp;</td>
                                  </tr>
                                </table>			  
                           	</td>
                        </tr>
          				<tr>
            				<td height="1" class="bordeGris"><div align="center"></div></td>
            			</tr>
                    </table>
      				<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      					<tr>
                            <td height="30">
                                <div align="center">
                        	        <input type="button" name="btnAceptar" class="botonBasico" id="btnAceptar" onclick="javascript: window.location.href = 'unidades_modificar_precios.php';" value="Aceptar" />                			
                                </div>
                            </td>
                        </tr>
                    </table>
                    
                <?php } ?>
    
                </div>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>