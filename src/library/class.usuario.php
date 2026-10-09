<?php

class Usuario
{
	public $IdUsuario;
	public $IdUbicacion;
	public $IdSector;
	public $IdPerfil;
	public $Nombre;
	public $Apellido;
	public $Email;
	public $Login;
	public $Password;
	public $Especial;

	const Administrador = 1;
	const Vendedor 		= 2;
	const Taller 		= 5;
	const Mecanico 		= 14;
	const Plan	 		= 32;
	
	public function ParseFromArray(array $arr)
	{
		$this->IdUsuario 	= $arr['IdUsuario'];
		$this->IdUbicacion 	= $arr['IdUbicacion'];
		$this->IdSector 	= $arr['IdSector'];
		$this->IdPerfil 	= $arr['IdPerfil'];
		$this->Nombre 		= stripslashes($arr['Nombre']);
		$this->Apellido 	= stripslashes($arr['Apellido']);
		$this->Email 		= stripslashes($arr['Email']);
		$this->Login 		= stripslashes($arr['Login']);
		$this->Password 	= stripslashes($arr['Password']);
		$this->Especial 	= $arr['Especial'];
	}
	
	
	public function HavePerm($IdPermiso)
	{
		$Usuarios = new Usuarios();
		
		return $Usuarios->CheckPerm($this, $IdPermiso);
	}
	
	
	public function CanDelete()
	{
		/* con el borrado logico (Deleted = 1) los registros relacionados no se
		   tocan, asi que un usuario con minutas tambien se puede eliminar */
		return true;
	}
	
	
	public function GetAllMinutas()
	{
		$Minutas = new Minutas();
		
		return $Minutas->GetAllByUsuario($this);
	}
}

?>