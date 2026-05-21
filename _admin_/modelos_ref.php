<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_MODE_IMPORT))
	Session::NoPerm();

/* declaracion de variables */
$oMarcas 			= new Marcas();
$oTiposModelo		= new TiposModelo();
$oCategoriasModelo	= new CategoriasModelo();

/* obtenemos listado de marcas */
$arrMarcas = $oMarcas->GetAll();

/* obtenemos listado de tipos de modelos */
$arrTiposModelo = $oTiposModelo->GetAll();

/* obtenemos listado de categorias de modelos */
$arrCategoriasModelo = $oCategoriasModelo->GetAll();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
  	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloRubro">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Modelos - Referencias Importador</span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
  		<td colspan="3">&nbsp;</td>
  	</tr>
	
<?php if ($arrMarcas != NULL) { ?>
  	
	<tr>
    	<td>			
			<span class="tituloCategoriaMenu">Marcas</span>
			<br>
			<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
				<tr class="bordeGrisFondo">
        			<td width="10">&nbsp;</td>
        			<td width="128" height="25"><strong>C&oacute;digo</strong></td>
        			<td width="601" height="25"><strong>Nombre</strong></td>
      			</tr>
      
	<?php foreach ($arrMarcas as $oMarca) { ?>
      
	  			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
        			<td width="10" height="25">&nbsp;</td>
        			<td height="25"><?=$oMarca->Codigo?></td>
        			<td height="25"><?=$oMarca->Nombre?></td>
      			</tr>
	  			<tr>
        			<td colspan="5">
                    	<div align="center">
                            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                </tr>
                            </table>
                        </div>
                   	</td>
      			</tr>
				
	<?php } ?>      
					
		  	</table>		
    	</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
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
            		<td><div align="center"><strong>No hay marcas cargadas en el sistema.</strong></div></td>
          		</tr>
          		<tr>
            		<td>&nbsp;</td>
          		</tr>
        	</table>		
      	</td>
  	</tr>
      
<?php } ?>
<?php if ($arrTiposModelo != NULL) { ?>
  	
	<tr>
    	<td>			
        	<br>
		  <span class="tituloCategoriaMenu">Tipos de Veh&iacute;culos</span><br>
			<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		  		<tr class="bordeGrisFondo">
        			<td width="10">&nbsp;</td>
					<td width="149" height="25"><strong>ID</strong></td>
        			<td width="134" height="25"><strong>C&oacute;digo</strong></td>
        			<td width="595" height="25"><strong>Nombre</strong></td>
      			</tr>
      
	<?php foreach ($arrTiposModelo as $oTipoModelo) { ?>
      
	  			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
        			<td width="10" height="25">&nbsp;</td>
       			  	<td height="25"><?=$oTipoModelo->IdTipoModelo?></td>
       			  	<td height="25"><?=$oTipoModelo->Codigo?></td>
       			  	<td height="25"><?=$oTipoModelo->Nombre?></td>
   			  	</tr>
	  			<tr>
        			<td colspan="5">
                    	<div align="center">
          					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            					<tr>
              						<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            					</tr>
          					</table>
        				</div>
                 	</td>
      			</tr>
      
	<?php } ?> 
                 
			</table>
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
            		<td><div align="center"><strong>No hay tipos de modelos cargados en el sistema.</strong></div></td>
          		</tr>
          		<tr>
            		<td>&nbsp;</td>
          		</tr>
        	</table>
		</td>
  	</tr>
      
<?php } ?>
<?php if ($arrCategoriasModelo != NULL) { ?>
  	
	<tr>
    	<td>			
        	<br>
		  <span class="tituloCategoriaMenu">Categor&iacute;as de Veh&iacute;culos</span><br>
			<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		  		<tr class="bordeGrisFondo">
        			<td width="10">&nbsp;</td>
					<td width="149" height="25"><strong>ID</strong></td>
        			<td width="134" height="25"><strong>C&oacute;digo</strong></td>
        			<td width="595" height="25"><strong>Nombre</strong></td>
      			</tr>
      
	<?php foreach ($arrCategoriasModelo as $oCategoriaModelo) { ?>
      
	  			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
        			<td width="10" height="25">&nbsp;</td>
       			  	<td height="25"><?=$oCategoriaModelo->IdCategoriaModelo?></td>
       			  	<td height="25"><?=$oCategoriaModelo->Codigo?></td>
       			  	<td height="25"><?=$oCategoriaModelo->Nombre?></td>
   			  	</tr>
	  			<tr>
        			<td colspan="5">
                    	<div align="center">
          					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            					<tr>
              						<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            					</tr>
          					</table>
        				</div>
                 	</td>
      			</tr>
      
	<?php } ?> 
                 
			</table>
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
            		<td><div align="center"><strong>No hay categor&iacute;as de modelos cargados en el sistema.</strong></div></td>
          		</tr>
          		<tr>
            		<td>&nbsp;</td>
          		</tr>
        	</table>
		</td>
  	</tr>
      
<?php } ?>

	<tr>
    	<td>			
        	<br>
			<span class="tituloCategoriaMenu">Tipos de Combustible</span>
			<br>
			<br>
			<table width="100%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		  		<tr class="bordeGrisFondo">
        			<td width="10">&nbsp;</td>
					<td width="149" height="25"><strong>ID</strong></td>
        			<td width="595" height="25"><strong>Nombre</strong></td>
      			</tr>
      
	<?php foreach (CombustibleTipos::GetAll() as $oTipoCombustible) { ?>
      
	  			<tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
        			<td width="10" height="25">&nbsp;</td>
       			  	<td height="25"><?=$oTipoCombustible['IdTipo']?></td>
       			  	<td height="25"><?=$oTipoCombustible['Descripcion']?></td>
   			  	</tr>
	  			<tr>
        			<td colspan="5">
                    	<div align="center">
          					<table width="100%"  border="0" cellspacing="0" cellpadding="0">
            					<tr>
              						<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
            					</tr>
          					</table>
        				</div>
                 	</td>
      			</tr>
      
	<?php } ?> 
                 
			</table>
	  	</td>
  	</tr>
	<tr>
    	<td>&nbsp;</td>
    </tr>
	<tr>
    	<td>
        	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
          		<tr>
            		<td height="30">
                    	<div align="right">
              				<label>
              					<input name="button" type="button" class="botonBasico" onClick="javascript: window.history.back();" id="button" value="Volver">
              				</label>
            			</div>
                 	</td>
            		<td width="10" height="30">&nbsp;</td>
          		</tr>
        	</table>
       	</td>
    </tr>
</table>

</body>
</html>