<?php

class UnidadArchivo
{
	public $IdUnidadArchivo;
	public $IdUnidad;
	public $Nombre;
	public $Archivo;
	public $Certificado;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdUnidadArchivo 	= $arr['IdUnidadArchivo'];
		$this->IdUnidad		 	= $arr['IdUnidad'];
		$this->Nombre 			= stripslashes($arr['Nombre']);
		$this->Archivo 			= stripslashes($arr['Archivo']);
		$this->Certificado	 	= $arr['Certificado'];
	}
}

?>