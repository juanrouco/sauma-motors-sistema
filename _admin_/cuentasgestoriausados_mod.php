<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GESCUE_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$IdCuentaGestoria		= intval($_REQUEST['IdCuentaGestoria']);
$Action					= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oUsados				= new Usados();
$oMinutas				= new MinutasUsados();
$oCuentasGestoria		= new CuentasGestoriaUsados();
$oModelos				= new Modelos();
$oGestores				= new Gestores();
$TotalAPagar = 0;

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oCuentaGestoria = $oCuentasGestoria->GetById($IdCuentaGestoria))
{
	header('Location: cuentasgestoriausados.php');
	exit;
}

$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);

/* si el formulario fue enviado... */
if ($Submit)
{
				
				
				$PatentamientoCalculado = $_REQUEST['PatentamientoCalculado_' . $oCuentaGestoria->IdCuentaGestoria];
				$PrendaCalculado = $_REQUEST['PrendaCalculado_' . $oCuentaGestoria->IdCuentaGestoria];
				$AltaCalculado = $_REQUEST['AltaCalculado_' . $oCuentaGestoria->IdCuentaGestoria];
				$SelladoCalculado = $_REQUEST['SelladoCalculado_' . $oCuentaGestoria->IdCuentaGestoria];
				$TotalCalculado = $_REQUEST['TotalCalculado_' . $oCuentaGestoria->IdCuentaGestoria];
				$IdGestor = $_REQUEST['IdGestor_' . $oCuentaGestoria->IdCuentaGestoria];
				
				$PatentamientoCalculado = str_replace(',', '.', $PatentamientoCalculado);
				$PrendaCalculado = str_replace(',', '.', $PrendaCalculado);
				$AltaCalculado = str_replace(',', '.', $AltaCalculado);
				$SelladoCalculado = str_replace(',', '.', $SelladoCalculado);
				$TotalCalculado = str_replace(',', '.', $TotalCalculado);
			if ($IdGestor == '')
					$err = 1;
			
			if ($err == 0)
			{		
				$oCuentaGestoria->PatentamientoCalculado = $PatentamientoCalculado;
				$oCuentaGestoria->PrendaCalculado = $PrendaCalculado;
				$oCuentaGestoria->AltaCalculado = $AltaCalculado;
				$oCuentaGestoria->SelladoCalculado = $SelladoCalculado;
				$oCuentaGestoria->TotalCalculado = $TotalCalculado;
				$oCuentaGestoria->IdGestor = $IdGestor;
				
				$oCuentasGestoria->Update($oCuentaGestoria);
			
				header('Location: cuentasgestoriausados.php');
				exit;
			}
			
	
}


$arrGestores = $oGestores->GetAll();

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterIdMinuta(IdMinuta, NumeroVin)
{	
	if ((IdMinuta == '') && (NumeroVin == ''))
	{
		Get('NumeroVin').value 		= '';
		Get('IdMinuta').value 		= '';
		Get('VehiculoModelo').value = '';
	}

	var oUnidad = GetUnidad(IdMinuta);
	if (!(oUnidad))
		return;

	var oModelo = GetModelo(oUnidad.IdModelo);
	if (!(oModelo))
		return;

	Get('NumeroVin').value 		= oUnidad.NumeroChasis;
	Get('IdMinuta').value 		= oUnidad.IdMinuta;
	Get('NumeroFactura').value 		= oUnidad.NumeroFacturaCompra;
	Get('FechaFactura').value 		= oUnidad.FechaFacturaCompra;
	Get('Neto').value 		= oUnidad.ImporteCompraNeto;
	Get('Importe').value 		= oUnidad.ImporteCompraBruto;
	Get('VehiculoModelo').value = oModelo.DenominacionModelo;
}

function Add()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Add';	
	frmData.submit();
	
	return true;
}

function Delete(IdCuentaGestoria)
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
					
	if (frmData == undefined)
		return false;

	if (confirm('¿Desea realmente eliminar el registro?'))
	{
		MainAction.value = 'Delete';	
		IdField.value = IdCuentaGestoria;	
		frmData.submit();
	}
	
	return true;
}

function Next()
{
	var frmData 	= Get('frmData');
	var MainAction 	= Get('MainAction');
				
	if (frmData == undefined)
		return false;

	MainAction.value = 'Next';	
	frmData.submit();
	
	return true;
}


$j(document).ready(function() {
	$j('.pago-parcial').keydown(function(event) {
        // Allow: backspace, delete, tab, escape, and enter
        if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 13 || 
             // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) || 
             // Allow: home, end, left, right
			 (event.keyCode == 190 || event.keyCode == 188 || event.keyCode == 110) || 
             // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        else {
            // Ensure that it is a number and stop the keypress
            if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                event.preventDefault(); 
            }   
        }
    });
	$j(".pago-parcial").on('input',function() {
		var Id = $j(this).attr("id-element");
		var Total = 0;
		$j('.pago-parcial').each(function() {
			if ($j(this).attr("id-element") == Id)
				Total += parseFloat($j(this).val().replace(',', '.'));
		});
		$j('#TotalCalculado_' + Id).val(Total.toFixed(2));
	});
});

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Id" id="Id" value="" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdCuentaGestoria" id="IdCuentaGestoria" value="<?=$IdCuentaGestoria?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Gestor&iacute;a de Usados - Modificar</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    
            
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Precio Venta</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Transferencia</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Gasto Gestor</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Gestor</strong></div></td>
                    </tr>
          
                    <?php $oUsado = $oUsados->GetById($oMinuta->IdUsado); ?>
                    <?php $oGestor = $oGestores->GetById($oCuentaGestoria->IdGestor); ?>
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oUsado->IdUsado?></div></td>
                        <td width="125" height="25"><div id="margen"  align="center"><?=$oUsado->Modelo?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center">$ <?=number_format($oMinuta->PrecioVenta, 2)?></div></td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChico pago-parcial" id-element="<?= $oCuentaGestoria->IdCuentaGestoria ?>" id="PatentamientoCalculado_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="PatentamientoCalculado_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->PatentamientoCalculado, 2, '.', '') ?>" />
							</div>
						</td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChico pago-parcial" id-element="<?= $oCuentaGestoria->IdCuentaGestoria ?>" id="PrendaCalculado_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="PrendaCalculado_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->PrendaCalculado, 2, '.', '') ?>" />
							</div>
						</td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChicoSuggest pago-parcial" id="TotalCalculado_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="TotalCalculado_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->TotalCalculado, 2, '.', '') ?>" />
							</div>
						</td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								<select class="camporFormularioSimple" id="IdGestor_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="IdGestor_<?= $oCuentaGestoria->IdCuentaGestoria ?>">
									<option value="">Seleccione</option>
									<?php
									foreach ($arrGestores as $oGestorL)
									{
										$selected = '';
										if ($oGestor->IdGestor == $oGestorL->IdGestor)
											$selected = 'selected="selected"';
									?>
									<option value="<?= $oGestorL->IdGestor ?>" <?= $selected ?>><?= $oGestorL->RazonSocial ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</td>
                    </tr>
                    <tr>
                        <td colspan="9">
                            <div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
				<?php
					if ($err == 1)
					{
				?>
					<tr>
                        <td colspan="9"><li style="color:#FF0000;">Debe seleccionar un Gestor.</li>
                        </td>
                    </tr>
					<tr>
                        <td colspan="9">
                            <div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
				  <?php
				  }
				  ?>
                
                </table>		
           	</td>
      	</tr>
        <tr>
        	<td>&nbsp;</td>
        </tr>
        <tr>
        	<td>
                <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="30">
                            <div align="center">
                            	<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'cuentasgestoriausados.php'" />
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Aceptar" onClick="javascript: Next();" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>