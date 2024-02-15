<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";



function ver_info($guia, $origen,$estado){
	global $tipobd_totvs,$conexion_totvs;
	

	$querysel = "SELECT NVL(RD_ID, 0) AS RD_ID, NVL(RD_GUIA, 0) AS RD_GUIA, RD_ORIGEN, RD_FECHA,RD_ESTADO,COUNT(RD_COD_MCH) AS CUENTA_CODIGOS, SUM(RD_CANTIDAD) AS SUMA_CANTIDAD
					FROM Z2B_RECEP_DEVOLUCIONES
					WHERE (RD_GUIA='$guia' OR RD_ORIGEN='$origen')
					AND RD_ESTADO LIKE '%$estado%'
					AND D_E_L_E_T_<>'*'
					GROUP BY  RD_ID,RD_GUIA,RD_ORIGEN,RD_FECHA,RD_ESTADO
					ORDER BY RD_ID DESC";//,substr(BO_SECUENCIA,-2),BO_FECHA
				// echo $querysel;
	
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);


	//echo "QUERY: ".$querysel;
	//echo "<br>";

	while($v = ver_result($rss, $tipobd_totvs)){
		$numero = $v["RD_ID"];

		$cargar[]=array(
					"ID"		=>$v["RD_ID"],
					"GUIA"	=>$v["RD_GUIA"],
					"ORIGEN"	=>$v["RD_ORIGEN"],
					"CODIGOS"	=>$v["CUENTA_CODIGOS"],
					"CANTIDAD"	=>$v["SUMA_CANTIDAD"],
					"FECHA"	=>formatDate($v["RD_FECHA"]),
					"ESTADO"	=>$v["RD_ESTADO"],
					"VER_SOLICITUD" 			=>"<a target='_blank' class='btn btn-block bg-gradient-info btn-sm' href='devoluciones/devoluciones_escaniadas.php?escaneo_id=$numero'>CID $numero</a>"
			);
	}

	echo json_encode($cargar);
}
function anula_cid($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querydel = "UPDATE Z2B_RECEP_DEVOLUCIONES SET D_E_L_E_T_='*', RD_ESTADO='30' WHERE RD_ID='$numero'";
	$rsd = querys($querydel, $tipobd_totvs, $conexion_totvs);
	echo "CID $numero ANULADO";
}
//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){
   $canal =  $_GET["canal"];
   $guia = $_GET["guia"];
   $estado = $_GET["estado"];

   if ($estado == 'todos'){
	$estado = '';
   }

    ver_info($guia, $canal, $estado);
}
if(isset($_GET["anula_cid"])){
	
	$numero = $_GET["numero"];
	
    anula_cid($numero);	
}

?>