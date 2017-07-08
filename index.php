<?php
session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
global $opc;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>E. Tejerina - Redes 2</title>
<link href="css/estilos.css" rel="stylesheet" type="text/css" />
<meta charset="iso-8859-1" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.js"></script>
<script type='text/javascript' src='js/funciones.js'></script>	

</head>
<body>
    <div id="derecha">
	   	<?php include("nuevo.php"); ?>
	</div> 
</body>
</html>