<?php

require_once('../library/class.ordenesTrabajo.php');
require_once('../library/class.tallerunidades.php');
require_once('../library/class.estadoorden.php');

class ModuleOrdenesTrabajo
{
	function GetById(array $array)
	{
		$oOrdenesTrabajo = new OrdenesTrabajo();

		return $oOrdenesTrabajo->GetById($array['IdOrdenTrabajo']);
	}
		
	function GetAll(array $array)
	{
		$oOrdenesTrabajo = new OrdenesTrabajo();
		$oTallerUnidades = new TallerUnidades();
		
		$filter = array();		
		$filter['Dominio'] 	= $array['FilterDominio'];
		$filter['IdOrdenTrabajoLike'] 	= $array['FilterIdOrdenTrabajo'];
		$filter['IdEstadoOrden'] 	= EstadoOrden::Aceptada;
		
		$arrOrdenesTrabajo = array();
		
		foreach ($oOrdenesTrabajo->GetAll($filter, NULL) as $oOrdenTrabajo)
		{
			$oTallerUnidad = $oTallerUnidades->GetById($oOrdenTrabajo->IdTallerUnidad);
			$oOrdenTrabajo->Dominio = $oTallerUnidad->Dominio . ' - OT N&deg;: ' . $oOrdenTrabajo->IdOrdenTrabajo;
			$oOrdenTrabajo->Comentarios = '';
			array_push($arrOrdenesTrabajo, $oOrdenTrabajo);
		}		
		
		return $arrOrdenesTrabajo;
	}
}

?>