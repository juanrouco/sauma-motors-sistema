<?php

require_once('../library/class.modulopermisos.php');


class ModuleModuloPermisos
{
	function GetAll(array $array)
	{
		$ModuloPermisos = new ModuloPermisos();
		
		$filter = array();
		$filter['IdModulo'] = $array['IdModulo'];
		
		return $ModuloPermisos->GetAll($filter, $oPage);
	}
}

?>