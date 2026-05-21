<?php

require_once('../library/class.tareatrabajo.php');
require_once('../library/class.tareastrabajo.php');


class ModuleTareasTrabajo
{
	function GetById(array $array)
	{
		$TareasTrabajo = new TareasTrabajo();
		
		$oTareaTrabajo = $TareasTrabajo->GetById($array['IdTareaTrabajo']);
		$oTareaTrabajo->TotalImporte = $oTareaTrabajo->ImporteTotal();
		$oTareaTrabajo->Descripcion = '';
		return $oTareaTrabajo;
	}	
}

?>