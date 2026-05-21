<?php
require_once("WSpooler/WSpooler.php");
function sanear_string($string)
{

    $tofind = "ÀÁÂÄÅàáâäÒÓÔÖòóôöÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
        $replac = "AAAAAaaaaOOOOooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        $string= utf8_encode(strtr(utf8_decode($string), 
                                utf8_decode($tofind),
                                $replac));
	
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
	
	$string = str_replace('Ó', 'O', $string);

    //Esta parte se encarga de eliminar cualquier caracter extraño


    return $string;
}
class GeneradorDocumentos
{

	const DatosCliente = 1;
	const AbrirRecibo = 2;
	const ImprimirItem = 3;
	const CerrarRecibo = 4;
	const ImprimirSubtotal = 5;
	const ImprimirTotal = 6;
	const ImprimirDescuento = 7;
	const ImprimirPercepciones = 8;
	
	protected $Host;
	protected $Port;
	protected $Separador;
	protected $WSpooler;	
	protected $Compra;
	protected $Ret;
	protected $Iva21;
	protected $Iva10;
	
	public function __construct()
	{
		$this->Host = '192.168.1.36';
		$this->Port = 1000;
		$this->Separador = chr(10);
		$this->WSpooler = new CWSpooler();
		$this->Iva21	= 0;
		$this->Iva10	= 0;
	}
	
	protected function ImprimirDatosCliente($oCliente)
	{
		$oTiposIva 			= new TiposIva();
		$oLocalidades 		= new Localidades();
		$oPartidos 			= new Partidos();
		$oProvincias 		= new Provincias();
		$oPaises 			= new Paises();
		
		if (!$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva))
			throw new Exception('Condicion de IVA del cliente no existente.');
			
		$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
		$oPartido = $oPartidos->GetById($oCliente->DomicilioIdPartido);
		$oProvincia = $oProvincias->GetById($oCliente->DomicilioIdProvincia);
		
		$comando = "@SetCustomerData|" . str_replace('-', '', $oCliente->RazonSocial) . "|";
		
		if (!$oCliente->DocumentoNumero && !$oCliente->ClaveFiscalNumero)
			$oCliente->DocumentoNumero = '1';
		if ($oCliente->DocumentoNumero && $oCliente->IdTipoIva != TipoIva::RI)
			$comando .= preg_replace('#[^0-9]#','',strip_tags($oCliente->DocumentoNumero)) . "|";
		else
			$comando .= str_replace('-', '', preg_replace('#[^0-9]#','',strip_tags($oCliente->ClaveFiscalNumero))) . "|";
		
		switch($oCliente->IdTipoIva)
		{
			case TipoIva::RI:
				$comando .= "I|";
				break;
			case TipoIva::RNI:
				$comando .= "N|";
				break;
			case TipoIva::MO:			
				$comando .= "M|";
				break;
			case TipoIva::EX:
				$comando .= "E|";
				break;
			default:
				$comando .= "C|";
				break;
		}
		if ($oCliente->DocumentoNumero && $oCliente->IdTipoIva != TipoIva::RI)
		{
			switch($oCliente->DocumentoTipo)
			{
				case TipoDocumento::LE:
					$comando .= "0|";
					break;
				case TipoDocumento::LC:
					$comando .= "1|";
					break;
				case TipoDocumento::DNI:
					$comando .= "2|";
					break;
				case TipoDocumento::PA:
					$comando .= "3|";
					break;
				default:
					$comando .= "2|";
					break;
			}
		}
		else
		{
			if ($oCliente->ClaveFiscalTipo == ClaveFiscalTipos::Cuit  || $oCliente->IdTipoIva == TipoIva::RI)
				$comando .= "C|";
			else
				$comando .= "L|";
		}
		
		$comando .= substr(trim($oCliente->GetDomicilio()), 0, 50) . $this->Separador;
		
		$this->ProcesarComando($comando, GeneradorDocumentos::DatosCliente);
	}
	
	protected function AbrirRecibo($oCliente)
	{
	}
	
	protected function ImprimirItem($oCompraDetalle)
	{		
	}
	
	protected function CerrarRecibo()
	{
	}
	
	protected function Cancelar()
	{
		$comando = "@Cancel|" . chr(152) . $this->Separador;
		$this->Ret = $this->WSpooler->if_write($comando);
	}
	
	protected function ProcesarComando($Comando, $TipoComando)
	{
		$Comando = sanear_string($Comando);
		$this->Ret = $this->WSpooler->if_write($Comando);
		
		if ($this->Ret == -1 || $this->Ret == '-1')
		{
			switch ($TipoComando)
			{
				case GeneradorDocumentos::DatosCliente:
					throw new Exception('Error: Por favor revise los datos del cliente.<br>' . $Comando);
				break;
				case GeneradorDocumentos::AbrirRecibo:
					throw new Exception('Error al abrir el recibo.<br>' . $Comando);
				break;
				case GeneradorDocumentos::ImprimirItem:
					throw new Exception('Error al imprimir items.<br>' . $Comando);
				break;
				case GeneradorDocumentos::CerrarRecibo:
				break;
				
			}
		}
	}
	
	protected function CancelarPorError($Mensaje)
	{
		$this->Cancelar();
		$this->WSpooler->if_close();
		print_r($Mensaje);
		exit;
	}
	
	public function Imprimir($oCompra)
	{	
	}
}

?>