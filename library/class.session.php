<?php

require_once('class.usuarios.php');

abstract class Session
{
	const LoginError = 0;
	
	static private $justOnce = false;

	static function Initialize()
	{
		if (Session::$justOnce == true)
			return;
		
		ob_start();
		session_start();
		
		Session::$justOnce = true;
	}
	
	static function GetCurrentUser()
	{
		$us = new Usuarios();
		$usuario = $us->GetById($_SESSION['IdUsuario']);
		
		return $usuario;
	}
	
	static function Login($username, $password)
	{
		$us = new Usuarios();
		
		// autentificamos al usuario		
		$_SESSION['IdUsuario'] = '';
		
		$usuario = $us->GetByCredentials($username, $password);

		// si el usuario no existe con esas credenciales...
		if (!$usuario)
			return Session::LoginError;
		
		$_SESSION['IdUsuario'] = $usuario->IdUsuario;
		
		return $usuario;
	}
	
	static function LoginWithoutPassword($username)
	{
		$us = new Usuarios();
		
		// autentificamos al usuario		
		$usuario = $us->GetByLogin($username);
		
		// si el usuario no existe...
		if (!$usuario)
			return Session::LoginError;
			
		$_SESSION['IdUsuario'] = $usuario->IdUsuario;
		
		return $usuario;
	}
	
	static function Logout()
	{
		session_destroy();
		Session::$justOnce = false;
		return true;
	}
	
	static function ForceLogin($username = '', $url = '', $lastError = 0)
	{
		// si existe una sesión valida y activa, no es necesario forzar login
		$u = Session::GetCurrentUser();
		
		if ($u)
		{
			//ob_end_flush();
			return;
		}
		
		$location = "Location: index.php";
		if ($username != '')
			$location.= '?Usuario=' . _urlencode($username);
			
		if ($url != '')
		{
			if ($username != '')
				$location.= '&url=' . _urlencode($url);
			else
				$location.= '?url=' . _urlencode($url);
		}
			
		if ($lastError != 0)
			$location.= '&lasterror=' . $lastError;
		
		//ob_end_clean();
		header($location);
		return;
	}
	
	
	static function CheckPerm($IdPermiso)
	{
		if (!$_SESSION['IdUsuario'])
		{
			header('Location: logout.php');
			exit;
		}
		/* verificamos si existe una sesión valida y activa */
		$u = Session::GetCurrentUser();
		if (!$u)
			return false;

		/* cerificamos si posee permiso de acceso */		
		if (!$u->HavePerm($IdPermiso))
			return false;
		
		return true;
	}


	static function NoPerm()
	{
		//Session::Logout();
		header('Location: access_denied.php');
		exit;
	}
}

?>