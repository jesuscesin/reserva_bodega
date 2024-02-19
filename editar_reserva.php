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
$recno          = $_GET['recno'];
$reserva        = $_GET['reserva'];
$os             = $_GET['os'];
$requerimiento  = $_GET['requerimiento'];
$articulos      = $_GET['articulos'];
$cantidad       = $_GET['cantidad'];
$compra         = $_GET['compra'];


function ver_reserva(){
	
	
		$cargar[]=array(
					"OS"			=>$os,
					"REQUERIMIENTO"	=>$requerimiento,
					"RESERVA"		=>$reserva,
					"ARTICULOS"		=>$articulos,
					"CANTIDAD"		=>$cantidad,
            		"COMPRA"    	=> $compra,
					"R_E_C_N_O_"	=> $recno
			);

            echo json_encode($cargar);
	}





?>