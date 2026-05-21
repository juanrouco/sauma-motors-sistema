<?php

require_once('../library/class.ordenesTrabajo.php');
require_once('../library/class.ordenestrabajotareas.php');

class ModuleOrdenesTrabajoTareas
{
	function GetById(array $array)
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();

		return $oOrdenesTrabajoTareas->GetById($array['IdOrdenTrabajoTarea']);
	}
		
	function GetAll(array $array)
	{
		$oOrdenesTrabajoTareas = new OrdenesTrabajoTareas();
		
		$filter = array();		
		$filter['IdOrdenTrabajo'] 	= $array['IdOrdenTrabajo'];
		
		$arrOrdenesTrabajoTareas = $oOrdenesTrabajoTareas->GetAll($filter, NULL);
		
		foreach ($arrOrdenesTrabajoTareas as $oOrdenTrabajoTarea)
		{
			$oOrdenTrabajoTarea->Descripcion = '';
		}
		
		return $arrOrdenesTrabajoTareas;
	}
}

?>