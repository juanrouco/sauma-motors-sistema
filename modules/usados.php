<?php

require_once('../library/class.usados.php');


class ModuleUsados
{
	function GetById(array $array)
	{
		$Usados = new Usados();

		return $Usados->GetById($array['IdUsado']);
	}
}


?>