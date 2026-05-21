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
$Patente				= strval($_REQUEST['Patente']);
$Action					= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= 0;
$oUnidades				= new Unidades();
$oMinutas				= new Minutas();
$oCuentasGestoria		= new CuentasGestoria();
$oModelos				= new Modelos();
$oGestores				= new Gestores();
$TotalAPagar = 0;

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oCuentaGestoria = $oCuentasGestoria->GetById($IdCuentaGestoria))
{
	header('Location: cuentasgestoria.php');
	exit;
}

$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);

$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad);
$oModelo = $oModelos->GetById($oUnidad->IdModelo);
$oGestor = $oGestores->GetById($oCuentaGestoria->IdGestor);

/* si el formulario fue enviado... */
if ($Submit)
{
				
				
				$PatentamientoFinal = $_REQUEST['PatentamientoFinal_' . $oCuentaGestoria->IdCuentaGestoria];
				$PrendaFinal = $_REQUEST['PrendaFinal_' . $oCuentaGestoria->IdCuentaGestoria];
				$AltaFinal = $_REQUEST['AltaFinal_' . $oCuentaGestoria->IdCuentaGestoria];
				$SelladoFinal = $_REQUEST['SelladoFinal_' . $oCuentaGestoria->IdCuentaGestoria];
				$TotalFinal = $_REQUEST['TotalFinal_' . $oCuentaGestoria->IdCuentaGestoria];
				$ComisionGestor = $_REQUEST['ComisionGestor_' . $oCuentaGestoria->IdCuentaGestoria];
				
				$PatentamientoFinal = str_replace(',', '.', $PatentamientoFinal);
				$PrendaFinal = str_replace(',', '.', $PrendaFinal);
				$AltaFinal = str_replace(',', '.', $AltaFinal);
				$SelladoFinal = str_replace(',', '.', $SelladoFinal);
				$TotalFinal = str_replace(',', '.', $TotalFinal);
				$ComisionGestor = str_replace(',', '.', $ComisionGestor);
			
			if ($err == 0)
			{		
				$oCuentaGestoria->PatentamientoFinal = $PatentamientoFinal;
				$oCuentaGestoria->PrendaFinal = $PrendaFinal;
				$oCuentaGestoria->AltaFinal = $AltaFinal;
				$oCuentaGestoria->SelladoFinal = $SelladoFinal;
				$oCuentaGestoria->TotalFinal = $TotalFinal;
				$oCuentaGestoria->ComisionGestor = $ComisionGestor;
				$oCuentaGestoria->FechaRendicion = date('d-m-Y');
				$oCuentaGestoria->TotalRendicion = $oCuentaGestoria->TotalCalculado - $TotalFinal - $ComisionGestor;
				
				$oCuentasGestoria->UpdateRendicion($oCuentaGestoria);
				
				$oUnidad->Patente = $Patente;
				
				$oUnidades->Update($oUnidad);
				
				$oMinuta->Alta = $AltaFinal;
				$oMinuta->RentasFinal = $SelladoFinal;
				
				$oMinutas->Update($oMinuta);
			
				header('Location: cuentasgestoria.php');
				exit;
			}
			
	
}
else
{
	$Patente = $oUnidad->Patente;
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
	$j(".pago-parcial, .rendicion").on('input',function() {
		var Id = $j(this).attr("id-element");
		var Total = 0;
		$j('.pago-parcial').each(function() {
			if ($j(this).attr("id-element") == Id)
				Total += parseFloat($j(this).val().replace(',', '.'));
		});
		var TotalCalculado = <?= number_format($oCuentaGestoria->TotalCalculado, 2, '.', '') ?>;
		$j('#TotalFinal_' + Id).val(Total.toFixed(2));
		//var Comision = parseFloat($j('.rendicion').val().replace(',', '.'));
		TotalCalculado-= Total;
		//TotalCalculado-= Comision; 
		$j('#TotalRendicion_' + Id).val(TotalCalculado.toFixed(2));
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
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Gestor&iacute;a - Rendici&oacute;n</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
		<tr>
            <td>
				<table width="90%" align="center" cellpadding="0" cellspacing="0" class="borderGris">
					<tr>
						<td width="50%">&nbsp;</td>
						<td width="50%">&nbsp;</td>
					</tr>
					<tr>
						<td align="right"><strong>Nro. Interno:&nbsp;</strong></td>
						<td align="left">&nbsp;<?= $oUnidad->IdUnidad ?></td>
					</tr>
					<tr>
						<td align="right"><strong>Gestor:&nbsp;</strong></td>
						<td align="left">&nbsp;<?= $oGestor->RazonSocial ?></td>
					</tr>
					<tr>
						<td align="right"><strong>Modelo:&nbsp;</strong></td>
						<td align="left">&nbsp;<?=$oModelo->DenominacionComercial?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Dominio</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Patentamiento</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Gasto Gestor</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Rendici&oacute;n</strong></div></td>
                    </tr>
          
                    
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="125" height="25">
							<div id="margen"  align="center">
								<input type="text" class="camporFormularioChico" id="Patente" name="Patente" value="<?= $Patente ?>" />
							</div>
						</td>
                        <td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChico pago-parcial" id-element="<?= $oCuentaGestoria->IdCuentaGestoria ?>" id="PatentamientoFinal_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="PatentamientoFinal_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->PatentamientoFinal, 2, '.', '') ?>" />
							</div>
						</td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChico pago-parcial" id-element="<?= $oCuentaGestoria->IdCuentaGestoria ?>" id="PrendaFinal_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="PrendaFinal_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->PrendaFinal, 2, '.', '') ?>" />
							</div>
						</td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChicoSuggest pago-parcial" readonly="readonly" id="TotalFinal_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="TotalFinal_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->TotalFinal, 2, '.', '') ?>" />
							</div>
						</td>
						<td width="120" height="25">
							<div id="margen"  align="center">
								$<input type="text" class="camporFormularioChicoSuggest pago-parcial" readonly="readonly" id="TotalRendicion_<?= $oCuentaGestoria->IdCuentaGestoria ?>" name="TotalRendicion_<?= $oCuentaGestoria->IdCuentaGestoria ?>" value="<?= number_format($oCuentaGestoria->TotalRendicion, 2, '.', '') ?>" />
							</div>
						</td>
                    </tr>
                    <tr>
                        <td colspan="11">
                            <div align="center">
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
				
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
                            	<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="javascript: window.location.href = 'cuentasgestoria.php'" />
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