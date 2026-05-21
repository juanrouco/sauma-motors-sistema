<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_UPDATE))
	Session::NoPerm();

/* solo podemos acceder si tenemos el id de fatura */
if (GestoriaCreate::$IdMinuta == '')
	exit();

/* obtiene datos enviados */
$Action	= strval($_REQUEST['MainAction']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err 				= 0;
$errCedula 			= 0;
$error				= false;
$errFormulario 		= array();
$oFacturaUnidades 	= new FacturaUnidades();
$oMinutas 			= new Minutas();
$oClientes 			= new Clientes();
$oLocalidades 		= new Localidades();
$oUnidades 			= new Unidades();
$oModelos 			= new Modelos();
$oFormularios 		= new Formularios();
$oTiposFormulario 	= new TiposFormulario();
$oTiposDocumento 	= new TiposDocumento();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* obtenemos los datos de la venta */
if (!$oMinuta = $oMinutas->GetById(GestoriaCreate::$IdMinuta))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del cliente */
if (!$oCliente = $oClientes->GetById($oMinuta->IdCliente))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del condominio en caso de que existiera */
$oClienteCondominio = $oClientes->GetById(GestoriaCreate::$IdClienteCondominio);

/* obtenemos los datos de la localidad */
if (!$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la unidad */
if (!$oUnidad = $oUnidades->GetById($oMinuta->IdUnidad))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos del modelo */
if (!$oModelo = $oModelos->GetById($oUnidad->IdModelo))
{	
	header("Location: gestorias.php" . $strParams);
	exit();
}

/* obtenemos los datos de la factura */
$oFacturaUnidad = $oFacturaUnidades->GetById($oMinuta->IdMinuta);

/* obtenemos listado de tipos de formularios */
$arrTiposFormulario = $oTiposFormulario->GetAll();

/* definimos si requiere formulario de prenda */
$Prenda = false;
if (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != '0')) $Prenda = true;

/* obtenemos listado de tipos de formularios a generar en la gestoria */
$arrTiposFormularioGestoria = $oTiposFormulario->GetAllForGestoria($oLocalidad->Jurisdiccion, $oModelo->Origen, $Prenda);

/* si el formulario fue enviado */
if ($Submit)
{
	/* verificamos los formularios seleccionados */
	foreach ($arrTiposFormulario as $oTipoFormulario)
	{
		if ($_REQUEST['IdTipoFormulario_' . $oTipoFormulario->IdTipoFormulario])
		{
			if ($oTipoFormulario->Longitud == 0)
			{
				if (!($oFormulario = $oFormularios->GetByIdGestoriaIdTipoFormulario(GestoriaCreate::GetIdGestoria(), $oTipoFormulario->IdTipoFormulario)))
				{
					/* si es un titulo automotor, generamos el fomulario porque no existen autonumerados */
					$oFormulario = new Formulario();
					$oFormulario->IdTipoFormulario 	= $oTipoFormulario->IdTipoFormulario;
					$oFormulario->Numero 			= '--------';
					$oFormulario->IdEstado 			= FormularioEstados::Libre;

					/* geeneramos el formulario y lo asignamos */	
					if ($oFormulario = $oFormularios->Create($oFormulario))
						GestoriaCreate::AddFormulario($oFormulario);
				}
			}
			else
			{
				$Numero = $_REQUEST['Numero_' . $oTipoFormulario->IdTipoFormulario];
				$Fecha 	= $_REQUEST['Fecha_' . $oTipoFormulario->IdTipoFormulario];
				
				if ($Numero == '') 
				{
					$error = true;
					$errFormulario[$oTipoFormulario->IdTipoFormulario]['NumeroVacio'] = true;
				}
				elseif (!($oFormulario = $oFormularios->GetByNumero($oTipoFormulario->IdTipoFormulario, $Numero)))
				{
					$error = true;
					$errFormulario[$oTipoFormulario->IdTipoFormulario]['NumeroInexistente'] = true;
				}
				elseif (($oFormulario->IdGestoria != GestoriaCreate::GetIdGestoria()) && 
						($oFormulario->IdEstado != FormularioEstados::Libre))
				{
					print_R($oFormulario);
					exit;
					$error = true;
					$errFormulario[$oTipoFormulario->IdTipoFormulario]['FormularioUtilizado'] = true;
				}

				/* asignamos la fecha */
				if ($Fecha != '')
				{
					$oFormulario->Fecha = $Fecha;
				}
				else
				{
					$oFormulario->Fecha = $oFacturaUnidad->Fecha;
				}
				
				/* si el formulario existe lo agregamos */	
				if ($oFormulario)
				{
					GestoriaCreate::AddFormulario($oFormulario);
				}
			}
		}
	}

	/* procesamos la acción principal */
	switch ($Action)
	{
		case 'AddCedula':

			/* obtenemos los datos para la cedula */
			$Code					= strval($_REQUEST['Code']);
			$Nombre 				= strval($_REQUEST['Nombre']);
			$Apellido 				= strval($_REQUEST['Apellido']);
			$DocumentoTipo 			= intval($_REQUEST['DocumentoTipo']);
			$DocumentoTipoNombre 	= strval($_REQUEST['DocumentoTipoNombre']);
			$DocumentoTipoCodigo 	= strval($_REQUEST['DocumentoTipoCodigo']);
			$DocumentoNumero 		= strval($_REQUEST['DocumentoNumero']);
		
			/* verificamos los campos obligatorios */
			if ($Nombre == '')
				$errCedula |= 1;
			if ($Apellido == '')
				$errCedula |= 2;
			if ($DocumentoTipo == '')
				$errCedula |= 4;
			if ($DocumentoNumero == '')
				$errCedula |= 8;
	
			/* creamos el objeto de Cedula */		
			$oCedula = new Cedula($Code);
			$oCedula->Nombre 			= $Nombre;
			$oCedula->Apellido 			= $Apellido;
			$oCedula->DocumentoTipo 	= $DocumentoTipo;
			$oCedula->DocumentoNumero 	= $DocumentoNumero;
	
			/* si no ha habido ningun error durante las verificaciones, */
			/* lo guardamos y reseteamos los campos para que siga cargando */
			if ($errCedula == 0)
			{
				$oCedula->Id			= GestoriaCreate::AddCedula($oCedula);
				$Nombre					= "";
				$Apellido				= "";
				$DocumentoTipo			= "";
				$DocumentoNumero		= "";
				$DocumentoTipoNombre 	= "";
				$DocumentoTipoCodigo 	= "";
			}	
		
			break;

		case 'RemoveCedula':
			GestoriaCreate::RemoveCedula($_REQUEST['Id']);
			break;
			
		case 'AddSocio':

			/* obtenemos los datos para la cedula */
			$Code					= strval($_REQUEST['Code']);
			$IdCliente 				= strval($_REQUEST['IdCliente']);
			$Cliente 				= strval($_REQUEST['Cliente']);
			$Porcentaje				= strval($_REQUEST['Porcentaje']);
			
			/* verificamos los campos obligatorios */
			if ($IdCliente == '')
				$errSocio |= 1;
			if ($Porcentaje == '')
				$errSocio |= 2;
			
			/* creamos el objeto de Cedula */		
			$oSocio = new Socio($Code);
			$oSocio->IdCliente 			= $IdCliente;
			$oSocio->Porcentaje			= $Porcentaje;
			
			/* si no ha habido ningun error durante las verificaciones, */
			/* lo guardamos y reseteamos los campos para que siga cargando */
			if ($errSocio == 0)
			{
				$oSocio->Id				= GestoriaCreate::AddSocio($oSocio);
				$IdCliente				= "";
				$Cliente				= "";
				$Porcentaje				= "";				
			}	
		
			break;

		case 'RemoveSocio':
			GestoriaCreate::RemoveSocio($_REQUEST['Id']);
			break;
		
		case 'Next':
			
			/* verificamos si se seleccionaron formularios */
			if (GestoriaCreate::GetFormulariosCount() == 0)
				$err |= 1;
			
			/* si no hay erroes... */
			if (($err == 0) && ($error === false))
			{
				/* modificamos toda la operatoria de gestoria */
				if (GestoriaCreate::Update())
				{
					$IdGestoria = GestoriaCreate::GetIdGestoria();
					
					/* limpiamos los datos que quedaron */
					GestoriaCreate::ClearAll();		
					
					header("Location: gestorias_details.php" . $strParams . "&IdGestoria=" . $IdGestoria);
					exit;
				}
			}

			break;
			
		case 'Back':

			/* si la venta es con prenda, se procede a la carga de datos de la prenda, */
			/* por el contrario, salteamos este paso */
			if (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != 0))
			{
				if (($oCliente->IdEstadoCivil != EstadoCivil::Casado) && (!GestoriaCreate::$CondominioConyuge) && 
                	($oClienteCondominio->IdEstadoCivil != EstadoCivil::Casado)) 
				{
					header("Location: gestorias_mod_paso4.php" . $strParams);
					exit;
				}
				else
				{
					header("Location: gestorias_mod_paso3.php" . $strParams);
					exit;
				}
			}
			else
			{
				header("Location: gestorias_mod_paso2.php" . $strParams);
				exit;
			}

			break;				
			
		case 'Cancel':
		
			/* cancelamos la transaccion */
			GestoriaCreate::Cancel();		
			GestoriaCreate::ClearAll();		

			header("Location: gestorias.php" . $strParams);
			exit;

			break;
	}
}
else
{
	$Fecha = date('d-m-Y');

	if ($oFacturaUnidad) $Fecha = CambiarFecha($oFacturaUnidad->Fecha);
}

/* incluimkos funcion para armar suggest */
IncludeSUGGEST();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<?php include('include/head.inc.php'); ?>

<script language="javascript">

function FilterCliente(IdCliente, RazonSocial)
{
	if ((IdCliente == '') && (RazonSocial == ''))
	{
		Get('IdCliente').value 	= '';
		Get('Cliente').value 	= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdCliente').value 	= oCliente.IdCliente;
	Get('Cliente').value 	= oCliente.RazonSocial;
	
	/* si posee vendedor asignado, entonces levsntamos los datos */
	if (oCliente.IdVendedor != '')
	{
		FilterUsuario(oCliente.IdVendedor, '');
	}
}

function VerificarFormulario(IdTipoFormulario)
{
	var oTipoFormulario = GetTipoFormulario(IdTipoFormulario);
	if (!(oTipoFormulario))
		return;

	var Section 		= Get('trFormulario_' + IdTipoFormulario);
	var chkFormulario	= Get('IdTipoFormulario_' + IdTipoFormulario);
	
	if (Section == undefined)
		return false;

	HideSection('trFormulario_' + IdTipoFormulario);
	
	if (chkFormulario.checked) ShowSection('trFormulario_' + IdTipoFormulario);

	if (oTipoFormulario.CedulaAzul == '1')
	{
		HideSection('trCedulas');
	
		if (chkFormulario.checked) ShowSection('trCedulas');
	}
	
	/* habilitamos la fecha si se trata del formulario 12 */
	if (IdTipoFormulario == '<?=TipoFormulario::Formulario12?>')
	{
		HideSection('trFormularioFecha_' + IdTipoFormulario);

		if (chkFormulario.checked) ShowSection('trFormularioFecha_' + IdTipoFormulario);
	}
}

function AddCedula()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'AddCedula';
	frmData.action = 'gestorias_mod_paso5.php#Cedulas';
	frmData.submit();
	return true;
}

function RemoveCedula(Id)
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
	
	if (frmData == undefined)
		return false;
	
	MainAction.value = 'RemoveCedula';
	IdField.value = Id;
	frmData.submit();
	return true;
}

function AddSocio()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'AddSocio';
	frmData.action = 'gestorias_mod_paso5.php';
	frmData.submit();
	return true;
}

function RemoveSocio(Id)
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	var IdField 	= Get('Id');
	
	if (frmData == undefined)
		return false;
	
	MainAction.value = 'RemoveSocio';
	IdField.value = Id;
	frmData.submit();
	return true;
}

function Next()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'Next';
	frmData.submit();
	return true;
}

function Back()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'Back';
	frmData.submit();
	return true;
}

function Cancel()
{
	var frmData		= Get('frmData');
	var MainAction 	= Get('MainAction');
	
	if (frmData == undefined)
		return false;

	MainAction.value = 'Cancel';
	frmData.submit();
	return true;
}

function FilterTipoDocumento(IdTipoDocumento, Nombre)
{
	if ((IdTipoDocumento == '') && (Nombre == ''))
	{
		Get('DocumentoTipoCodigo').value 	= '';
		Get('DocumentoTipoNombre').value 	= '';
		Get('DocumentoTipo').value 			= '';
	}

	var oTipoDocumento = GetTipoDocumento(IdTipoDocumento);
	if (!(oTipoDocumento))
		return;

	Get('DocumentoTipoCodigo').value 	= oTipoDocumento.Codigo;
	Get('DocumentoTipoNombre').value 	= oTipoDocumento.Nombre;
	Get('DocumentoTipo').value 			= oTipoDocumento.IdTipoDocumento;
}

function SetNumeroFormulario(IdFormulario, NumeroFormulario)
{
	var oFormulario = GetFormulario(IdFormulario);
	if (!(oFormulario))
		return;
	
	Get('Numero_' + oFormulario.IdTipoFormulario).value = NumeroFormulario;
}

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" c|ellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Modificar - Paso <?=(($oMinuta->FinanciacionCapital == '') || ($oMinuta->FinanciacionCapital == 0)) ? '3' : '5'?></span></td>
      			</tr>
    		</table>
		</td>
  	</tr>
  	<tr>
    	<td valign="top">&nbsp;</td>
  	</tr>
  	<tr>
    	<td>
			<div align="center">
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
				    <input type="hidden" name="MainAction" id="MainAction" value="" />
				    <input type="hidden" name="Id" id="Id" value="" />
					<input type="hidden" name="Code" id="Code" value="<?php echo md5(rand(1, 100)); ?>" />
				    <input type="hidden" name="DocumentoTipo" id="DocumentoTipo" value="<?php echo $DocumentoTipo; ?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                    	<td>
                                        	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td height="40" align="center"><span class="tituloPagina">Formularios a Generar</span></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>                                    
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="45">&nbsp;</td>
                                                                    <td>
                                                                        <div align="left">
                                                                            <table width="454" border="0" cellpadding="0" cellspacing="0">
                                                                            <?php foreach ($arrTiposFormularioGestoria as $oTipoFormulario) { ?>
                                                                                <?php $oFormulario = GestoriaCreate::GetFormulario($oTipoFormulario->IdTipoFormulario); ?>
                                                                                <?php $oNextFormulario = $oFormularios->GetNext($oTipoFormulario->IdTipoFormulario); ?>
                                                                                <tr>
                                                                                    <td width="30">
                                                                                        <input type="checkbox" name="IdTipoFormulario_<?=$oTipoFormulario->IdTipoFormulario?>" id="IdTipoFormulario_<?=$oTipoFormulario->IdTipoFormulario?>" value="1" onclick="javascript: VerificarFormulario(<?=$oTipoFormulario->IdTipoFormulario?>);" <?php echo ($oFormulario) ? 'checked' : '' ?> />
                                                                                    </td>
                                                                                    <td width="148"><span><?=$oTipoFormulario->Descripcion?></span></td>
                                                                                    <td width="8">&nbsp;</td>
                                                                                    <td width="127">
                                                                                        <?php if ($oTipoFormulario->Longitud != 0) { ?>
                                                                                        <table border="0" cellpadding="0" cellspacing="0">
                                                                                            <tr id="trFormulario_<?=$oTipoFormulario->IdTipoFormulario?>" style="display:none;">
                                                                                                <td><span>N&uacute;mero:</span></td>
                                                                                                <td>
                                                                                                    <input type="text" name="Numero_<?=$oTipoFormulario->IdTipoFormulario?>" id="Numero_<?=$oTipoFormulario->IdTipoFormulario?>" value="<?=($oFormulario) ? $oFormulario->Numero : $oNextFormulario->Numero?>" class="camporFormularioChico" />
                                                                                                    <script language="javascript">
                                                                                                    var arrParams = new Array();
                                                                                                    arrParams['FilterIdTipoFormulario'] = '<?=$oTipoFormulario->IdTipoFormulario?>';
                                                                                                    arrParams['FilterIdEstado'] = '<?=FormularioEstados::Libre?>';
                                                                                                    SUGGESTRequest('Formularios', 'GetAll', 'Numero_<?=$oTipoFormulario->IdTipoFormulario?>', 'SetNumeroFormulario', 'IdFormulario', 'Numero', 'FilterNumero', arrParams);
                                                                                                    </script>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                    <td width="141">
                                                                                        <?php if ($oTipoFormulario->IdTipoFormulario == TipoFormulario::Formulario12) { ?>
                                                                                        <table border="0" cellpadding="0" cellspacing="0">
                                                                                            <tr id="trFormularioFecha_<?=$oTipoFormulario->IdTipoFormulario?>" style="display:none;">
                                                                                                <td><span>Fecha:</span></td>
                                                                                                <td>
																									<input name="Fecha_<?=$oTipoFormulario->IdTipoFormulario?>" type="text" class="camporFormularioChico" id="Fecha_<?=$oTipoFormulario->IdTipoFormulario?>" value="<?=($oFormulario) ? $oFormulario->Fecha : $Fecha?>" size="12" maxlength="12" />
																									<script language="javascript">
                                                                                                    new tcal({'formname': 'frmData', 'controlname': 'Fecha_<?=$oTipoFormulario->IdTipoFormulario?>'});
                                                                                                    </script>
                                                                                                    
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php if (($errFormulario[$oTipoFormulario->IdTipoFormulario]['NumeroVacio']) || ($errFormulario[$oTipoFormulario->IdTipoFormulario]['NumeroInexistente']) || ($errFormulario[$oTipoFormulario->IdTipoFormulario]['FormularioUtilizado'])) { ?>
                                                                                <tr>
                                                                                    <td colspan="5"><?php if ($errFormulario[$oTipoFormulario->IdTipoFormulario]['NumeroVacio']) { ?><li style="color:#FF0000;">Ingrese el nro. de formulario</li><?php } ?><?php if ($errFormulario[$oTipoFormulario->IdTipoFormulario]['NumeroInexistente']) { ?><li style="color:#FF0000;">El nro. de formulario ingresado no existe registrado</li><?php } ?><?php if ($errFormulario[$oTipoFormulario->IdTipoFormulario]['FormularioUtilizado']) { ?><li style="color:#FF0000;">El nro. de formulario ingresado ya fue utilizado</li><?php } ?></td>
                                                                                </tr>
                                                                                <?php } ?>    
                                                                                <tr>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td>&nbsp;</td>
                                                                                </tr>    
                                                                            <?php } ?>
                                                                            </table>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php if ($err & 1) { ?>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td><li style="color:#FF0000;">Seleccione el/los formularios que desea generar</li></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                </tr>    
                                                                <?php } ?>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                           	</table>
                                     	</td>
                                 	</tr>
                                    <tr id="trCedulas" style="display:none;">
                                    	<td>
                                        	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td height="40" align="center"><span class="tituloPagina">C&eacute;dulas Azules</span></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>                                    
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                                <tr>
                                                                    <td width="50">&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                    <td width="50">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Nombre:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="Nombre" id="Nombre" class="camporFormularioSimple" maxlength="128" value="<?=$Nombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($errCedula & 1) { ?><li style="color:#FF0000;">Ingrese el nombre</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Apellido:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="Apellido" id="Apellido" class="camporFormularioSimple" maxlength="128" value="<?=$Apellido?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($errCedula & 2) { ?><li style="color:#FF0000;">Ingrese el apellido</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Tipo Documento:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DocumentoTipoNombre" id="DocumentoTipoNombre" class="camporFormularioSuggest" maxlength="128" value="<?=$DocumentoTipoNombre?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('TiposDocumento', 'GetAll', 'DocumentoTipoNombre', 'FilterTipoDocumento', 'IdTipoDocumento', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DocumentoTipoCodigo" id="DocumentoTipoCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$DocumentoTipoCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddTipoDocumento" class="botonBasico"  onClick="javascript:AddTipoDocumento('Documento');" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($errCedula & 4) { ?><li style="color:#FF0000;">Ingrese el tipo de documento</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Nro. Documento:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="DocumentoNumero" id="DocumentoNumero" class="camporFormularioSimple" maxlength="128" value="<?=$DocumentoNumero?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($errCedula & 8) { ?><li style="color:#FF0000;">Ingrese el nro. de documento</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="button" name="btnAddCedula" class="botonBasico" value="Agregar C&eacute;dula" onclick="javascript: AddCedula();" />
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                
                                                                    <?php if (GestoriaCreate::GetCedulasCount() > 0) { ?>
                                            
                                                                        <table width="100%">
                                                                            <tr>
															                    <a name="Cedulas" id="Cedulas"></a>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                        <table width="100%" align="left" class="bordeGris">
                                                                            <tr class="bordeGrisFondo">
                                                                                <td width="20%" height="20" class="bordeGrisTitulo"><div id="margen">Nombre</div></td>
                                                                                <td width="20%" height="20" class="bordeGrisTitulo"><div id="margen">Apellido</div></td>
                                                                                <td width="22%" height="20" class="bordeGrisTitulo"><div id="margen">Documento</div></td>
                                                                                <td width="14%" class="bordeGrisTitulo">&nbsp;</td>
                                                                            </tr>
                                                                                                        
                                                                        <?php foreach (GestoriaCreate::GetAllCedulas() as $oCedula) { ?>
                                                                            <?php $oTipoDocumento = $oTiposDocumento->GetById($oCedula->DocumentoTipo); ?>
                                            
                                                                            <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                                                <td height="20"><div id="margen"><?=$oCedula->Nombre?></div></td>
                                                                                <td height="20"><div id="margen"><?=$oCedula->Apellido?></div></td>
                                                                                <td height="20"><div id="margen"><?=$oTipoDocumento->Codigo . ' - ' . $oCedula->DocumentoNumero?></div></td>
                                                                                <td height="20">
                                                                                    <div align="center">
                                                                                        <a href="#bottom" onclick="javascript: RemoveCedula('<?=$oCedula->Id?>');"><strong>Quitar</strong>&nbsp;<img border="0" align="absbottom" src="images/iconos/delete-icon.gif" /></a>                                      	
                                                                                    </div>                                                
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="5"><div align="center">
                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                                        <tr>
                                                                                            <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div></td>
                                                                            </tr>
                                                                                
                                                                            <?php } ?>
                                            
                                                                            </table>
                                            
                                                                        <?php } ?>
                                            
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                	<td>&nbsp;</td>
                                                                	<td>&nbsp;</td>
                                                                	<td>&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                           	</table>
                                     	</td>
                                 	</tr>
									<?php
									if (GestoriaCreate::$SociedadHecho)
									{
									?>
                                    <tr id="trSocios">
                                    	<td>
                                        	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                                <tr>
                                                                    <td height="40" align="center"><span class="tituloPagina">Socios</span></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>                                    
                                                <tr>
                                                    <td>
                                                        <div align="center">
                                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                                <tr>
                                                                    <td width="50">&nbsp;</td>
                                                                    <td>&nbsp;</td>
                                                                    <td width="50">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                    <td>
                                                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Cliente:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="Cliente" id="Cliente" class="camporFormularioSimple" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
																									<input type="hidden" id="IdCliente" name="IdCliente" value="<?= $IdCliente ?>" />
																									<script language="javascript">
																									SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
																									</script>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($errSocio & 1) { ?><li style="color:#FF0000;">Ingrese un cliente</li><?php } ?></td>
                                                                            </tr>                                                                            
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Porcentaje:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="Porcentaje" id="Porcentaje" class="camporFormularioSimple" maxlength="128" value="<?=$Porcentaje?>" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($errSocio & 2) { ?><li style="color:#FF0000;">Ingrese un porcentaje de titularidad</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="button" name="btnAddCedula" class="botonBasico" value="Agregar Socio" onclick="javascript: AddSocio();" />
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                
                                                                    <?php if (GestoriaCreate::GetSociosCount() > 0) { ?>
                                            
                                                                        <table width="100%">
                                                                            <tr>
															                    <a name="Socios" id="Socios"></a>
                                                                                <td>&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                        <table width="100%" align="left" class="bordeGris">
                                                                            <tr class="bordeGrisFondo">
                                                                                <td width="20%" height="20" class="bordeGrisTitulo"><div id="margen">Nombre y Apellido</div></td>                                                                                
                                                                                <td width="22%" height="20" class="bordeGrisTitulo"><div id="margen">Porcentaje</div></td>
                                                                                <td width="14%" class="bordeGrisTitulo">&nbsp;</td>
                                                                            </tr>
                                                                                                        
                                                                        <?php foreach (GestoriaCreate::GetAllSocios() as $oSocio) { 
																			$oCliente = $oClientes->GetById($oSocio->IdCliente);
																		?>                                            
                                                                            <tr onMouseOver="bgColor='#f3f3f3'" onMouseOut="bgColor=''">
                                                                                <td height="20"><div id="margen"><?=$oCliente->RazonSocial?></div></td>
                                                                                <td height="20"><div id="margen"><?=$oSocio->Porcentaje?>%</div></td>
                                                                                <td height="20">
                                                                                    <div align="center">
                                                                                        <a href="#bottom" onclick="javascript: RemoveSocio('<?=$oSocio->Id?>');"><img border="0" align="absbottom" src="images/iconos/del.gif" /></a>                                      	
                                                                                    </div>                                                
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="5"><div align="center">
                                                                                    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                                                                        <tr>
                                                                                            <td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </div></td>
                                                                            </tr>
                                                                                
                                                                            <?php } ?>
                                            
                                                                            </table>
                                            
                                                                        <?php } ?>
                                            
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                	<td>&nbsp;</td>
                                                                	<td>&nbsp;</td>
                                                                	<td>&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;</td>
                                                </tr>
                                           	</table>
                                     	</td>
                                 	</tr>
									<?php
									}
									?>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr height="30">
                        	<td>
                            	<div align="center">
                                	<input type="button" value="Anterior" class="botonBasico" onclick="javascript: Back();" />
                        			<input type="button" value="Cancelar" class="botonBasico" onclick="javascript: Cancel();" />
                        			<input type="button" value="Finalizar" class="botonBasico" onclick="javascript: Next();" />
                               	</div>
                          	</td>
						</tr>
					</table>
				</form>
    		</div>
		</td>
  	</tr>
  	<tr>
    	<td>&nbsp;</td>
  	</tr>
</table>

<script language="javascript">
<?php foreach ($arrTiposFormulario as $oTipoFormulario) { ?>
VerificarFormulario('<?=$oTipoFormulario->IdTipoFormulario?>');
<?php } ?>
</script>

</body>
</html>