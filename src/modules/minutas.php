<?php

require_once('../library/class.minutas.php');


class ModuleMinutas
{
	function GetById(array $array)
	{
		$Minutas = new Minutas();
		
		$oMinuta = $Minutas->GetById($array['IdMinuta']);
		
		$oMinuta->Observaciones = '';

		return $oMinuta;
	}
	
	function UpdateReportado(array $array)
	{
		$oMinutas	= new Minutas();
		$oMinuta = $oMinutas->GetById($array['IdMinuta']);
		$oMinuta->Reportado = $array['Reportado'];
		$oMinutas->Update($oMinuta);
		return $oMinuta;
	}
}


?>