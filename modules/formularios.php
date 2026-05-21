<?php

require_once('../library/class.formularios.php');

class ModuleFormularios
{		
	function GetById(array $array)
	{
		$Formularios = new Formularios();

		return $Formularios->GetById($array['IdFormulario']);
	}

	function GetAll(array $array)
	{
		$Formularios = new Formularios();
		
		$filter = array();		
		$filter['IdTipoFormulario'] = $array['FilterIdTipoFormulario'];
		$filter['Numero'] 			= $array['FilterNumero'];
		$filter['IdEstado'] 		= $array['FilterIdEstado'];

		return $Formularios->GetAll($filter, NULL);
	}
	
	function GetAllSinUsar(array $array)
	{
		$Formularios = new Formularios();
		
		$filter = array();		
		$filter['IdTipoFormulario'] = $array['FilterIdTipoFormulario'];
		$filter['Numero'] 			= $array['FilterNumero'];
		$filter['IdEstado'] 		= $array['FilterIdEstado'];

		return $Formularios->GetAll($filter, NULL);
	}
}

?>