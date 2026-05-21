<?php 

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_CAJA_LIST) && $currentUser->IdPerfil != Perfil::Tesorero && !($currentUser->IdPerfil == Perfil::Vendedor && ($currentUser->IdUsuario == 24 || $currentUser->IdUsuario == 10)))
	Session::NoPerm();

/* obtenemos datos enviados */
$filter			= ReceiveArray($_REQUEST['filter']);
$Page 			= intval($_REQUEST['Page']);
$PageSize 		= intval($_REQUEST['PageSize']);
$IdCajaDetalle	= intval($_REQUEST['IdCajaDetalle']);

/* declaramos e instanciamos variables necesarias */
$err				= 0;
$arrData 			= array();
$oCajasMovimientos	= new CajasMovimientos();
$oCajasDetalles		= new CajasDetalles();
$oPagos				= new Pagos();
$oMinutas			= new Minutas();
$oClientes			= new Clientes();
$oUnidades			= new Unidades();
$oModelos			= new Modelos();
$oUsuarios			= new Usuarios();
$oCuentasGestorias	= new CuentasGestoria();

$filter = array();
$filter['FechaDesde']		= date('d-m-Y');
$filter['FechaHasta']		= date('d-m-Y') . ' 23:59:00';
$filter['IdCajaDetalle'] 	= $IdCajaDetalle;

$arrData 	= $oCajasMovimientos->GetAll($filter);


$Ingreso = 0;
$Egreso = 0;
foreach ($arrData as $oCajaMovimiento) 
{ 
	$Ingreso+= $oCajaMovimiento->Total > 0 ? $oCajaMovimiento->Total : 0;
	$Egreso+= $oCajaMovimiento->Total < 0 ? $oCajaMovimiento->Total : 0;
}


$oCajaDetalle = $oCajasDetalles->GetById($IdCajaDetalle);
$MontoInicial = $oCajaDetalle->Total - $Ingreso - $Egreso;


/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
//$oMpdf->watermarkText = '';

$oMpdf->SetImportUse();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>


<style>
body {
	background-color: #FFFFFF;
}
td {
	font-size: 14px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
.texto20 {
	font-size: 20px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.bordeBottom {
	border-bottom: 2px solid #000000;
}
.textoPie {
	font-size: 11px; 
	color: #000000; 
	font-family: Arial, Helvetica, sans-serif;
}
.bordeGrisFondo {
  border: 1px solid #E8E8E8;
  background-color: #F3F3F3;
}
</style>

</head>
<body>

    <table width="850"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="850"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="40" align="center"><span class="texto20"><?= utf8_encode($oCajaDetalle->Nombre) ?></span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="850"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="40" align="center"><span class="texto20">DIA: <?= date('d/m/Y') ?></span></td>
                    </tr>
                </table>
            </td>
        </tr>	
      
    <?php if ($arrData != NULL) { ?>
        
        <tr>
            <td>
                <table width="850" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr class="bordeGrisFondo">
                        <td width="425" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Detalle</strong></div></td>
                        <td width="212" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Ingreso</strong></div></td>
                        <td width="213" height="25" class="bordeGrisTitulo"><div id="margen" align="center"><strong>Egreso</strong></div></td>
                    </tr> 
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td height="25"><div id="margen">Monto Inicial</div></td>
                        <td height="25"><div id="margen" align="center">$ <?=number_format($MontoInicial, 2, ',', '.') ?></div></td>
                        <td height="25"><div id="margen" align="center">$ 0,00</div></td>
                    </tr>
                    <tr>
                        <td colspan="7">
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
					$Ingreso = 0;
					$Egreso = 0;
					foreach ($arrData as $oCajaMovimiento) 
					{ 
						$oPago = null;
						$oMinuta = null;
						$oCuentaGestoria = null;
						if ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Pago)
						{
							$oPago = $oPagos->GetById($oCajaMovimiento->IdEntidad);
							$oMinuta = $oMinutas->GetById($oPago->IdMinuta);
						}
						elseif ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Rendicion || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente)
						{
							$oCuentaGestoria = $oCuentasGestorias->GetById($oCajaMovimiento->IdEntidad);
							$oMinuta = $oMinutas->GetById($oCuentaGestoria->IdMinuta);
						}
						$Ingreso+= $oCajaMovimiento->Total > 0 ? $oCajaMovimiento->Total : 0;
						$Egreso+= $oCajaMovimiento->Total < 0 ? $oCajaMovimiento->Total : 0;
						$oUnidad = $oUnidades->GetById($oMinuta->IdMinuta);
						$oModelo = $oModelos->GetById($oUnidad->IdModelo);
						$oCliente = $oClientes->GetById($oMinuta->IdCliente);
						$oUsuario = $oUsuarios->GetById($oCajaMovimiento->IdUsuario);
						$Usuario = '';
						if ($oUsuario)
							$Usuario = ' ' . $oUsuario->Nombre . ' ' . $oUsuario->Apellido;
				?>      
                
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td><div id="margen">
							<?php
							if  ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Pago)
							{
							?>
								<?=  'Pago Unidad: ' . $oPago->IdMinuta . ' (' . utf8_encode($oModelo->DenominacionComercial) . ') - Cliente: ' . utf8_encode($oCliente->RazonSocial) . ' - ' . utf8_encode($oPago->Observaciones) . ' ' ?> 
							<?php
							}
							elseif ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::Rendicion || $oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::CuentaCorriente)
							{
							?>	<?=  TiposMovimientosCaja::GetById($oCajaMovimiento->IdTipoMovimiento) . ' | Nro. Carpeta: ' . $oMinuta->IdMinuta . ' (' . utf8_encode($oModelo->DenominacionComercial) . ') - Cliente: ' . utf8_encode($oCliente->RazonSocial) . ' ' ?> 
							<?php
							}
							elseif ($oCajaMovimiento->IdConcepto == ConceptosCajas::Sueldos)
							{
								echo 'PAGO SUELDO ';
							}
							elseif ($oCajaMovimiento->IdTipoMovimiento == TiposMovimientosCaja::PagoPV)
							{
								echo $oCajaMovimiento->GetDetalle();
							}
							?>
							<?= utf8_encode($oCajaMovimiento->Comentarios) ?></div></td>
                        <td><div id="margen" align="center">$ <?=number_format($oCajaMovimiento->Total > 0 ? $oCajaMovimiento->Total : 0, 2, ',', '.') ?></div></td>
                        <td><div id="margen" align="center">$ <?=number_format($oCajaMovimiento->Total < 0 ? $oCajaMovimiento->Total : 0, 2, ',', '.')?></div></td>
                    </tr>
                    <tr>
                        <td colspan="7">
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
					<tr bgColor='#f3f3f3'>
                        <td height="25"><div id="margen">Total</div></td>
                        <td height="25"><div id="margen" align="center">$ <?= number_format($Ingreso, 2, ',', '.') ?></div></td>
                        <td height="25"><div id="margen" align="center">$ <?= number_format($Egreso, 2, ',', '.') ?></div></td>
                    </tr>				
                
                </table>
          </td>
        </tr>
        <tr>
            <td></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="850"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="40" align="center"><span class="texto20">Total: $<?= number_format($oCajaDetalle->Total, 2, ',', '.') ?></span></td>
                    </tr>
                </table>
            </td>
        </tr>
          
    <?php } ?>
    
    </table>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();


$CurrentUser = Session::GetCurrentUser();

$oMpdf->WriteHTML($Contenido);


$oMpdf->Output('caja.pdf', 'D'); 

?>