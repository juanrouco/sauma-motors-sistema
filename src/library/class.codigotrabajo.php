<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class CodigoTrabajo
{
	public $IdCodigoTrabajo;
	public $IdModeloPV;
	public $Descripcion;
	public $CodigoHistorico;
	public $Codigo;
	public $Tiempo;
	public $IdArticulo;

	public function __construct()
	{
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdCodigoTrabajo 		= $arr['IdCodigoTrabajo'];
		$this->IdModeloPV 			= $arr['IdModeloPV'];
		$this->Descripcion 			=$arr['Descripcion'];
		$this->CodigoHistorico 		= $arr['CodigoHistorico'];
		$this->Codigo				= $arr['Codigo'];
		$this->Tiempo				= $arr['Tiempo'];
		$this->IdArticulo			= $arr['IdArticulo'];
	}
}

?>