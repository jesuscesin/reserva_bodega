<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
require_once "config.php";
require_once "conexion.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}

function servidores(){
	global $tipobd_mysql,$conexion_mysql;
	
	$querysel = "SELECT * FROM	SERVIDORES_MCH";
	$rss = querys($querysel, $tipobd_mysql, $conexion_mysql);
	while($v = ver_result($rss,$tipobd_mysql)){
		$cargar[]=array(
					"NOMBRE"  			=>trim($v["SV_NOMBRE"]),
					"IP" 					=>$v["SV_IP"],
					"IPEXT" 				=>$v["SV_IPEXT"],
					"USUARIO" 			=>$v["SV_USUARIO"],
					"CLAVE" 				=>$v["SV_CLAVE"],
					"PUERTO" 				=>$v["SV_PUERTO"],
					"ESTADO" 				=>$v["SV_ESTADO"],
					
			);
		
		
	}
	echo json_encode($cargar);
}
function editar_servidor($ip){
		global $tipobd_mysql,$conexion_mysql;
	
	
	
	$querysel = "SELECT SV_ID,SV_NOMBRE,	SV_IP,	SV_IPEXT,	SV_USUARIO,	SV_CLAVE,	SV_PUERTO,	SV_ESTADO 
					FROM
						SERVIDORES_MCH
				WHERE SV_IP =  '$ip'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_mysql, $conexion_mysql);
	while($v = ver_result($rss,$tipobd_mysql)){
		$editar[]=array(
			"SV_ID" 			=> trim($v["SV_ID"]),
			"NOMBRE" 			=> trim($v["SV_NOMBRE"]),
			"IP" 				=> trim($v["SV_IP"]),
			"IPEXT" 			=> trim($v["SV_IPEXT"]),
			"USUARIO" 		    => trim($v["SV_USUARIO"]),
			"CLAVE" 			=> trim($v["SV_CLAVE"]),		  
			"PUERTO"			=> trim($v["SV_PUERTO"]),			  
			"ESTADO"	 		=> trim($v["SV_ESTADO"])  
			//"ANO" 		=> $v["ANO"],
			//"ARCHIVO" => "<a href='./archivos_de_venta/$archivo'>Ver archivo</a>"
		);
	}
//	  echo "<pre>";
//    print_r($editar);
//    echo "</pre>";
	echo json_encode($editar);
}
function insertar_servidor(){
    global $tipobd_mysql,$conexion_mysql;

    $nombre     = $_POST["nombre"];
    $ip         = $_POST["ip"];
    $ip_ext     = $_POST["ip_ext"];
    $usuario    = $_POST["usuario"];
    $clave      = $_POST["clave"];
    $puerto      = $_POST["puerto"];
    $estado     = "S";
    $recno      = recno_tabla();

    $queryin = "insert into SERVIDORES_MCH (SV_ID, SV_NOMBRE, SV_IP, SV_IPEXT, SV_USUARIO, SV_CLAVE, SV_PUERTO, SV_ESTADO)
                    values ($recno,'$nombre','$ip','$ip_ext','$usuario','$clave','$puerto','$estado');";
    $rsi = querys($queryin, $tipobd_mysql, $conexion_mysql);

    echo "SERVIDOR INGRESADO !";

}
function recno_tabla(){
    global $tipobd_mysql,$conexion_mysql;

    $querysel = "SELECT MAX(SV_ID) as RECNO FROM SERVIDORES_MCH";
    $rss = querys($querysel, $tipobd_mysql, $conexion_mysql);
    $fila = ver_result($rss, $tipobd_mysql);
    if($fila["RECNO"]==null or $fila["RECNO"]==0){
        return 1;
    }else{
        return $fila["RECNO"]+1;
    }
}

function update_servidor($post){
    global $tipobd_mysql,$conexion_mysql;
	
	// echo "<pre>";
		// print_r($post);
	// echo "</pre>";
	
	// die();

    $sv_id         = $post["sv_id"];
    $nombre     = $post["nombre"];
    $ip         = $post["ip"];
    $ip_ext     = $post["ip_ext"];
    $usuario    = $post["usuario"];
    $clave      = $post["clave"];
    $puerto      =$post["puerto"];
    $estado     = $post["estado"];

    $queryup = "UPDATE SERVIDORES_MCH 
					SET SV_NOMBRE='$nombre', SV_IP='$ip', SV_IPEXT='$ip_ext',
					SV_USUARIO='$usuario', SV_CLAVE='$clave', SV_PUERTO='$puerto', SV_ESTADO='$estado'
					WHERE SV_ID=$sv_id";
	$result = querys($queryup, $tipobd_mysql, $conexion_mysql);
	// print_r($result);
	if($result){
		echo "SERVIDOR $ip ACTUALIZADO CON EXITO" ;
	}else{
		echo "ERROR: SERVIDOR NO ACTUALIZADOS" ;
	}
}
function existe_servidor($sv_id){
	global $tipobd_mysql,$conexion_mysql;

	$queryexit = "SELECT	COUNT(*) AS FILAS FROM	SERVIDORES_MCH WHERE SV_ID=$sv_id";
	echo $queryexit;
	$rse = querys($queryexit, $tipobd_mysql, $conexion_mysql);
	$fila = ver_result($rse, $tipobd_mysql);
	if($fila["FILAS"]>0){
        return true;
    }else{
        return false;
    }
}
//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){   

    servidores();
}
if(isset($_GET["cargar"])){
    $ip 		= $_GET["ip"];
    editar_servidor($ip);
}
if(isset($_POST["insertar"])){

    if($_POST["sv_id"]==''){
        insertar_servidor($_POST);
		  //echo "ACTUALIZADO";Q
    }else{
        update_servidor($_POST);
		  //echo "INSERTADO";
    }
    
}
?>