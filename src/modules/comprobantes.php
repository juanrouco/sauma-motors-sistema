<?php

require_once('../library/class.comprobantes.php');


class ModuleComprobantes
{
	function GetNext(array $array)
	{
		$Comprobantes = new Comprobantes();
		
		if ($array['Prefijo'])
			return $Comprobantes->GetNextPrefijo($array['IdTipoComprobante'], $array['Prefijo']);
		return $Comprobantes->GetNext($array['IdTipoComprobante']);
	}
	
	function GetById(array $array)
	{
		$Comprobantes = new Comprobantes();
		
		return $Comprobantes->GetById($array['IdComprobante']);
	}
	
	function GetAll(array $array)
	{
		$Comprobantes = new Comprobantes();
		
		$filter = array();		
		$filter['IdTipoComprobante'] 	= $array['FilterIdTipoComprobante'];
		$filter['NumeroCompleto'] 		= $array['FilterNumero'];
		$filter['IdEstado'] 			= $array['FilterIdEstado'];
		$filter['Prefijo'] 				= $array['Prefijo'];
		
		$arrComprobantes = $Comprobantes->GetAll($filter, NULL);
		
		foreach ($arrComprobantes as $oComprobante)
		{
			$oComprobante->Numero = $oComprobante->Prefijo . '-' . $oComprobante->Numero;
		}
		
		return $arrComprobantes;
	}
}

?>