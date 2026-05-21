<?php

require_once('../inc_library.php'); 

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

/* verificamos si posee permisos */
if (!Session::CheckPerm(PERM_RECE_CREATE))
	Session::NoPerm();

/* declaracion de variables */
$oDeclaracionesJuradas = new DeclaracionesJuradas();

/* definimos cadena a mandar por get */
$strParams = '?' . $_SERVER['QUERY_STRING'];

/* generamos la declaracion */
$oDeclaracionesJuradas->GenerateDeclaracion();

header('Location: declaracionesjuradas.php' . $strParams);
exit;

?>