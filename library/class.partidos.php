<?php 

require_once('class.dbaccess.php');
require_once('class.partido.php');
require_once('class.provincia.php');
require_once('class.pais.php');
require_once('class.filter.php');
require_once('class.page.php');

class Partidos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
			$sql.= " AND d.Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";

		if ((isset($filter['IdPais'])) && ($filter['IdPais'] != ''))
			$sql.= " AND d.IdPais = " . DB::Number($filter['IdPais']);
		
		if ((isset($filter['IdProvincia'])) && ($filter['IdProvincia'] != ''))
			$sql.= " AND d.IdProvincia = " . DB::Number($filter['IdProvincia']);

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT d.*";
		$sql.= " FROM TB_Partidos d";
		$sql.= " INNER JOIN TB_Provincias p ON d.IdProvincia = p.IdProvincia";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY p.Nombre, Nombre ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oPartido = new Partido();
			$oPartido->ParseFromArray($oRow);
			
			array_push($arr, $oPartido);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByProvincia(Provincia $oProvincia)
	{	
		$sql = "SELECT d.*";
		$sql.= " FROM TB_Partidos d";
		$sql.= " WHERE d.IdProvincia = " . DB::Number($oProvincia->IdProvincia);
		$sql.= " ORDER BY IdProvincia, d.Nombre ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPartido = new Partido();
			$oPartido->ParseFromArray($oRow);
			
			array_push($arr, $oPartido);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	
	public function GetById($IdPartido)
	{
		$sql = "SELECT d.*";
		$sql.= " FROM TB_Partidos d";
		$sql.= " WHERE IdPartido = " . DB::Number($IdPartido);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPartido = new Partido();
		$oPartido->ParseFromArray($oRow);
		
		return $oPartido;		
	}
	
	
	public function GetByNombre($Nombre)
	{
		$sql = "SELECT d.*";
		$sql.= " FROM TB_Partidos d";
		$sql.= " WHERE d.Nombre = " . DB::String($Nombre);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oPartido = new Partido();
		$oPartido->ParseFromArray($oRow);
		
		return $oPartido;		
	}
	
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT d.*";
		$sql.= " FROM TB_Partidos d";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Partido $oPartido)
	{
		$arr = array
		(
			'IdProvincia' 	=> DB::Number($oPartido->IdProvincia),
			'IdPais' 		=> DB::Number($oPartido->IdPais),
			'Nombre' 		=> DB::String($oPartido->Nombre)
		);
		
		if (!$this->Insert('TB_Partidos', $arr))
			return false;
			
		/* asignamos el id generado */
		$oPartido->IdPartido = DBAccess::GetLastInsertId();
			
		return $oPartido;
	}
	
	
	public function Update(Partido $oPartido)
	{
		$where = " IdPartido = " . DB::Number($oPartido->IdPartido);
		
		$arr = array
		(
			'IdProvincia' 	=> DB::Number($oPartido->IdProvincia),
			'IdPais' 		=> DB::Number($oPartido->IdPais),
			'Nombre' 		=> DB::String($oPartido->Nombre)
		);
		
		if (!DBAccess::Update('TB_Partidos', $arr, $where))
			return false;
		
		return $oPartido;
	}
	

	public function Delete($IdPartido)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdPartido = " . DB::Number($IdPartido);

		if (!DBAccess::Delete('TB_Partidos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>