<?php

$dir="./ambientes/";
$carpeta_abierta = opendir($dir);
while(($archivo= readdir($carpeta_abierta)) !== false){
   if($archivo <> '.' and $archivo <> '..'){
      $array_file = file($dir.$archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
   }
}


foreach($array_file as $linea){
   $x=strpos($linea,"//");
   $linea=($x===FALSE)?$linea:substr($linea,0,$x);
   $linea=str_replace(";","",$linea);
   //echo $linea.'<br>';
   list($variable,$valor)=explode("=",$linea);
   //var_dump($variable).'<br>';
   $x=trim(strpos($linea,";"));
   
   if (strlen($linea)!=0) {
    //echo $variable.'<br>';
    eval("global ".$variable.";");
    //echo $linea.'__<br>';
    eval($linea.";");
    }
   
}


if ($Gtipo_conexion=='oracle') {require_once "oracle_functions.php";}
if ($Gtipo_conexion=='mysql') {require_once "lib/mysql_functions.php";}
if ($Gtipo_conexion=='mssql') {require_once "odbc_functions.php";}
?>
