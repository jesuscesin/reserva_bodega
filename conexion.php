<?php
error_reporting(E_ALL);
require_once "lib/gestordb.php";
/*
	//conexion a BD PTL_MONARCH 100.230 
	$resultado_ptl 		= selec_server('PTL_MCH');
	$tipobd_ptl 		= $resultado_ptl[0];
	$conexion_ptl 		= $resultado_ptl[1];
	//CONEXION A PTL_DICOTEX 100.41
	$resultado_dico 	= selec_server('PTL_DICOTEX');
	$tipobd_dico 		= $resultado_dico[0];
	$conexion_dico 		= $resultado_dico[1];
	//CONEXION A TOTVS MONARCH 100.232 //TOTVS_MCHV12
	$resultado_totvs 	= selec_server('TOTVS_MCHV12');
	$tipobd_totvs		= $resultado_totvs[0];
	$conexion_totvs 	= $resultado_totvs[1];
	//CONEXION A TOTVS_DEV MONARCH 100.155
	$resultado_totvs_dev 	= selec_server('TOTVS_MCHV12_DEV');
	$tipobd_totvs_dev		= $resultado_totvs_dev[0];
	$conexion_totvs_dev 	= $resultado_totvs_dev[1];
	//CONEXION A MYSQL GRUPOMONARCH 100.75
	$resultado 		= selec_server('MYSQL_GRUPOMONARCH');
	$tipobd_mysql 	= $resultado[0];
	$conexion_mysql = $resultado[1];	
	//CONEXION A SQL SERVER WINPER MONARCH 100.89
	$resultado 			= selec_server('WINPER_MCH');
	$tipobd_winper 		= $resultado[0];
	$conexion_winper 	= $resultado[1];
	//CONEXION A TOTVS MONARCH V12 100.151 
*/
	//CONEXION A SQL SERVER WINPER MONARCH 100.89
	$resultado 			= selec_server('');
	$tipobd_portal 		= $resultado[0];
	$conexion_portal 	= $resultado[1];

	//CONEXION A SQL SERVER WINPER MONARCH 100.89
	$resultado_totvs 	= selec_server('');
	$tipobd_totvs		= $resultado_totvs[0];
	$conexion_totvs 	= $resultado_totvs[1];

	

?>
