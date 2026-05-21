<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ContratoPrendaUsado extends DBAccess 
{
	public $IdContratoPrenda;
	public $IdMinuta;
	public $NumeroContrato;
	public $MontoSolicitado;
	public $CostoOtorgamiento;
	public $Comision;
	public $MontoAcreditado;
	public $Resultado;
	public $FechaLiquidacion;
	public $GastoOtorgamiento;
	public $MontoOtorgado;
	public $IdAcreedor;
	public $FechaEnvioCarpeta;
	public $FechaAprobado;
	public $FechaRechazado;
	public $FechaObservacion;
	public $Observacion;
	public $CarpetaCompleta;
	public $PrePrenda;
	public $PrendaInscripta;
	public $IdEstado;
	public $FechaGestoria;
	public $FechaEnvioPrenda;
	
	public function __construct()
	{
		$this->IdContratoPrenda		= '';
		$this->IdMinuta 			= '';
		$this->NumeroContrato		= '';
		$this->MontoSolicitado		= '';
		$this->CostoOtorgamiento	= '';
		$this->Comision 			= '';
		$this->MontoAcreditado 		= '';
		$this->Resultado 			= '';
		$this->FechaLiquidacion		= '';
		$this->GastoOtorgamiento	= '';
		$this->MontoOtorgado		= '';
		$this->IdAcreedor			= '';
		$this->FechaEnvioCarpeta	= '';
		$this->FechaAprobado		= '';
		$this->FechaRechazado		= '';
		$this->FechaObservacion		= '';
		$this->Observacion			= '';
		$this->CarpetaCompleta		= '';
		$this->PrePrenda			= '';
		$this->PrendaInscripta		= '';
		$this->IdEstado				= '';
		$this->FechaGestoria		= '';
		$this->FechaEnvioPrenda		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdContratoPrenda		= $arr['IdContratoPrenda'];
		$this->IdMinuta 			= $arr['IdMinuta'];
		$this->NumeroContrato		= $arr['NumeroContrato'];
		$this->MontoSolicitado		= $arr['MontoSolicitado'];
		$this->CostoOtorgamiento	= $arr['CostoOtorgamiento'];
		$this->Comision 			= $arr['Comision'];
		$this->MontoAcreditado 		= $arr['MontoAcreditado'];
		$this->Resultado 			= $arr['Resultado'];
		$this->FechaLiquidacion		= $arr['FechaLiquidacion'];
		$this->GastoOtorgamiento	= $arr['GastoOtorgamiento'];
		$this->MontoOtorgado		= $arr['MontoOtorgado'];
		$this->IdAcreedor			= $arr['IdAcreedor'];
		$this->FechaEnvioCarpeta	= $arr['FechaEnvioCarpeta'];
		$this->FechaAprobado		= $arr['FechaAprobado'];
		$this->FechaRechazado		= $arr['FechaRechazado'];
		$this->FechaObservacion		= $arr['FechaObservacion'];
		$this->Observacion			= $arr['Observacion'];
		$this->CarpetaCompleta		= $arr['CarpetaCompleta'];
		$this->PrePrenda			= $arr['PrePrenda'];
		$this->PrendaInscripta		= $arr['PrendaInscripta'];
		$this->IdEstado				= $arr['IdEstado'];
		$this->FechaGestoria		= $arr['FechaGestoria'];
		$this->FechaEnvioPrenda		= $arr['FechaEnvioPrenda'];
	}
}
?>
