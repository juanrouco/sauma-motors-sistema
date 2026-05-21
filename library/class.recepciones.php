<?php 

require_once('class.dbaccess.php');
require_once('class.recepcion.php');
require_once('class.filter.php');
require_once('class.page.php');

class Recepciones extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdRecepcion'])) && ($filter['IdRecepcion'] != ''))
			$sql.= " AND IdRecepcion = " . DB::Number($filter['IdRecepcion']);
		
		if ((isset($filter['NumeroCartaPorte'])) && ($filter['NumeroCartaPorte'] != ''))
			$sql.= " AND NumeroCartaPorte LIKE '%" . DB::StringUnquoted($filter['NumeroCartaPorte']) . "%'";

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Recepciones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdRecepcion DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oRecepcion = new Recepcion();
			$oRecepcion->ParseFromArray($oRow);
			
			array_push($arr, $oRecepcion);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdRecepcion)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Recepciones";
		$sql.= " WHERE IdRecepcion = " . DB::Number($IdRecepcion);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRecepcion = new Recepcion();
		$oRecepcion->ParseFromArray($oRow);
		
		return $oRecepcion;		
	}
	

	public function GetByNumeroCartaPorte($NumeroCartaPorte)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Recepciones";
		$sql.= " WHERE NumeroCartaPorte = " . DB::String($NumeroCartaPorte);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oRecepcion = new Recepcion();
		$oRecepcion->ParseFromArray($oRow);
		
		return $oRecepcion;		
	}


	public function GetNextId()
	{
		$sql = "SELECT MAX(IdRecepcion) AS IdRecepcion";
		$sql.= " FROM TB_Recepciones";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$IdRecepcion = $oRow['IdRecepcion'];
		$IdRecepcion++;
		
		return $IdRecepcion;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Recepciones";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Recepcion $oRecepcion)
	{
		$arr = array
		(
			'NumeroCartaPorte' 	=> DB::String($oRecepcion->NumeroCartaPorte),
			'FechaRecepcion' 	=> DB::Date($oRecepcion->FechaRecepcion),
			'Observaciones' 	=> DB::String($oRecepcion->Observaciones),
			'IdEstado' 			=> DB::Number($oRecepcion->IdEstado)
		);
		
		if (!$this->Insert('TB_Recepciones', $arr))
			return false;

		/* asignamos el id generado */
		$oRecepcion->IdRecepcion = DBAccess::GetLastInsertId();
			
		return $oRecepcion;
	}
	
	
	public function Update(Recepcion $oRecepcion)
	{
		$where = " IdRecepcion = " . DB::Number($oRecepcion->IdRecepcion);
		
		$arr = array
		(
			'NumeroCartaPorte' 	=> DB::String($oRecepcion->NumeroCartaPorte),
			'FechaRecepcion' 	=> DB::Date($oRecepcion->FechaRecepcion),
			'Observaciones' 	=> DB::String($oRecepcion->Observaciones),
			'IdEstado' 			=> DB::Number($oRecepcion->IdEstado)
		);
		
		if (!DBAccess::Update('TB_Recepciones', $arr, $where))
			return false;
		
		return $oRecepcion;
	}
	

	public function Delete($IdRecepcion)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdRecepcion = " . DB::Number($IdRecepcion);

		if (!DBAccess::Delete('TB_RecepcionDetalles', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_Recepciones', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>