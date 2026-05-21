<?php

require_once('../library/class.clientes.php');

class ModuleClientes
{	
	function GetAll(array $array)
	{
		$Clientes = new Clientes();

		$filter = array();		
		$filter['RazonSocial'] 			= $array['FilterRazonSocial'];
		$filter['Email'] 				= $array['FilterEmail'];
		$filter['ClaveFiscalNumero'] 	= $array['FilterClaveFiscalNumero'];
		$filter['IdTipoPersona'] 		= $array['FilterIdTipoPersona'];
		$oPage 				= new Page(0, 10);
		return $Clientes->GetAll($filter, $oPage);
	}


	function GetById(array $array)
	{
		$Clientes = new Clientes();

		return $Clientes->GetById($array['IdCliente']);
	}
}

?>