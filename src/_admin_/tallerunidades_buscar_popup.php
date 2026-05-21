<?php 

require_once('../library/class.tallerunidades.php');
require_once('../library/class.session.php'); 
require_once('../library/class.ivas.php'); 


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$Dominio				= $_REQUEST['FilterDominio'];
$NumeroVin				= $_REQUEST['FilterNumeroVin'];
$Cliente				= $_REQUEST['FilterCliente'];
$Descripcion			= $_REQUEST['FilterDescripcion'];
$filter['Dominio'] 		= $Dominio;
$filter['NumeroVin']	= $NumeroVin;
$filter['Cliente']		= $Cliente;
$filter['Modelo']		= $Modelo;

/* declaracion de variables */
$arr 			= array();
$oTallerUnidades = new TallerUnidades();

?>

<script type="text/javascript">

$j(document).ready(function() {
	$j('#btnBuscar').click(function() {
		$j('#frmBusquedaCodigo #Page').val(0);
	});
	
	$j('#frmBusquedaCodigo').ajaxForm({
		success: function(responseText, statusText, xhr, $form) {
			$j('#tr_resultado').html(responseText);
		}
	});
});

function SetPageBusqueda(page)
{
	$j('#frmBusquedaCodigo #Page').val(page);
	$j('#frmBusquedaCodigo').submit();
}

	
</script>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">  	
  	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr>
  		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr><td>&nbsp;</td></tr>
				<tr>
					<td>
						<form id="frmBusquedaCodigo" name="frmBusquedaCodigo" method="get" action="tallerunidades_buscar_resultado.php">
							<input type="hidden" name="Page" id="Page" value="<?= $Page ?>" />
							<table width="90%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><div align="right">Dominio:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterDominio" name="FilterDominio" value="<?= $filter['Dominio'] ?>" /></div></td>
									<td>&nbsp;</td>
									<td><div align="right">Cliente:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterCliente" name="FilterCliente" value="<?= $filter['Cliente'] ?>" /></div></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="right">Modelo:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterModelo" name="FilterModelo" value="<?= $filter['Modelo'] ?>" /></div></td>
									<td>&nbsp;</td>
									<td><div align="right">N&uacute;mero de Vin:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterNumeroVin" name="FilterNumeroVin" value="<?= $filter['NumeroVin'] ?>" /></div></td>
									<td>&nbsp;</td>
									<td><input type="submit" name="btnBuscar" class="botonBasico" id="btnBuscar" value="Buscar" /></td>
								</tr>
							</table>
						</form>
					</td>
				</tr>
				<tr><td>&nbsp;</td></tr>
			</table>
		</td>
  	</tr>
	<tr>
  		<td>&nbsp;</td>
  	</tr>
	<tr id="tr_resultado">
		<?php include('tallerunidades_buscar_resultado.php'); ?>
	</tr>
	<tr>
    	<td>&nbsp;</td>
  	</tr>
  	<tr>
    	<td align="right"><input type="button" id="btnAgregar" name="btnAgregar" value="Agregar Unidad" class="botonBasico" onclick="AddTallerUnidad()" /></td>
  	</tr>
</table>