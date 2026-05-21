<?php 

require_once('class.dbaccess.php');
require_once('class.localidad.php');
require_once('class.partidos.php');
require_once('class.provincias.php');
require_once('class.filter.php');
require_once('class.page.php');

class Localidades extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
			$sql.= " AND l.Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";

		if ((isset($filter['CodigoPostal'])) && ($filter['CodigoPostal'] != ''))
			$sql.= " AND l.CodigoPostal LIKE '%" . DB::StringUnquoted($filter['CodigoPostal']) . "%'";

		if ((isset($filter['IdPais'])) && ($filter['IdPais'] != ''))
			$sql.= " AND l.IdPais = " . DB::Number($filter['IdPais']);
		
		if ((isset($filter['IdProvincia'])) && ($filter['IdProvincia'] != ''))
			$sql.= " AND l.IdProvincia = " . DB::Number($filter['IdProvincia']);

		if ((isset($filter['IdPartido'])) && ($filter['IdPartido'] != ''))
			$sql.= " AND l.IdPartido = " . DB::Number($filter['IdPartido']);

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT l.*";
		$sql.= " FROM TB_Localidades l";
		$sql.= " INNER JOIN TB_Partidos d ON l.IdPartido = d.IdPartido";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY d.Nombre, l.Nombre ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oLocalidad = new Localidad();
			$oLocalidad->ParseFromArray($oRow);
			
			array_push($arr, $oLocalidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllSuggest(array $array)
	{
		$arrData		= array();
		$Localidades 	= new Localidades();
		
		$filter = array();		
		$filter['Nombre'] = $array['Nombre'];
		
		$sql = "SELECT l.IdLocalidad, CONCAT(l.Nombre, ', ', d.Nombre, ', ',p.Nombre) AS Nombre";
		$sql.= " FROM TB_Localidades l";
		$sql.= " INNER JOIN TB_Partidos d ON l.IdPartido = d.IdPartido";
		$sql.= " INNER JOIN TB_Provincias p ON p.IdProvincia = d.IdProvincia";		
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY l.Nombre ASC";		
		$sql.= " LIMIT 10";		

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oLocalidad = new Localidad();
			$oLocalidad->ParseFromArray($oRow);
			
			array_push($arr, $oLocalidad);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByPartido(Partido $oPartido)
	{	
		$sql = "SELECT l.*";
		$sql.= " FROM TB_Localidades l";
		$sql.= " WHERE l.IdPartido = " . DB::Number($oPartido->IdPartido);
		$sql.= " ORDER BY IdPartido, l.Nombre ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oLocalidad = new Localidad();
			$oLocalidad->ParseFromArray($oRow);
			
			array_push($arr, $oLocalidad);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	
	public function GetById($IdLocalidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Localidades";
		$sql.= " WHERE IdLocalidad = " . DB::Number($IdLocalidad);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oLocalidad = new Localidad();
		$oLocalidad->ParseFromArray($oRow);
		
		return $oLocalidad;		
	}
	
	
	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Localidades";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oLocalidad = new Localidad();
		$oLocalidad->ParseFromArray($oRow);
		
		return $oLocalidad;		
	}
	

	public function GetByCodigoPostal($CodigoPostal)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Localidades";
		$sql.= " WHERE CodigoPostal RLIKE " . DB::String($Nombre);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oLocalidad = new Localidad();
		$oLocalidad->ParseFromArray($oRow);
		
		return $oLocalidad;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Localidades";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Localidad $oLocalidad)
	{
		$arr = array
		(
			'IdPartido' 	=> DB::Number($oLocalidad->IdPartido),
			'IdProvincia' 	=> DB::Number($oLocalidad->IdProvincia),
			'IdPais' 		=> DB::Number($oLocalidad->IdPais),
			'Nombre' 		=> DB::String($oLocalidad->Nombre),
			'CodigoPostal' 	=> DB::String($oLocalidad->CodigoPostal),
			'Jurisdiccion' 	=> DB::Number($oLocalidad->Jurisdiccion)
		);
		
		return $arr;
	}
	
	
	public function Create(Localidad $oLocalidad)
	{
		$arr = $this->GetArrayDB($oLocalidad);
		
		if (!$this->Insert('TB_Localidades', $arr))
			return false;
			
		/* asignamos el id generado */
		$oLocalidad->IdLocalidad = DBAccess::GetLastInsertId();
			
		return $oLocalidad;
	}
	
	
	public function Update(Localidad $oLocalidad)
	{
		$where = " IdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);
		
		$arr = $this->GetArrayDB($oLocalidad);
		
		if (!DBAccess::Update('TB_Localidades', $arr, $where))
			return false;
		
		return $oLocalidad;
	}
	

	public function Delete($IdLocalidad)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdLocalidad = " . DB::Number($IdLocalidad);

		if (!DBAccess::Delete('TB_Localidades', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>