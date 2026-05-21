<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CuentaGestoria extends DBAccess 
{
	public $IdCuentaGestoria;
	public $IdMinuta;
	public $IdGestor;
	public $PatentamientoCalculado;
	public $PatentamientoFinal;
	public $PrendaCalculado;
	public $PrendaFinal;
	public $AltaCalculado;
	public $AltaFinal;
	public $SelladoCalculado;
	public $SelladoFinal;
	public $TotalCalculado;
	public $TotalFinal;
	public $ComisionGestor;
	public $Fecha;
	public $FechaRendicion;
	public $TotalRendicion;
	public $Comentarios;
	
	public function __construct()
	{
		$this->IdCuentaGestoria			= '';
		$this->IdMinuta 				= '';
		$this->IdGestor					= '';
		$this->PatentamientoCalculado	= '';
		$this->PatentamientoFinal		= '';
		$this->PrendaCalculado 			= '';
		$this->PrendaFinal 				= '';
		$this->AltaCalculado 			= '';
		$this->AltaFinal				= '';
		$this->SelladoCalculado			= '';
		$this->SelladoFinal				= '';
		$this->TotalCalculado			= '';
		$this->TotalFinal				= '';
		$this->ComisionGestor			= '';
		$this->Fecha					= '';
		$this->FechaRendicion			= '';
		$this->TotalRendicion			= '';
		$this->Comentarios				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCuentaGestoria			= $arr['IdCuentaGestoria'];
		$this->IdMinuta 				= $arr['IdMinuta'];
		$this->IdGestor					= $arr['IdGestor'];
		$this->PatentamientoCalculado	= $arr['PatentamientoCalculado'];
		$this->PatentamientoFinal		= $arr['PatentamientoFinal'];
		$this->PrendaCalculado 			= $arr['PrendaCalculado'];
		$this->PrendaFinal 				= $arr['PrendaFinal'];
		$this->AltaCalculado 			= $arr['AltaCalculado'];
		$this->AltaFinal				= $arr['AltaFinal'];
		$this->SelladoCalculado			= $arr['SelladoCalculado'];
		$this->SelladoFinal				= $arr['SelladoFinal'];
		$this->TotalCalculado			= $arr['TotalCalculado'];
		$this->TotalFinal				= $arr['TotalFinal'];
		$this->ComisionGestor			= $arr['ComisionGestor'];
		$this->Fecha					= $arr['Fecha'];
		$this->FechaRendicion			= $arr['FechaRendicion'];
		$this->TotalRendicion			= $arr['TotalRendicion'];
		$this->Comentarios				= $arr['Comentarios'];
	}
}
?>
