<?php

require_once('../library/class.comisiones.php');
require_once('../library/class.comision.php');


class ModuleComisiones
{
	function Update(array $array)
	{
		$IndiceComision = floatval($array['IndiceComision']);
		
		if ($IndiceComision != 0 || $array['IndiceComision'] == '0')
		{
			$oComisiones = new Comisiones();

			$crear = true;
			
			if ($oComision = $oComisiones->GetByIdMinuta($array['IdMinuta']))
			{
				$crear = false;
			}
			else
			{
				$oComision = new Comision();
			}
			
			$oComision->IdMinuta = $array['IdMinuta'];
			$oComision->IndiceComision = $array['IndiceComision'];
			
			if ($crear)
				$oComision = $oComisiones->Create($oComision);
			else
				$oComision = $oComisiones->Update($oComision);
				
			return $oComision;
		}
		return false;
	}
}


?>