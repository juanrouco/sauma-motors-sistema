<?php

class Gestor
{
	public $IdGestor;
	public $Disponible;
	public $RazonSocial;
	public $Email;
	public $Telefono;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdGestor 	= $arr['IdGestor'];
		$this->Disponible 	= $arr['Disponible'];
		$this->RazonSocial 	= stripslashes($arr['RazonSocial']);
		$this->Email 		= stripslashes($arr['Email']);
		$this->Telefono 	= stripslashes($arr['Telefono']);
	}
	
	
	
}

?>