<?php

require_once('../library/class.modeloimagenes.php');

class ModuleModeloImagenes
{
	function GetById(array $array)
	{
		$ModeloImagenes = new ModeloImagenes();

		return $ModeloImagenes->GetById($array['IdImagen']);
	}


	function GetLastId(array $array)
	{
		$ModeloImagenes = new ModeloImagenes();

		return $ModeloImagenes->GetLastId();
	}

	
	function GetAll(array $array)
	{
		$ModeloImagenes 	= new ModeloImagenes();
		$oPage 				= new Page($array['CurrentPage']);
		
		$filter = array();		
		$filter['Nombre'] = $array['Filter_Nombre'];
		
		return $ModeloImagenes->GetAll($filter, $oPage);
	}


	function Create(array $array)
	{
		$ModeloImagenes	= new ModeloImagenes();
		$oModeloImagen	= new ModeloImagen();

		$oModeloImagen->IdModelo	= $array['IdModelo'];
		$oModeloImagen->Imagen 		= $array['Imagen'];
		$oModeloImagen->Epigrafe 		= $array['Epigrafe'];
			
		$oModeloImagen = $ModeloImagenes->Create($oModeloImagen);
		if (!$oModeloImagen)
			return false;
		
		return $oModeloImagen;
	}


	function Update(array $array)
	{
		$ModeloImagenes = new ModeloImagenes();

		/* obtiene los datos del registro */
		$oModeloImagen = $ModeloImagenes->GetById($array['IdImagen']);
		if (!$oModeloImagen)
			return false;
		
		$oModeloImagen->Epigrafe = $array['Epigrafe'];
		
		return $ModeloImagenes->Update($oModeloImagen);
	}


	function Delete(array $array)
	{
		$ModeloImagenes = new ModeloImagenes();

		/* obtiene los datos del registro */
		$oModeloImagen = $ModeloImagenes->GetById($array['IdImagen']);
		if (!$oModeloImagen)
			return false;
		
		return $ModeloImagenes->Delete($oModeloImagen->IdImagen);
	}
}

?>