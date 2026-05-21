<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_PEDMAY_LIST))
	Session::NoPerm();

/* obtiene datos enviados */
$IdPedidoMayorista		= intval($_REQUEST['IdPedidoMayorista']);
$Action					= strval($_REQUEST['MainAction']);
$Submit					= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err						= array();
$oUnidades					= new Unidades();
$oMinutas					= new Minutas();
$oPedidosMayorista			= new PedidosMayorista();
$oPedidosMayoristaDetalles	= new PedidosMayoristaDetalles();
$oModelos					= new Modelos();
$oClientes					= new Clientes();
$oUsuarios					= new Usuarios();
$TotalAPagar = 0;

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

if (!$oPedidoMayorista = $oPedidosMayorista->GetById($IdPedidoMayorista))
{
	header('Location: pedidosmayorista.php' . $strParams);
	exit;
}

$oCliente = $oClientes->GetById($oPedidoMayorista->IdCliente);

/* obtenemos todos las unidades de la recepcion */
$arrData = $oPedidosMayoristaDetalles->GetAllByPedidoMayorista($oPedidoMayorista);

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

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

</script>

</head>
<body>

<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
    <input type="hidden" name="Id" id="Id" value="" />
    <input type="hidden" name="MainAction" id="MainAction" value="" />
    <input type="hidden" name="Submitted" id="Submitted" value="1" />
    <input type="hidden" name="IdPedidoMayorista" id="IdPedidoMayorista" value="<?=$IdPedidoMayorista?>" />

    <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
        <tr>
            <td>
                <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td width="20" height="40" class="TituloGrupo">&nbsp;</td>
                        <td height="40"><span class="tituloPagina">Administraci&oacute;n de Pedidos Mayoristas - Modificar</span></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
		<tr>
            <td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGris">
                    <tr>
                        <td align="right" width="50%" height="40"><strong>N&deg; Pedido:</strong>&nbsp;</td>
                        <td height="40"><?= $oPedidoMayorista->IdPedidoMayorista ?></td>
                    </tr>
					<tr>
                        <td  align="right" width="50%" height="40"><strong>Fecha Pedido:</strong>&nbsp;</td>
                        <td height="40"><?= CambiarFecha($oPedidoMayorista->FechaPedidoMayorista) ?></td>
                    </tr>
					<tr>
                        <td  align="right" width="50%" height="40"><strong>Cliente:</strong>&nbsp;</td>
                        <td height="40"><?= $oCliente->RazonSocial ?></td>
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
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Cliente</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Vendedor</strong></div></td>
                        <td height="25" class="bordeGrisTitulo"><div id="margen"  align="center"><strong>Precio Venta</strong></div></td>
                    </tr>
          
                <?php foreach ($arrData as $oPedidoMayorista) { ?>
                    <?php $oMinuta = $oMinutas->GetById($oPedidoMayorista->IdMinuta); ?>
                    <?php $oUnidad = $oUnidades->GetById($oMinuta->IdUnidad); ?>
                    <?php $oModelo = $oModelos->GetById($oUnidad->IdModelo); ?>
                    <?php $oCliente = $oClientes->GetById($oMinuta->IdCliente); ?>
                    <?php $oUsuario = $oUsuarios->GetById($oMinuta->IdUsuario); ?>
                    
                    <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oUnidad->IdUnidad?></div></td>
                        <td width="125" height="25"><div id="margen"  align="center"><?=$oModelo->DenominacionComercial?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oCliente->RazonSocial?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center"><?=$oUsuario->Nombre . ' ' . $oUsuario->Apellido?></div></td>
                        <td width="120" height="25"><div id="margen"  align="center">$ <?=number_format($oMinuta->PrecioVenta, 2)?></div></td>
                    </tr>
                    <tr>
                        <td colspan="6">
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
                <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                    <tr>
                        <td height="30">
                            <div align="center">
                            	<input type="button" name="btnSiguiente" class="botonBasico" id="btnSiguiente" value="Volver" onClick="javascript: window.location.href='pedidosmayorista.php<?= $strParams ?>';" />
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