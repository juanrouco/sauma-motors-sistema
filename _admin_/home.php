<?php

require_once('../inc_library.php');

/* sección exclusiva para usuarios autentificados */
Session::ForceLogin();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
</head>


<frameset rows="*" cols="230,*" framespacing="0" frameborder="NO" border="0">
	<frame src="menu.php" name="leftFrame" scrolling="YES" noresize>
	<frame src="welcome.php" name="mainFrame">
</frameset><noframes></noframes>


<body>


</body>


</html>
