<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GESCUE_UPDATE))
	Session::NoPerm();

/* obtiene datos enviados */
$Action					= strval($_REQUEST['MainAction']);
$Fecha					= strval($_REQUEST['Fecha']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err					= array();
$oUnidades				= new Unidades();
$oMinutas				= new Minutas();
$oCuentasGestoria		= new CuentasGestoria();
$oModelos				= new Modelos();
$oGestores				= new Gestores();
$TotalAPagar = 0;

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* si el formulario fue enviado... */
if ($Submit)
{
	/* procesamos la accion requerida */
	switch ($Action)
	{
		case 'Select':
		
			/* obtenemos los datos del detalle a agregar */
			$arrIdMinuta 	= $_REQUEST['IdMinuta'];
			
			foreach ($arrIdMinuta as $IdMinuta)
			{
				$err = 0;
				/* validaciones... */
				if ($IdMinuta == '')
					$err |= 1;
				elseif (!($oMinuta = $oMinutas->GetById($IdMinuta)))
					$err |= 2;
			
				/* si no hay errores... */
				if ($err == 0)
				{
					$oCuentaGestoria = new CuentaGestoria();
					$oCuentaGestoria->IdMinuta				 = $IdMinuta;
					$oCuentaGestoria->PatentamientoCalculado = $oMinuta->GastosPatentamiento * 0.8;
					$oCuentaGestoria->PrendaCalculado		 = $oMinuta->GastosPrenda;
					$oCuentaGestoria->AltaCalculado			 = 0;
					$oCuentaGestoria->SelladoCalculado		 = $oMinuta->PrecioVenta * 0.01;
					$oCuentaGestoria->TotalCalculado		 = $oCuentaGestoria->PatentamientoCalculado + $oMinuta->GastosPrenda + $oCuentaGestoria->AltaCalculado + $oCuentaGestoria->SelladoCalculado;
					$oCuentaGestoria->Fecha					= date('d-m-Y');
					
					
					$oCuentaGestoria = $oCuentasGestoria->Create($oCuentaGestoria);
					
				}
			}
		
			break;
			
		case 'Delete':

			/* obtenemos el detalle a eliminar */
			$IdCuentaGestoria = intval($_REQUEST['Id']);
			
			$oCuentaGestoria = $oCuentasGestoria->GetById($IdCuentaGestoria);
			$oCuentasGestoria->Delete($oCuentaGestoria->IdCuentaGestoria);
			
			break;

		case 'Next':
			
			$arrData = $oCuentasGestoria->GetAllByFecha($Fecha);
			foreach ($arrData as $oCuentaGestoria)
			{
				$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
				
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
					$err[$oCuentaGestoria->IdCuentaGestoria] = 1;
					
				$oCuentaGestoria->PatentamientoCalculado = $PatentamientoCalculado;
				$oCuentaGestoria->PrendaCalculado = $PrendaCalculado;
				$oCuentaGestoria->AltaCalculado = $AltaCalculado;
				$oCuentaGestoria->SelladoCalculado = $SelladoCalculado;
				$oCuentaGestoria->TotalCalculado = $TotalCalculado;
				$oCuentaGestoria->IdGestor = $IdGestor;
				
				$oCuentasGestoria->Update($oCuentaGestoria);
			}
			
			if (count($err) == 0)
			{
				header('Location: cuentasgestoria.php');
				exit;
			}
			break;

		default:
			break;
	}
}

/* obtenemos todos las unidades de la recepcion */
$arrData = $oCuentasGestoria->GetAllByFecha($Fecha);

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
    <input type="hidden" name="MainAction" id="MainAction" value="Next" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdMinutaPago" id="IdMinutaPago" value="<?=$IdMinutaPago?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Cuentas Corriente de Gestor&iacute;a - Modificar Lote</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
    
    <?php if ($arrData != NULL) { ?>
            
        <tr>
            <td>
                <table width="90%" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="120" height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Nro. Interno</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Modelo</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Precio Venta</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Patentamiento</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Gasto Gestor</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Total</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Gestor</strong></div></td>
                        <td width="85" class="bordeGrisTitulo"><div align="center"><strong>Acciones</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oCuentaGestoria) { ?>
                    <?php $oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta); ?>
                    <?php $oUnidad = $oUnidades->GetById($oMinuta->IdUnidad); ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
                    <?php $oGestor = $oGestores->GetById($oCuentaGestoria->IdGestor); ?>
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="125" height="25"><div id="margen"  align="center"><?=$oModelo->DenominacionComercial?></div></td>
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
                        <td width="85" height="25">
                            <div align="center"> 
                                <a href="javascript: void(0);" onclick="Delete('<?=$oCuentaGestoria->IdCuentaGestoria?>');"><img src="images/iconos/del.gif" border="0" /></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10">
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
					if ($err[$oCuentaGestoria->IdCuentaGestoria])
					{
				?>
					<tr>
                        <td colspan="10">
                            <?php if ($err[$oCuentaGestoria->IdCuentaGestoria] & 1) { ?><li style="color:#FF0000;">Debe seleccionar un Gestor.</li><?php } ?>
                            
                        </td>
                    </tr>
					<tr>
                        <td colspan="10">
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
          
                <?php } ?>      
                
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
                            	<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" value="Cancelar" onClick="window.location.href = 'cuentasgestoria.php';" />
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Modificar" onClick="javascript: Next();" />
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    
    <?php } else { ?>  
    
        <tr>
            <td>
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><div align="center"> <img src="images/iconos/alerta.gif" border="0"> </div></td>
                    </tr>
                    <tr>
                        <td><div align="center"><strong>La recepci&oacute;n no posee unidades cargadas.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>		
            </td>
        </tr>
          
    <?php } ?>
    
    	<tr>
        	<td>&nbsp;</td>
        </tr>
    </table>
</form>

</body>
</html>