<?php

require_once('../inc_library.php'); 
require_once('../library/suggest/include.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_GEST_CREATE))
	Session::NoPerm();

/* solo podemos acceder si tenemos el id de fatura */
if (GestoriaCreate::$IdMinuta == '')
	exit();

/* obtiene datos enviados */
$Action	= strval($_REQUEST['MainAction']);
$Submit	= (isset($_REQUEST['Submitted']));

/* declaracion de variables */
$err 		= 0;
$oMinutas 	= new Minutas();
$oClientes 	= new Clientes();
$oUnidades 	= new Unidades();
$oModelos 	= new Modelos();

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

/* si el formulario fue enviado */
if ($Submit)
{
	GestoriaCreate::$PorcentajeTitularidad 			= $_REQUEST['PorcentajeTitularidad'];
	GestoriaCreate::$IdClienteCondominio 			= $_REQUEST['IdClienteCondominio'];
	GestoriaCreate::$ClienteCondominio 				= $_REQUEST['ClienteCondominio'];
	GestoriaCreate::$CondominioConyuge 				= $_REQUEST['CondominioConyuge'];
	GestoriaCreate::$NumeroCertificado 				= $_REQUEST['NumeroCertificado'];
	GestoriaCreate::$IdTipoUso 						= $_REQUEST['IdTipoUso'];
	GestoriaCreate::$DomicilioFiscalCalle 			= $_REQUEST['DomicilioFiscalCalle'];
	GestoriaCreate::$DomicilioFiscalNumero 			= $_REQUEST['DomicilioFiscalNumero'];
	GestoriaCreate::$DomicilioFiscalPiso 			= $_REQUEST['DomicilioFiscalPiso'];
	GestoriaCreate::$DomicilioFiscalDpto 			= $_REQUEST['DomicilioFiscalDpto'];
	GestoriaCreate::$DomicilioFiscalIdLocalidad 	= $_REQUEST['DomicilioFiscalIdLocalidad'];
	GestoriaCreate::$DomicilioFiscalCodigoPostal 	= $_REQUEST['DomicilioFiscalCodigoPostal'];
	GestoriaCreate::$SociedadHecho					= $_REQUEST['SociedadHecho'];

	/* procesamos la acción principal */
	switch ($Action)
	{
		case 'Next':
		
			/* validaciones... */
			if ((GestoriaCreate::$PorcentajeTitularidad == '') || !(is_numeric(GestoriaCreate::$PorcentajeTitularidad)))
				$err |= 1;
			elseif ((GestoriaCreate::$PorcentajeTitularidad < 100) && (GestoriaCreate::$IdClienteCondominio == ''))
				$err |= 2;
			if (GestoriaCreate::$NumeroCertificado == '')
				$err |= 4;
			if (GestoriaCreate::$IdTipoUso == '')
				$err |= 8;
			if ((GestoriaCreate::$DomicilioFiscalCalle == '') ||
				(GestoriaCreate::$DomicilioFiscalNumero == '') ||
				(GestoriaCreate::$DomicilioFiscalIdLocalidad == '') ||
				(GestoriaCreate::$DomicilioFiscalCodigoPostal == ''))
				$err |= 16;
			
			/* si no hay erroes... */
			if ($err == 0)
			{
				/* borramos datos decondominio en caso de que sea titularidad 100% */
				if (GestoriaCreate::$PorcentajeTitularidad == 100)
				{
					GestoriaCreate::$IdClienteCondominio = '';
					GestoriaCreate::$ClienteCondominio = '';
				}

				/* si la venta es con prenda, se procede a la carga de datos de la prenda, */
				/* por el contrario, salteamos este paso */
				if (($oMinuta->FinanciacionCapital != '') && ($oMinuta->FinanciacionCapital != 0) && false)
				{
					header("Location: gestorias_add_paso3.php" . $strParams);
					exit;
				}
				else
				{
					header("Location: gestorias_add_paso5.php" . $strParams);
					exit;
				}
			}

			break;
			
		case 'Back':
		
			header("Location: gestorias_add_paso1.php");
			exit;

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
	GestoriaCreate::$NumeroCertificado = $oUnidad->NumeroCertificado;
	GestoriaCreate::$IdTipoUso = UsoTipos::Privado;
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
		Get('IdClienteCondominio').value 	= '';
		Get('ClienteCondominio').value 		= '';
	}

	var oCliente = GetCliente(IdCliente);
	if (!(oCliente))
		return;

	Get('IdClienteCondominio').value 	= oCliente.IdCliente;
	Get('ClienteCondominio').value 		= oCliente.RazonSocial;
}

function FilterDomicilioFiscalLocalidad(IdLocalidad, Nombre)
{
	if ((IdLocalidad == '') && (Nombre == ''))
	{
		Get('DomicilioFiscalIdLocalidad').value 	= '';
		Get('DomicilioFiscalCodigoPostal').value 	= '';
		Get('DomicilioFiscalLocalidad').value 		= '';
	}

	var oLocalidad = GetLocalidad(IdLocalidad);
	if (!(oLocalidad))
		return;

	Get('DomicilioFiscalIdLocalidad').value 	= oLocalidad.IdLocalidad;
	Get('DomicilioFiscalCodigoPostal').value 	= oLocalidad.CodigoPostal;
	Get('DomicilioFiscalLocalidad').value 		= oLocalidad.Nombre;
}

function VerificarCondominio()
{
	var PorcentajeTitularidad = Get('PorcentajeTitularidad').value;

	HideSection('trClienteCondominio');
	HideSection('trClienteCondominioError');
	HideSection('trClienteCondominioConyuge');
	HideSection('trClienteCondominioConyugeError');

	if ((PorcentajeTitularidad != '') && (PorcentajeTitularidad < 100))
	{
		ShowSection('trClienteCondominio');
		ShowSection('trClienteCondominioError');
		ShowSection('trClienteCondominioConyuge');
		ShowSection('trClienteCondominioConyugeError');
	}
}

function VerificarClienteCondominio()
{
	var IdClienteCondominio = Get('IdClienteCondominio').value;

	HideSection('trModificarClienteCondominio');
	
	if (IdClienteCondominio != '')
	{
		ShowSection('trModificarClienteCondominio');
	}
}

function ModCliente()
{
	var IdCliente = Get('IdClienteCondominio').value;

	if (IdCliente == '')
		return;
	
	var Url = 'clientes_mod_popup.php?IdCliente=' + IdCliente;
	
	window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
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

</script>

</head>
<body>

<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
	<tr>
    	<td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
      			<tr>
        			<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
        			<td height="40"><span class="tituloPagina">Administraci&oacute;n de Gestor&iacute;as - Agregar - Paso 2</span></td>
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
                    <input type="hidden" name="DomicilioFiscalIdLocalidad" id="DomicilioFiscalIdLocalidad" value="<?=GestoriaCreate::$DomicilioFiscalIdLocalidad?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
                                    <tr>
                                        <td><div align="right">Porcentaje Titularidad:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="PorcentajeTitularidad" id="PorcentajeTitularidad" class="camporFormularioChico" maxlength="5" onblur="javascript: VerificarCondominio();" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=GestoriaCreate::$PorcentajeTitularidad?>" />
                                                &nbsp;%&nbsp;&nbsp;
												SH&nbsp;<input type="checkbox" id="SociedadHecho" name="SociedadHecho" <?= GestoriaCreate::$SociedadHecho ? 'checked="true"' : ''?> />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el porcentaje de titularidad</li><?php } ?></td>
                                    </tr>
                                    <tr id="trClienteCondominio" style="display:none;">
                                        <td><div align="right">Condominio:</div></td>
                                        <td>
                                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="ClienteCondominio" id="ClienteCondominio" class="camporFormularioSuggest" maxlength="128" value="<?=GestoriaCreate::$ClienteCondominio?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarClienteCondominio();" />
                                                            <script language="javascript">
                                                            SUGGESTRequest('Clientes', 'GetAll', 'ClienteCondominio', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                                            </script>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="IdClienteCondominio" id="IdClienteCondominio" class="camporFormularioChicoSuggest" maxlength="5" value="<?=GestoriaCreate::$IdClienteCondominio?>" readonly="readonly" />
                                                            
                                                        </div>
                                                    </td>
                                                    <td>&nbsp;</td>
                                                    <td><input type="button" id="btnAddClienteCondominio" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " /></td>
                                                </tr>
                                                <tr id="trModificarClienteCondominio" style="display:none;">
                                                    <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModCliente('IdClienteCondominio');">Modificar datos del Condominio</a></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr id="trClienteCondominioError" style="display:none;">
                                        <td>&nbsp;</td>
                                        <td><?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el la informaci&oacute;n de condominio</li><?php } ?></td>
                                    </tr>
                                    <tr id="trClienteCondominioConyuge" style="display:none;">
                                        <td><div align="right"><label id="lblCertificado">Condominio con C&oacute;nyuge:</label></div></td>
                                        <td>
                                            <div align="left">
                                            	<select id="CondominioConyuge" name="CondominioConyuge" class="camporFormularioSimple">
                                                	<option value="0" <?=(!GestoriaCreate::$CondominioConyuge) ? 'selected="selected"' : ''?> >NO</option>
                                                	<option value="1" <?=(GestoriaCreate::$CondominioConyuge) ? 'selected="selected"' : ''?> >SI</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr id="trClienteCondominioConyugeError" style="display:none;">
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right"><label id="lblCertificado">N&uacute;mero Certificado <?=Origen::GetDescripcionById($oModelo->Origen)?>:</label></div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="NumeroCertificado" id="NumeroCertificado" class="camporFormularioSimple" maxlength="20" onkeyup="javascript: StrToUpper(this.id);" value="<?=GestoriaCreate::$NumeroCertificado?>" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el nro. de certificado</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Uso</div></td>
                                        <td>
                                            <div align="left">
                                                <select name="IdTipoUso" id="IdTipoUso" class="camporFormularioSimple">
                                                    <option value="">[SELECCIONE]</option>
                                                    <?php foreach (UsoTipos::GetAll() as $oUsoTipo) { ?>
                                                    <option value="<?=$oUsoTipo['IdTipo']?>" <?=(GestoriaCreate::$IdTipoUso == $oUsoTipo['IdTipo']) ? 'selected="selected"' : ''?> ><?=$oUsoTipo['Descripcion']?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><?php if ($err & 8) { ?><li style="color:#FF0000;">Seleccione el uso</li><?php } ?></td>
                                    </tr>
                                    <tr>
                                    	<td><div align="right">Domicilio Fiscal - Localidad:</div></td>
                                        <td>
                                            <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="DomicilioFiscalLocalidad" id="DomicilioFiscalLocalidad" class="camporFormularioSuggest" maxlength="128" value="<?=GestoriaCreate::$DomicilioFiscalLocalidad?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="Off" />
                                                            <script language="javascript">
                                                            SUGGESTRequest('Localidades', 'GetAllSuggest', 'DomicilioFiscalLocalidad', 'FilterDomicilioFiscalLocalidad', 'IdLocalidad', 'Nombre', 'FilterNombre', null);
                                                            </script>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div align="left">
                                                            <input type="text" name="DomicilioFiscalCodigoPostal" id="DomicilioFiscalCodigoPostal" class="camporFormularioChicoSuggest" maxlength="10" value="<?=GestoriaCreate::$DomicilioFiscalCodigoPostal?>" />
                                                            
                                                        </div>
                                                    </td>
                                                    <td>&nbsp;</td>
                                                    <td><input type="button" id="btnAddLocalidad" class="botonBasico"  onClick="javascript:AddLocalidad('DomicilioFiscal');" value=" + " /></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Domicilio Fiscal - Calle:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="DomicilioFiscalCalle" id="DomicilioFiscalCalle" class="camporFormularioSimple" maxlength="128" value="<?=GestoriaCreate::$DomicilioFiscalCalle?>" onkeyup="javascript: StrToUpper(this.id);" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Domicilio Fiscal - N&uacute;mero:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="DomicilioFiscalNumero" id="DomicilioFiscalNumero" class="camporFormularioChico" maxlength="12" value="<?=GestoriaCreate::$DomicilioFiscalNumero?>" onkeyup="javascript: StrToUpper(this.id);" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Domicilio Fiscal - Piso:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="DomicilioFiscalPiso" id="DomicilioFiscalPiso" class="camporFormularioChico" maxlength="4" value="<?=GestoriaCreate::$DomicilioFiscalPiso?>" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td><div align="right">Domicilio Fiscal - Dpto.:</div></td>
                                        <td>
                                            <div align="left">
                                                <input type="text" name="DomicilioFiscalDpto" id="DomicilioFiscalDpto" class="camporFormularioChico" maxlength="4" value="<?=GestoriaCreate::$DomicilioFiscalDpto?>" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese los datos del domicilio fiscal</li><?php } ?></td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
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
                                    &nbsp;
                        			<input type="button" value="Cancelar" class="botonBasico" onclick="javascript: Cancel();" />
                                    &nbsp;
                        			<input type="button" value="Siguiente" class="botonBasico" onclick="javascript: Next();" />
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
VerificarCondominio();
VerificarClienteCondominio();
</script>

</body>
</html>