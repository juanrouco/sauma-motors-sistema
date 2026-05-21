<?php

require_once('../library/class.cuentasgestoria.php');
require_once('../library/class.cuentagestoria.php');


class ModuleCuentasGestoria
{
	function Update(array $array)
	{
		$oCuentasGestoria = new CuentasGestoria();
		if ($oCuentaGestoria = $oCuentasGestoria->GetById($array['IdCuentaGestoria']))
		{
			$oCuentaGestoria->Comentarios = $array['Comentarios'];
			$oCuentaGestoria = $oCuentasGestoria->Update($oCuentaGestoria);
				
			return $oCuentaGestoria;
		}
		
		return false;
	}
}


?>