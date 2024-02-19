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



function ver_reservas($requerimiento, $os, $reserva){
	global $tipobd_totvs,$conexion_totvs;
	

	$querysel = "SELECT SC.C0_OS, SC.C0_NUMREQ, SC.C0_NUM, SC.C0_PRODUTO, SC.C0_QUANT, SC.R_E_C_N_O_,
						XZY.XZY_COMPRA 
				FROM SC0020 SC 
				LEFT JOIN XZY020 XZY ON SC.C0_OS = XZY.XZY_NUMOS
                                      AND SC.C0_NUMREQ = XZY.XZY_NUMREQ
                                      AND SC.C0_PRODUTO = XZY.XZY_COD
                                      AND SC.C0_ITEMREQ = XZY.XZY_ITEM
				WHERE 1=1 ";



	if (!empty($os)) {
		$querysel .= " AND SC.C0_OS = '$os'";
	}

	if (!empty($requerimiento)) {
		$querysel .= " AND SC.C0_NUMREQ = '$requerimiento'";
	}

	if (!empty($reserva)) {
		$querysel .= " AND SC.C0_NUM = '$reserva'";
	}

	// Agregar la condición D_E_L_E_T_E <> '*'
	$querysel .= " AND SC.D_E_L_E_T_<> '*'";
	$querysel .= " AND SC.C0_TABORI = 'SD1'";
	// Agregar la cláusula ORDER BY
	//$querysel .= " ORDER BY SC.C0_NUMREQ";


	//echo "<pre>".$querysel."</pre>";
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id_traspaso = $v["C0_SECUENCIA"];
		$diferencia = ($v["C0_QUANT"] > $v["XZY_COMPRA"]) ? 'X' : ''; // Añadir esta línea para determinar la letra 'X'

		$cargar[]=array(
					"OS"			=>$v["C0_OS"],
					"REQUERIMIENTO"	=>$v["C0_NUMREQ"],
					"RESERVA"		=>$v["C0_NUM"],
					"ARTICULOS"		=>$v["C0_PRODUTO"],
					"CANTIDAD"		=>$v["C0_QUANT"],
            		"COMPRA"    	=> $v["XZY_COMPRA"],
					"CANTMAYOR"		=> $diferencia,
					"R_E_C_N_O_"	=> $v["R_E_C_N_O_"]
			);
	}

	echo json_encode($cargar);
}



if(isset($_GET["ver"])){
   $requerimiento 	=  $_GET["requerimiento"];
   $os 				=  $_GET["os"];
   $reserva 		=  $_GET["reserva"];
   ver_reservas($requerimiento,$os,$reserva);
}


?>