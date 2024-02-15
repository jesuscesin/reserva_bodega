<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}


function ver_articulos_nuevos(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT B1_COD, B1_DESC,B1_CODBAR  FROM  sb1010 where D_E_L_E_T_<>'*'  AND B1_COD not in (SELECT B1_COD FROM SB1340@LK_MCHV11 WHERE D_E_L_E_T_<>'*') order by B1_COD";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss,$tipobd_totvs)){
		$cargar[]=array(
					"ARTICULO"  		=>trim($v["B1_COD"]),
					"DESCRIPCION" 		=>$v["B1_DESC"],
					"BARRA" 				=>$v["B1_CODBAR"],
					
			);
		
		
	}
	echo json_encode($cargar);
}

//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){   

    ver_articulos_nuevos();
}

?>