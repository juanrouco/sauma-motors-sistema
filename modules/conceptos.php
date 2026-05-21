<?php

require_once('../library/class.concepto.php');
require_once('../library/class.conceptos.php');


class ModuleConceptos
{
	function GetById(array $array)
	{
		$oConceptos = new Conceptos();

		return $oConceptos->GetById($array['IdConcepto']);
	}
	
	function GetAll(array $array)
	{
		$oConceptos = new Conceptos();
		$oPage 	= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Nombre'] = $array['Filter_Concepto'];
		
		return $oConceptos->GetAll($filter, $oPage);
	}
}

?>