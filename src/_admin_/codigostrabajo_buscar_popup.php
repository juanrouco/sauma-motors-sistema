<?php 

require_once('../library/class.codigostrabajo.php');
require_once('../library/class.modelospv.php');
require_once('../library/class.session.php'); 
require_once('../library/class.ivas.php'); 


/* obtiene datos enviados */
$Page 	= (isset($_REQUEST['Page'])) ? intval($_REQUEST['Page']) : 0;


/* armamos el filtro */
$filter = array();
$IdModeloPV					= $_REQUEST['FilterIdModeloPV'];
$CodigoHistorico			= $_REQUEST['FilterCodigoHistorico'];
$Codigo						= $_REQUEST['FilterCodigo'];
$Descripcion				= $_REQUEST['FilterDescripcion'];
$filter['IdModeloPV'] 		= $IdModeloPV;
$filter['CodigoHistorico'] 	= $CodigoHistorico;
$filter['Codigo']			= $Codigo;
$filter['Descripcion']		= $Descripcion;

/* declaracion de variables */
$arr 			= array();
$CodigosTrabajo = new CodigosTrabajo();
$oModelosPV = new ModelosPV();

$arrModelosPV = $oModelosPV->GetAll();

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
						<form id="frmBusquedaCodigo" name="frmBusquedaCodigo" method="get" action="codigostrabajo_buscar_resultado.php">
							<input type="hidden" name="Page" id="Page" value="<?= $Page ?>" />
							<table width="90%" cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><div align="right">Modelo:</div></td>
									<td><div id="margen">
										<select class="camporFormularioSimple" id="FilterIdModeloPV" name="FilterIdModeloPV">
											<option value="">[Indistinto]</option>
											<?php
											foreach ($arrModelosPV as $oModeloPV)
											{
												$selected = '';
												if ($oModeloPV->IdModeloPV == $filter['IdModeloPV'])
													$selected = 'selected="selected"';
											?>
											<option value="<?= $oModeloPV->IdModeloPV ?>" <?= $selected ?>><?= $oModeloPV->Modelo ?></option>
											<?php
											}
											?>
										</select>
									</div></td>
									<td>&nbsp;</td>
									<td><div align="right">C&oacute;digo Hist&oacute;rico:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterCodigoHistorico" name="FilterCodigoHistorico" value="<?= $filter['CodigoHistorico'] ?>" /></div></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><div align="right">C&oacute;digo:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterCodigo" name="FilterCodigo" value="<?= $filter['Codigo'] ?>" /></div></td>
									<td>&nbsp;</td>
									<td><div align="right">Descripci&oacute;n:</div></td>
									<td><div id="margen"><input type="text" class="camporFormularioSimple" id="FilterDescripcion" name="FilterDescripcion" value="<?= $filter['Descripcion'] ?>" /></div></td>
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
		<?php include('codigostrabajo_buscar_resultado.php'); ?>
	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>