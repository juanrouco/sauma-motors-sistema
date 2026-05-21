<?php 

require_once('../inc_library.php'); 
require_once('../library/mpdf/mpdf.php');
ob_clean();

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdDeclaracionJurada = intval($_REQUEST['IdDeclaracionJurada']);

/* declaracion de variables */
$arrData 				= array();
$oFormularios 			= new Formularios();
$oGestorias				= new Gestorias();
$oDeclaracionesJuradas 	= new DeclaracionesJuradas();
$oFacturaUnidades 		= new FacturaUnidades();
$oMinutas 				= new Minutas();
$oClientes 				= new Clientes();

/* verifica si existe el registro */
if (!$oDeclaracionJurada = $oDeclaracionesJuradas->GetById($IdDeclaracionJurada))
	exit();

$arrData = $oFormularios->GetAllByDeclaracionJurada($oDeclaracionJurada);

/* creamos el objeto para manipular el .pdf */
$oMpdf = new mPDF();
$oMpdf->watermarkText = '';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<style>

body 	{background-color: #FFFFFF;}
td 		{font-size: 14px; color: #000000; font-family: Arial, Helvetica, sans-serif;}

</style>

</head>
<body>

<form name="frmData" id="frmData" method="post" onSubmit="Filtrar();">

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <tr align="center">
                        <td height="40" align="center">DECLARACION JURADA DE CONSUMO DE</td>
                    </tr>
                    <tr align="center">
                        <td height="40" align="center"> SOLICITUDES TIPO '01' <?=($oDeclaracionJurada->IdTipo == 1) ? 'NACIONAL' : 'IMPORTADO'?></td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40">Nombre del Adquiriente: VICTOR H. TOLOSA S.A.</td>
                    </tr>
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40">(Empresa Terminal, Consecionaria, Representante o Distribuidor, etc.)</td>
                    </tr>
                </table>		
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40">Fecha: <?=CambiarFecha($oDeclaracionJurada->Fecha)?></td>
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
                <table width="100%" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="18%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nro. Solicitud Tipo</strong></div></td>
                        <td width="5">&nbsp;</td>
                        <td width="19%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>N&uacute;mero Certificado</strong></div></td>
                        <td width="5">&nbsp;</td>
                        <td width="39%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Nombre del Adquiriente del Automotor</strong></div></td>
                        <td width="5">&nbsp;</td>
                        <td width="24%" height="25" class="bordeGrisTitulo"><div id="margen"><strong>Fecha de Entrega de Solicitud</strong></div></td>
                    </tr>
              	</table>
          	</td>
      	</tr>
          
                <?php foreach ($arrData as $oFormulario) { ?>
                    <?php $oGestoria = $oGestorias->GetById($oFormulario->IdGestoria); ?>
                    <?php $oMinuta = $oMinutas->GetById($oGestoria->IdMinuta); ?>
                    <?php $oFacturaUnidad = $oFacturaUnidades->GetByIdMinuta($oGestoria->IdMinuta); ?>
                    <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>

        <tr>
            <td>
                <table width="100%" align="center" cellpadding="0" cellspacing="0">
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="166" height="25"><div id="margen"><?=$oFormulario->Numero?></div></td>
                        <td width="10">&nbsp;</td>
                        <td width="168" height="25"><div id="margen"><?=($oGestoria) ? $oGestoria->NumeroCertificado : 'STOCK'?></div></td>
                        <td width="9">&nbsp;</td>
                        <td width="353" height="25"><div id="margen"><?=utf8_encode($oCliente->RazonSocial)?></div></td>
                        <td width="8">&nbsp;</td>
                        <td width="215" height="25"><div id="margen"><?=CambiarFecha($oFacturaUnidad->Fecha)?></div></td>
                    </tr>
              	</table>
          	</td>
      	</tr>
    
                <?php } ?>      
                
        <tr>
            <td>&nbsp;</td>
        </tr>

    <?php } ?>
    
    </table>
</form>

</body>
</html>

<?php

$Contenido = ob_get_contents();
ob_end_clean();

$oMpdf->WriteHTML($Contenido);
$oMpdf->Output('declaracion_jurada.pdf', 'D'); 

?>