<?php 
//require_once('ssi_errores.php'); 
require_once('../inc_library_includes.php'); 

Session::ForceLogin();

if (!Session::CheckPerm(PERM_TALL_REPORTES))
	Session::NoPerm();

$filter = array();
$filter['FechaDesde'] 		= $_REQUEST['FilterFechaDesde'];
$filter['FechaHasta']		= $_REQUEST['FilterFechaHasta'];
$filter['IdUsuario']		= $_REQUEST['IdUsuario'];

$filterStyle = "display:none;";
$filterMostrar = "";

if (!isset($filter['FechaHasta']) || $filter['FechaHasta'] == '')
{
	$filter['FechaHasta'] = date('d-m-Y');
}

if (!isset($filter['FechaDesde']) || $filter['FechaDesde'] == '')
{
	$filter['FechaDesde'] = date('d-m-Y', strtotime("-7 days"));
}

if ($filter['FechaDesde'] != '' || $filter['FechaHasta'] != '' || $filter['IdUbicacion'] != '')
{
	$filterStyle = "";
	$filterMostrar = "display:none;";
}



$arr = array();

$oUbicaciones 			= new Ubicaciones();
$oUsuarioJornadas		= new UsuarioJornadas();
$oOrdenesTrabajo		= new OrdenesTrabajo();
$oUsuarios				= new Usuarios();
$oCostosManoObra		= new CostosManoObra();
$oOrdenesTrabajoTareas	= new OrdenesTrabajoTareas();

$arrUbicaciones 	= $oUbicaciones->GetAll();

//$HorasTotales	= $oUsuarioJornadas->GetHorasEntreFechas($filter['FechaDesde'], $filter['FechaHasta']);

//$HorasAsignadas	= $oOrdenesTrabajo->GetHorasEntreFechas($filter['FechaDesde'], $filter['FechaHasta']);

$oUsuario = $oUsuarios->GetById($filter['IdUsuario']);
$arrOrdenesTrabajoHitos = $oOrdenesTrabajo->GetReporteHorasDetalle($filter['FechaDesde'], $filter['FechaHasta'], $filter['IdUsuario']);

$FileName = "horas.xls";
		
header("Pragma: no-cache");
header("Expires: -1");
header("Cache-Control: no-store, no-cache, must-revalidate");		
header("Content-Type: application/x-unknown");
$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
header($header);

$Separador 	= "\t";
$SaltoLinea = "\n";

$csv.= "Inicio";
$csv.= $Separador;
$csv.= "Mecanico";
$csv.= $Separador;
$csv.= "N OT";
$csv.= $Separador;
$csv.= "Tarea";
$csv.= $Separador;
$csv.= "Sector";
$csv.= $Separador;
$csv.= "Estado";
$csv.= $Separador;
$csv.= "TareaHoras";
$csv.= $SaltoLinea;

if ($arrOrdenesTrabajoHitos)
{
	foreach ($arrOrdenesTrabajoHitos as $oOrdenTrabajoHito)
	{
		$oOrdenTrabajo = $oOrdenesTrabajo->GetById($oOrdenTrabajoHito->IdOrdenTrabajo);
		$oOrdenTrabajoTarea = $oOrdenesTrabajoTareas->GetByIdIncrement($oOrdenTrabajoHito->IdOrdenTrabajoTarea);
		$oCategoria = Categorias::GetById($oOrdenTrabajoTarea->IdCategoria);
		
		$csv.= str_replace('(\t|\n)','', trim(CambiarFechaHora($oOrdenTrabajoHito->FechaHora)));
		$csv.= $Separador;
		$csv.= str_replace('(\t|\n)','', trim($oUsuario->IdUsuario . ' - ' .$oUsuario->Nombre . ' ' . $oUsuario->Apellido ));
		$csv.= $Separador;
		$csv.= str_replace('(\t|\n)','', trim($oOrdenTrabajo->IdOrdenTrabajo));
		$csv.= $Separador;
		$csv.= str_replace('(\t|\n)','', trim($oOrdenTrabajoTarea->Titulo));
		$csv.= $Separador;
		$csv.= str_replace('(\t|\n)','', trim($oCategoria['Nombre']));
		$csv.= $Separador;
		$csv.= str_replace('(\t|\n)','', trim(OrdenTrabajoHito::GetById($oOrdenTrabajoHito->TipoHito)));
		$csv.= $Separador;
		$csv.= str_replace('(\t|\n)','', trim($oOrdenTrabajoHito->Tiempo));
		$csv.= $SaltoLinea;
	}
}
print_r($csv);exit;
?>