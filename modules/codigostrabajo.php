<?php

require_once('../library/class.codigotrabajo.php');
require_once('../library/class.codigostrabajo.php');


class ModuleCodigosTrabajo
{
	function GetById(array $array)
	{
		$CodigosTrabajo = new CodigosTrabajo();

		return $CodigosTrabajo->GetById($array['IdCodigoTrabajo']);
	}
	
	function GetAll(array $array)
	{
		$CodigosTrabajo = new CodigosTrabajo();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Descripcion'] = $array['Filter_Descripcion'];
		
		return $CodigosTrabajo->GetAll($filter, $oPage);
	}
}

?>