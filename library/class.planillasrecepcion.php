<?php 

require_once('class.dbaccess.php');
require_once('class.planillarecepcion.php');
require_once('class.ubicacion.php');
require_once('class.filter.php');
require_once('class.page.php');

class PlanillasRecepcion extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdPlanillaRecepcion'])) && ($filter['IdPlanillaRecepcion'] != ''))
			$sql.= " AND IdPlanillaRecepcion = " . DB::Number($filter['IdPlanillaRecepcion']);
		
		if ((isset($filter['NumeroCartaPorte'])) && ($filter['NumeroCartaPorte'] != ''))
			$sql.= " AND NumeroCartaPorte LIKE '%" . DB::StringUnquoted($filter['NumeroCartaPorte']) . "%'";

		if ((isset($filter['FechaRecepcion'])) && ($filter['FechaRecepcion'] != ''))
			$sql.= " AND FechaRecepcion = " . DB::Date($filter['FechaRecepcion']);
			
		if ((isset($filter['IdUnidad'])) && ($filter['IdUnidad'] != ''))
			$sql.= " AND IdPlanillaRecepcion IN (SELECT IdPlanillaRecepcion FROM TB_Unidades WHERE IdUnidad = " . DB::Number($filter['IdUnidad']) . ")";


		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasRecepcion";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdPlanillaRecepcion DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oPlanillaRecepcion = new PlanillaRecepcion();
			$oPlanillaRecepcion->ParseFromArray($oRow);
			
			array_push($arr, $oPlanillaRecepcion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdPlanillaRecepcion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasRecepcion";
		$sql.= " WHERE IdPlanillaRecepcion = " . DB::Number($IdPlanillaRecepcion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPlanillaRecepcion = new PlanillaRecepcion();
		$oPlanillaRecepcion->ParseFromArray($oRow);
		
		return $oPlanillaRecepcion;		
	}
	

	public function GetByNumeroCartaPorte($NumeroCartaPorte)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasRecepcion";
		$sql.= " WHERE NumeroCartaPorte = " . DB::String($NumeroCartaPorte);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPlanillaRecepcion = new PlanillaRecepcion();
		$oPlanillaRecepcion->ParseFromArray($oRow);
		
		return $oPlanillaRecepcion;		
	}


	public function GetNextId()
	{
		$sql = "SELECT MAX(IdPlanillaRecepcion) AS IdPlanillaRecepcion";
		$sql.= " FROM TB_PlanillasRecepcion";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$IdPlanillaRecepcion = $oRow['IdPlanillaRecepcion'];
		$IdPlanillaRecepcion++;
		
		return $IdPlanillaRecepcion;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_PlanillasRecepcion";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(PlanillaRecepcion $oPlanillaRecepcion)
	{
		$arr = array
		(
			'NumeroCartaPorte' 	=> DB::String($oPlanillaRecepcion->NumeroCartaPorte),
			'FechaRecepcion' 	=> DB::Date($oPlanillaRecepcion->FechaRecepcion),
			'Observaciones' 	=> DB::String($oPlanillaRecepcion->Observaciones),
			'IdEstado' 			=> DB::Number($oPlanillaRecepcion->IdEstado)
		);
		
		if (!$this->Insert('TB_PlanillasRecepcion', $arr))
			return false;

		/* asignamos el id generado */
		$oPlanillaRecepcion->IdPlanillaRecepcion = DBAccess::GetLastInsertId();
			
		return $oPlanillaRecepcion;
	}
	
	
	public function Update(PlanillaRecepcion $oPlanillaRecepcion)
	{
		$where = " IdPlanillaRecepcion = " . DB::Number($oPlanillaRecepcion->IdPlanillaRecepcion);
		
		$arr = array
		(
			'NumeroCartaPorte' 	=> DB::String($oPlanillaRecepcion->NumeroCartaPorte),
			'FechaRecepcion' 	=> DB::Date($oPlanillaRecepcion->FechaRecepcion),
			'Observaciones' 	=> DB::String($oPlanillaRecepcion->Observaciones),
			'IdEstado' 			=> DB::Number($oPlanillaRecepcion->IdEstado)
		);
		
		if (!DBAccess::Update('TB_PlanillasRecepcion', $arr, $where))
			return false;
		
		return $oPlanillaRecepcion;
	}
	

	public function Delete($IdPlanillaRecepcion)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPlanillaRecepcion = " . DB::Number($IdPlanillaRecepcion);

		/* actualizamos las unidades asociadas */
		$arr = array
		(
			'IdPlanillaRecepcion' 	=> '',
			'CodigoLlaves' 			=> '',
			'IdUbicacion' 			=> Ubicacion::Transito
		);
		
		if (!DBAccess::Update('TB_Unidades', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_PlanillasRecepcion', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>