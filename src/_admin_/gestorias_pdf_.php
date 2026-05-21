<?php 

require_once('../inc_library.php');

/* seccion exclusiva para usuarios autentificados */
Session::ForceLogin();

$IdFormulario 		= intval($_REQUEST['IdFormulario']);
$IdTipoFormulario 	= intval($_REQUEST['IdTipoFormulario']);
$Submit 			= (isset($_REQUEST['Submitted']));

$oFormularios = new Formularios();

/* obtenemos los datos del formulario o gestoria, segun corresponda */
if (!$oFormulario = $oFormularios->GetById($IdFormulario))
	exit();

if ($Submit)
{
	$OfssetX = $_REQUEST['posX'] / 37.795275591;
	$OfssetY = $_REQUEST['posY'] / 37.795275591;
	
	switch ($oFormulario->IdTipoFormulario)
	{
		case TipoFormulario::Formulario01Importado:
			$File = 'gestorias_pdf_1.php?IdFormulario=' . $IdFormulario;
			break;
	
		case TipoFormulario::Formulario01Nacional:
			$File = 'gestorias_pdf_2.php?IdFormulario=' . $IdFormulario;
			break;
			
		case TipoFormulario::TituloAutomotor:
			$File = 'gestorias_pdf_3_.php?IdFormulario=' . $IdFormulario;
			break;
		
		case TipoFormulario::Formulario12:
			$File = 'gestorias_pdf_4.php?IdFormulario=' . $IdFormulario;
			break;
		
		case TipoFormulario::Formulario13ACapital:
			$File = 'gestorias_pdf_6.php?IdFormulario=' . $IdFormulario;
			break;
		
		case TipoFormulario::Formulario13AProvincia:
			$File = 'gestorias_pdf_5.php?IdFormulario=' . $IdFormulario;
			break;
	
		case TipoFormulario::Formulario03:
			$File = 'gestorias_pdf_7.php?IdFormulario=' . $IdFormulario;
			break;
	
		case TipoFormulario::ContratoPrenda:
			$File = 'gestorias_pdf_8.php?IdFormulario=' . $IdFormulario;
			break;
	
		case TipoFormulario::ContratoPrendaStandardBank:
			$File = 'gestorias_pdf_9.php?IdFormulario=' . $IdFormulario;
			break;
	}

	$File.= '&OfssetX=' . $OfssetX;
	$File.= '&OfssetY=' . $OfssetY;
	
	header('Location: ' . $File);
	//exit;
}
else
{
	/* generamos un nombre de archivo temporal */
	/* para almacenar el PDF generado por este formulario */
	$pdfFile = tempnam("/tmp", "abb");		
	
	$oFormularios->ExportToPDF($oFormulario->IdFormulario, $pdfFile);
	
	//print_r($pdfFile);
	
	echo exec("convert $pdfFile ../_recursos/temp/test.jpeg");
	
	/* eliminamos el archivo temporal */
	unlink($pdfFile);
}

?>

<html>
<head>
<title>EasyDrag Demo</title>
<link href="../css/basico_backend.css" rel="stylesheet" type="text/css">
<script src="../js/jquery.js" type="text/javascript"></script>
<script src="../js/jquery.easydrag.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
    $("#box1").easydrag(false, "#posX", "#posY", "#content");
});
</script>
</head>
<body>

<form name="frmData" id="frmData" action="">
	<input type="hidden" name="IdFormulario" id="IdFormulario" value="<?=$IdFormulario?>" />
	<input type="hidden" name="IdTipoFormulario" id="IdTipoFormulario" value="<?=$IdTipoFormulario?>" />
	<input type="hidden" name="Submitted" id="Submitted" value="1" />

    <table>
        <tr>
            <td>
                Mover Ancho (P&iacute;xeles):
                <input type="text" name="posX" id="posX" class="camporFormularioChicoSuggest" readonly="readonly" value="0" />
                &nbsp;&nbsp;
                Mover Alto (P&iacute;xeles):
                <input type="text" name="posY" id="posY" class="camporFormularioChicoSuggest" readonly="readonly" value="0" />
            </td>
        </tr>
        <tr>
            <td>
                <table id="content" class="bordeNegro" width="450" height="600" border="0">
                    <tr>
                        <td align="center" valign="middle">
                            <div id="box1"><img src="../_recursos/temp/test-0.jpeg" border="0" width="400" class="bordeImagen" /></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table>
                    <tr>
                        <td align="center" valign="middle">
                            <div align="center"><input type="submit" class="botonBasico" value="GENERAR PDF" /></div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

</body>
</html>

<script language="javascript" type="text/javascript">

function GetFileName()
{
	var FileName = '<?=$FileName?>';
	var IdTipoFormulario = '<?=$oFormulario->IdTipoFormulario?>';
	
	if (IdTipoFormulario == '<?=TipoFormulario::Formulario12?>' || 
		IdTipoFormulario == '<?=TipoFormulario::Formulario01Nacional?>' ||
		IdTipoFormulario == '<?=TipoFormulario::Formulario01Importado?>')
	{
		if (confirm('Desea imprimir con leyenda?'))
		{
			FileName = FileName + '&ImprimeLeyenda=1';
		}
		else
		{
			FileName = FileName + '&ImprimeLeyenda=0';
		}
	}
			
	window.location.href = FileName;
}

//GetFileName();

</script>