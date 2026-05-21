<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class ConceptoFactura
{
	public $IdConceptoFactura;
	public $Nombre;
	public $IvaGravado;
	
	
	public function __construct()
	{
		$this->IdConceptoFactura 	= '';
		$this->Nombre				= '';
		$this->IvaGravado 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdConceptoFactura 	= $arr['IdConceptoFactura'];
		$this->Nombre 				= $arr['Nombre'];
		$this->IvaGravado 			= $arr['IvaGravado'];
	}
}

?>