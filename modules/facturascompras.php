<?php

require_once('../library/class.facturascompras.php');
require_once('../library/class.misc.php');


class ModuleFacturasCompras
{
	function GetById(array $array)
	{
		$oFacturasCompras = new FacturasCompras();

		$oFacturaCompra = $oFacturasCompras->GetById($array['IdFacturaCompra']);
		$oFacturaCompra->Fecha = CambiarFecha($oFacturaCompra->Fecha);
		
		return $oFacturaCompra;
	}

	function GetAll(array $array)
	{
		$oFacturasCompras = new FacturasCompras();
		
		$filter = array();		
		$filter['Numero'] 	= $array['FilterNumero'];
		$filter['IdProveedor'] 	= $array['FilterIdProveedor'];
		
		$oPage 				= new Page(0, 10);		
		return $oFacturasCompras->GetAll($filter, $oPage);
	}
}

?>