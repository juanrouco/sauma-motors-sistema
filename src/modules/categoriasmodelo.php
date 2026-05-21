<?php

require_once('../library/class.categoriasmodelo.php');

class ModuleCategoriasModelo
{	
	function GetAll(array $array)
	{
		$CategoriasModelo = new CategoriasModelo();

		$filter = array();		
		$filter['Nombre'] 			= $array['Nombre'];
		
		return $CategoriasModelo->GetAll($filter, NULL);
	}


	function GetById(array $array)
	{
		$CategoriasModelo = new CategoriasModelo();

		return $CategoriasModelo->GetById($array['IdCategoriaModelo']);
	}
}

?>