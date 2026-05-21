<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.turnostareas.php');
require_once('class.articulos.php');
require_once('class.compras.php');
require_once('class.ivas.php');
require_once('class.tipoventa.php');
require_once('class.ordenestrabajo.php');
require_once('class.ordentrabajo.php');
require_once('class.turnoscomentarios.php');
require_once('class.ordentrabajocomentario.php');
require_once('class.ordentrabajocomentarios.php');
require_once('class.turnostareas.php');
require_once('class.ordenestrabajotareas.php');
require_once('class.ordentrabajotarea.php');
require_once('class.ordenestrabajotareasarticulos.php');
require_once('class.ordentrabajotareaarticulo.php');

class Turno
{
	const PathImageBig		= '../_recursos/ordentrabajo/imagenes/big/';
	const PathImageThumb	= '../_recursos/ordentrabajo/imagenes/thumb/';
	const PathFile			= '../_recursos/ordentrabajo/archivos/';
	
	public $IdTurno;
	public $IdEstadoOrden;
	public $IdTallerUnidad;
	public $Fecha;
	public $FechaInicio;
	public $FechaFin;
	public $IdUsuarioCreacion;
	public $IdUsuarioAsignado;
	public $Kilometros;
	public $Comentarios;
	public $IdTipoVenta;
	public $Bahia;
	public $IdOrdenTrabajo;
	public $Remis;
	public $Reconfirmado;
	
	public function __construct()
	{
		$this->IdTurno		 		= '';
		$this->IdEstadoOrden		= '';
		$this->IdTallerUnidad 		= '';
		$this->Fecha 				= '';
		$this->FechaInicio	 		= '';
		$this->FechaFin			 	= '';
		$this->IdUsuarioCreacion	= '';
		$this->IdUsuarioAsignado 	= '';
		$this->Kilometros			= '';
		$this->Comentarios			= '';
		$this->IdTipoVenta			= '';
		$this->Bahia				= '';
		$this->IdOrdenTrabajo		= '';
		$this->Remis				= '';
		$this->Reconfirmado			= '';
	}
	
	
	public function ParseFromArray(array $arr)
	{
		$this->IdTurno		 		= $arr['IdTurno'];
		$this->IdEstadoOrden		= $arr['IdEstadoOrden'];
		$this->IdTallerUnidad		= $arr['IdTallerUnidad'];
		$this->Fecha				= $arr['Fecha'];
		$this->FechaInicio	 		= $arr['FechaInicio'];
		$this->FechaFin				= $arr['FechaFin'];
		$this->IdUsuarioCreacion	= $arr['IdUsuarioCreacion'];
		$this->IdUsuarioAsignado	= $arr['IdUsuarioAsignado'];
		$this->Kilometros			= $arr['Kilometros'];
		$this->Comentarios			= $arr['Comentarios'];
		$this->IdTipoVenta			= $arr['IdTipoVenta'];
		$this->Bahia				= $arr['Bahia'];
		$this->IdOrdenTrabajo 		= $arr['IdOrdenTrabajo'];
		$this->Remis		 		= $arr['Remis'];
		$this->Reconfirmado	 		= $arr['Reconfirmado'];
	}
		
	public function ImporteEstimado()
	{
		$oTurnosTareas 	= new TurnosTareas();
		
		$arrTurnosTareas = $oTurnosTareas->GetAllByTurno($this);
		
		$total = 0;
		foreach ($arrTurnosTareas as $oTurnoTarea)
		{
			if ($oTurnoTarea->IdTipoVenta == TipoVenta::OrdenReparacion || $oTurnoTarea->IdTipoVenta == TipoVenta::ChapaYPintura || $oTurnoTarea->IdTipoVenta == TipoVenta::Accesorios)
				$total += $oTurnoTarea->Importe;
		}
		
		return number_format($total, 2);
	}
	
	public function GetAllComentarios()
	{
		$oTurnosComentarios = new TurnosComentarios();
		
		return $oTurnosComentarios->GetByIdTurno($this->IdTurno);
	}
	
	public function GetAllTareas()
	{
		$oTurnosTareas = new TurnosTareas();
		
		return $oTurnosTareas->GetAllByTurno($this);
	}
	
	public function GenerarOrdenTrabajo()
	{
		$oOrdenesTrabajo				= new OrdenesTrabajo();
		$oOrdenTrabajoComentarios		= new OrdenTrabajoComentarios();
		$oOrdenesTrabajoTareas			= new OrdenesTrabajoTareas();
		$oOrdenesTrabajoTareasArticulos	= new OrdenesTrabajoTareasArticulos();
		
		$oOrdenTrabajo = new OrdenTrabajo();
		
		$oOrdenTrabajo->IdEstadoOrden 		= EstadoOrden::Aceptada;
		$oOrdenTrabajo->IdTallerUnidad		= $this->IdTallerUnidad;
		$oOrdenTrabajo->Fecha				= date('d-m-Y H:i:s');
		$oOrdenTrabajo->FechaInicio			= date('d-m-Y H:i:s');
		$oOrdenTrabajo->FechaFin			= $this->FechaFin;
		$oOrdenTrabajo->IdUsuarioCreacion	= $this->IdUsuarioCreacion;
		$oOrdenTrabajo->IdUsuarioAsignado	= $this->IdUsuarioAsignado;
		$oOrdenTrabajo->Kilometros			= $this->Kilometros;
		$oOrdenTrabajo->Comentarios			= $this->Comentarios;
		$oOrdenTrabajo->IdTipoVenta			= $this->IdTipoVenta;
		$oOrdenTrabajo->Bahia				= $this->Bahia;
		
		if ($oOrdenTrabajo = $oOrdenesTrabajo->Create($oOrdenTrabajo))
		{
			$arrComentarios = $this->GetAllComentarios();
			
			if ($arrComentarios)
			{
				foreach ($arrComentarios as $oTurnoComentario)
				{
					$oOrdenTrabajoComentario = new OrdenTrabajoComentario();
					$oOrdenTrabajoComentario->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
					$oOrdenTrabajoComentario->Comentarios = $oTurnoComentario->Comentarios;
					$oOrdenTrabajoComentario->IdUsuario = $oTurnoComentario->IdUsuario;
					$oOrdenTrabajoComentario->IdTipoRechazo = $oTurnoComentario->IdTipoRechazo;
					
					$oOrdenTrabajoComentarios->Create($oOrdenTrabajoComentario);
				}
			}
			
			$arrTareas = $this->GetAllTareas();
			
			if ($arrTareas)
			{
				foreach ($arrTareas as $oTurnoTarea)
				{
					$oOrdenTrabajoTarea = new OrdenTrabajoTarea();
					$oOrdenTrabajoTarea->IdOrdenTrabajo = $oOrdenTrabajo->IdOrdenTrabajo;
					$oOrdenTrabajoTarea->Importe = $oTurnoTarea->Importe;
					$oOrdenTrabajoTarea->Titulo = $oTurnoTarea->Titulo;
					$oOrdenTrabajoTarea->Descripcion = $oTurnoTarea->Descripcion;
					$oOrdenTrabajoTarea->HorasEstimadas = $oTurnoTarea->HorasEstimadas;
					$oOrdenTrabajoTarea->IdTareaTrabajo = $oTurnoTarea->IdTareaTrabajo;
					$oOrdenTrabajoTarea->IdTipoVenta = $oTurnoTarea->IdTipoVenta;
					$oOrdenTrabajoTarea->IdEstado = $oTurnoTarea->IdEstado;
					$oOrdenTrabajoTarea->IdCodigoTrabajo = $oTurnoTarea->IdCodigoTrabajo;
					$oOrdenTrabajoTarea->IdFacturaCompra = $oTurnoTarea->IdFacturaCompra;
					$oOrdenTrabajoTarea->IdCategoria = Categorias::Taller;
					
					if ($oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->Create($oOrdenTrabajoTarea))
					{
						$arrArticulos = $oTurnoTarea->GetAllArticulos();
						
						if ($arrArticulos)
						{
							foreach ($arrArticulos as $oTurnoTareaArticulo)
							{
								$oOrdenTrabajoTareaArticulo = new OrdenTrabajoTareaArticulo();
								$oOrdenTrabajoTareaArticulo->IdOrdenTrabajoTarea = $oOrdenTrabajoTarea->IdOrdenTrabajoTarea;
								$oOrdenTrabajoTareaArticulo->IdArticulo = $oTurnoTareaArticulo->IdArticulo;
								$oOrdenTrabajoTareaArticulo->Cantidad = $oTurnoTareaArticulo->Cantidad;
								$oOrdenTrabajoTareaArticulo->PrecioTotal = $oTurnoTareaArticulo->PrecioTotal;
								
								$oOrdenesTrabajoTareasArticulos->Create($oOrdenTrabajoTareaArticulo);
							}
						}
					}
				}
			}
		}
		
		return $oOrdenTrabajo;
	}
}

?>