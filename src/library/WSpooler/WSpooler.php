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
/**
 * Description of CWSpooler
 *
 * @author Marcelo
 */

//Requerimientos: 
//Debera tener instalada la clase Net_Socket: 
//ej: pear install Net_Socket

require_once "Net/Socket.php";

class CWSpooler extends Net_Socket {
    //put your code here

    var $glb_strResult;

    function if_open($strHostname,$nPort) {

        if(PEAR::isError($this->connect($strHostname,$nPort, true, 5))) {

            return -1;
        }

        $this->setTimeout(12,0);

        if(PEAR::isError($result = $this->readLine())) {

            return -1;

        }

        if($result == "+OK") {
            return 0;
        }

        $this->disconnect();

        return -1;
    }

    function if_close() {

        $this->disconnect();

        return 0;
    }

    //@Comando|OK|0080|0600|Param1|Param2|Param3|Param4|Param5
    //
    //Field 1 corresponde a la posicion de 0080 (Codigo de estado 1)
    //Field 2 corresponde a la posicion de 0600 (Codigo de estado 2)

    function if_read($nField) {

        if ( ($nField < 1 ) )
            return "@NA";

        $fields = explode("|",$this->glb_strResult);

        $nCount = count($fields);

        if ( $nField > $nCount - 2  )
            return "@NA";

        return $fields[$nField + 1 ];
    }

    function if_write($strCommand) {

        $params = explode("|", $strCommand);

        $strSend = $strCommand . "\n";

        if (PEAR::isError($this->write($strSend))) {

            $this->glb_strResult = $params[0] . "|ERROR|808C|0600";

            return -1;
        }

        if (PEAR::isError($results = $this->readLine())) {

            $this->glb_strResult = $params[0] . "|ERROR|808C|0600";

            return -1;
        }


        $this->glb_strResult = $results;

        $fields = explode("|",$this->glb_strResult);

        $nCount = count($fields);

        if($nCount  < 4 ) {
            $this->glb_strResult = $params[0] ."|ERROR|808C|0600";

            return -1;
        }

        if ($fields[1] == "ERROR") {
            return -1;
        }

        return 0;
    }

    function if_error1($nBit = 0) {

        $fields = explode("|",$this->glb_strResult);

        $nStatus = hexdec($fields[2]);

        if ( $nBit < 1 || $nBit > 16 )
            return $nStatus;

        $nMask = 0x0001 << ( $nBit - 1 );

        if ( ($nStatus & $nMask) == $nMask )
            return 1;

        return 0;

    }

    function if_error2($nBit = 0) {

        $nMask = 0x0001;

        $fields = explode("|",$this->glb_strResult);

        $nStatus = hexdec($fields[3]);
        
        if ( $nBit < 1 || $nBit > 16 )
            return $nStatus;
        
        $nMask = 0x0001 << ( $nBit - 1 );

        if ( ($nStatus & $nMask) == $nMask )
            return 1;

        return 0;
    }
}

?>
