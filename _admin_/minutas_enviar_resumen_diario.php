<?php
//phpinfo();exit;
//require_once('ssi_errores.php'); 
require_once('../inc_library.php'); 
require_once('../library/mail/Mail.php');
include('../library/mail/Mail/mime.php');

$from = "<servicios.aspenmotors@gmail.com>";
$to = "<juanmanuel.rouco@gmail.com>";

$Subject = "Encuesta de satisfacción de cliente - Motopier Concesionario Oficial Honda";

$oMinutas	= new Minutas();
$oClientes	= new Clientes();

$filter	= array();
$filter['FechaMinutaDesde'] = '01-09-2015';
$filter['FechaMinutaHasta'] = date('d-m-Y');		
$filter['ReportadoSeguros'] = '0';
$filter['Facturado'] = '0';

$arrMinutas = $oMinutas->GetAll($filter);

if ($arrMinutas)
{
	$from = "Departamente de Calidad <calidad@motopier.com.ar>";
	$crlf = "\r\n";
 	$host = "tls://190.210.181.189";
	$port = "465";
	$username = "calidad@motopier.com.ar";  //<> give errors
	$password = "Calidad_2015";

	
	
	$smtp =& Mail::factory('smtp', array ('host' => $host,
            'port' => $port,
            'auth' => true,
			//'debug' => 3,
            'username' => $username,
            'password' => $password));

	foreach ($arrMinutas as $oMinuta)
	{
		$oCliente = $oClientes->GetById($oMinuta->IdCliente);
		if ($oCliente->Email && strpos($oCliente->Email, 'NOPOSEE') === false)
		{
			$headers = array ('MIME-Version' => '1.0rn',
			'Content-Type' => "text/html; charset=UTF-8",
			'From' => $from,
			'To' => $oCliente->Email, //$oCliente->Email,
			'Subject' => utf8_decode($Subject));
		
			$mime = new Mail_mime($crlf);
			$hdrs = $mime->headers($headers);
			
			$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
			<html>
			<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8">
			</head>
			<body>
			<table>
			<tr>
				<td>Estimado ' . $oCliente->RazonSocial . ' ,</td>
			</tr>
			<tr>
				<td>Como parte de nuestro compromiso para mejorar la calidad de nuestro servicio le pedimos que responda una breve encuesta de satisfacci&oacute;n. Su evaluaci&oacute;n es muy &uacute;til para atender mejor las necesidades de nuestros clientes.</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>S&oacute;lo precisamos unos segundos de su tiempo.</td>
			</tr>
			<tr>
				<td>Por favor, haga clic <a href="http://motopier.com.ar/control_de_calidad/encuesta_ventas.php">aqu&iacute;</a> y complete la encuesta.</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Muchas gracias.</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Atentamente,</td>
			</tr>
			<tr>
				<td>Depto. de calidad</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>MOTOPIER</td>
			</tr>
			<tr>
				<td>Concesionario Oficial Honda</td>
			</tr>
			<tr>
				<td><a href="http://www.motopier.com.ar">http://www.motopier.com.ar</a></td>
			</tr>
		</table></body></html>';

			//$mime->setTXTBody(strip_tags($text));
			$mime->setHTMLBody(utf8_encode($text));
			$body = $mime->get();
			
			$mail = $smtp->send($oCliente->Email, $hdrs, $text);
			
			$headers = array ('MIME-Version' => '1.0rn',
			'Content-Type' => "text/html; charset=UTF-8",
			'From' => $from,
			'To' => 'juan@crossingnet.com', //$oCliente->Email,
			'Subject' => utf8_decode($Subject));
		
			$mime = new Mail_mime($crlf);
			$hdrs = $mime->headers($headers);
			//$mail = $smtp->send('juan@crossingnet.com', $hdrs, $text);;
			
			$headers = array ('MIME-Version' => '1.0rn',
			'Content-Type' => "text/html; charset=UTF-8",
			'From' => $from,
			'To' => 'martin@crossingnet.com', //$oCliente->Email,
			'Subject' => utf8_decode($Subject));
		
			$mime = new Mail_mime($crlf);
			$hdrs = $mime->headers($headers);
			$mail = $smtp->send('martin@crossingnet.com', $hdrs, $text);
			if (!PEAR::isError($mail))
			{
					$oMinuta->ReportadoSeguros = 1;
					$oMinutas->Update($oMinuta);
			}
		}
	}
}

//header('Location: minutas_enviar_resumen_diario_seguros.php');

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }

exit;


?>
ok