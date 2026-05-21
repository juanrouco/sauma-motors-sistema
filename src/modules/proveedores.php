<?php

require_once('../library/class.proveedor.php');
require_once('../library/class.proveedores.php');


class ModuleProveedores
{
	function GetById(array $array)
	{
		$Proveedores = new Proveedores();

		return $Proveedores->GetById($array['IdProveedor']);
	}
	
	function GetAll(array $array)
	{
		$Proveedores = new Proveedores();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Empresa'] = $array['Filter_Empresa'];
		$filter['Cuit'] = $array['Filter_Cuit'];
		
		return $Proveedores->GetAll($filter, $oPage);
	}
}

?>