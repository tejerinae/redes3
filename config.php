<?php
function conectar(){
	
	$host = "localhost"; 
	$user = "root";
	$pass = "";
	$dbname = "quequi";
/*
	$host = "localhost"; 
    $user = "acceso_db_piure";
    $pass = "uQmNe5l95MXoEWsCZ";
    $dbname = "piure";

	$host = "localhost"; 
	$user = "tejerinae";
	$pass = "Quequi75";
	$dbname = "tejerinae_quequi";
*/
	$conecta = mysql_connect($host,$user,$pass);
	mysql_select_db($dbname,$conecta);
}
?>
