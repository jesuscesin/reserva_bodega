<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}



function ver_listado($vta_code){
	//global $conexion_mysql;//apunta  a la 230 BD PTL
	global $tipobd_ptl,$conexion_ptl;
	

		$querysel = "SELECT VTA_CODE, TRIM(VTA_NOMBRE) AS NOMBRE, VTA_SKU, TRIM(VTA_DESCRI) AS DESCR
						FROM MVE_VENTAS
						WHERE VTA_CODE='$vta_code'";
						//echo $querysel;			
	$rss = querys($querysel,$tipobd_ptl,$conexion_ptl);
	while($v = ver_result($rss,$tipobd_ptl)){
		$cargar[]=array(
					"VTA_CODE"  		=>trim($v["VTA_CODE"]),
					"NOMBRE" 			=>utf8_encode($v["NOMBRE"]),
					"SKU" 				=>$v["VTA_SKU"],
					"DESCRIPCION"    	=>$v["DESCR"],
					
			);
	}

	echo json_encode($cargar);
}
function procesa_codigo($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$queryup = "UPDATE SB1010 SET B1_MODELO=' ',B1_ECC='S' WHERE B1_COD='$articulo'";
	$rsu = querys($queryup,$tipobd_totvs,$conexion_totvs);
	//echo $queryup;
	
	echo "CODIGO $articulo ACTUALIZADO EN TOTVS";
}
//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){   
		$vta_code = $_GET["vta_code"];
		ver_listado($vta_code);
}
if(isset($_GET["update_articulo"])){   
		$sku = $_GET["articulo"];
		procesa_codigo($sku);
}

?>