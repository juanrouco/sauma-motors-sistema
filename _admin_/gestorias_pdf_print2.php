<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

/* obtenemos datos enviados */
$IdFormulario 		= intval($_REQUEST['IdFormulario']);
$IdTipoFormulario 	= intval($_REQUEST['IdTipoFormulario']);
$OffsetX 			= floatval($_REQUEST['OffsetX']);
$OffsetY 			= floatval($_REQUEST['OffsetY']);

/* declaramos variables necesarias */
$oFormularios 		= new Formularios();
$oGestorias			= new Gestorias();
$oGestoriaSocios	= new GestoriaSocios();
$oClientes			= new Clientes();
$oPrendas			= new Prendas();

/* obtenemos los datos del formulario o gestoria, segun corresponda */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();
	
$oGestoria = $oGestorias->GetById($oFormulario->IdGestoria);

/* formamos cadena con paramentros */
$strParams = '';
$strParams.= '?IdFormulario=' . $IdFormulario;
$strParams.= '&OffsetX=' . $OffsetX;
$strParams.= '&OffsetY=' . $OffsetY;

switch ($oFormulario->IdTipoFormulario)
{
	case TipoFormulario::Formulario01Importado:
		$File = 'gestorias_pdf_print4.php' . $strParams;
		break;

	case TipoFormulario::Formulario01Nacional:
		$File = 'gestorias_pdf_print4.php' . $strParams;
		break;
		
	case TipoFormulario::TituloAutomotor:
		$File = 'gestorias_pdf_3.php' . $strParams;
		break;
	
	case TipoFormulario::Formulario12:
		$File = 'gestorias_pdf_4.php' . $strParams;
		break;
	
	case TipoFormulario::Formulario13ACapital:
		$File = 'gestorias_pdf_6.php' . $strParams;
		break;
	
	case TipoFormulario::Formulario13AProvincia:
		$File = 'gestorias_pdf_5.php' . $strParams;
		break;

	case TipoFormulario::Formulario03:
		$File = 'gestorias_pdf_7.php' . $strParams;
		break;

	case TipoFormulario::ContratoPrenda:
		if (!$oPrenda = $oPrendas->GetByIdGestoria($oGestoria->IdGestoria))
			exit();
		if ($oPrenda->IdAcreedor == 4)
		{
			$File = 'gestorias_pdf_print5.php' . $strParams;
		}
		else
		{
			$File = 'gestorias_pdf_8.php' . $strParams;
		}
		break;

	case TipoFormulario::ContratoPrendaStandardBank:
		$File = 'gestorias_pdf_9.php' . $strParams;
		break;
		
	case TipoFormulario::Formulario02:
		$File = 'gestorias_pdf_print3.php' . $strParams;
		break;
}

$arrGestoriaSocios	= $oGestoriaSocios->GetAllByGestoria($oGestoria);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('include/head.inc.php'); ?>
<script language="javascript" type="text/javascript">

function GetFile()
{
	var File = '<?=$File?>';
	var IdTipoFormulario = '<?=$oFormulario->IdTipoFormulario?>';
	
	
			
	if (IdTipoFormulario != '<?=TipoFormulario::TituloAutomotor?>')	
	{
		if (IdTipoFormulario == '<?=TipoFormulario::Formulario12?>')
		{
			if (confirm('Desea imprimir con leyenda?'))
			{
				File = File + '&ImprimeLeyenda=1';
			}
			else
			{
				File = File + '&ImprimeLeyenda=0';
			}
		}
		window.location.href = File;
	}
	
}

function Generar(value)
{
	var File = '<?=$File?>';
	File = File + "&Observaciones=" + document.getElementById('observaciones').value;
	
	window.open(File,'_newtab');	
}

function GenerarSocio(IdSocio)
{
	var File = '<?=$File?>';
	File = File + "&IdSocio=" + IdSocio + "&Observaciones=" + document.getElementById('observaciones').value;
	
	window.open(File,'_newtab');	
}

function GenerarReverso()
{
	var IdTipoFormulario = '<?=$oFormulario->IdTipoFormulario?>';
	var File = '<?=$File?>';
	File = File + "&Observaciones=" + encodeURIComponent(document.getElementById('observaciones').value.replace(/\r\n/g, ' ').replace(/\n/g, ' '));
	if (IdTipoFormulario == '<?=TipoFormulario::Formulario12?>' || 
		IdTipoFormulario == '<?=TipoFormulario::Formulario01Nacional?>' ||
		IdTipoFormulario == '<?=TipoFormulario::Formulario01Importado?>')
	{
		if (confirm('Desea imprimir con leyenda?'))
		{
			File = File + '&ImprimeLeyenda=1';
		}
		else
		{
			File = File + '&ImprimeLeyenda=0';
		}
	}
	window.open(File.replace('.php', '_reverso.php'),'_newtab');
}

GetFile();

</script>
</head>
<body>
	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
		<tr>
			<td>
				<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
					<tr>
						<td width="20" height="40" class="TituloGrupo">&nbsp;</td>
						<td height="40"><span class="tituloPagina"><?= $oFormulario->IdTipoFormulario == TipoFormulario::TituloAutomotor ? 'Titulo' : 'Formulario 01' ?> - Observaciones</span></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign="top">&nbsp;</td>
		</tr>	
		<tr>
			<td valign="top" align="center">
				<textarea id="observaciones" name="observaciones" style="width:550px;height:200px"></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="center">
				<table align="center" border="0" width="550" cellpadding="0" cellspacing="0">
					<tr align="right">
						<td>
							<input type="button" class="botonBasico" id="btnAceptar" value="Generar Frente" onclick="javascript: Generar();" />	
							<input type="button" class="botonBasico" id="btnAceptar" value="Generar Reverso" onclick="javascript: GenerarReverso();" />						
						</td>
					</tr>
					<?php
					if ($oGestoria->SociedadHecho && $arrGestoriaSocios)
					{
						foreach ($arrGestoriaSocios as $oGestoriaSocio)
						{
							$oCliente = $oClientes->GetById($oGestoriaSocio->IdCliente);
					?>
					<tr align="right">
						<td>
							&nbsp;
						</td>
					</tr>
					<tr align="right">
						<td>
							<input type="button" class="botonBasico" id="btnAceptar_<?= $oGestoriaSocio->IdCliente ?>" value="Generar Frente - <?= $oCliente->RazonSocial ?>" onclick="javascript: GenerarSocio(<?= $oGestoriaSocio->IdGestoriaSocio ?>);" />								
						</td>
					</tr>
					<?php
						}
					}
					?>
				</table>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</body>