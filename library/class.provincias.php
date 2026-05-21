<?php 

require_once('class.dbaccess.php');
require_once('class.provincia.php');
require_once('class.pais.php');
require_once('class.filter.php');
require_once('class.page.php');

class Provincias extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
		{
			$sql.= " AND ( pr.Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
			$sql.= " OR pr.Nombre IS NULL)";
		}
		
		if ((isset($filter['IdPais'])) && ($filter['IdPais'] != ''))
			$sql.= " AND pr.IdPais = " . DB::Number($filter['IdPais']);

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " INNER JOIN TB_Paises p ON pr.IdPais = p.IdPais";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY p.Nombre, Nombre ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
		
		while ($oRow = $oRes->GetRow())	
		{	
			$oProvincia = new Provincia();
			$oProvincia->ParseFromArray($oRow);
			
			array_push($arr, $oProvincia);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByPais(Pais $oPais)
	{	
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " WHERE pr.IdPais = " . DB::Number($oPais->IdPais);
		$sql.= " ORDER BY IdPais, pr.Nombre ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oProvincia = new Provincia();
			$oProvincia->ParseFromArray($oRow);
			
			array_push($arr, $oProvincia);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	
	public function GetAllUsedByPais(array $filter)
	{
		$arr = array();
	
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " LEFT JOIN TB_Usuarios u ON u.IdProvincia = pr.IdProvincia";
		$sql.= " LEFT JOIN TB_UsuariosNewsletter un ON un.IdProvincia = pr.IdProvincia";
		$sql.= " WHERE (u.IdProvincia IS NOT NULL";
		$sql.= " OR un.IdProvincia IS NOT NULL)";
		
		if ($filter['IdPais'] != '')
			$sql.= " AND pr.IdPais = " . DB::Number($filter['IdPais']);
			
		$sql.= " GROUP BY pr.IdProvincia";
		$sql.= " ORDER BY pr.Nombre ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oProvincia = new Provincia();
			$oProvincia->ParseFromArray($oRow);
			
			array_push($arr, $oProvincia);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}


	public function GetById($IdProvincia)
	{
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " WHERE IdProvincia = " . DB::Number($IdProvincia);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oProvincia = new Provincia();
		$oProvincia->ParseFromArray($oRow);
		
		return $oProvincia;		
	}
	
	
	public function GetByNombre($Nombre)
	{
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " WHERE pr.Nombre RLIKE " . DB::String($Nombre);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oProvincia = new Provincia();
		$oProvincia->ParseFromArray($oRow);
		
		return $oProvincia;		
	}
	
	public function GetByCodigo($Codigo)
	{
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " WHERE pr.Codigo = " . DB::String($Codigo);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oProvincia = new Provincia();
		$oProvincia->ParseFromArray($oRow);
		
		return $oProvincia;		
	}
	
	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT pr.*";
		$sql.= " FROM TB_Provincias pr";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Provincia $oProvincia)
	{
		$arr = array
		(
			'IdPais' => DB::Number($oProvincia->IdPais),
			'Nombre' => DB::String($oProvincia->Nombre),
			'Codigo' => DB::String($oProvincia->Codigo)
		);
		
		if (!$this->Insert('TB_Provincias', $arr))
			return false;
			
		/* asignamos el id generado */
		$oProvincia->IdProvincia = DBAccess::GetLastInsertId();
			
		return $oProvincia;
	}
	
	
	public function Update(Provincia $oProvincia)
	{
		$where = " IdProvincia = " . DB::Number($oProvincia->IdProvincia);
		
		$arr = array
		(
			'IdPais' => DB::Number($oProvincia->IdPais),
			'Nombre' => DB::String($oProvincia->Nombre),
			'Codigo' => DB::String($oProvincia->Codigo)
		);
		
		if (!DBAccess::Update('TB_Provincias', $arr, $where))
			return false;
		
		return $oProvincia;
	}
	

	public function Delete($IdProvincia)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdProvincia = " . DB::Number($IdProvincia);

		if (!DBAccess::Delete('TB_Provincias', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>