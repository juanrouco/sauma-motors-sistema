<?php 

require_once('../inc_library.php');

/* finaliza la sesion */
Session::Logout();

/* redirecciona al login */
header('Location: index.php');

?>