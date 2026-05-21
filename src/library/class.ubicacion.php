<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class Ubicacion
{
	public $IdUbicacion;
	public $Codigo;
	public $Nombre;
	public $Predeterminado;

	const VelezSarsfield 	= 1;
	const Sureda			= 2;
	const Libertador		= 3;
	const Liberador		= 3;
	const RioTurbio			= 4;
	const Rodriguez			= 5;
	const Transito 			= 8;
	const Entregado			= 9;
	
	
	public function __construct()
	{
		$this->IdUbicacion 		= '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
		$this->Predeterminado 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdUbicacion 		= $arr['IdUbicacion'];
		$this->Codigo			= stripslashes($arr['Codigo']);
		$this->Nombre 			= stripslashes($arr['Nombre']);
		$this->Predeterminado	= (bool)$arr['Predeterminado'];
	}
	
	
	public function CanDelete()
	{
		if ($this->GetAllUnidades())
			return false;
		
		return true;
	}
	
	
	public function GetAllUnidades()
	{
		$Unidades = new Unidades();
		
		return $Unidades->GetAllByUbicacion($this);
	}
}

?>