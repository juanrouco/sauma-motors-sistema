<?php

require_once('../library/class.cuentasgestoria.php');


class ModuleGestorias
{
	function GetByIdMinuta(array $array)
	{
		$CuentasGestoria = new CuentasGestoria();

		return $CuentasGestoria->GetByIdMinuta($array['IdMinuta']);
	}
}


?>