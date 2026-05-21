<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class TipoFormulario
{
	public $IdTipoFormulario;
	public $IdJurisdiccion;
	public $IdOrigen;
	public $Nombre;
	public $Descripcion;
	public $Longitud;
	public $CantidadCopias;
	public $Repositorio;
	public $DeclaracionJurada;
	public $CedulaAzul;
	
	const Formulario01Nacional			= 1;
	const Formulario01Importado			= 2;
	const TituloAutomotor				= 3;
	const Formulario12					= 4;
	const Formulario13ACapital			= 5;
	const Formulario13AProvincia		= 6;
	const Formulario03					= 7;
	const ContratoPrenda				= 8;
	const ContratoPrendaStandardBank	= 9;
	const Formulario02					= 10;

	
	public function __construct()
	{
		$this->IdTipoFormulario 	= '';
		$this->IdJurisdiccion		= '';
		$this->IdOrigen				= '';
		$this->Nombre 				= '';
		$this->Descripcion 			= '';
		$this->Longitud 			= '';
		$this->CantidadCopias 		= '';
		$this->Repositorio 			= '';
		$this->DeclaracionJurada 	= '';
		$this->CedulaAzul 			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTipoFormulario 	= $arr['IdTipoFormulario'];
		$this->IdJurisdiccion		= $arr['IdJurisdiccion'];
		$this->IdOrigen				= $arr['IdOrigen'];
		$this->Nombre 				= stripslashes($arr['Nombre']);
		$this->Descripcion 			= stripslashes($arr['Descripcion']);
		$this->Longitud 			= $arr['Longitud'];
		$this->CantidadCopias 		= $arr['CantidadCopias'];
		$this->Repositorio 			= (ord($arr['Repositorio']) == 1);
		$this->DeclaracionJurada 	= (ord($arr['DeclaracionJurada']) == 1);
		$this->CedulaAzul 			= (ord($arr['CedulaAzul']) == 1);
	}
}

?>