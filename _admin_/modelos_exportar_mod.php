<?php

require_once('../library/class.misc.php');
require_once('../library/class.modelos.php');

$oModelos = new Modelos();

$oModelos->ExportXlsToUpdate();

exit();

?>