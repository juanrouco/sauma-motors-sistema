<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoDocumento
{
	public $IdTipoDocumento;
	public $Codigo;
	public $Nombre;
	public $Expedido;
	public $Predeterminado;
	public $CodigoMigracion;
	
	const DNI 	= 1;
	const CI 	= 2;
	const LC 	= 3;
	const LE 	= 4;
	const PA 	= 5;

	
	public function __construct()
	{
		$this->IdTipoDocumento 	= '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
		$this->Expedido 		= '';
		$this->Predeterminado 	= '';
		$this->CodigoMigracion	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoDocumento 	= $arr['IdTipoDocumento'];
		$this->Codigo			= stripslashes($arr['Codigo']);
		$this->CodigoMigracion	= stripslashes($arr['CodigoMigracion']);
		$this->Nombre 			= stripslashes($arr['Nombre']);
		$this->Expedido 		= stripslashes($arr['Expedido']);
		$this->Predeterminado 	= (bool)$arr['Predeterminado'];
	}


	public function CanDelete()
	{
		if ($this->GetAllClientes())
			return false;

		if ($this->GetAllGestoriaCedulas())
			return false;

		if ($this->GetAllPrendaFiadores())
			return false;
		
		return true;
	}

	
	public function GetAllClientes()
	{
		$Clientes = new Clientes();
		
		return $Clientes->GetAllByTipoDocumento($this);
	}	


	public function GetAllGestoriaCedulas()
	{
		$GestoriaCedulas = new GestoriaCedulas();
		
		return $GestoriaCedulas->GetAllByTipoDocumento($this);
	}	


	public function GetAllPrendaFiadores()
	{
		$PrendaFiadores = new PrendaFiadores();
		
		return $PrendaFiadores->GetAllByTipoDocumento($this);
	}	
}

?>