<?php 

require_once('class.dbaccess.php');
require_once('class.clientecontacto.php');
require_once('class.filter.php');
require_once('class.page.php');

class ClienteContactos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		$sql.= " WHERE Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClienteContactos";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oClienteContacto = new ClienteContacto();
			$oClienteContacto->ParseFromArray($oRow);
			
			array_push($arr, $oClienteContacto);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdContacto)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClienteContactos";
		$sql.= " WHERE IdContacto = " . DB::Number($IdContacto);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oClienteContacto = new ClienteContacto();
		$oClienteContacto->ParseFromArray($oRow);
		
		return $oClienteContacto;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClienteContactos";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oClienteContacto = new ClienteContacto();
		$oClienteContacto->ParseFromArray($oRow);
		
		return $oClienteContacto;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_ClienteContactos";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Nombre";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(ClienteContacto $oClienteContacto)
	{
		$arr = array
		(
			'IdCliente' 			=> DB::Number($oClienteContacto->IdCliente),
			'Nombre' 				=> DB::String($oClienteContacto->Nombre),
			'Apellido' 				=> DB::String($oClienteContacto->Apellido),
			'TelefonoCodigoArea' 	=> DB::String($oClienteContacto->TelefonoCodigoArea),
			'Telefono' 				=> DB::String($oClienteContacto->Telefono),
			'DocumentoTipo' 		=> DB::Number($oClienteContacto->DocumentoTipo),
			'DocumentoNumero' 		=> DB::String($oClienteContacto->DocumentoNumero),
			'FechaNacimiento' 		=> DB::Date($oClienteContacto->FechaNacimiento),
			'Email' 				=> DB::String($oClienteContacto->Email)
		);
		
		if (!$this->Insert('TB_ClienteContactos', $arr))
			return false;

		/* asignamos el id generado */
		$oClienteContacto->IdContacto = DBAccess::GetLastInsertId();
			
		return $oClienteContacto;
	}
	
	
	public function Update(ClienteContacto $oClienteContacto)
	{
		$where = " IdContacto = " . DB::Number($oClienteContacto->IdContacto);
		
		$arr = array
		(
			'IdCliente' 			=> DB::Number($oClienteContacto->IdCliente),
			'Nombre' 				=> DB::String($oClienteContacto->Nombre),
			'Apellido' 				=> DB::String($oClienteContacto->Apellido),
			'TelefonoCodigoArea' 	=> DB::String($oClienteContacto->TelefonoCodigoArea),
			'Telefono' 				=> DB::String($oClienteContacto->Telefono),
			'DocumentoTipo' 		=> DB::Number($oClienteContacto->DocumentoTipo),
			'DocumentoNumero' 		=> DB::String($oClienteContacto->DocumentoNumero),
			'FechaNacimiento' 		=> DB::Date($oClienteContacto->FechaNacimiento),
			'Email' 				=> DB::String($oClienteContacto->Email)
		);
		
		if (!DBAccess::Update('TB_ClienteContactos', $arr, $where))
			return false;
		
		return $oClienteContacto;
	}
	

	public function Delete($IdContacto)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdContacto = " . DB::Number($IdContacto);

		if (!DBAccess::Delete('TB_ClienteContactos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>