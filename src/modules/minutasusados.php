<?php

require_once('../library/class.minutasusados.php');


class ModuleMinutasUsados
{
	function GetById(array $array)
	{
		$Minutas = new MinutasUsados();

		return $Minutas->GetById($array['IdMinuta']);
	}
	
	function UpdateReportado(array $array)
	{
		$oMinutas	= new MinutasUsados();
		$oMinuta = $oMinutas->GetById($array['IdMinuta']);
		$oMinuta->Reportado = $array['Reportado'];
		$oMinutas->Update($oMinuta);
		return $oMinuta;
	}
}


?>