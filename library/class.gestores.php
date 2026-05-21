<?php 

require_once('class.dbaccess.php');
require_once('class.gestor.php');
require_once('class.perfil.php');
require_once('class.filter.php');
require_once('class.page.php');

class Gestores extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['RazonSocial'])) && ($filter['RazonSocial'] != ''))
			$sql.= " AND RazonSocial LIKE '%" . DB::StringUnquoted($filter['RazonSocial']) . "%'";

		return $sql;
	}	
	
	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Gestores";
		$sql.= " WHERE Disponible = 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY RazonSocial";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oGestor = new Gestor();
			$oGestor->ParseFromArray($oRow);
			
			array_push($arr, $oGestor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetById($IdGestor)
	{
		$sql = "SELECT a.*";
		$sql.= " FROM TB_Gestores a";
		$sql.= " WHERE a.IdGestor = " . DB::Number($IdGestor);	
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oGestor = new Gestor();
		$oGestor->ParseFromArray($oRow);
		
		return $oGestor;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Gestores";
		$sql.= " WHERE Disponible = 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function Create(Gestor $oGestor)
	{
		if (!DBAccess::$db->Begin())
			return false;

		$arr = array
		(
			'Disponible' 	=> DB::Bool('1'),
			'RazonSocial' 	=> DB::String($oGestor->RazonSocial),
			'Email' 		=> DB::String($oGestor->Email),
			'Telefono' 		=> DB::String($oGestor->Telefono)
		);
		
		if (!$this->Insert('TB_Gestores', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		/* asignamos el id generado */
		$oGestor->IdGestor = DBAccess::GetLastInsertId();

		DBAccess::$db->Commit();
			
		return $oGestor;
	}
	
	
	public function Update(Gestor $oGestor)
	{
		if (!DBAccess::$db->Begin())
			return false;

		$where = " IdGestor = " . DB::Number($oGestor->IdGestor);
		
		$arr = array
		(
			'Disponible' 	=> DB::Bool('1'),
			'RazonSocial' 	=> DB::String($oGestor->RazonSocial),
			'Email' 		=> DB::String($oGestor->Email),
			'Telefono' 		=> DB::String($oGestor->Telefono)
		);
		
		if (!DBAccess::Update('TB_Gestores', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return $oGestor;
	}
	
	public function Delete($IdGestor)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdGestor = " . DB::Number($IdGestor);
		
		$arr = array
		(
			'Disponible' 	=> DB::Bool('0')
		);
		
		if (!DBAccess::Update('TB_Gestores', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>