<?php

require_once('../inc_library.php'); 

set_time_limit (1000000);

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_UNID_CREATE))
	Session::NoPerm();

/* obtenemos datos enviados */
$Archivo	= $_FILES['Archivo'];
$Submit		= (isset($_REQUEST['Submitted']));

/* declaramos e instanciamos variables necesarias */
$err 		= 0;
$errUpload 	= 0;
$Mensaje 	= '';

/* definimos cadena a mandar por get */
$strParams = (strlen($_SERVER['QUERY_STRING']) > 0) ? '?' . $_SERVER['QUERY_STRING'] : '';

/* declaramos variables necesarias */
$oColores = new Colores();

$FechaImportacion = '';

/* si el formulario fue enviado... */
if ($Submit)
{
	/* si no hay errores... */
	if ($err == 0)
	{
		/*$oUpload = new Up
		(
			$Archivo['name'], 
			$Archivo['tmp_name'], 
			$Archivo['size'], 
			$Archivo['type'], 
			Unidad::PathFile,
			array('txt')
		);*/

		
			/* importamos los usuarios */	
			$res = $oColores->ImportTxt('COLOR.TXT');
			
			$Mensaje = $res->Mensaje;

			/* eliminamos el archivo almacenado en el servidor */
			//Up::DeleteFile(Unidad::PathFile, 'CLIENTES.TXT');
		
	}
}

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
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Importar Unidades</span></td>
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
      		<form name="frmData" id="frmData" method="post" enctype="multipart/form-data">
	  			<input type="hidden" name="Submitted" id="Submitted" value="1" />
				<input type="hidden" name="Page" id="Page" value="<?=$Page?>" />
                
			<?php if ($Mensaje == '') { ?>
            
        		<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
				  	<tr>
            			<td class="bordeGris">
							<table  border="0" align="center" cellpadding="0" cellspacing="0">
              					<tr>
                					<td>&nbsp;</td>
              					</tr>
              					<tr>
                					<td><div align="right">Archivo:</div></td>
                					<td>
										<input type="file" name="Archivo" id="Archivo" class="camporFormularioSimple">									</td>
								</tr>
								<?php if ($err & 1) { ?>
								<tr>
									<td>&nbsp;</td>
									<td align="left"><span style="color:#FF0000;">Debe cargar un archivo para poder importarlo.</span></td>
              					</tr>								
								<?php } ?>									
								<?php if ((is_object($oUpload)) && ($oUpload->strError != '')) { ?>
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
  				<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
  					<tr>
            			<td height="30">
              				<div align="center">
                				<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
                				<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'unidades.php<?=$strParams?>';" value="Cancelar" />
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
  				<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
  					<tr>
            			<td height="30">
              				<div align="center">
								<?php
								if ($res->Fecha)
								{
								?>
                				<input type="button" name="btnExportar" class="botonBasico" id="btnExportar" onclick="javascript: window.location.href = 'unidades_exportar_pdf.php<?=$strParams?>&FechaFacturaDesde=<?= $res->Fecha ?>';" value="Exportar Importados" />
                				<?php
								}
								?>
								<input type="button" name="btnAceptar" class="botonBasico" id="btnAceptar" onclick="javascript: window.location.href = 'unidades.php<?=$strParams?>';" value="Aceptar" />
                			</div>
						</td>
            		</tr>
        		</table>
                
			<?php } ?>

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