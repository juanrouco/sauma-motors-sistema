<?php

class Perfil
{	
	public $IdPerfil;
	public $Codigo;
	public $Nombre;
	public $Predeterminado;

	const Administrador = 1;
	const Vendedor 		= 2;
	const Tesorero 		= 19;
		

	public function __construct()
	{
		$this->IdPerfil			= '';
		$this->Codigo			= '';
		$this->Nombre			= '';
		$this->Predeterminado	= '';
	}

	
	public function ParseFromArray(array $arr)
	{
		$this->IdPerfil			= $arr['IdPerfil'];
		$this->Codigo			= $arr['Codigo'];
		$this->Nombre			= $arr['Nombre'];
		$this->Predeterminado	= $arr['Predeterminado'];
	}
	

	public function CanDelete()
	{
		if ($this->GetAllUsuarios())
			return false;
		
		return true;
	}
	
	
	public function GetAllUsuarios()
	{
		$Usuarios = new Usuarios();
		
		return $Usuarios->GetAllByPerfil($this);
	}	
	
	
	public function GetAllPermisos()
	{
		$PerfilPermisos = new PerfilPermisos();
		
		return $PerfilPermisos->GetAllByPerfil($this);
	}	


	public function GetAllModulos()
	{
		$PerfilModulos = new PerfilModulos();
		
		return $PerfilModulos->GetAllByPerfil($this);
	}	

	
	public function DeleteAllPermisos()
	{
		$PerfilPermisos = new PerfilPermisos();
		
		return $PerfilPermisos->DeleteByPerfil($this);
	}	


	public function DeleteAllModulos()
	{
		$PerfilModulos = new PerfilModulos();
		
		return $PerfilModulos->DeleteByPerfil($this);
	}	
}

?>