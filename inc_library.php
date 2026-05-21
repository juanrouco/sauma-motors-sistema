<?php

error_reporting(E_ERROR | E_PARSE);
session_cache_limiter('private-no-cache');
//session_cache_limiter('public');
session_cache_expire(3600);
// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 3600);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(3600);

require_once('library/class.gestoriacreate.php');
require_once('library/class.session.php');
require_once('library/class.misc.php');

function __autoload($ClassName)
{
	$ClassFile 	= 'class.' . strtolower($ClassName) . '.php';
	$Path 		= '../library/';

	if (!(file_exists($Path . $ClassFile)))	
		return false;
	
	require_once($Path . $ClassFile);
	
	if (!(class_exists($ClassName)))	
		return false;
		
	return true;
}

/* incluimos archivo con definicion de permisos */
require_once('inc_perms.php');

/* incluimos libreria para manipular imagenes */
require_once('thumbnail/thumblib.inc.php');

/* inicializamos sesion */
Session::Initialize();

/* inicializamos la gestoria */
GestoriaCreate::Initialize();

/* obtenemos los datos del usuario logueado */
$currentUser = Session::GetCurrentUser();

/* obtenemos los datos generales de la empresa */
$oDatosEmpresa = new DatosEmpresa();
$oDatosEmpresa = $oDatosEmpresa->GetAll();

/* imprimimos contenido de javascript */
Modules::WriteClientFunctions();

/* eliminamos variables de sesion y globales por cuestiones de seguridad */
if (ini_get('register_globals') == 1)
{
	if (is_array($_REQUEST)) foreach(array_keys($_REQUEST) as $var_to_kill) unset($$var_to_kill);
	if (is_array($_SESSION)) foreach(array_keys($_SESSION) as $var_to_kill) unset($$var_to_kill);
	if (is_array($_SERVER))  foreach(array_keys($_SERVER)  as $var_to_kill) unset($$var_to_kill);
    unset($var_to_kill);
}

?>