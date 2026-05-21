<?php

require_once('../library/class.ordentrabajoimagenes.php');

class ModuleOrdenTrabajoImagenes
{
	function GetById(array $array)
	{
		$OrdenTrabajoImagenes = new OrdenTrabajoImagenes();

		return $OrdenTrabajoImagenes->GetById($array['IdImagen']);
	}


	function GetLastId(array $array)
	{
		$OrdenTrabajoImagenes = new OrdenTrabajoImagenes();

		return $OrdenTrabajoImagenes->GetLastId();
	}

	
	function GetAll(array $array)
	{
		$OrdenTrabajoImagenes 	= new OrdenTrabajoImagenes();
		$oPage 				= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Nombre'] = $array['Filter_Nombre'];
		
		return $OrdenTrabajoImagenes->GetAll($filter, $oPage);
	}


	


	function Update(array $array)
	{
		$OrdenTrabajoImagenes = new OrdenTrabajoImagenes();

		/* obtiene los datos del registro */
		$oOrdenTrabajoImagen = $OrdenTrabajoImagenes->GetById($array['IdImagen']);
		if (!$oOrdenTrabajoImagen)
			return false;
		
		$oOrdenTrabajoImagen->Epigrafe = $array['Epigrafe'];		
		
		return $OrdenTrabajoImagenes->Update($oOrdenTrabajoImagen);
	}


	function Delete(array $array)
	{
		$OrdenTrabajoImagenes = new OrdenTrabajoImagenes();

		/* obtiene los datos del registro */
		$oOrdenTrabajoImagen = $OrdenTrabajoImagenes->GetById($array['IdImagen']);
		if (!$oOrdenTrabajoImagen)
			return false;
		
		return $OrdenTrabajoImagenes->Delete($oOrdenTrabajoImagen->IdImagen);
	}
}

?>