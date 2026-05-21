<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class DeclaracionJurada
{
	public $IdDeclaracionJurada;
	public $IdTipo;
	public $Fecha;
	
	
	public function __construct()
	{
		$this->IdDeclaracionJurada 	= '';
		$this->IdTipo 				= '';
		$this->Fecha				= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdDeclaracionJurada 	= $arr['IdDeclaracionJurada'];
		$this->IdTipo				= $arr['IdTipo'];
		$this->Fecha				= $arr['Fecha'];
	}
	

	public function GetCountFormularios()
	{
		return count($this->GetAllFormularios());
	}

	
	public function GetAllFormularios()
	{
		$Formularios = new Formularios();
		
		return $Formularios->GetAllByDeclaracionJurada($this);
	}
}

?>