<?php

require_once('class.db.php');
require_once('class.dbaccess.php');

class OrdenTrabajoHito
{
	public $IdOrdenTrabajoHito;
	public $IdOrdenTrabajo;
	public $IdOrdenTrabajoTarea;
	public $IdUsuario;
	public $FechaHora;
	public $TipoHito;
	public $FechaHoraFin;
	public $Tiempo;
	
	const Iniciar 			= 1;
	const Detener 			= 2;
	const Finalizar 		= 3;
	const FinalizarSistema 	= 4;
	
	static function GetById($IdTipoHito)
	{
		if (OrdenTrabajoHito::Iniciar == $IdTipoHito)
			return 'Iniciado';
		if (OrdenTrabajoHito::Detener == $IdTipoHito)
			return 'Detenido';
		if (OrdenTrabajoHito::Finalizar == $IdTipoHito)
			return 'Finalizado';
		if (OrdenTrabajoHito::FinalizarSistema == $IdTipoHito)
			return 'Finalizado x sistema';
	}
	
	public function __construct()
	{
		$this->IdOrdenTrabajoHito 	= '';
		$this->IdOrdenTrabajo	 	= '';
		$this->IdOrdenTrabajoTarea 	= '';
		$this->IdUsuario		 	= '';
		$this->FechaHora		 	= '';
		$this->TipoHito			 	= '';
		$this->FechaHoraFin		 	= '';
		$this->Tiempo			 	= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdOrdenTrabajoHito 	= $arr['IdOrdenTrabajoHito'];
		$this->IdOrdenTrabajo	 	= $arr['IdOrdenTrabajo'];
		$this->IdOrdenTrabajoTarea 	= $arr['IdOrdenTrabajoTarea'];
		$this->IdUsuario		 	= $arr['IdUsuario'];
		$this->FechaHora		 	= $arr['FechaHora'];
		$this->TipoHito			 	= $arr['TipoHito'];
		$this->FechaHoraFin		 	= $arr['FechaHoraFin'];
		$this->Tiempo			 	= $arr['Tiempo'];
	}
	
	public function GetTipoHito()
	{
		if ($this->TipoHito == OrdenTrabajoHito::Iniciar)
			return 'Iniciar';
		if ($this->TipoHito == OrdenTrabajoHito::Detener)
			return 'Detener';
		if ($this->TipoHito == OrdenTrabajoHito::Finalizar)
			return 'Finalizar';
		if ($this->TipoHito == OrdenTrabajoHito::FinalizarSistema)
			return 'Finalizada Sistema';
	}
}

?>