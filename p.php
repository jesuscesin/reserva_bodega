<?php


require_once "conexion.php";
require_once "config.php";



function p(){
	global $tipobd_portal,$conexion_portal;
	 
	$querysel = "SELECT * FROM PRUEBA";
	$rss = querys($querysel, $tipobd_portal, $conexion_portal);
	while($v = ver_result($rss, $tipobd_portal)){
		
		$prueba = $v["TEST"];
		
		echo $prueba;
	}
}

p();
?>