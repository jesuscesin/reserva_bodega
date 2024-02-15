<?php
error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');
//conectarse a la base de datos
function db_logon($usuario,$clave,$oracle)
{
	//$con=oci_connect($usuario,$clave,$oracle);
	//TODO: $con=oci_connect($usuario,$clave,$oracle,'WE8ISO8859P1', 'AL32UTF8');
  $con=oci_connect($usuario,$clave,$oracle,'AL32UTF8');
	//echo $usuario;
	//echo $clave;
	//echo $oracle;
	return($con);
}

//ejecutar un query
function db_exec($con,$query)
{
    //TODO:
    //echo "<br>...$query";
    $result=oci_parse($con,$query);
	  $r = oci_execute($result);
    if (!$r){
      $e = oci_error($result);
      $sql = $e['sqltext']."\n";
      $msg = $e['message']."\n\n";
      if (trim($sql)<>'' || trim($msg)<>''){
        $myfile = fopen("/var/www/html/oci_log/oci_log.log", "a") or die("Unable to open file!");
        fwrite($myfile,"Log date: ".date('Y/m/d H:m:s')."\nSource: /var/www/html".trim($_SERVER['REQUEST_URI'])."\nDesc:\n");
        fwrite($myfile, $sql);
        fwrite($myfile, $msg);
        fclose($myfile);
      }
    }
	  return($result);
}
// devolver una fila a un arreglo 
function db_fetch_array($result)
{
 $x=ocifetchinto($result,$row,OCI_RETURN_NULLS + OCI_ASSOC);
 return($row);
}

//Desconectarse de la base de datos
function db_logoff($con)
{
 //ocilogoff($con);
 oci_close($con);
}

function db_num_fields($result)
{
return(ocinumcols($result));
}

function db_field_name($result,$n)
{
 return(ocicolumnname($result,$n));
}
//avanza un registro en result_set
function db_fetch_go($result,$n)
{
  if ($n==0){return;}
  $j=0;
  while ($x=ocifetch($result))
  {
    $j++; 
	if ($j>=$n){break;}
  }
  return($x);
}

function db_fetch_next($result)
{
 return(ocifetch($result));
}

function db_result($result,$n)
{
 return(ociresult($result,$n));
}

function db_field_size($result,$n)
{
  return (ocicolumnsize($result,$n));
}

function db_field_type($result,$n)
{
  return (ocicolumntype($result,$n));
}
function db_num_rows($result)
{
  return (ocirowcount($result));
}


?>
