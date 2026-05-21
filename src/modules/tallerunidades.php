<?php

require_once('../library/class.tallerunidades.php');

class ModuleTallerUnidades
{
	function GetById(array $array)
	{
		$TallerUnidades = new TallerUnidades();

		return $TallerUnidades->GetById($array['IdTallerUnidad']);
	}
		
	function GetAll(array $array)
	{
		$TallerUnidades = new TallerUnidades();
		
		$filter = array();		
		$filter['Dominio'] 	= $array['FilterDominio'];
		
		return $TallerUnidades->GetAll($filter, NULL);
	}
}

?>