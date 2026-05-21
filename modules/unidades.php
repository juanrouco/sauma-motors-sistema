<?php

require_once('../library/class.unidades.php');
require_once('../library/class.ubicacion.php');

class ModuleUnidades
{
	function GetById(array $array)
	{
		$Unidades = new Unidades();

		return $Unidades->GetById($array['IdUnidad']);
	}
		
	function GetAll(array $array)
	{
		$Unidades = new Unidades();
		
		$filter = array();		
		$filter['IdUnidad'] 	= $array['FilterIdUnidad'];
		$filter['CodigoComercial'] 	= $array['FilterCodigoComercial'];
		$filter['NumeroVinPrefijo'] = $array['FilterNumeroVinPrefijo'];
		$filter['NumeroVin'] 		= $array['FilterNumeroVin'];
		$filter['NumeroChasis'] 	= $array['FilterNumeroChasis'];
		$filter['IdModelo'] 		= $array['FilterIdModelo'];
		$filter['IdUbicacion'] 		= $array['FilterIdUbicacion'];
		$filter['IdEstado'] 		= $array['FilterIdEstado'];
		
		$oPage 				= new Page(0, 10);

		$arr = $Unidades->GetAll($filter, $oPage);
		$arrResult = array();
		
		foreach($arr as $oUnidad)
		{
			$oUnidad->Comentarios = '';
			array_push($arrResult, $oUnidad);
		}
			return $arrResult;
	}
	
	function GetAllTransito(array $array)
	{
		$Unidades = new Unidades();
		
		$filter = array();		
		$filter['CodigoComercial'] 	= $array['FilterCodigoComercial'];
		$filter['NumeroVinPrefijo'] = $array['FilterNumeroVinPrefijo'];
		$filter['NumeroVin'] 		= $array['FilterNumeroVin'];
		$filter['NumeroChasis'] 	= $array['FilterNumeroChasis'];
		$filter['IdModelo'] 		= $array['FilterIdModelo'];
		$filter['IdUbicacion'] 		= Ubicacion::Transito;
		$filter['IdEstado'] 		= $array['FilterIdEstado'];
		
		$oPage 				= new Page(0, 10);

		return $Unidades->GetAll($filter, $oPage);
	}
	
	function GetByNumeroVin(array $array)
	{
		$Unidades = new Unidades();

		return $Unidades->GetByNumeroVin($array['FilterNumeroVin']);
	}
	
	function GetByNumeroChasis(array $array)
	{
		$Unidades = new Unidades();

		return $Unidades->GetByNumeroChasis($array['FilterNumeroChasis']);
	}
	
	function UpdateChecks(array $array)
	{
		$oUnidades = new Unidades();
		$oUnidad = $oUnidades->GetById($array['IdUnidad']);
		
		$oUnidad->Cancelada 	= $array['Cancelada'];
		$oUnidad->Verificado 	= $array['Verificado'];
		$oUnidad->Certificado 	= $array['Certificado'];
		$oUnidad->Lavado 		= $array['Lavado'];
		
		return $oUnidades->Update($oUnidad);
	}
}

?>