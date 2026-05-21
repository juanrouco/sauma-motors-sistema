<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class EstadoOrden
{
	public $IdEstado;
	public $Codigo;
	public $Nombre;
	public $Color;
	public $Predeterminado;
	
	const Aceptada 			= 9;
	const Presupuesto 		= 10;
	const Rechazado 		= 11;
	const Finalizado		= 12;
	const Auditoria			= 13;
	
	public function __construct()
	{
		$this->IdEstado 		= '';
		$this->Codigo			= '';
		$this->Nombre 			= '';
		$this->Color 			= '';
		$this->Predeterminado 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdEstado 		= $arr['IdEstado'];
		$this->Codigo			= $arr['Codigo'];
		$this->Nombre 			= stripslashes($arr['Nombre']);
		$this->Color 			= stripslashes($arr['Color']);
		$this->Predeterminado	= $arr['Predeterminado'];
	}	
}

?>