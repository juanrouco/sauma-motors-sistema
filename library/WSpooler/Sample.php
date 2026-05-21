<?php

// Este modulo contiene el código a disposicion por parte de IFDRIVERS
// en una base TAL CUAL. Todo receptor del Modulo se considera
// bajo licencia de los derechos de autor de IFDRIVERS para utilizar el
// codigo fuente siempre en modo que el o ella considere conveniente,
// incluida la copia, la compilacion, su modificacion o la redistribucion,
// con o sin modificaciones. Ninguna licencia o patentes de IFDRivers
// este implicita en la presente licencia.
//
// El usuario del codigo fuente debera entender que IFDRIVERS no puede
// Proporcionar apoyo tecnico para el modulo y no sera Responsable
// de las consecuencias del uso del programa.
//
// Todas las comunicaciones, incluida esta, no deben ser removidos
// del modulo sin el consentimiento previo por escrito de IFDRIVERS
// www: http://www.impresoras-fiscales.com.ar/
// email: soporte@impresoras-fiscales.com.ar

//** IMPORTANTE ** Requerimientos: 
//Debera tener instalada la clase Net_Socket: 
//ej: pear install Net_Socket

require_once "WSpooler.php";

$oWSpooler = new CWSpooler();

//Configurar la direccion IP o el nombre del Host donde esta el WinSpooler
//** IMPORTANTE **
//Si usa una IP Publica de un router debera configurar la característica de 
//Virtual Server o PortForwarding en el Router e indicar la direccion IP dentro de 
//la red local donde se encuentra el WinSpooler. Consulte el Manual para mas 
//información.

$strHost = "192.168.0.30"; 

$nRet = $oWSpooler->if_open( $strHost, 1000);

if($nRet != 0) {
    exit ("Error de conexion");
}

/*Los comandos de este ejemplo corresponden a la impresora fiscal Hasar de Tickets de la Argentina */
/*Debera cambiar los comandos por los que correspondan al modelo de la impresora fiscal */

$sp = chr(10);

$arr = array();
$arr[] = "@SetCustomerData|Mi Empresa SRL|30692137449|I|C|Mexico 564" . $sp;
$arr[] = "@OpenFiscalReceipt|A|T" . $sp;
$arr[] = "@PrintLineItem|Mouse Genius XScroll Optico Negro Ps/2|1.0|4.08|10.50|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Patchcord Cat.5E Gris Blindado|5.0|4.10|21.00|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Microfono NG-H300 Noganet|1.0|4.12|21.00|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Mouse Genius Netscroll 120 Metallic Opt|1.0|4.12|10.50|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Ventilador Cyber Cooler P4 S.478|2.0|4.12|21.00|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Lector 3.5 MultiCard Sony Mod MRW620 Oe|2.0|4.22|21.00|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Teclado Noganet Espanol -ps/2 -black|2.0|4.30|10.50|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Antena SMA Kozumi Wireless 7Dbi Omnidir|2.0|4.33|21.00|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Teclado Ecovision W98 Espanol PS2 KE990|1.0|4.39|10.50|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Limpiador para Pantallas SC Screen Clea|1.0|4.44|21.00|M|0.0|0|B" . $sp;
$arr[] = "@PrintLineItem|Auricular Genius Mod. HS-02B C/Microfon|1.0|4.46|21.00|M|0.0|0|B" . $sp;
$arr[] = "@Subtotal|P|Subtotal|0|" . $sp;
$arr[] = "@TotalTender|Efectivo|100.00|T|0" . $sp;
$arr[] = "@CloseFiscalReceipt" . $sp;

foreach ($arr as $line)
{
	$comando = $line . $sp;
	$nRet = $oWSpooler->if_write($comando);	
}


echo "nRet = " . $nRet . "<br/>";

for ( $nBit = 0; $nBit <= 16; $nBit++)
{
 echo 'if_error1(' .$nBit.  ') = ' . $oWSpooler->if_error1($nBit)."<br/>";
}

for ( $nBit = 0; $nBit <= 16; $nBit++)
{
 echo 'if_error2(' .$nBit . ') = ' . $oWSpooler->if_error2($nBit)."<br/>";
}

echo $oWSpooler->if_read(1)."\n";

echo $oWSpooler->if_read(2)."\n";

echo $oWSpooler->if_read(3)."\n";

$nRet = $oWSpooler->if_close();

?>
