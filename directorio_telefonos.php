<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
//require_once "conexion.php";
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";


function ver_listado($empresa){
	//global $conexion_mysql;//apunta  a la 230 BD PTL
	global $conexion_mysql,$tipobd_mysql;
	
	if($empresa =='TODOS'){
		$querysel = "SELECT NOMBRE,CARGO,NUMERO,ANEXO,EMPRESA,DEPARTAMENTO 
								FROM DIRECTORIO_TELEFONOS
								ORDER BY NOMBRE";
					//echo $querysel;	
	}else{
		$querysel = "SELECT NOMBRE,CARGO,NUMERO,ANEXO,EMPRESA,DEPARTAMENTO 
									FROM DIRECTORIO_TELEFONOS
									WHERE EMPRESA='$empresa'
									ORDER BY NOMBRE";
						//echo $querysel;			
	}
	$rss = querys($querysel,$tipobd_mysql,$conexion_mysql);
	while($v = ver_result($rss,$tipobd_mysql)){
		$cargar[]=array(
					"NOMBRE"  		=>trim($v["NOMBRE"]),
					"CARGO" 		=>$v["CARGO"],
					"NUMERO" 		=>$v["NUMERO"],
					"ANEXO"	    	=>$v["ANEXO"],
					"EMPRESA"	    =>$v["EMPRESA"],
					"DEPARTAMENTO"	=>$v["DEPARTAMENTO"]
					
			);
	}

	echo json_encode($cargar);
}

//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){   
   $empresa  =  $_GET["empresa"];
    ver_listado($empresa);
}

?>