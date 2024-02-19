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

// Recibir variables por GET
$recno          = $_POST['recno'];
$reserva        = $_POST['reserva'];
$os             = $_POST['os'];
$requerimiento  = $_POST['requerimiento'];
$np		        = $_POST['np'];
$cantidad       = $_POST['cantidad'];
$compra         = $_POST['compra'];

$cant_new    = $_POST['cant_new'];

//echo $cant_new;
//echo $recno;
//echo $np;
//echo "hola";


function delete_reserva($recno, $np, $reserva){
	global $tipobd_totvs,$conexion_totvs;
		//borrar la reserva
		$queryup_1 = "UPDATE SC0020 SET D_E_L_E_T_ = '*', R_E_C_D_E_L_ = '".$recno."'
		WHERE R_E_C_N_O_ = '".$recno."'";
		//echo $queryup_1;
		//$rsu_1 = querys($queryup_1, $tipobd_totvs, $conexion_totvs);

		echo "Reserva '".$reserva."' con NParte ".$np." ELIMINADA";
		echo "<br>";
}

function nueva_reserva($recno, $cant_new, $reserva, $np){

	global $tipobd_totvs,$conexion_totvs;


			$query_03 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
			R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT)
			SELECT C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, (SELECT LPAD(MAX(C0_NUM) +1,6,0) FROM SC0020), C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, '$cant_new' , C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, ' ', 
			(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0 , ' ', ' ', C0_NUM
			FROM SC0020 WHERE R_E_C_N_O_ = '".$recno."' ";
			//echo $query_03;
			//$rsu_3 = querys($query_03, $tipobd_totvs, $conexion_totvs);

            $secuencia_query = "SELECT MAX(C0_NUM) FROM SC0020";
            $secuencia_resultado = querys($secuencia_query, $tipobd_totvs, $conexion_totvs);
            $nueva_reserva = oci_fetch_assoc($secuencia_resultado);

            echo "<br>";
            echo "Se Creo Nueva Reserva N° '".$nueva_reserva['MAX(C0_NUM)']."' con NParte ".$np." y la cantidad : ".$cant_new."";
            echo "<br>";
}

function reserva_stock($recno, $cant_new, $reserva, $cantidad){

	global $tipobd_totvs,$conexion_totvs;


            $cant_stock = $cantidad - $cant_new;

			$query_03 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
			R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT)
			SELECT C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, (SELECT LPAD(MAX(C0_NUM) +1,6,0) FROM SC0020), C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, '$cant_stock' , C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, '*', 
			(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), (SELECT MAX(R_E_C_N_O_) +1 FROM SC0020) , ' ', ' ', C0_NUM
			FROM SC0020 WHERE R_E_C_N_O_ = '".$recno."' ";
			//echo $query_03;
			//$rsu_3 = querys($query_03, $tipobd_totvs, $conexion_totvs);
}


function actualiza_stock($cantidad,$np, $recno, $cant_new){

	global $tipobd_totvs,$conexion_totvs;

        // cantidad que se devulve a stock 
        $cant_stock = $cantidad - $cant_new;

		//quitar la cantidad reservada del sb2
		$queryup_sd2 = "UPDATE SB2020 SET B2_RESERVA=(B2_RESERVA-$cant_stock) WHERE  B2_COD='$np' AND B2_LOCAL= (SELECT C0_FILIAL FROM SC0020 WHERE R_E_C_N_O_ ='".$recno."')";
		//echo $queryup_sd2;
		//$rsu_sd2 = querys($queryup_sd2, $tipobd_totvs, $conexion_totvs);

        echo "<br>";
		echo "Se Devuelve a Bodega la cantidad:  ".$cant_stock." ";
		echo "<br>";
		
}

delete_reserva($recno, $np, $reserva);
nueva_reserva($recno, $cant_new, $reserva, $np);
reserva_stock($recno, $cant_new, $reserva, $cantidad);
actualiza_stock($cantidad,$np, $recno, $cant_new);

?>