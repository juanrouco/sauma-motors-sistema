<?php

require_once('../library/class.rubros.php');

/* armamos el filtro */
$filter = array();
$filter['Nombre'] = $_REQUEST['FilterNombre'];

$oRubros = new Rubros();

$oRubros->ExportCsv($filter);

exit();

?>