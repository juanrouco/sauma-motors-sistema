<?

require_once('../library/class.partidos.php');


class ModulePartidos extends Modules
{
	function GetName()
	{
		return "Partidos";
	}


	function GetXMLCommands()
	{
		$Commands = array();

		$Commands[] = 'GetAll';
		
		return $Commands;
	}

	
	function GetAll(array $array)
	{
		$Partidos = new Partidos();
		
		if ($array['CurrentPage'])
			$oPage = new Page($array['CurrentPage']);
		else
			$oPage = NULL;
		
		$filter = array();

		$filter['IdProvincia'] = $array['IdProvincia'];
		
		return $Partidos->GetAll($filter, $oPage);
	}
}

?>