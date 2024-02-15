<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
	


function ver_listado(){
	//global $conexion_mysql;//apunta  a la 230 BD PTL
	global $tipobd_ptl,$conexion_ptl;
	

		$querysel = "SELECT
							AO2_TOKEN,
							AO2_CLIENTID,
							AO2_CLIENTSECRET,
							AO2_AUTHORIZATION_CODE,
							AO2_FECACT,
							AO2_HORAACT 
						FROM
							PTL.AOUTH2_MV";
						//echo $querysel;			
	$rss = querys($querysel,$tipobd_ptl,$conexion_ptl);
	while($v = ver_result($rss,$tipobd_ptl)){
		$cargar[]=array(
					"TOKEN"  			=>substr(trim($v["AO2_TOKEN"]),0,20),
					"CLIENTID" 			=>utf8_encode($v["AO2_CLIENTID"]),
					"CLI_SECRET" 		=>$v["AO2_CLIENTSECRET"],
					"AUT_CODE"	    	=>$v["AO2_AUTHORIZATION_CODE"],
					"FECHA"    			=>formatDate($v["AO2_FECACT"]),
					"HORA"    			=>$v["AO2_HORAACT"],
					
			);
	}

	echo json_encode($cargar);
}
function procesa_codigo($auth_code){
	global $tipobd_ptl,$conexion_ptl;
	
	$queryup = "UPDATE PTL.AOUTH2_MV  
					SET	AO2_AUTHORIZATION_CODE='$auth_code'";
	$rsu = querys($queryup,$tipobd_ptl,$conexion_ptl);
	echo "TOKEN ACTUALIZADO";
}
function editar_token($ip){
		global $tipobd_gr,$conexion_mysql;
	
	
	
	$querysel = "SELECT	AO2_AUTHORIZATION_CODE
					FROM PTL.AOUTH2_MV";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_gr, $conexion_mysql);
	while($v = ver_result($rss,$tipobd_gr)){
		$editar[]=array(
			"AUTH_CODE" 			=> trim($v["AO2_AUTHORIZATION_CODE"]),
		);
	}
//	  echo "<pre>";
//    print_r($editar);
//    echo "</pre>";
	echo json_encode($editar);
}
//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){ 
		ver_listado();
}
if(isset($_GET["update_auth_code"])){   
		$auth_code = $_GET["auth_code"];
		procesa_codigo($auth_code);
}

?>