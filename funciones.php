<?php

// VERIFICA CONTROL DE NEWSLETTERS ENVIADOS
function vericontrol(){
	conectar();
	$query =  ("SELECT control FROM control WHERE 1 LIMIT 0,1");
	$result = MYSQL_QUERY($query);
	if (!$result) {
		$reto=-1;
	} else {
		$numbers = mysql_numrows($result);
		if ($numbers<>1){
			$reto=-1;
		} else {
			while($j=mysql_fetch_assoc($result)){
				$reto=$j[control];
			}
		}
		mysql_free_result($result);
	}
	mysql_close();
	return $reto;
}

// ACTUALIZA ARCHIVO DE CONTROL DE ENVIO DE NEWSLETTER
function actualizacontrol(){
	conectar();
	$query ="UPDATE control SET control='0' WHERE 1";
	$result=MYSQL_QUERY($query);
}

// CREAR DIRECTORIO
function creardirectorio($x) {
	umask(0000); 
	return mkdir($x,0777);
}

// BORRA DIRECTORIO
function borrardir($path) {
	$hndl=opendir($path);
	while($file=readdir($hndl))
	{
		if ($file=='.' || $file=='..')  continue;
		if (is_dir($path.'/'.$file)) {
			borrardir($path.'/'.$file);
			rmdir($path.'/'.$file);
		} else {
			unlink($path.'/'.$file);
		}
	}
	closedir($hndl);
	rmdir($path);
	return true;
}

// BORRAR ARCHIVO
function borrafile($path) {
	if (file_exists($path)) unlink($path);
	return true;
}

// CREAR ARCHIVO
function creararchivo($x,$txt) {
	$r=false;
	umask(0000);
	$fp = fopen($x,"w+");
	if ($fp) {
		fwrite($fp, $txt);
		fclose($fp);
		$r=true;
	}
	return $r;
}

// VERIFICA BIBLIOTECA GD
function chequea_gd() {
	$testGD = get_extension_funcs("gd");
	if (!$testGD) {
		return "";
	} else {
		ob_start();
		phpinfo(8);
		$grab = ob_get_contents();
		ob_end_clean();
		$version = strpos($grab,"2.0");
		if ( $version ) return "gd2";
		else return "gd";
	}
}

// ACHICA IMAGENES
function resizejpg($image_file_path,$new_image_file_path,$max_width=468, $max_height=351, $calidad=76,$gd_version) {
	$return_val = 1;
	$return_val = ( ($img = ImageCreateFromJPEG ( $image_file_path)) && $return_val == 1 ) ? "1" : "0";
	$FullImage_width = imagesx ($img);
	$FullImage_height = imagesy ($img);
	$ratio =  ( $FullImage_width > $max_width ) ? (real)($max_width / $FullImage_width) : 1 ;
	$new_width = ((int)($FullImage_width * $ratio));
	$new_height = ((int)($FullImage_height * $ratio));
	$ratio =  ( $new_height > $max_height ) ? (real)($max_height / $new_height) : 1 ;
	$new_width = ((int)($new_width * $ratio));
	$new_height = ((int)($new_height * $ratio));
	if($new_width<$new_height){
		$new_width=$new_height;
	} else {
		$new_height=$new_width;
	}
	if ( $new_width == $FullImage_width && $new_height == $FullImage_height ) {
		@copy ($image_file_path, $new_image_file_path );
	} else {
		if ( $gd_version == "gd2" ) {
			$full_id =  ImageCreateTrueColor ( $new_width , $new_height );
			ImageCopyResampled ( $full_id, $img, 0,0,0,0, $new_width, $new_height, $FullImage_width, $FullImage_height );
		}	else {
			if ( $gd_version == "gd" ){
				$full_id = ImageCreate ( $new_width , $new_height );
				ImageCopyResized ( $full_id, $img, 0,0,0,0, $new_width, $new_height, $FullImage_width, $FullImage_height );
			} else {
				$return_val=0;
			}
		}
		if ($return_val) {
			$return_val = ( $full = ImageJPEG( $full_id, $new_image_file_path, $calidad ) && $return_val == 1 ) ? "1" : "0";
		}
	}
	return $return_val;
	exit();
}

//INSERTA HEADERS HTML 
function poneheader() {
	header("Cache-control: private");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Pragma: no-cache"); // HTTP/1.0
	header("Content-Type: text/html; charset=iso-8859-1");
}

// REEMPLAZA CARACTERES ESPECIALES CON ENTITIES
function reemplazar($a,$u=0) {
	$chrorig= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ä","Ä","ë","Ë","ï","Ï","ö","Ö","ü","Ü","º","ª","º");
	$chrrepl= array ("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&ntilde;","&Ntilde;","&auml;","&Auml;","&euml;","&Euml;","&iuml;","&Iuml;","&ouml;","&Ouml;","&uuml;","&Uuml;","&deg;","&ordf;","&ordm;");
	$t=0;
	foreach ($chrorig as $v) {
		$t++;
	}
	if ($u<>0) {
		$xx=$chrorig;
		$chrorig=$chrrepl;
		$chrrepl=$xx;
	}
	$xx=$a;
	for ($i=0;$i<$t;$i++) {
		$xx = str_replace($chrorig[$i], $chrrepl[$i], $xx);
	}
	$pos=strpos($xx,"#cenlace#");
	while ($pos > -1) {
		$xx=ve_enlace($xx,$pos);
		$pos=strpos($xx,"#cenlace#");
	}
	return $xx;
}

// CORTA CADENAS DE TEXTO EN EL 1ER PUNTO DESPUES DEL LONG DADO
function cortar($a, $l=150, $lt=100) {
	if (strlen($a)<=$l) {
		$r=$a;
	}else{
		$r=substr($a,$l);
		$pos=strpos($r,'.');
		if (($pos>-1) && ($pos<$lt)){
			$r=substr($a,0,($l+$pos+1));
		}else{
			$r=substr($a,0,$l);
		}
	}
	return $r;
}


// DEVUELVE LA FECHA FORMATEADA 
function fechaformateada($a){
	$mess[1]='Enero';
	$mess[2]='Febrero';
	$mess[3]='Marzo';
	$mess[4]='Abril';
	$mess[5]='Mayo';
	$mess[6]='Junio';
	$mess[7]='Julio';
	$mess[8]='Agosto';
	$mess[9]='Setiembre';
	$mess[10]='Octubre';
	$mess[11]='Noviembre';
	$mess[12]='Diciembre';
	return(substr($a,8,2)." de ".$mess[intval(substr($a,5,2))]." de ".substr($a,0,4));
}

//CAMBIA SALTOS DE LINEA unix POR html
function saltos($objeto){
	return $objeto=ereg_replace("\n","<br />",$objeto);
}

//DEVUELVE EL NRO DEL MES
function mes($m){
	$mes[1]='Enero';
	$mes[2]='Febrero';
	$mes[3]='Marzo';
	$mes[4]='Abril';
	$mes[5]='Mayo';
	$mes[6]='Junio';
	$mes[7]='Julio';
	$mes[8]='Agosto';
	$mes[9]='Setiembre';
	$mes[10]='Octubre';
	$mes[11]='Noviembre';
	$mes[12]='Diciembre';
	return($mes[intval($m)]);
}

// DEVUELVE EL TAMAÑOS DE LA IMAGEN DADO LOS MAXIMOS
function nfg_calc_image_size($width, $height, $max_width = 0, $max_height = 0, $percent = 0) {
	$percent    = ($percent > 0) ? $percent : 100;
	$width      = max(1, ($percent > 0) ? round($width*$percent/100) : $width);
	$height     = max(1, ($height > 0) ? round($height*$percent/100) : $height);
	$max_width  = ($max_width > 0) ? $max_width : $width;
	$max_height = ($max_height > 0) ? $max_height : $height;
	$ratio      = min(min(1, $max_width/$width), min(1, $max_height/$height));
	return array(round($width*$ratio), round($height*$ratio));
}

// CONVIERTE HTML ENTITIES EN CARACTERES NO VALIDOS 
function normalizar($a){
	$a=sacatag($a);
	$chrorig=array ("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&ntilde;","&Ntilde;","&auml;","&Auml;","&euml;","&Euml;","&iuml;","&Iuml;","&ouml;","&Ouml;","&uuml;","&Uuml;","&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","&ntilde;","&Ntilde;","&auml;","&Auml;","&euml;","&Euml;","&iuml;","&Iuml;","&ouml;","&Ouml;","&uuml;","&Uuml;");
	$chrrepl=array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ä","Ä","ë","Ë","ï","Ï","ö","Ö","ü","Ü");
	$t=0;
	foreach ($chrorig as $v) {
		$t++;
	}
	if ($u<>0) {
		$xx=$chrorig;
		$chrorig=$chrrepl;
		$chrrepl=$xx;
	}
	$xx=$a;
	for ($i=0;$i<$t;$i++) {
		$xx = str_replace($chrorig[$i], $chrrepl[$i], $xx);
	}
	$pos=strpos($xx,"#cenlace#");
	while ($pos > -1) {
		$xx=ve_enlace($xx,$pos);
		$pos=strpos($xx,"#cenlace#");
	}
	return $xx;
}

// PONE EN CAPITAL EL PARAMETRO PASADO USANDO CSS (capitalize)
function cap($objeto){
	return $objeto='<span style="text-transform:capitalize;">'.$objeto.'</span>';
}

// quitar HTML tags para META TAGS
function sacatag($objeto){
	$objeto=ereg_replace("<","",$objeto);
	$objeto=ereg_replace(">","",$objeto);
	$objeto=ereg_replace("!","",$objeto);
	$objeto=ereg_replace("\?","",$objeto);
	$objeto=ereg_replace("%","",$objeto);
	$objeto=(eregi_replace("á","a",$objeto));
	$objeto=(eregi_replace("é","e",$objeto));
	$objeto=(eregi_replace("í","i",$objeto));
	$objeto=(eregi_replace("ó","o",$objeto));
	$objeto=(eregi_replace("ú","u",$objeto));
	$objeto=(eregi_replace("ñ","ni",$objeto));
	$objeto=(eregi_replace("ë","e",$objeto));
	$objeto=(eregi_replace(",","",$objeto));
	$objeto=(eregi_replace("/","",$objeto));
	return $objeto;
}

// GENERA NUEVA PASS ALEATORIA
function newpass(){
	srand((double)microtime()*1000000);
	$t1 = rand(97,122);
	$t1 = chr($t1);
	srand((double)microtime()*1000000);
	$t2 = rand(97,122);
	$t2 = chr($t2);
	srand((double)microtime()*1000000);
	$t3 = rand(97,122);
	$t3 = chr($t3);
	srand((double)microtime()*1000000);
	$t4 = rand(97,122);
	$t4 = chr($t4);

	srand((double)microtime()*1000000);
	$n1 = rand(0,9);
	srand((double)microtime()*1000000);
	$n2 = rand(0,9);
	$semilla = $t1.$t2.$n1.$t3.$t4.$n2;
	return $semilla;
}

// PREPARA HTML PARA ENVIAR MAIL MULTIPART
function prepara_html($a) {
	global $archiincu,$cnt_archinc;
	$b=$a;
	$reto="";
	$findme="src=".'"';
	$pos=strpos($b,$findme);
	while ($pos > -1) {
		$reto.=substr($b,0,$pos+strlen($findme))."acaimagen[".$cnt_archinc."]";
		$b=substr($b,$pos+strlen($findme));
		$archiincu[$cnt_archinc++]=substr($b,0,strpos($b, '"'));
		$b=substr($b,strpos($b, '"'));
		$pos=strpos($b,$findme);
	}
	$reto.=$b;
	$findme="background=".'"';
	$b=$reto;
	$reto="";
	$pos=strpos($b,$findme);
	while ($pos > -1) {
		$reto.=substr($b,0,$pos+strlen($findme))."acaimagen[".$cnt_archinc."]";
		$b=substr($b,$pos+strlen($findme));
		$archiincu[$cnt_archinc++]=substr($b,0,strpos($b, '"'));
		$b=substr($b,strpos($b, '"'));
		$pos=strpos($b,$findme);
	}
	$reto.=$b;
	return $reto;
}

//chequea contraseña
function chequear($user, $pass) {
	unset($_SESSION["otro"]);
	$acc="logout";
	conectar();
	$query = "SELECT * FROM admin WHERE user='$user'";
	$resultados = mysql_query($query);
	if ($resultados){
		$cantusers=mysql_num_rows($resultados);
		if ($cantusers>0){
			$dta=mysql_fetch_array($resultados);
//			$md5pass = md5($pass);			
			$md5pass = $pass;
			if ($dta["pass"] == $md5pass) { //saque md5 en $pass
				$_SESSION["otro"]=1;
				$_SESSION["usuario"]=$user;
				$acc="login";
			} else {
				$acc="El password ingresado no es correcto";
	unset($_SESSION["otro"]);
			}
		}else{
			$acc= 'No se encontraron usuarios en la base de datos';
	unset($_SESSION["otro"]);
		}

	}else{
		$acc= 'No se pudieron verificar los datos ingresados';
	unset($_SESSION["otro"]);
	}
	return $acc;
}


//chequea contraseña front
function chequearf($user, $pass) {
	unset($_SESSION["mina_esta"]);
	$acc="logout";
	conectar();
	$query = "SELECT * FROM login2 WHERE user='$user' AND estado='si'";
	$resultados = mysql_query($query);
	if ($resultados){
		$cantusers=mysql_num_rows($resultados);
		if ($cantusers>0){
			$dta=mysql_fetch_array($resultados);
			$md5pass = md5($pass);
			if ($dta["pass"] == $md5pass) { //saque md5 en $pass
				$_SESSION["mina_esta"]=1;
				$_SESSION["usuario"]=$user;
				$acc="login";
			} else {
				$acc="El password ingresado no es correcto";
			}
		}else{
			$acc= 'No se encontraron usuarios en la base de datos';
		}

	}else{
		$acc= 'No se pudieron verificar los datos ingresados';
	}
	return $acc;
}
?>