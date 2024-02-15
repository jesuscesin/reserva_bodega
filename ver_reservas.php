<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
include('send_mail.php');
include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";



function ver_traspasos($requerimiento,$os, $reserva, $status){
	global $tipobd_totvs,$conexion_totvs;
	

	$querysel = "select * from SC0020 WHERE ";

	if (!empty($os)) {
		$querysel .= "C0_OS = '$os' ";
	}
	
	if (!empty($requerimiento)) {
		if (strpos($querysel, "=") !== false) {
			$querysel .= "AND ";
		}
		$querysel .= "C0_NUMREQ = '$requerimiento' ";
	}
	
	if (!empty($reserva)) {
		if (strpos($querysel, "=") !== false) {
			$querysel .= "AND ";
		}
		$querysel .= "C0_NUM = '$reserva' ";
	}
	
	


	 //echo "<pre>".$querysel."</pre>";
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id_traspaso = $v["C0_SECUENCIA"];
		$cargar[]=array(
					"OS"			=>$v["C0_OS"],
					"REQUERIMIENTO"	=>$v["C0_NUMREQ"],
					"RESERVA"		=>$v["C0_NUM"],
					"ARTICULOS"		=>$v["C0_PRODUTO"],
					"CANTIDAD"		=>$v["C0_QUANT"]
			);
	}

	echo json_encode($cargar);
}
function lista_traspaso($id_traspaso){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT C0_SECUENCIA,C0_COD,C0_DESCR,C0_CANTIDAD,R_E_C_N_O_
			FROM ZC0020
			WHERE C0_SECUENCIA='$id_traspaso'
			AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	//echo $querysel;
	while($v = ver_result($rss, $tipobd_totvs)){
		$ver[]=array(
					"ID"		=>$v["C0_SECUENCIA"],					
					"ARTICULOS"	=>$v["C0_COD"],
					"DESCR"		=>$v["C0_DESCR"],
					"CANTIDAD"	=>$v["C0_CANTIDAD"],
					"RECNO"		=>$v["R_E_C_N_O_"]
					
			);
	}
	echo json_encode($ver);
}



		


if(isset($_GET["ver"])){
   $requerimiento =  $_GET["requerimiento"];
   $os =  $_GET["os"];
   $reserva =  $_GET["reserva"];
   $status 			=  $_GET["status"];
    ver_traspasos($requerimiento,$os,$reserva,$status);
}
if(isset($_GET["ver_traspaso"])){
   $id_traspaso = $_GET["id_traspaso"];
    lista_traspaso($id_traspaso);
}

?>