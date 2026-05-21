<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoUso
{
	public $IdTipoUso;
	public $Codigo;
	public $Nombre;	
	
	public function __construct()
	{
		$this->IdTipoUso 	= '';
		$this->Codigo		= '';
		$this->Nombre 		= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoUso 	= $arr['IdTipoUso'];
		$this->Codigo		= stripslashes($arr['Codigo']);
		$this->Nombre 		= stripslashes($arr['Nombre']);
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
		
		return false;// $Modelos->GetAllByTipoUso($this);
	}
}

?>