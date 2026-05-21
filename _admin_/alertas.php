<?php 

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_ALER_LIST))
	Session::NoPerm();

/* declaracion de variables */
$err 				= false;
$errComprobantes 	= array();
$errFormularios 	= array();
$oComprobantes 		= new Comprobantes();
$oFormularios 		= new Formularios();
$oTiposFormulario 	= new TiposFormulario();

/* recorremos y verificamos los comprobantes */
foreach (ComprobanteTipos::GetAll() as $oComprobanteTipo)
{
	$IdTipoComprobante = $oComprobanteTipo['IdTipo'];
	
	$filter = array();
	$filter['IdTipoComprobante'] 	= $IdTipoComprobante;
	$filter['IdEstado'] 			= ComprobanteEstados::Libre;
	
	$arrComprobantes = $oComprobantes->GetAll($filter);	
	if (count($arrComprobantes) <= $oDatosEmpresa->CantidadMinimaComprobantes)
	{
		$errComprobantes[$IdTipoComprobante] = true;
		$err = true;
	}
}

/* obtenemos el listado de tipos de formulario */
$arrTiposFormulario = $oTiposFormulario->GetAllForRepositorio();

/* recorremos y verificamos los formularios */
foreach ($arrTiposFormulario as $oTipoFormulario)
{
	$filter = array();
	$filter['IdTipoFormulario'] = $oTipoFormulario->IdTipoFormulario;
	$filter['IdEstado'] 		= FormularioEstados::Libre;
	
	$arrFormularios = $oFormularios->GetAll($filter);	
	if (count($arrFormularios) <= $oDatosEmpresa->CantidadMinimaFormularios)
	{
		$errFormularios[$oTipoFormulario->IdTipoFormulario] = true;
		$err = true;
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<?php include('include/head.inc.php'); ?>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="">
    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloRubro">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Alertas</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>		
      
    <?php if ($err === true) { ?>
        
        <tr>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                
			<?php foreach (ComprobanteTipos::GetAll() as $oComprobanteTipo) { ?>
                <?php $IdTipoComprobante = $oComprobanteTipo['IdTipo']; ?>
                <?php if ($errComprobantes[$IdTipoComprobante] === true) { ?>
                   
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #FF0000; padding: 5px; background:#FFCC99;">
                                <tr>
                                    <td>
                                        <strong style="padding-left: 10px"><?=$oComprobanteTipo['Descripcion']?> - Menos de <?=$oDatosEmpresa->CantidadMinimaComprobantes?> Comprobantes</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    
				<?php } ?>
			<?php } ?>
			<?php foreach ($arrTiposFormulario as $oTipoFormulario) { ?>
                <?php if ($errFormularios[$oTipoFormulario->IdTipoFormulario] === true) { ?>
                   
                    <tr>
                        <td>
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border: 2px solid #FF0000; padding: 5px; background:#FFCC99;">
                                <tr>
                                    <td>
                                        <strong style="padding-left: 10px"><?=$oTipoFormulario->Descripcion?> - Menos de <?=$oDatosEmpresa->CantidadMinimaFormularios?> Formularios</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    	<td>&nbsp;</td>
                    </tr>
                    
				<?php } ?>
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
                        <td><div align="center"><strong>No hay alertas para mostrar.</strong></div></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
          
    <?php } ?>
    
    </table>
</form>

</body>
</html>