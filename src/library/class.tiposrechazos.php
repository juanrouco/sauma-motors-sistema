<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TiposRechazos
{
	const Ausente	 		= 1;
	const CostoAlto		 	= 2;
	const Reprogramacion 	= 3;
	const Otro			 	= 4;
	
	public static function GetAll()
	{
		$arr = array(
			array('IdTipoRechazo' => TiposRechazos::Ausente, 'Nombre' => 'No se present&oacute;'),
			array('IdTipoRechazo' => TiposRechazos::CostoAlto, 'Nombre' => 'Costo alto'),
			array('IdTipoRechazo' => TiposRechazos::Reprogramacion, 'Nombre' => 'Reprogramaci&oacute;n'),
			array('IdTipoRechazo' => TiposRechazos::Otro, 'Nombre' => 'Otro')
		);
		return $arr;
	}
	
	public static function GetById($IdTipoRechazo)
	{
		foreach (TiposRechazos::GetAll() as $oTipoRechazo)
		{
			if ($IdTipoRechazo == $oTipoRechazo['IdTipoRechazo'])
				return $oTipoRechazo;
		}
		return false;
	}
}

?>