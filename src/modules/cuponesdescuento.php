<?php

require_once('../library/class.cuponesdescuento.php');

class ModuleCuponesDescuento
{	
	function GetAll(array $array)
	{
		$CuponesDescuento = new CuponesDescuento();

		$filter = array();		
		$filter['Numero'] 			= $array['FilterNumero'];
		
		return $CuponesDescuento->GetAll($filter, NULL);
	}


	function GetById(array $array)
	{
		$CuponesDescuento = new CuponesDescuento();

		return $CuponesDescuento->GetById($array['IdCuponDescuento']);
	}
	
	function GetByNumero(array $array)
	{
		$CuponesDescuento = new CuponesDescuento();

		$oCupon = $CuponesDescuento->GetByNumero($array['Numero']);
		
		if (!$oCupon || $oCupon->IdEstado != ComprobanteEstados::Libre)
			return false;
		return $oCupon;
	}
}

?>