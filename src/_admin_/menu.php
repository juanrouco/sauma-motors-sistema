<?php 

require_once('../inc_library.php'); 

$oTiposFormulario = new TiposFormulario();

/* obtenemos los tipos de formulario que requieren llenar el repositorio */
$arrTiposFormulario = $oTiposFormulario->GetAllForRepositorio();

$Section = 0;

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>

<script language="javascript">

function Section(IdSection)
{	
	var Seccion 	= Get('Section_' + IdSection);
	var lblSection	= Get('lblSection_' + IdSection);

	if (Seccion == undefined)
		return false;
	
	if (lblSection == undefined)
		return false;

	if (Seccion.style.display == '')
	{
		HideSection('Section_' + IdSection);
		lblSection.innerText = '[+]';
	}	
	else
	{
		ShowSection('Section_' + IdSection);
		lblSection.innerText = '[-]';
	}
	
	return true;
}

</script>

</head>
<body>

<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordeGris" id="contenedora">
  	<tr>
    	<td width="10"><p>&nbsp;</p></td>
    	<td><div align="center"><a href="welcome.php" target="mainFrame"><img src="images/logo_compania.jpg" border="0" width="160" ></a></div></td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>&nbsp;</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>&nbsp;</td>
    	<td width="10">&nbsp;</td>
  	</tr>
<?php if ((Session::CheckPerm(PERM_TAREAS_LIST)) && (Session::CheckPerm(PERM_PRESUP_LIST))) { ?>
	<tr>
    	<td width="10">&nbsp;</td>
    	<td class="bordeGrisFondo"><div align="center"><a href="dashboard_vendedores.php" target="mainFrame" style="text-decoration: none" class="tituloCategoriaMenu">INICIO</a></div></td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	
<?php } else { ?>
	<tr>
    	<td width="10">&nbsp;</td>
    	<td class="bordeGrisFondo"><div align="center"><span class="tituloCategoriaMenu">MENU</span></div></td>
    	<td width="10">&nbsp;</td>
  	</tr>
<?php } ?>
	<tr>
    	<td width="10">&nbsp;</td>
    	<td>&nbsp;</td>
    	<td width="10">&nbsp;</td>
  	</tr>

<?php if ((Session::CheckPerm(PERM_UNID_STOCK)) || (Session::CheckPerm(PERM_UNID_LIST)) || (Session::CheckPerm(PERM_MODE_LIST)) || (Session::CheckPerm(PERM_RECE_LIST)) || 
		(Session::CheckPerm(PERM_COMP_LIST)) || (Session::CheckPerm(PERM_TIPM_LIST)) || (Session::CheckPerm(PERM_ESTU_LIST))) { ?>
	<?php $Section++; ?>

  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris2">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/auto_modelo.png" width="16" height="16"></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">0 KM </strong></td>
       	 			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

			           	<?php if (Session::CheckPerm(PERM_UNID_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="unidades.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las undiades." class="linkMenu">Unidades</a></p></td>
                            </tr>
			           	<?php } if (Session::CheckPerm(PERM_UNID_STOCK)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="unidades_stock.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las undiades." class="linkMenu">Stock</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="unidades_lista_precios.php" target="mainFrame" title="Desde aqu&iacute; se puede ver la lista de precios." class="linkMenu">Lista de Precio</a></p></td>
                            </tr>
			           	<?php } if (Session::CheckPerm(PERM_MODE_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="modelos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los modeloes." class="linkMenu">Modelos</a></p></td>
                            </tr>
			           	<?php } if (Session::CheckPerm(PERM_RECE_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="recepciones.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las recepciones de unidades." class="linkMenu">Recepci&oacute;n de Unidades</a></p></td>
                            </tr>
						<?php } if (Session::CheckPerm(PERM_COMP_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="compras.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los importes de compra." class="linkMenu">Datos de Compra</a></p></td>
                            </tr>
			           	<?php } ?>
						<?php if (Session::CheckPerm(PERM_ACTUALIZAR_PRECIO)) { ?>
							<tr>
                                <td height="15" class="Botonera"><p><a href="unidades_modificar_precios.php?MainAction=Select" target="mainFrame" title="Desde aqu&iacute; se modifican los precios de las unidades." class="linkMenu">Actualizar Precio</a></p></td>
                            </tr>
			           	<?php } ?>
						<?php if ((Session::CheckPerm(PERM_MARC_LIST)) || (Session::CheckPerm(PERM_COLO_LIST)) || 
									(Session::CheckPerm(PERM_UBIC_LIST)) || (Session::CheckPerm(PERM_TIPM_LIST)) || 
									(Session::CheckPerm(PERM_ESTU_LIST))) { ?>
							<?php $Section++; ?>

                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

											   <?php if (Session::CheckPerm(PERM_MARC_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="marcas.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las marcas." class="linkMenu">Marcas</a></p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_COLO_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="colores.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los colores." class="linkMenu">Colores</a></p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_UBIC_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="ubicaciones.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las ubicaciones de las unidades." class="linkMenu">Ubicaciones</a></p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_TIPM_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="tiposmodelo.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los tipos de modelos." class="linkMenu">Tipos</a></p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_CATM_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="categoriasmodelo.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las categorias de modelos." class="linkMenu">Categor&iacute;as</a></p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_ESTU_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="estadosunidad.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los estados de las unidades." class="linkMenu">Estados</a></p></td>
                                                    </tr>
											   <?php } ?>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
                            
                       	<?php } ?>
                            
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
    
<?php 
	}
	if ((Session::CheckPerm(PERM_USADOS_LIST)) || (Session::CheckPerm(PERM_RECEPUS_LIST)) || Session::CheckPerm(PERM_VENTUS_LIST)) { ?>
	<?php $Section++; ?>

  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris2">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/auto_modelo.png" width="16" height="16"></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Usados </strong></td>
       	 			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

			           	<?php if (Session::CheckPerm(PERM_USADOS_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="usados.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver las unidades usadas tomadas como parte de pago." class="linkMenu">Unidades - Usados</a></p></td>
                            </tr>
			           	<?php } /*if (Session::CheckPerm(PERM_UNID_STOCK)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="unidades_stock.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las undiades." class="linkMenu">Stock</a></p></td>
                            </tr>
			           	<?php }*/ if (Session::CheckPerm(PERM_RECEPUS_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="recepcionesusados.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las recepciones de usados." class="linkMenu">Recepci&oacute;n de Usados</a></p></td>
                            </tr>
						<?php } if (Session::CheckPerm(PERM_ACTUALIZAR_PRECIO)) { ?>
							<tr>
                                <td height="15" class="Botonera"><p><a href="usados_modificar_precios.php?MainAction=Select" target="mainFrame" title="Desde aqu&iacute; se modifican los precios de los usados." class="linkMenu">Actualizar Precio</a></p></td>
                            </tr>
							<?php } ?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
    
<?php 
	}if (true) {
	
	if (((Session::CheckPerm(PERM_TAREAS_LIST)) || (Session::CheckPerm(PERM_PRESUP_LIST)))) { ?>
	<?php $Section++; ?>

  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris2">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/check.gif" width="16" height="16"></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">CRM </strong></td>
       	 			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

			           	<?php if (Session::CheckPerm(PERM_TAREAS_LIST)) { /*?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="tareas.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las tareas." class="linkMenu">Tareas</a></p></td>
                            </tr> */ ?>
							 <tr>
                                <td height="15" class="Botonera"><p><a href="tareas_agenda.php" target="mainFrame" title="Desde aqu&iacute; se accede a la agenda del usuario." class="linkMenu">Agenda</a></p></td>
                            </tr>
			           	<?php } ?>
        				
			           	<?php if (Session::CheckPerm(PERM_PRESUP_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="presupuestos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los presupuestos." class="linkMenu">Presupuestos</a></p></td>
                            </tr>
			           	<?php } ?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
    
<?php 
	}
	if (Session::CheckPerm(PERM_ARTI_LIST) || Session::CheckPerm(PERM_STOCK_UPDATE))
	{
		$Section++; 
?>
	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris5">
        		<tr>
          			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/catalogo.png" /></strong></div></td>
          			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Repuestos</strong></td>
          			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
        		</tr>
        		<tr id="Section_<?=$Section?>">
          			<td width="25" height="15" class="Botonera"><p>&nbsp;</p></td>
          			<td class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
						
			           	<?php 
						if (Session::CheckPerm(PERM_ARTI_LIST))
						{ 
						?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="articulos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los repuestos." class="linkMenu">Repuestos</a> </p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="articulostocks_modificarstock.php?TipoOperacion=0" target="mainFrame" title="Desde aqu&iacute; agrega stock a los art&iacute;culos." class="linkMenu">Agregar Stock</a> </p></td>
                            </tr>
			           	<?php 
						} 
						if (Session::CheckPerm(PERM_STOCK_UPDATE)) 
						{ 
						?>
							<tr>
								<td height="15" class="Botonera"><p><a href="ventarepuestos.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver y administrar las ventas de repuestos en mostrador." class="linkMenu">Ventas Mostrador</a></p></td>
							</tr>
			           	<?php 
						}
						if (Session::CheckPerm(PERM_ARTI_LIST))
						{ 
						?>
							<tr>
								<td height="15" class="Botonera"><p><a href="ventarepuestos_ot.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver y administrar los repuestos asignados a las Ordenes de Trabajo." class="linkMenu">Repuestos asignados OT</a></p></td>
							</tr>
							<tr>
								<td height="15" class="Botonera"><p><a href="ventarepuestos_add.php" target="mainFrame" title="Desde aqu&iacute; se generan e imprimen los reportes de unidades facturadas." class="linkMenu">Realizar Venta</a></p></td>
							</tr>
							<tr>
								<td height="15" class="Botonera"><p><a href="devolucionrepuestos.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver las devoluciones de repuestos." class="linkMenu">Devoluciones</a></p></td>
							</tr>
							<tr>
								<td height="15" class="Botonera"><p><a href="devolucionrepuestos_add.php" target="mainFrame" title="Desde aqu&iacute; se generan las devoluciones de repuestos." class="linkMenu">Realizar Devoluci&oacute;n</a></p></td>
							</tr>							
						<?php 
						} 
						if (Session::CheckPerm(PERM_STOCK_AJUSTE))
						{
						?>
							<tr>
								<td height="15" class="Botonera"><p><a href="articulostocks_ajuste.php" target="mainFrame" title="Desde aqu&iacute; se realizar los ajustes de stock de repuestos." class="linkMenu">Ajuste Stock</a></p></td>
							</tr>
						<?php 
						}
						if (Session::CheckPerm(PERM_CUPON_LIST))
						{ 
							$Section++;
						?>
							<tr>
								<td height="15" class="Botonera"><p><a href="cierresz.php" target="mainFrame" title="Desde aqu&iacute; se administran los Cierres Z." class="linkMenu">Cierre Z</a></p></td>
							</tr>
							<tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
							<tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
													<tr>
														<td height="15" class="Botonera"><p><a href="cuponesdescuento.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta y anulan los cupones de descuento." class="linkMenu">Cupones Descuento</a> </p></td>
													</tr>
												</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
			           	<?php 
						}
						?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
<?php 
	}
	
	if ((Session::CheckPerm(PERM_TALL_LIST) || Session::CheckPerm(PERM_ORDE_LIST) || Session::CheckPerm(PERM_TARE_LIST) || Session::CheckPerm(PERM_CODTRA_LIST)))
	{
		$Section++; 
?>
	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris5">
        		<tr>
          			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/adm_general.png" /></strong></div></td>
          			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Taller</strong></td>
          			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
        		</tr>
        		<tr id="Section_<?=$Section?>">
          			<td width="25" height="15" class="Botonera"><p>&nbsp;</p></td>
          			<td class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
			           	<?php 
						if (Session::CheckPerm(PERM_TALL_CREATE))
						{ 
						?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="tallerunidades.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS las unidades." class="linkMenu">Unidades</a> </p></td>
                            </tr>
			           	<?php 
						}
						if (Session::CheckPerm(PERM_ORDE_LIST))
						{ 
						?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="ordenestrabajo.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS las ordenes de trabajo." class="linkMenu">Ordenes Trabajo</a> </p></td>
                            </tr>
						<?php
						}
						if (Session::CheckPerm(PERM_TURNO_LIST))
						{
						?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="ordenestrabajo_ingresados.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver las ordenes de trabajo no cerradas." class="linkMenu">Ordenes Trabajo Abiertas</a> </p></td>
                            </tr>
							<tr>
                                <td height="15" class="Botonera"><p><a href="ordenestrabajo_turnos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los turnos." class="linkMenu">Calendario de Turnos</a> </p></td>
                            </tr>
							<tr>
                                <td height="15" class="Botonera"><p><a href="turnos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los turnos." class="linkMenu">Turnos</a> </p></td>
                            </tr>
			           	<?php 
						}
						/*if (Session::CheckPerm(PERM_ORDE_TALLER))
						{ 
						?>
							<tr>
                                <td height="15" class="Botonera"><p><a href="ordenestrabajo_taller.php" target="mainFrame" title="Desde aqu&iacute; se ven, inician, pausan y finalizan las ordenes de trabajo asignadas." class="linkMenu">Ordenes Trabajo Asignadas</a> </p></td>
                            </tr>
						<?php 
						}*/
						if (Session::CheckPerm(PERM_TARE_LIST) || Session::CheckPerm(PERM_CODTRA_LIST))
						{ 
							$Section++;
						?>
							<tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
							<tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
												<?php 
												if (Session::CheckPerm(PERM_CODTRA_LIST))
												{ 
												?>
													<tr>
														<td height="15" class="Botonera"><p><a href="codigostrabajo.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los c&oacute;digos de trabajo." class="linkMenu">C&oacute;digos de Trabajo</a> </p></td>
													</tr>
												<?php
												}
												if (Session::CheckPerm(PERM_TARE_LIST))
												{ 
												?>
													<tr>
														<td height="15" class="Botonera"><p><a href="tareastrabajo.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS las tareas." class="linkMenu">Tareas</a> </p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="modelospv.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS las tareas." class="linkMenu">Modelos Taller</a> </p></td>
													</tr>
												<?php
												}
												if (Session::CheckPerm(PERM_TARE_CREATE))
												{
												?>
													<tr>
														<td height="15" class="Botonera"><p><a href="costosmanoobra_mod.php" target="mainFrame" title="Desde aqu&iacute; se modifica el costo de la mano de obra." class="linkMenu">Costo Mano Obra</a> </p></td>
													</tr>
												<?php
												}
												?>
												</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
			           	<?php 
						}
						?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
<?php
	}
	
	if ($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && (Session::CheckPerm(PERM_PROVE_LIST) || Session::CheckPerm(PERM_RUBR_LIST)))
	{ 
		$Section++;
?>
	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris5">
        		<tr>
          			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/delivery.png" /></strong></div></td>
          			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Proveedores</strong></td>
          			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
        		</tr>
        		<tr id="Section_<?=$Section?>">
          			<td width="25" height="15" class="Botonera"><p>&nbsp;</p></td>
          			<td class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           	<?php if (Session::CheckPerm(PERM_PROVE_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="proveedores.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los proveedores." class="linkMenu">Proveedores</a> </p></td>
                            </tr>
			           	<?php } ?>

						<?php if (Session::CheckPerm(PERM_RUBR_LIST)) { ?>
							<?php $Section++; ?>

                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

											   <?php if (Session::CheckPerm(PERM_RUBR_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="rubros.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los rubros." class="linkMenu">Rubros</a> </p></td>
                                                    </tr>                                               
                                               <?php } ?>
                                               
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
                            
                       	<?php } ?>
                            
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
<?php } ?>
<?php if (true) /*($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && ((Session::CheckPerm(PERM_CLIE_LIST)) || (Session::CheckPerm(PERM_ESTC_LIST)) || 
		(Session::CheckPerm(PERM_TIPI_LIST)) || (Session::CheckPerm(PERM_TIPD_LIST)) || 
		(Session::CheckPerm(PERM_PROF_LIST))))*/ { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris5">
        		<tr>
          			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/usuarios.png" /></strong></div></td>
          			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Clientes</strong></td>
          			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
        		</tr>
        		<tr id="Section_<?=$Section?>">
          			<td width="25" height="15" class="Botonera"><p>&nbsp;</p></td>
          			<td class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

                            <tr>
                                <td height="15" class="Botonera"><p><a href="clientes.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los clientes que podr&aacute;n navegar por el sitio web." class="linkMenu">Clientes</a> </p></td>
                            </tr>

						<?php if ((Session::CheckPerm(PERM_ESTC_LIST)) || (Session::CheckPerm(PERM_TIPI_LIST)) || (Session::CheckPerm(PERM_TIPD_LIST)) || (Session::CheckPerm(PERM_PROF_LIST))) { ?>
							<?php $Section++; ?>

                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

											   <?php if (Session::CheckPerm(PERM_ESTC_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="estadosciviles.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los clientes que podr&aacute;n navegar por el sitio web." class="linkMenu">Estados Civiles</a> </p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_TIPI_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="tiposiva.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los clientes que podr&aacute;n navegar por el sitio web." class="linkMenu">Tipos Iva</a> </p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_TIPD_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="tiposdocumento.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los clientes que podr&aacute;n navegar por el sitio web." class="linkMenu">Tipos Documento</a> </p></td>
                                                    </tr>
                                               <?php } if (Session::CheckPerm(PERM_PROF_LIST)) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="profesiones.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja, modifican y exportan a XLS los clientes que podr&aacute;n navegar por el sitio web." class="linkMenu">Profesiones</a> </p></td>
                                                    </tr>
                                               <?php } ?>
                                               
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
                            
                       	<?php } ?>
                            
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>
<?php if ((Session::CheckPerm(PERM_VENT_LIST)) || (Session::CheckPerm(PERM_FACU_LIST)) || (Session::CheckPerm(PERM_FACV_LIST)) || 
		(Session::CheckPerm(PERM_REMI_LIST)) || (Session::CheckPerm(PERM_NONR_LIST)) || (Session::CheckPerm(PERM_PACC_LIST)) || 
		(Session::CheckPerm(PERM_IPRE_LIST)) || (Session::CheckPerm(PERM_PLAV_LIST)) || (Session::CheckPerm(PERM_CMPB_LIST)) ||
		(Session::CheckPerm(PERM_REPF_LIST))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/facturacion.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Ventas</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           <?php if (Session::CheckPerm(PERM_VENT_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="minutas.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las minutas de ventas." class="linkMenu">Minutas 0 Km</a></p></td>
                            </tr>
							<?php if ($currentUser->IdPerfil != 18 && $currentUser->IdPerfil != 2) { ?>
			           <?php
						}
						?>
			           	
					   <?php } if (Session::CheckPerm(PERM_VENTUS_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="minutasusados.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las minutas de ventas de usados." class="linkMenu">Minutas Usados</a></p></td>
                            </tr>
						<?php } ?>
						<?php if (Session::CheckPerm(PERM_VENT_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="minutasespera.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las minutas de ventas es espera." class="linkMenu">Preventa</a></p></td>
                            </tr>
			           <?php
						
						}
						?>
							
			           <?php } if (Session::CheckPerm(PERM_FACU_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="facturaunidades.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta las facturas de venta de unidades." class="linkMenu">Facturas Unidades</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_REMI_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="remitos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta los remitos." class="linkMenu">Remitos</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_ORDS_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="ordenessalida.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las &oacute;rdenes de salida." class="linkMenu">&Oacute;rdenes de Salida de 0Km</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="ordenessalidausados.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las &oacute;rdenes de salida." class="linkMenu">&Oacute;rdenes de Salida de Usados</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_NONR_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="notasnorodamiento.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las notas de no rodamiento." class="linkMenu">Notas de No Rodamiento</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_PACC_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="pedidosaccesorios.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los pedidos de accesorios." class="linkMenu">Pedidos de Accesorios</a></p></td>
                            </tr>
			           <?php } /*if (Session::CheckPerm(PERM_IPRE_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="planillaspreentrega.php" target="mainFrame" title="Desde aqu&iacute; se generan e imprimen los informes de preentrega." class="linkMenu">Informes de Preentrega</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_PLAV_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="planillaslavado.php" target="mainFrame" title="Desde aqu&iacute; se generan e imprimen las planilla de lavado." class="linkMenu">Planillas de Lavado</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_REPF_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="reportesfacturacion.php" target="mainFrame" title="Desde aqu&iacute; se generan e imprimen los reportes de unidades facturadas." class="linkMenu">Reportes Unidades Facturadas</a></p></td>
                            </tr>
			           <?php }*/ ?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>    

<?php if ((Session::CheckPerm(PERM_CPRE_LIST) && false)) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/detalles.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Prendas</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			          <?php if (Session::CheckPerm(PERM_CPRE_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="contratosprendas.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta los contratos de prenda." class="linkMenu">Contratos de Prenda</a></p></td>
                            </tr>
                       	<?php } ?>
						 <?php if (Session::CheckPerm(PERM_CPREUS_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="contratosprendasusados.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta los contratos de prenda de usados." class="linkMenu">Contratos de Prenda Usados</a></p></td>
                            </tr>
                       	<?php } ?>

        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>  

<?php if ($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && ((Session::CheckPerm(PERM_CMPB_LIST)) || (Session::CheckPerm(PERM_FACV_LIST)) || (Session::CheckPerm(PERM_FACU_LIST))  || (Session::CheckPerm(PERM_FACTUS_LIST)))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/detalles.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Facturaci&oacute;n</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			          <?php if (Session::CheckPerm(PERM_FACU_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="facturaunidades.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta las facturas de venta de unidades." class="linkMenu">Facturas Unidades</a></p></td>
                            </tr>
						<?php } if (Session::CheckPerm(PERM_FACTUS_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="facturausados.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y anulan las facturas de usados." class="linkMenu">Facturas Usados</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_FACV_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="facturavarias.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y anulan las facturas varias." class="linkMenu">Facturas Varias</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_FACTPV_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="facturaspostventas.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y anulan las facturas varias." class="linkMenu">Facturas PostVenta</a></p></td>
                            </tr>
			           <?php } ?>
					   <?php if (Session::CheckPerm(PERM_CMPB_LIST)) { ?>
							<tr>
								<td height="15" class="Botonera"><p><a href="notascredito.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta y visualizan las notas de cr&eacute;dito." class="linkMenu">Notas de Cr&eacute;dito</a> </p></td>
							</tr>
							<?php $Section++; ?>

                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

											   <?php foreach (ComprobanteTipos::GetAllMenu() as $oComprobanteTipo) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="comprobantes.php?IdTipoComprobante=<?=$oComprobanteTipo['IdTipo']?>" target="mainFrame" title="Desde aqu&iacute; se dan de alta y anulan los comprobantes." class="linkMenu"><?=$oComprobanteTipo['Descripcion']?></a> </p></td>
                                                    </tr>
                                               <?php } ?>													
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
                            
                       	<?php } ?>

        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>  










<?php if ($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && ((Session::CheckPerm(PERM_GEST_LIST)) || (Session::CheckPerm(PERM_FORM_LIST)) || (Session::CheckPerm(PERM_GESTOR_LIST))) && true) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/newsletter.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Gestor&iacute;a</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           
			           <?php if (Session::CheckPerm(PERM_GEST_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="gestorias.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver y generar los distintos formularios." class="linkMenu">Operatoria Gestor&iacute;a</a></p></td>
                            </tr>

                           <?php /* <tr>
                                <td height="15" class="Botonera"><p><a href="declaracionesjuradas.php" target="mainFrame" title="Desde aqu&iacute; se pueden generar la delcaraci&oacute;n jurada de solicitudes tipo 01." class="linkMenu">Declaraci&oacute;n Jurada '01'</a></p></td>
                            </tr>
			           <?php */ } ?>
						 <?php if (Session::CheckPerm(PERM_GESCUE_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cuentasgestoria.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar , modificar y eliminar las cuentas corrientes de gestoria." class="linkMenu">Cuentas Corriente</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cuentasgestoriausados.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar , modificar y eliminar las cuentas corrientes de gestoria de usados." class="linkMenu">Cuentas Corriente de Usados</a></p></td>
                            </tr>
			           <?php } ?>
						<?php if (Session::CheckPerm(PERM_CAJGES_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cajasgestoria.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar , modificar y eliminar los movientos de la caja de gestoria." class="linkMenu">Caja</a></p></td>
                            </tr>
			           <?php } ?>
					   
						<?php if (Session::CheckPerm(PERM_FORM_LIST) || Session::CheckPerm(PERM_GESTOR_LIST)) { ?>
							<?php $Section++; ?>

                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
												<?php if (Session::CheckPerm(PERM_ACRE_LIST)) { ?>
												<tr>
													<td height="15" class="Botonera"><p><a href="acreedores.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar, modificar y eliminar los acreedores prendarios." class="linkMenu">Acreedores Prendarios</a></p></td>
												</tr>
										   <?php } ?>
												<?php
												if (Session::CheckPerm(PERM_FORM_LIST) && false)
												{
												?>
											   <?php foreach ($arrTiposFormulario as $oTipoFormulario) { ?>
                                                    <tr>
                                                        <td height="15" class="Botonera"><p><a href="formularios.php?IdTipoFormulario=<?=$oTipoFormulario->IdTipoFormulario?>" target="mainFrame" title="Desde aqu&iacute; se dan de alta y anulan los formulario." class="linkMenu"><?=$oTipoFormulario->Descripcion?></a> </p></td>
                                                    </tr>
                                               <?php } ?>
                                               <?php } ?>
                                               <?php
												if (Session::CheckPerm(PERM_GESTOR_LIST))
												{
												?>
													<tr>
                                                        <td height="15" class="Botonera"><p><a href="gestores.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, modifican y eliminan los gestores." class="linkMenu">Gestores</a> </p></td>
                                                    </tr>
												<?php } ?>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
                            
                       	<?php } ?>

        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>    
<?php if ($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && ((Session::CheckPerm(PERM_COMPRA_LIST)) || Session::CheckPerm(PERM_PERIOD_LIST))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/newsletter.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Contabilidad</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           <?php if (Session::CheckPerm(PERM_COMPRA_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="facturascompras.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar, modificar y eliminar las facturas de proveedores." class="linkMenu">Compras</a></p></td>
                            </tr>
			           <?php } 
							if (Session::CheckPerm(PERM_LIBRO_IVA)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="libroivaventas.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, y exportar los libros de IVA de ventas." class="linkMenu">Libro IVA Ventas</a></p></td>
                            </tr>
							<tr>
                                <td height="15" class="Botonera"><p><a href="libroivacompras.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, y exportar los libros de IVA de compras." class="linkMenu">Libro IVA Compras</a></p></td>
                            </tr>
			           <?php } ?>
					   <?php if (Session::CheckPerm(PERM_UNID_REPORT)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="unidades_reservadas_exportar.php" target="mainFrame" title="Desde aqu&iacute; se pueden obtener el reporte de unidades se&ntilde;adas." class="linkMenu">Reporte Se&ntilde;as</a></p></td>
                            </tr>
			           <?php } ?>
						<?php $Section++; ?>

                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/categorias.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tablas </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">

						                            <tr>
                                                        <td height="15" class="Botonera"><p><a href="conceptos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los conceptos." class="linkMenu">Conceptos</a></p></td>
                                                    </tr>
													
													<?php if (Session::CheckPerm(PERM_PERIOD_LIST)) { ?>
													<tr>
                                                        <td height="15" class="Botonera"><p><a href="periodos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los periodos contables." class="linkMenu">Periodos Contables</a></p></td>
                                                    </tr>
													<?php 
													} ?>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>  
<?php if (Session::CheckPerm(PERM_MINP_LIST) || Session::CheckPerm(PERM_CUECOR_LIST)) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/calculadora.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Tesorer&iacute;a</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           <?php if (Session::CheckPerm(PERM_MINP_LIST) && false) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="minutaspago.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar, modificar las minutas de pago." class="linkMenu">Minutas de Pago</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="unidades_deuda.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar, modificar las minutas de pago." class="linkMenu">Unidades con deuda</a></p></td>
                            </tr>
			           <?php } ?>
					   <?php if (Session::CheckPerm(PERM_CUECOR_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cuentascorriente.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar, modificar las cuentas corrientes." class="linkMenu">Cuentas Corriente</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cuentascorrienteusados.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, agregar, modificar las cuentas corrientes de usados." class="linkMenu">Cuentas Corriente de Usados</a></p></td>
                            </tr>
			           <?php } ?>
													<tr>
														<td height="15" class="Botonera"><p><a href="facturaspostventas_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las ordenes de trabajo." class="linkMenu">Facturas de PV</a></p></td>
													</tr>
						<?php if (Session::CheckPerm(PERM_PAGO_CREATE)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="creditos_pendientes.php" target="mainFrame" title="Desde aqu&iacute; se pueden agregar los pagos para cada unidad." class="linkMenu">Cr&eacute;ditos Pendientes</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="pagos_listado.php?IdTipoPago=<?= TipoPago::Pagare ?>" target="mainFrame" title="Desde aqu&iacute; se pueden agregar los pagos para cada unidad." class="linkMenu">Listado de Pagares</a></p></td>
                            </tr>
			           <?php } ?>
					   <?php if (Session::CheckPerm(PERM_CHEQUE_CREATE)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cheques.php" target="mainFrame" title="Desde aqu&iacute; se pueden agregar los cheques." class="linkMenu">Cheques</a></p></td>
                            </tr>
			           <?php } ?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>  
<?php if ((Session::CheckPerm(PERM_COMIS_LIST))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/facturacion.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Comisiones</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           <?php if (Session::CheckPerm(PERM_COMIS_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="comisiones.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, y administrar las comisiones." class="linkMenu">Comisiones 0Km</a></p></td>
                            </tr>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="comisionesusados.php" target="mainFrame" title="Desde aqu&iacute; se pueden ver, y administrar las comisiones de usados." class="linkMenu">Comisiones Usados</a></p></td>
                            </tr>
			           <?php } ?>
						
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>    
<?php if ((Session::CheckPerm(PERM_TALL_REPORTES)) || (Session::CheckPerm(PERM_STOCK_REPORTES))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/chart_bar.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Reportes</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<?php 
						if (Session::CheckPerm(PERM_STOCK_REPORTES)) 
						{
							$Section++; 
						?>
                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
							<tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/catalogo.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Repuestos </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
													<tr>
														<td height="15" class="Botonera"><p><a href="articulos_valorizacion_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de valorizaci&oacute;n de Stock." class="linkMenu">Valorizaci&oacute;n</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="stockmovimientos_totales_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de ventas totales." class="linkMenu">Ventas Totales</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="articulos_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los repuestos actualmente en stock." class="linkMenu">En Stock</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="ventasinternas.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los repuestos actualmente en stock." class="linkMenu">Ventas totales por c&oacute;digo</a></p></td>
													</tr><?php /*
													<tr>
														<td height="15" class="Botonera"><p><a href="stockmovimientos_reporte.php?FilterTipoOperacion=<?= TipoVenta::Garantia ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los movimientos de stock por concepto de garant&iacute;a." class="linkMenu">Garant&iacute;a</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="stockmovimientos_reporte.php?FilterTipoOperacion=<?= TipoVenta::VentaInterna ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los movimientos de stock por concepto de garant&iacute;a." class="linkMenu">Cargo Interno</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="stockmovimientos_reporte.php?FilterTipoOperacion=<?= TipoVenta::Mostrador ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los movimientos de stock por ventas." class="linkMenu">Ventas</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="stockmovimientos_reporte.php?FilterTipoOperacion=<?= TipoVenta::PreEntrega ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los movimientos de stock por preentrega." class="linkMenu">Preentrega</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="stockmovimientos_ajuste_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de los ajustes de stock." class="linkMenu">Ajustes</a></p></td>
													</tr>*/ ?>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
						<?php
						}
						if (Session::CheckPerm(PERM_TALL_REPORTES)) 
						{
							$Section++; 
						?>
                            <tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
							<tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/adm_general.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Taller </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
													<tr>
														<td height="15" class="Botonera"><p><a href="ordenestrabajo_reporte_foc.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las ordenes de trabajo." class="linkMenu">FOC</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="ordenestrabajo_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las ordenes de trabajo." class="linkMenu">OT</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="ordenestrabajo_horas_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de horas de las ordenes de trabajo." class="linkMenu">Horas OT</a></p></td>
													</tr>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>
						<?php
						}
						if (Session::CheckPerm(PERM_STOCK_UNIDADES)) 
						{
							$Section++; 
						?>
							<tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
							<tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/auto_modelo.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Unidades </strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
													<tr>
														<td height="15" class="Botonera"><p><a href="unidades_reporte.php?EnStock=1" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las unidades actualmente en stock." class="linkMenu">0Km En Stock</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="usados_reporte.php?EnStock=1" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las unidades actualmente en stock." class="linkMenu">Usados En Stock</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="stock_estado.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las unidades en stock." class="linkMenu">Estado Stock</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="unidades_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las unidades vendidas." class="linkMenu">0Km Ventas</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="usados_reporte.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las unidades vendidas." class="linkMenu">Usados Ventas</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="unidades_reporte_quincenal.php" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las unidades vendidas." class="linkMenu">Reporte Quincenal</a></p></td>
													</tr>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>								
			           <?php
						}
						if (Session::CheckPerm(PERM_STOCK_UNIDADES) && false) 
						{
							$Section++; 
						?>
							<tr>
                                <td height="10">
                                    <div align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="linea12">
                                            <tr>
                                                <td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
							<tr>
                            	<td>
                                	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
                                    	<tr>
                                            <td width="25" height="15" class="Botonera"><div align="center"><img src="images/iconos/trackeo.png" /></div></td>
                                            <td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Contabilidad PostVenta</strong></td>
                                            <td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
                                        </tr>
                                        <tr id="Section_<?=$Section?>">
                                            <td height="15" class="Botonera">&nbsp;</td>
                                            <td height="15" class="Botonera">
                                                <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris">
													<tr>
														<td height="15" class="Botonera"><p><a href="contabilidad_reporte.php?IdTipoComprobante=<?= ComprobanteTipos::FacturaA ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las facturas A del &aacute;rea de Post Venta." class="linkMenu">Facturas A</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="contabilidad_reporte.php?IdTipoComprobante=<?= ComprobanteTipos::FacturaB ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las facturas B del &aacute;rea de Post Venta." class="linkMenu">Facturas B</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="contabilidad_notascredito_reporte.php?IdTipoComprobante=<?= ComprobanteTipos::NotaCreditoA ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las notas de cr&eacute;dito A del &aacute;rea de Post Venta." class="linkMenu">Notas Cr&eacute;dito A</a></p></td>
													</tr>
													<tr>
														<td height="15" class="Botonera"><p><a href="contabilidad_notascredito_reporte.php?IdTipoComprobante=<?= ComprobanteTipos::NotaCreditoB ?>" target="mainFrame" title="Desde aqu&iacute; se puede ver el reporte de las notas de cr&eacute;dito B del &aacute;rea de Post Venta." class="linkMenu">Notas Cr&eacute;dito B</a></p></td>
													</tr>
                                               	</table>
                                        	</td>
                                    	</tr>
                                    </table>
                                </td>
                            </tr>								
			           <?php } ?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>
<?php if ($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && ((Session::CheckPerm(PERM_PAIS_LIST)) || (Session::CheckPerm(PERM_PROV_LIST)) || 
		(Session::CheckPerm(PERM_PART_LIST)) || (Session::CheckPerm(PERM_LOCA_LIST)))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/adm_general.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Adm. General</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           <?php if (Session::CheckPerm(PERM_PAIS_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="paises.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los paises." class="linkMenu">Pa&iacute;ses</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_PROV_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="provincias.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las provincias de cada pa&iacute;s." class="linkMenu">Provincias</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_PART_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="partidos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las provincias de cada pa&iacute;s." class="linkMenu">Partidos</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_LOCA_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="localidades.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican las provincias de cada pa&iacute;s." class="linkMenu">Localidades</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_ALER_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="datos.php" target="mainFrame" title="Desde aqu&iacute; se modifican los datos generales de la empresa." class="linkMenu">Datos de la Empresa</a></p></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_ALER_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="alertas.php" target="mainFrame" title="Desde aqu&iacute; se ver los alertas por faltante de formularios y comprobantres." class="linkMenu">Alertas</a></p></td>
                            </tr>
			           <?php } ?>

        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>
<?php if ((Session::CheckPerm(PERM_CAJA_LIST)) || ($currentUser->IdUsuario != 24 && $currentUser->IdPerfil == Perfil::Vendedor && ($currentUser->IdUsuario == 10 || $currentUser->IdUsuario == 24)) || ($currentUser->IdUsuario == 21) || ($currentUser->IdUsuario == 29) || ($currentUser->IdPerfil == Perfil::Tesorero)) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/facturacion.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Disponibilidades</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

							<tr>
	                            <td height="15" class="Botonera"><p style="margin-bottom: 0;"><a href="cajas.php" target="mainFrame" title="Desde aqu&iacute; se ven y administran las disponibilidades." class="linkMenu">Disponibilidades</a></p></td>
                            </tr>
							<?php if (Session::CheckPerm(PERM_CAJADEFT_LIST) && $currentUser->IdUsuario == 1) { ?>
                            <tr>
                                <td height="15" class="Botonera"><p><a href="cajasdefault_listado.php" target="mainFrame" title="" class="linkMenu">Cajas Default</a></p></td>
                            </tr>
							<?php } ?>
							<?php if ((Session::CheckPerm(PERM_CAJA_LIST) && false)) { ?>

                            <tr>
	                            <td height="15" class="Botonera"><p style="margin-bottom: 0;"><a href="cajasaperturas_abrir.php" target="mainFrame" title="Desde aqu&iacute; se ven y administran las cajas." class="linkMenu">Apertura / Cierra de Caja</a></p></td>
                            </tr>
                            <tr>
	                            <td height="15" class="Botonera"><p style="margin-bottom: 0;"><a href="cajas_historial.php" target="mainFrame" title="Desde aqu&iacute; se ven y administran las cajas." class="linkMenu">Historial de Apertura / Cierra de Caja</a></p></td>
                            </tr>
							<?php
							}
							?>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>
<?php if ($currentUser->IdUsuario != 3 && $currentUser->IdUsuario != 26 && ((Session::CheckPerm(PERM_USUA_LIST)) || (Session::CheckPerm(PERM_SECT_LIST)) || 
		(Session::CheckPerm(PERM_MODU_LIST)) || (Session::CheckPerm(PERM_PERF_LIST)))) { ?>
	<?php $Section++; ?>
  
  	<tr>
    	<td height="15">&nbsp;</td>
    	<td height="15">
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris4">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/permisos.gif" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Usuarios</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        			<td height="15" class="Botonera">
                    	<table width="100%" border="0" cellspacing="0" cellpadding="0">

			           <?php if (Session::CheckPerm(PERM_USUA_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><a href="usuarios.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los usuarios del sistema." class="linkMenu">Usuarios</a></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_SECT_LIST)) { ?>
                            <tr>
                                <td height="15" class="Botonera"><a href="sectores.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los usuarios del sistema." class="linkMenu">Sectores</a></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_MODU_LIST)) { ?>
                            <tr>
	                            <td height="15" class="Botonera"><a href="modulos.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los modulos del sistema." class="linkMenu">M&oacute;dulos</a></td>
                            </tr>
			           <?php } if (Session::CheckPerm(PERM_PERF_LIST)) { ?>
                            <tr>
	                            <td height="15" class="Botonera"><a href="perfiles.php" target="mainFrame" title="Desde aqu&iacute; se dan de alta, baja y modifican los perfiles del sistema." class="linkMenu">Perfiles</a></td>
                            </tr>
			           <?php } ?>

        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>

<?php } ?>
<?php if ((Session::CheckPerm(PERM_CONT_UPDATE))) { ?>
	<?php $Section++; ?>
    
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td>
        	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="0" id="CajitaGris7">
      			<tr>
        			<td width="25" height="15" class="Botonera"><div align="center"><strong><img src="images/iconos/cuenta.png" /></strong></div></td>
        			<td height="15" class="Botonera"><strong class="tituloCategoriaMenu" style="cursor:pointer;" onClick="Section(<?=$Section?>);">Mi Cuenta</strong></td>
        			<td width="20" class="Botonera"><div align="center"><label id="lblSection_<?=$Section?>" style="cursor:pointer;" onClick="Section(<?=$Section?>);">[-]</label></div></td>
      			</tr>
      			<tr id="Section_<?=$Section?>">
        			<td width="25" height="15" class="Botonera">&nbsp;</td>
        				<td height="15" class="Botonera">
                        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
	                                <td height="15" class="Botonera"><p><a href="cuenta_contrasenia.php" target="mainFrame" title="Desde aqu&iacute; se puede modificar la contrase&ntilde;a de acceso al sistema." class="linkMenu">Cambiar Contrase&ntilde;a</a></p></td>
                                </tr>
        				</table>
                 	</td>
        			<td class="Botonera">&nbsp;</td>
      			</tr>
    		</table>
      	</td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10" height="15"><div align="center"></div></td>
    	<td height="15">
        	<div align="center">
        		<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea12">
          			<tr>
            			<td height="1" background="images/linea_punteada.gif"><div align="center"><img src="/imagenes/spacer.gif" width="1" height="1"></div></td>
          			</tr>
        		</table>
    		</div>
      	</td>
    	<td width="10" height="15"><div align="center"></div></td>
  	</tr>
    
<?php } ?>
    
  	<tr>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
  	</tr>
  	<tr>
    	<td width="10"><p>&nbsp;</p></td>
    	<td><div align="center"><span class="Botonera"><a href="logout.php" target="_top"><img src="images/iconos/cerrar_sesion.jpg" border="0"></a></span></div></td>
    	<td width="10">&nbsp;</td>
  	</tr>
  	<tr>
  		<td width="10">&nbsp;</td>
    	<td>&nbsp;</td>
    	<td width="10">&nbsp;</td>
  	</tr>
</table>

<script language="javascript">
for (var i=1; i<=<?=$Section?>;i++) Section(i);
</script>

</body>
</html>
