<?php

require_once('class.dbaccess.php');

function ObtenerMes($mes)
{
	switch ($mes)
	{
		case 1: return 'ENERO';
		case 2: return 'FEBRERO';
		case 3: return 'MARZO';
		case 4: return 'ABRIL';
		case 5: return 'MAYO';
		case 6: return 'JUNIO';
		case 7: return 'JULIO';
		case 8: return 'AGOSTO';
		case 9: return 'SEPTIEMBRE';
		case 10: return 'OCTUBRE';
		case 11: return 'NOVIEMBRE';
		case 12: return 'DICIEMBRE';
	}
}

function CantidadDiasPasados($fecha)
{
	$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
	$date2 = new DateTime();
	
	$iv = $date2->diff($dateTime);
	return $iv->d + $iv->m * 30 + $iv->y * 30 * 12;
}

function ParseDate($date)
{
	if (($ret = _parseDate($date, '%d-%m-%Y')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d/%m/%Y %H:%M:%s')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d/%m/%Y %H:%M')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d/%m/%Y %H')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d/%m/%Y')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d-%m-%Y %H:%M:%s')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d-%m-%Y %H:%M')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%d-%m-%Y %H')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%Y-%m-%d %H:%M:%s')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%Y-%m-%d %H:%M:%s')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%Y-%m-%d %H:%M')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%Y-%m-%d %H')) != false)
			return $ret;

	if (($ret = _parseDate($date, '%Y-%m-%d')) != false)
			return $ret;

	return false;
}


function _parseDate($date, $format) 
{
	// Builds up date pattern from the given $format, keeping delimiters in place.
  	if( !preg_match_all( "/%([YmdHMsu])([^%])*/", $format, $formatTokens, PREG_SET_ORDER ) ) 
   		return false;

  	foreach( $formatTokens as $formatToken ) 
	{
   		$delimiter = preg_quote( $formatToken[2], "/" );
   		if($formatToken[1] == 'Y') 
		{
	 		$datePattern .= '(.{1,4})'.$delimiter;
   		} 
		elseif($formatToken[1] == 'u') 
		{
	 		$datePattern .= '(.{1,5})'.$delimiter;
   		} 
		else 
		{
	 		$datePattern .= '(.{1,2})'.$delimiter;
   		}
  	}
	
  	// Splits up the given $date
  	if( !preg_match( "/^".$datePattern."/", $date, $dateTokens) ) 
	{
   		return false;
  	}
  
  	$dateSegments = array();
  	for($i = 0; $i < count($formatTokens); $i++) 
	{
   		$dateSegments[$formatTokens[$i][1]] = $dateTokens[$i+1];
  	}

  	// Reformats the given $date into rfc3339
  	if( $dateSegments["Y"] && $dateSegments["m"] && $dateSegments["d"] ) 
	{
   		if( ! checkdate ( $dateSegments["m"], $dateSegments["d"], $dateSegments["Y"] )) 
		{ 
			return false; 
		}
		
   		$dateReformated =
	 	str_pad($dateSegments["Y"], 4, '0', STR_PAD_LEFT)
	 	."-".str_pad($dateSegments["m"], 2, '0', STR_PAD_LEFT)
	 	."-".str_pad($dateSegments["d"], 2, '0', STR_PAD_LEFT);
  	} 
	else 
	{
   		return false;
  	}
  
  	if( $dateSegments["H"] && $dateSegments["M"] ) 
	{
   		$dateReformated .=
	 	" ".str_pad($dateSegments["H"], 2, '0', STR_PAD_LEFT)
	 	.':'.str_pad($dateSegments["M"], 2, '0', STR_PAD_LEFT);

   		if( $dateSegments["s"] ) 
		{
	 		$dateReformated .=
	   		":".str_pad($dateSegments["s"], 2, '0', STR_PAD_LEFT);
	 		if( $dateSegments["u"] ) 
			{
	   			$dateReformated .=
	   			'.'.str_pad($dateSegments["u"], 5, '0', STR_PAD_RIGHT);
	 		}
   		}
  	}

  	return $dateReformated;
}


function IsEmail($Email)
{
	$Email = trim($Email);
	
	if (strlen($Email) <= 6)
		return false;
		
	if (substr_count($Email, "@") != 1)
		return false;
		
	if (substr($Email, 0, 1) == "@")
		return false;

	if (substr($Email, strlen($Email) - 1, 1) == "@")
		return false;
	
	if (strstr($Email, "'"))
		return false;
		
	if (strstr($Email, "\""))
		return false;
		
	if (strstr($Email, "\\"))
		return false;
	
	if (strstr($Email, "\$"))
		return false;

	if (strstr($Email, " "))
		return false;

	if (strstr($Email, "?"))
		return false;
	
	if (strstr($Email, "¿"))
		return false;
	
	if (strstr($Email, "¿"))
		return false;
	
	if (strstr($Email, "&"))
		return false;
		
	if (strstr($Email, "!"))
		return false;

	if (strstr($Email, "¡"))
		return false;
		
	if (substr_count($Email, ".") < 1)
		return false;

	/* verifica el dominio */
	$Dominio = substr(strrchr($Email, "."), 1);
	if (strlen($Dominio) < 1 || strlen($Dominio) > 5 || strstr($Dominio, "@"))
		return false;	
			
	/* verifica el cuerpo */
   	$Cuerpo = substr($Email, 0, strlen($Email) - strlen($Dominio) - 1);
   	$UltimoCaracter = substr($Cuerpo, strlen($Cuerpo)-1, 1);
	if ($UltimoCaracter == "@" || $UltimoCaracter == ".")
		return false;
	
 	return true;;
} 


function FormatMoney($Money, $Number, $Cotizacion)
{
	$Value = '';

	switch ($Money)
	{
		case MonedaTipos::Pesos:
			$Value.= '$ ';
			if ($Number != 0) 	$Value.= number_format($Number, 2);
			else				$Value.= '0.00';
			break;
			
		case MonedaTipos::Dolar:
			$Value.= 'u$s ';
			if ($Number != 0) 	$Value.= number_format($Number / $Cotizacion, 2);
			else				$Value.= '0.00';
			break;
			
		case MonedaTipos::Euro:
			$Value.= '&euro; ';
			if ($Number != 0) 	$Value.= number_format($Number / $Cotizacion, 2);		
			else				$Value.= '0.00';
			break;
	}
	
	return $Value;
}


function ConstructGallery($arr)
{
	?>
	<link rel="stylesheet" href="../css/lightbox.css" type="text/css" media="screen" />
	<script src="js/prototype.js" type="text/javascript"></script>
	<script src="js/scriptaculous.js?load=effects" type="text/javascript"></script>
	<script src="js/lightbox.js" type="text/javascript"></script>
	<script languaje="javascript">

		function ShowImage(id)
		{
			var img_principal 		= Get('ImagenPrincipal');
			var EpigrafePrincipal 	= Get('EpigrafePrincipal');
			
			img_principal.style.display 	= 'none';
			EpigrafePrincipal.style.display = 'none';
			
			for (var i=0; i<100; i++)
			{
				var img_ocultar 	= Get(i + '_G');
				var EpigrafeOcultar = Get(i + '_Epigrafe');
				
				if (img_ocultar != undefined)
					img_ocultar.style.display = 'none';
					
				if (EpigrafeOcultar != undefined)
					EpigrafeOcultar.style.display = 'none';
			}

			if (id == 'ImagenPrincipal')
			{
				var img = Get('ImagenPrincipal');			
				
				img.style.display = '';
				EpigrafePrincipal.style.display = '';
			}
			else
			{
				var img 		= Get(id + '_G');			
				var Epigrafe 	= Get(id + '_Epigrafe');			
				
				img.style.display = '';
				Epigrafe.style.display = '';
			}
		}

	</script>
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="200" valign="top">
				<div align="left">

	<?php
	$Count = 1;					
	$Create = false;

	if ($arr)
	{	
		foreach ($arr as $oImagen)
		{
			if (file_exists($oImagen->Imagen))
				$Create = true;
		}
		
		reset($arr);
	}
	
	$Create = true;

	if ($Create)	
	{
		foreach ($arr as $oImagen)
		{			
			if ($Count == 1)
			{
				$ImagenPrincipal = $oImagen->Imagen;
				$EpigrafePrincipal = $oImagen->Epigrafe;
			}
	?>
    
					<a href="<?=$oImagen->Imagen?>" title="<?=$oImagen->Epigrafe?>" rel="lightbox[gallery]"><img src="<?=(!empty($oImagen->Imagen)) ? $oImagen->Imagen : 'images/no_foto.jpg'?>" id="<?=$Count?>_G" name="<?=$Count?>_G" width="165" height="165" border="0" style="display:none;" /></a>
					<span id="<?=$Count?>_Epigrafe" style="display:none;"><br /><?=$oImagen->Epigrafe?></span>
	
    <?php
			$Count++;
		}
	?>
	
					<a href="<?=$ImagenPrincipal?>" title="<?=$EpigrafePrincipal?>" rel="lightbox[gallery]"><img src="<?=(!empty($oImagen->Imagen)) ? $oImagen->Imagen : 'images/no_foto.jpg'?>" id="ImagenPrincipal" name="ImagenPrincipal" width="165" height="165" border="0" /></a>
					<span id="EpigrafePrincipal"><br /><?=$EpigrafePrincipal?></span>
					<br /><br />
					
  	<?php	  
		$i = 1;
		$Count = 1;
		reset($arr);
	?>
    
				</div>
                <div>
					<table>
                    
	<?php
		foreach ($arr as $oImagen)
		{
			if ($i == 1)
			{
	?>
    
						<tr>
                        
	<?php
			}
	?>
							
							<td valign="top">
								<img src="<?=(!empty($oImagen->Imagen)) ? $oImagen->Imagen : 'images/no_foto.jpg'?>" id="<?=$Count?>_C" name="<?=$Count?>_C" width="50" height="50" border="0" align="top" onclick="ShowImage('<?=$Count?>');" />
							</td>
	
    <?php														
			if ($i == 3)
			{
	?>
    
						</tr>
                        
  	<?php
				$i = 0;
			}
	
			$i++;
			$Count++;
		}
	}
	else
	{
	?>
    
                    </table>
                </div>
                <div>
                    <table>
                        <tr>
                            <td>
                                <img src="<?=Config::ImagenDefault?>" name="ImagenPrincipal" width="165" border="0" id="ImagenPrincipal" />
                                <br /><br />
                            </td>
                        </tr>
                    </table>
                </div>
                    
   	<?php
	}
	?>
    
            </td>
        </tr>
	</table>

	<?php
	//return $Gallery;
}


function PrintImageExtension($FileName)
{
	$Extension = explode(".", $FileName);
	$Extension = $Extension[sizeof($Extension)-1];
	
	switch ($Extension)
	{
		case 'pdf':
				echo "<img src='images/iconos/pdf.png' alt='PDF' />";
			break;
			
		case 'doc':
		case 'docx':
				echo "<img src='images/iconos/doc.png' alt='DOC'/>";
			break;
			
		case 'xls':
		case 'xlsx':
				echo "<img src='images/iconos/excel.png' alt='XLS' />";
			break;
			
		case 'txt':
				echo "<img src='images/iconos/txt.png' alt='TXT' />";
			break;
			
		case 'ppt':
		case 'pptx':
				echo "<img src='images/iconos/ppt.png' alt='PPT' />";
			break;
			
		case 'csv':
				echo "<img src='images/iconos/csv.png' alt='CSV' />";
			break;
			
		case 'jpg':
		case 'jpeg':
				echo "<img src='images/iconos/jpg.png' alt='JPG' />";
			break;
			
		case 'gif':
				echo "<img src='images/iconos/gif.png' alt='GIF' />";			
			break;
		
		case 'png':
				echo "<img src='images/iconos/png.png' alt='PNG' />";
			break;
	}
}


function GetUrlImagenVideoYouTube($EmbedCode)
{

	for ($i=0; $i<strlen($EmbedCode); $i++)
	{
		if (substr($EmbedCode, $i, 25) == "http://www.youtube.com/v/")
			return "<img src='http://i1.ytimg.com/vi/" . substr($EmbedCode, $i+25, 11) . "/default.jpg' border='0' class='bordeGris' />";
		
		if (substr($EmbedCode, $i, 29) == "http://www.youtube.com/embed/")
			return "<img src='http://i1.ytimg.com/vi/" . substr($EmbedCode, $i+29, 11) . "/default.jpg' border='0' class='bordeGris' />";
	}
	
	return false;
}


function CortarCadena($String, $CantidadCaracteres)
{
	if (strlen($String) > $CantidadCaracteres)
	{
		return substr($String, 0, $CantidadCaracteres) . '...';
	}

	return $String;
}


function UrlFriendly($url) 
{
	// Tranformamos todo a minusculas
	$url = strtolower($url);
	
	// Rememplazamos caracteres especiales latinos
	$find 	= array('á', 'é', 'í', 'ó', 'ú', 'ñ');
	$repl 	= array('a', 'e', 'i', 'o', 'u', 'n');
	$url 	= str_replace($find, $repl, $url);
	
	// Añaadimos los guiones
	$find 	= array(' ', '&', '\r\n', '\n', '+');
	$url 	= str_replace($find, '-', $url);
	
	// Eliminamos y Reemplazamos demás caracteres especiales
	$find 	= array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
	$repl 	= array('', '-', '');
	$url 	= preg_replace($find, $repl, $url);

	if (sizeof($url) == 0)
		$url = 'producto';
	
	return $url;
}


function ParseNumber($str)
{
	$str = number_format($str, 2, ',', '.');
	
	$str = str_replace(',00', '', $str);
	
	return $str;
}


function CambiarFecha($Fecha)
{
	if (sizeof($Fecha) == 0)
		return $Fecha;

	if (strpos($Fecha, '-') === false && strpos($Fecha, '/') === false)
	{
		$Anio 	= substr($Fecha, 0, 4);
		$Mes 	= substr($Fecha, 4, 2);
		$Dia 	= substr($Fecha, 6, 2);
		
		$Fecha = $Dia . '/' . $Mes . '/' . $Anio;
	}
	elseif (strpos($Fecha, '-'))
	{
		$Fecha = implode('-', array_reverse(explode('-', substr($Fecha, 0, 10))));
	}
	elseif (strpos($Fecha, '/'))
	{
		$Fecha = implode('/', array_reverse(explode('/', substr($Fecha, 0, 10))));
	}
	
	return $Fecha;
}


function CambiarFechaHora($String)
{
	$Fecha = implode('-', array_reverse(explode('-', substr($String, 0, 10))));
	
	$Hora = substr($String, 11, strlen($String));
	
	$FechaHora = $Fecha . " | " . $Hora;
	
	return $FechaHora;
}


function RestaFechas($FechaInicio, $FechaFin)
{
    $FechaInicio 	= str_replace("-", "", $FechaInicio);
    $FechaInicio 	= str_replace("/", "", $FechaInicio);
	$FechaFin 		= str_replace("-", "", $FechaFin);
    $FechaFin 		= str_replace("/", "", $FechaFin);

    ereg("([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $FechaInicio, $FechaInicio);
    ereg("([0-9]{1,2})([0-9]{1,2})([0-9]{2,4})", $FechaFin, $FechaFin);

    $date1 = mktime(0, 0, 0, $FechaInicio[2], $FechaInicio[1], $FechaInicio[3]);
    $date2 = mktime(0, 0, 0, $FechaFin[2], $FechaFin[1], $FechaFin[3]);

    return round(($date2 - $date1) / (60 * 60 * 24));
}


function RestaHoras($HoraInicio, $HoraFin)
{
	$horai 	= substr($HoraInicio, 0, 2);
	$mini 	= substr($HoraInicio, 3, 2);
	$segi 	= substr($HoraInicio, 6, 2);

	$horaf 	= substr($HoraFin, 0, 2);
	$minf 	= substr($HoraFin, 3, 2);
	$segf 	= substr($HoraFin, 6, 2);

	$ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
	$fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);

	$dif = $fin - $ini;

	$difh = floor($dif / 3600);
	$difm = floor(($dif - ($difh * 3600)) / 60);
	$difs = $dif - ($difm * 60) - ($difh * 3600);
	
	return date("H-i-s", mktime($difh, $difm, $difs));
}


function CalcularEdad($fecha_nac)
{
	$dia = date("j");
	$mes = date("n");
	$anno = date("Y");
	
	$dia_nac = substr($fecha_nac, 8, 2);
	$mes_nac = substr($fecha_nac, 5, 2);
	$anno_nac = substr($fecha_nac, 0, 4);
	
	if($mes_nac>$mes)
	{
		$calc_edad = $anno-$anno_nac-1;
	}
	else
	{
		if($mes == $mes_nac && $dia_nac > $dia)
		{
			$calc_edad = $anno-$anno_nac-1;
		}
		else
		{
			$calc_edad = $anno-$anno_nac;
		}
	}
	
	return $calc_edad;
}


function EliminarTildes($str)
{
	$str = ereg_replace("á", "a", $str);
	$str = ereg_replace("é", "e", $str);
	$str = ereg_replace("í", "i", $str);
	$str = ereg_replace("ó", "o", $str);
	$str = ereg_replace("ú", "u", $str);
	$str = ereg_replace("ä", "a", $str);
	$str = ereg_replace("ë", "e", $str);
	$str = ereg_replace("ï", "i", $str);
	$str = ereg_replace("ö", "o", $str);
	$str = ereg_replace("ü", "u", $str);
	$str = ereg_replace("ñ", "n", $str);
	$str = ereg_replace("Ñ", "N", $str);
	
	return $str;
}


function ParseCssAsArray ($FileName)
{
	$arr 		= array();
	$Class 		= false;
	$ClassName	= '';
	
	$File = fopen($FileName, 'r');
	
	$String = str_replace("\n", "", fread($File, filesize($FileName)));
	$String = str_replace("\r", "", $String);
	$String = str_replace("\t", "", $String);
	$String = str_replace(".", "", $String);
	$String = str_replace(" ", "", $String);
		
	for ($i=0; $i<=strlen($String)-1; $i++)
	{
		if (!$Class)
		{
			if ($String[$i] != '{')
			{
				$ClassName.= $String[$i];
			}
			else
			{
				$ClassName 			= str_replace("\n", "", $ClassName);
				$arr[$ClassName] 	= 'style="';
				$Class 				= true;
			}			
		}
		else
		{
			if ($String[$i] != '}')
				$arr[$ClassName].= $String[$i];
			else
			{
				$arr[$ClassName].= '}"';
				$Class 			 = false;
				$ClassName		 = '';
			}
		}		
	}
	
	return $arr;
}


function FindElement($arr, $id)
{
	if (!is_array($arr))
		return false;

	if ($arr == false || $arr == '')
		return false;

	for ($i=0; $i<sizeof($arr); $i++)
	{
		if ($arr[$i] == $id)
			return true;
	}
	
	return false;
}


function IsEmptyArray($arr)
{
	if (!is_array($arr))
		return true;

	foreach ($arr as $index=>$value)
	{
		if (trim($value) != '')
			return false;
	}
	
	return true;
}


function SendArray(array $arr) 
{
	if (!is_array($arr))
		return '';

    $tmp = serialize($arr);
    $tmp = urlencode($tmp);

    return $tmp;
}


function ReceiveArray($urlArray) 
{
    $tmp = stripslashes($urlArray);
    $tmp = urldecode($tmp);
    $tmp = unserialize($tmp);

   return $tmp;
}


function ParseUrlFacebook()
{
	$str = str_replace('/','%2F',$str);
	$str = str_replace(':','%3A',$str);
	
	return $str;
}

function CPcuitValido( $cuit ) {
    $esCuit=false;
    $cuit_rearmado="";
     //separo cualquier caracter que no tenga que ver con numeros
    for ($i=0; $i < strlen($cuit); $i++) {   
        if ((Ord(substr($cuit, $i, 1)) >= 48) && (Ord(substr($cuit, $i, 1)) <= 57))     {
            $cuit_rearmado = $cuit_rearmado . substr($cuit, $i, 1);
        }
    }
    $cuit=$cuit_rearmado;
    if ( strlen($cuit_rearmado) <> 11) {  // si to estan todos los digitos
        $esCuit=false;
    } else {
        $x=$i=$dv=0;
        // Multiplico los dígitos.
        $vec[0] = (substr($cuit, 0, 1)) * 5;
        $vec[1] = (substr($cuit, 1, 1)) * 4;
        $vec[2] = (substr($cuit, 2, 1)) * 3;
        $vec[3] = (substr($cuit, 3, 1)) * 2;
        $vec[4] = (substr($cuit, 4, 1)) * 7;
        $vec[5] = (substr($cuit, 5, 1)) * 6;
        $vec[6] = (substr($cuit, 6, 1)) * 5;
        $vec[7] = (substr($cuit, 7, 1)) * 4;
        $vec[8] = (substr($cuit, 8, 1)) * 3;
        $vec[9] = (substr($cuit, 9, 1)) * 2;
                    
        // Suma cada uno de los resultado.
        for( $i = 0;$i<=9; $i++) {
            $x += $vec[$i];
        }
        $dv = (11 - ($x % 11)) % 11;
        if ($dv == (substr($cuit, 10, 1)) ) { 
            $esCuit=true;
        } 
    }
    return( $esCuit );
}



?>