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
	//$querysel .= " AND SC.C0_TABORI = 'SD1'";
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

function generar_insert($recno, $cantidad_nueva){
    
	global $tipobd_totvs,$conexion_totvs;

	$query_03 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
	C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
	C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
	C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
	C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
	R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT)
	SELECT C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, (SELECT LPAD(MAX(C0_NUM) +1,6,0) FROM SC0020), C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
	C0_PRODUTO, C0_LOCAL, C0_XUBICA, '$cantidad_nueva' , C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
	C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
	C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
	C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, ' ', 
	(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0 , ' ', ' ', C0_NUM
	FROM SC0020 WHERE R_E_C_N_O_ = '".$recno."' ";
	//echo $query_03;
	$rsu_3 = querys($query_03, $tipobd_totvs, $conexion_totvs);
}


?>