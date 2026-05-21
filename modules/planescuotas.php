<?php

require_once('../library/class.planescuotas.php');


class ModulePlanesCuotas
{
	function GetById(array $array)
	{
		$oPlanesCuotas = new PlanesCuotas();

		return $oPlanesCuotas->GetById($array['IdPlanCuota']);
	}

	
	function GetAll(array $array)
	{
		$oPlanesCuotas = new PlanesCuotas();
		
		if ($array['CurrentPage'])
			$oPage = new Page($array['CurrentPage']);
		else
			$oPage = NULL;
		
		$filter = array();

		$filter['Nombre'] = $array['Nombre'];
		$filter['IdFormaPago'] = $array['IdFormaPago'];
		
		return $oPlanesCuotas->GetAll($filter, $oPage);
	}
}

?>