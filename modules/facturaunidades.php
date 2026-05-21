<?php

require_once('../library/class.facturaunidades.php');


class ModuleFacturaUnidades
{
	function GetById(array $array)
	{
		$FacturaUnidades = new FacturaUnidades();

		return $FacturaUnidades->GetById($array['IdFactura']);
	}
}

?>