<?php 

require_once('class.dbaccess.php');
require_once('class.unidad.php');
require_once('class.unidadarchivo.php');
require_once('class.filter.php');

class UnidadesArchivos extends DBAccess
{
	public function GetAll(Page $oPage = NULL)
	{
		$sql = "SELECT ";
		$sql.= " IdUnidadArchivo,";
		$sql.= " IdUnidad,";
		$sql.= " Archivo,";
		$sql.= " Nombre,";
		$sql.= " Certificado";
		$sql.= " FROM TB_UnidadesArchivos ";
		$sql.= " ORDER BY Certificado, IdUnidadArchivo ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadArchivo = new UnidadArchivo();
			$oUnidadArchivo->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadArchivo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetLastId()
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_UnidadesArchivos";
		$sql.= " ORDER BY Certificado, IdUnidadArchivo DESC";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oUnidadArchivo = new UnidadArchivo();
		$oUnidadArchivo->ParseFromArray($oRow);
		
		return $oUnidadArchivo;		
	}
	
	
	public function GetById($IdUnidadArchivo)
	{
		$sql = "SELECT ";
		$sql.= " IdUnidadArchivo,";
		$sql.= " IdUnidad,";
		$sql.= " Archivo,";
		$sql.= " Nombre,";
		$sql.= " Certificado";
		$sql.= " FROM TB_UnidadesArchivos ";
		$sql.= " WHERE IdUnidadArchivo = " . DB::Number($IdUnidadArchivo);	
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oUnidadArchivo = new UnidadArchivo();
		$oUnidadArchivo->ParseFromArray($oRow);
		
		return $oUnidadArchivo;		
	}
	
	
	public function GetAllByUnidad(Unidad $oUnidad, Page $oPage = NULL)
	{
		$arr = array();
	
		$sql = "SELECT ";
		$sql.= " IdUnidadArchivo,";
		$sql.= " IdUnidad,";
		$sql.= " Archivo,";
		$sql.= " Nombre,";
		$sql.= " Certificado";
		$sql.= " FROM TB_UnidadesArchivos ";
		$sql.= " WHERE IdUnidad = " . DB::Number($oUnidad->IdUnidad);
		$sql.= " ORDER BY Certificado, IdUnidadArchivo ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadArchivo = new UnidadArchivo();
			$oUnidadArchivo->ParseFromArray($oRow);
			
			array_push($arr, $oUnidadArchivo);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	public function GetCertificadoByUnidad(Unidad $oUnidad)
	{
		$arr = array();
	
		$sql = "SELECT ";
		$sql.= " IdUnidadArchivo,";
		$sql.= " IdUnidad,";
		$sql.= " Archivo,";
		$sql.= " Nombre,";
		$sql.= " Certificado";
		$sql.= " FROM TB_UnidadesArchivos ";
		$sql.= " WHERE IdUnidad = " . DB::Number($oUnidad->IdUnidad);
		$sql.= " AND Certificado = " . DB::Number('1');
		$sql.= " ORDER BY Certificado, IdUnidadArchivo ASC";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oUnidadArchivo = new UnidadArchivo();
		$oUnidadArchivo->ParseFromArray($oRow);
		
		return $oUnidadArchivo;
	}
	
	public function GetAllByIdUnidad($IdUnidad)
	{
		$arr = array();

		$sql = "SELECT ";
		$sql.= " *";
		$sql.= " FROM TB_UnidadesArchivos I ";
		$sql.= " INNER JOIN TB_Unidades N ON N.IdUnidad = I.IdUnidad ";
		$sql.= " WHERE N.IdUnidad = " . $IdUnidad;
		$sql.= " ORDER BY Certificado, IdUnidadArchivo ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oUnidadArchivo = new UnidadArchivo();
			$oUnidadArchivo->ParseFromArray($oRow);

			array_push($arr, $oUnidadArchivo);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	
	public function GetCountRows($IdUnidad)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_UnidadesArchivos";
		$sql.= " WHERE IdUnidad = " . DB::Number($IdUnidad);

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(UnidadArchivo $oUnidadArchivo)
	{
		$arr = array
		(
			'IdUnidad' 		=> DB::Number($oUnidadArchivo->IdUnidad),
			'Archivo' 		=> DB::String($oUnidadArchivo->Archivo),
			'Nombre' 		=> DB::String($oUnidadArchivo->Nombre),
			'Certificado' 	=> DB::Bool($oUnidadArchivo->Certificado)
		);
		
		if (!$this->Insert('TB_UnidadesArchivos', $arr))
			return false;
			
		return $oUnidadArchivo;
	}
	
	
	public function Update(UnidadArchivo $oUnidadArchivo)
	{
		$where = " IdUnidadArchivo = " . DB::Number($oUnidadArchivo->IdUnidadArchivo);
		
		$arr = array
		(
			'IdUnidad'		=> DB::Number($oUnidadArchivo->IdUnidad),
			'Archivo' 		=> DB::String($oUnidadArchivo->Archivo),
			'Nombre' 		=> htmlentities(DB::String($oUnidadArchivo->Nombre)), //FIXME:: LLEVA htmlentities para que guarde bien desde AJAX
			'Certificado'	=> DB::Bool($oUnidadArchivo->Certificado)
		);
		
		if (!DBAccess::Update('TB_UnidadesArchivos', $arr, $where))
			return false;
		
		return $oUnidadArchivo;
	}
	

	public function Delete($IdUnidadArchivo)
	{
		if (!DBAccess::$db->Begin())
			return false;

		/* obtiene los datos de la Archivo */
		$oUnidadArchivo = $this->GetById($IdUnidadArchivo);
		if (!$oUnidadArchivo)
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		/* elimina la imangen del directorio */
		Up::DeleteFile(Unidad::PathFile, $oUnidadArchivo->Archivo);
			
		/* elimina la Archivo de la tabla */	
		$where = " IdUnidadArchivo = " . DB::Number($IdUnidadArchivo);
		if (!DBAccess::Delete('TB_UnidadesArchivos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>