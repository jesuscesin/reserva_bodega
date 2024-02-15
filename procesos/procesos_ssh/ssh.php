<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servidor   = $_GET["servidor"];
$link       = $_GET["link"];
$usuario    = $_GET["usuario"];
$pass       = $_GET["pass"];
$archivo       = $_GET["archivo"];

echo $servidor."<br>";
echo $link."<br>";
echo $usuario."<br>";
echo $pass."<br>";
echo $archivo."<br>";

echo $link.$archivo."<br>";



$connection = ssh2_connect($servidor,22);
ssh2_auth_password($connection,$usuario,$pass);

//$stream = ssh2_exec($connection,$link);
$stream = ssh2_exec($connection,$link.$archivo);
//$salida = shell_exec('./prueba.sh');
//echo $salida;

 stream_set_blocking($stream,true);
 $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
 echo stream_get_contents($stream_out);
 

 //$stream2 = ssh2_exec($connection,'pwd');
 //
 //stream_set_blocking($stream2,true);
 //$stream2_out = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO);
 //echo "2 : ". stream_get_contents($stream2_out);
unset($connection);
?>