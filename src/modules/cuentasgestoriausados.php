<?php

require_once('../library/class.cuentasgestoriausados.php');


class ModuleCuentasGestoriaUsados
{
	function Update(array $array)
	{
		$oCuentasGestoria = new CuentasGestoriaUsados();
		/*if ($oCuentaGestoria = $oCuentasGestoria->GetById($array['IdCuentaGestoria']))
		{
			$oCuentaGestoria->Comentarios = $array['Comentarios'];
			$oCuentaGestoria = $oCuentasGestoria->Update($oCuentaGestoria);
				
			return true;
		}*/
		
		return true;
	}
}


?>