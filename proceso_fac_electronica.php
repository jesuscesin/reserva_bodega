<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}



function proceso_factura($tipo){
	global $tipobd_mysql,$conexion_mysql;
	
	$querysel = "SELECT
					PRO_NOMBRE,PRO_DESCRIPCION,PRO_SERVIDOR,PRO_ARCHIVO,PRO_LINK,SV_USUARIO,SV_CLAVE
					FROM
						PROCESOS , SERVIDORES_MCH
						WHERE PRO_TIPO='$tipo'
						and SV_IP=PRO_SERVIDOR";
	$rss = querys($querysel, $tipobd_mysql, $conexion_mysql);
	while($v = ver_result($rss,$tipobd_mysql)){
		$cargar[]=array(
					"NOMBRE"  			=>trim($v["PRO_NOMBRE"]),
					"DESCRIPCION" 		=>$v["PRO_DESCRIPCION"],
					"SERVIDOR" 			=>$v["PRO_SERVIDOR"],
					"ARCHIVO" 			=>$v["PRO_ARCHIVO"],
					"LINK" 				=>$v["PRO_LINK"],
					"BD_USER" 				=>$v["SV_USUARIO"],
					"BD_PASS" 				=>$v["SV_CLAVE"],
					
			);
		
		
	}
	echo json_encode($cargar);
}

//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){   
	
	$tipo = $_GET["tipo_proceso"];
    proceso_factura($tipo);
}

?>