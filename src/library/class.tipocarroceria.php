<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoCarroceria
{	
	public $IdTipoCarroceria;
	public $Codigo;
	public $Nombre;	
	
	public function __construct()
	{
		$this->IdTipoCarroceria = '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoCarroceria = $arr['IdTipoCarroceria'];
		$this->Codigo			= stripslashes($arr['Codigo']);
		$this->Nombre 			= stripslashes($arr['Nombre']);
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllModelos())
			return false;
		
		return true;
	}
	
	
	public function GetAllModelos()
	{
		$Modelos = new Modelos();
		
		return false;//$Modelos->GetAllByTipoCarroceria($this);
	}
}

?>