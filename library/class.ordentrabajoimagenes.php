<?php 

require_once('class.dbaccess.php');
require_once('class.ordentrabajo.php');
require_once('class.ordentrabajoimagen.php');
require_once('class.filter.php');

class OrdenTrabajoImagenes extends DBAccess
{
	public function GetAll(Page $oPage = NULL)
	{
		$sql = "SELECT ";
		$sql.= " IdImagen,";
		$sql.= " IdOrdenTrabajo,";
		$sql.= " Imagen,";
		$sql.= " Epigrafe,";
		$sql.= " Orden";
		$sql.= " FROM tblOrdenTrabajoImagenes ";
		$sql.= " ORDER BY Orden, IdImagen ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoImagen = new OrdenTrabajoImagen();
			$oOrdenTrabajoImagen->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoImagen);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetLastId()
	{	
		$sql = "SELECT *";
		$sql.= " FROM tblOrdenTrabajoImagenes";
		$sql.= " ORDER BY Orden, IdImagen DESC";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;

		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oOrdenTrabajoImagen = new OrdenTrabajoImagen();
		$oOrdenTrabajoImagen->ParseFromArray($oRow);
		
		return $oOrdenTrabajoImagen;		
	}
	
	
	public function GetById($IdImagen)
	{
		$sql = "SELECT ";
		$sql.= " IdImagen,";
		$sql.= " IdOrdenTrabajo,";
		$sql.= " Imagen,";
		$sql.= " Epigrafe,";
		$sql.= " Orden";
		$sql.= " FROM tblOrdenTrabajoImagenes ";
		$sql.= " WHERE IdImagen = " . DB::Number($IdImagen);	
					
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oOrdenTrabajoImagen = new OrdenTrabajoImagen();
		$oOrdenTrabajoImagen->ParseFromArray($oRow);
		
		return $oOrdenTrabajoImagen;		
	}
	
	
	public function GetAllByOrdenTrabajo(OrdenTrabajo $oOrdenTrabajo, Page $oPage = NULL)
	{
		$arr = array();
	
		$sql = "SELECT ";
		$sql.= " IdImagen,";
		$sql.= " IdOrdenTrabajo,";
		$sql.= " Imagen,";
		$sql.= " Epigrafe,";
		$sql.= " Orden";
		$sql.= " FROM tblOrdenTrabajoImagenes ";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($oOrdenTrabajo->IdOrdenTrabajo);
		$sql.= " ORDER BY Orden, IdImagen ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoImagen = new OrdenTrabajoImagen();
			$oOrdenTrabajoImagen->ParseFromArray($oRow);
			
			array_push($arr, $oOrdenTrabajoImagen);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	public function GetAllByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$arr = array();

		$sql = "SELECT ";
		$sql.= " *";
		$sql.= " FROM tblOrdenTrabajoImagenes I ";
		$sql.= " INNER JOIN tblOrdenesTrabajo N ON N.IdOrdenTrabajo = I.IdOrdenTrabajo ";
		$sql.= " WHERE N.IdOrdenTrabajo = " . $IdOrdenTrabajo;
		$sql.= " ORDER BY Orden, IdImagen ASC";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oOrdenTrabajoImagen = new OrdenTrabajoImagen();
			$oOrdenTrabajoImagen->ParseFromArray($oRow);

			array_push($arr, $oOrdenTrabajoImagen);
			
			$oRes->MoveNext();
		}	

		return $arr;
	}
	
	
	public function GetCountRows($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM tblOrdenTrabajoImagenes";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(OrdenTrabajoImagen $oOrdenTrabajoImagen)
	{
		$arr = array
		(
			'IdOrdenTrabajo' 	=> DB::Number($oOrdenTrabajoImagen->IdOrdenTrabajo),
			'Imagen' 		=> DB::String($oOrdenTrabajoImagen->Imagen),
			'Epigrafe' 		=> DB::String($oOrdenTrabajoImagen->Epigrafe),
			'Orden' 		=> DB::String($oOrdenTrabajoImagen->Orden)
		);
		
		if (!$this->Insert('tblOrdenTrabajoImagenes', $arr))
			return false;
			
		return $oOrdenTrabajoImagen;
	}
	
	
	public function Update(OrdenTrabajoImagen $oOrdenTrabajoImagen)
	{
		$where = " IdImagen = " . DB::Number($oOrdenTrabajoImagen->IdImagen);
		
		$arr = array
		(
			'IdOrdenTrabajo'	=> DB::Number($oOrdenTrabajoImagen->IdOrdenTrabajo),
			'Imagen' 		=> DB::String($oOrdenTrabajoImagen->Imagen),
			'Epigrafe' 		=> htmlentities(DB::String($oOrdenTrabajoImagen->Epigrafe)), //FIXME:: LLEVA htmlentities para que guarde bien desde AJAX
			'Orden'			=> DB::Number($oOrdenTrabajoImagen->Orden)
		);
		
		if (!DBAccess::Update('tblOrdenTrabajoImagenes', $arr, $where))
			return false;
		
		return $oOrdenTrabajoImagen;
	}
	

	public function Delete($IdImagen)
	{
		if (!DBAccess::$db->Begin())
			return false;

		/* obtiene los datos de la imagen */
		$oOrdenTrabajoImagen = $this->GetById($IdImagen);
		if (!$oOrdenTrabajoImagen)
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		/* elimina la imangen del directorio */
		Up::DeleteFile(OrdenTrabajo::PathImageThumb, $oOrdenTrabajoImagen->Imagen);
		Up::DeleteFile(OrdenTrabajo::PathImageBig, $oOrdenTrabajoImagen->Imagen);
			
		/* elimina la imagen de la tabla */	
		$where = " IdImagen = " . DB::Number($IdImagen);
		if (!DBAccess::Delete('tblOrdenTrabajoImagenes', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}		
}

?>