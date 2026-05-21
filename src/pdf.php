<?php

require_once('library/phmagick/phmagick.php');

$p = new phmagick('','destination.png');
 
$p->acquireFrame('test.pdf');

echo '<pre>', print_r($p->getLog()) , '</pre>';

$p = new phmagick('test.pdf', 'pdf-save.png');

$p->convert();

echo '<pre>', print_r($p->getLog()) , '</pre>';

$rutaconver="c:\imagemagick\ImageMagick-6.7.3-Q16\convert.exe";
system("$rutaconver test.pdf test.png");

 	?>