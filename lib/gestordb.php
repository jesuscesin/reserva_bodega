<?php
date_default_timezone_set('America/Santiago');
setlocale(LC_TIME, 'spanish');
function selec_server($servidor){
/*
	$server1 = mysqli_connect('192.168.100.75', 'root','root.mch.1920', 'utilitarios','3306');
	mysqli_set_charset($server1, "utf8"); 
	$query = "select BD_ID,BD_NOMBRE,BD_ID_TIPO,BD_HOST,BD_USER,BD_PASS,BD_PORT,BD_SID_BD,BD_ESTADO
	from BD_MCH
	where BD_NOMBRE = '$servidor'
	and BD_ESTADO <> '*'";
	//echo $query. "<br>";
	$result=querys($query,'MYSQL',$server1);
	if ($v=ver_result($result,'MYSQL')){
		$name = trim($v['BD_NOMBRE']);
		$data = trim($v['BD_ID_TIPO']);
		$host = trim($v['BD_HOST']);
		$user = trim($v['BD_USER']);
		$pass = trim($v['BD_PASS']);
		$port = trim($v['BD_PORT']);
		$base = trim($v['BD_SID_BD']);
	}
*/
	$data = 'ORACLE';

	// echo $query."<br><br>**".$data;
	//cierra_conexion('MYSQL',$server1);
	switch ($data) {
		/*
	    case 'MYSQL':
			$server = mysqli_connect($host, $user,$pass, $base,$port);
			mysqli_set_charset($server, "utf8");
	        break;
	    case 'POSTGRESQL':
			$server = pg_connect("host=".$host." user=".$user." password='".$pass."' dbname=".$base." port=".$port." options='--client_encoding=UTF8'");
	        break;
	    case 'SQLSERVER':
			$serverName = $host.", ".$port;
			$connectionOptions = array(
				"Database" => $base,
				"Uid" => $user,
				"PWD" => $pass,
				"CharacterSet" => "UTF-8"
			);
			$server = sqlsrv_connect($serverName, $connectionOptions);
	        break;
	        */
	    case 'ORACLE':
	    	$Gtabla_conexion = '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.100.110.79)(PORT=1521)))(CONNECT_DATA=(SERVER=DEDICATED)(SERVICE_NAME=TOTVS)))';
			$server = ocilogon('TOTVS12C', 'TOTVS12C', $Gtabla_conexion, 'AL32UTF8');
			//$server = oci_connect($user, $pass, $host.':'.$port.'/'.$base, "AL32UTF8");
	        break;
		default:
			echo "Problemas con Archivo de Conexion";//"<script>alert('Problemas con Archivo de Conexion');form1.submit();</script>";
	}
	$retornar[0] = $data;
	$retornar[1] = $server;
	$error = error_db($data,$server);
	if($error == ''){
		return ($retornar);
	}else{
		echo "<script>alert('".$error."');form1.submit();</script>";
	}
}
function error_db($data,$server){
	$error='';
	switch ($data) {
	    case 'MYSQL':
			if (!$server) {
				$error = mysqli_error($server);
			}
	        break;
	    case 'POSTGRESQL':
			if (!$server) {
				$error = pg_error($server);
			}
	        break;
	    case 'SQLSERVER':
			if( $server === false ) {
				$error = sqlsrv_errors($server);
			}
	        break;
	    case 'ORACLE':
			if (!$server) {
				$error = oci_error($server);
			}
	        break;
		default:
			echo "Problemas con Archivo de Conexion"; //"<script>alert('Problemas con Archivo de Conexion');form1.submit();</script>";
	}
	return($error);
}
function querys($query,$data,$server){
	set_time_limit(0);
	switch ($data) {
	    case 'MYSQL':
			$result=mysqli_query($server,$query); //mysql_query($query,$server);
	        break;
	    case 'POSTGRESQL':
			$result = pg_query($server,$query);
	        break;
	    case 'SQLSERVER':
			$result = sqlsrv_query($server, $query); //mssql_query($query,$server);
	        break;
	    case 'ORACLE':
			$result = oci_parse($server, $query); //$result = ociparse($server,$query);
			oci_execute($result); //ociexecute($result);
			 //echo $query."<br>";
	        break;
		default:
			echo $query."Problemas con Archivo de Conexion"; //"<script>alert('Problemas con Archivo de Conexion');form1.submit();</script>";
	}
	return($result);
}
function querys_ora($query,$data,$server){
	set_time_limit(0);
	switch ($data) {
	    case 'ORACLE':
			$sid = oci_parse($server, $query); //$result = ociparse($server,$query);
			$result = oci_execute($sid); //ociexecute($result);
	        break;
		default:
			echo $query."Problemas con Archivo de Conexion"; //"<script>alert('Problemas con Archivo de Conexion');form1.submit();</script>";
	}
	return($result);
}

function cierra_conexion($data,$server){
	switch ($data) {
	    case 'MYSQL':
			mysqli_close($server); //mysql_close($server);
	        break;
	    case 'POSTGRESQL':
			pg_close($server);
	        break;
	    case 'SQLSERVER':
			sqlsrv_close($server); //mssql_close($server);
	        break;
	    case 'ORACLE':
			oci_close($server); //ocilogoff($server);
	        break;
		default:
			echo "Problemas con Archivo de Conexion"; //"<script>alert('Problemas con Archivo de Conexion');form1.submit();</script>";
	}
}
function ver_result($result,$data){
	//ini_set('memory_limit', '1024M'); 
	switch ($data) {
	    case 'MYSQL':
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC); //mysql_fetch_array($result,MYSQL_ASSOC);
	        break;
	    case 'POSTGRESQL':
			$row = pg_fetch_array($result); // pg_fetch_array($result);
	        break;
	    case 'SQLSERVER':
			$row = sqlsrv_fetch_array($result,SQLSRV_FETCH_ASSOC); //mssql_fetch_array($result,MSSQL_ASSOC);SQLSRV_FETCH_NUMERIC,SQLSRV_FETCH_ASSOC
	        break;
	    case 'ORACLE':
			$row = oci_fetch_array($result, OCI_ASSOC); 
	        break;
		default:
			echo "Problemas con Archivo de Conexion"; //"<script>alert('Problemas al hacer consulta');form1.submit();</script>";
	}
	return($row);
}
function db_num_col($result,$data){
	ini_set('memory_limit', '1024M'); 
	switch ($data) {
	    case 'MYSQL':
			$row = mysqli_num_fields($result);
	        break;
	    case 'POSTGRESQL':
			$row = pg_num_fields($result);
	        break;
	    case 'SQLSERVER':
			$row = sqlsrv_num_fields($result);
	        break;
	    case 'ORACLE':
			$row = oci_num_fields($result);
	        break;
		default:
			echo "Problemas con Archivo de Conexion"; //"<script>alert('Problemas al hacer consulta');form1.submit();</script>";
	}
	return($row);
}
function db_num_fil($data,$result){
	ini_set('memory_limit', '1024M'); 
	switch ($data) {
	    case 'MYSQL':
			$row = mysqli_num_rows($result);
	        break;
	    case 'POSTGRESQL':
			$row = pg_num_rows($result);
	        break;
	    case 'SQLSERVER':
			$row = sqlsrv_num_rows($result);
	        break;
	    case 'ORACLE':
			$row = oci_num_rows($result);
	        break;
		default:
			echo "Problemas con Archivo de Conexion"; //"<script>alert('Problemas al hacer consulta');form1.submit();</script>";
	}
	return($row);
}
function db_nom_col($result,$ncol,$data){
	ini_set('memory_limit', '1024M'); 
	switch ($data) {
	    case 'MYSQL':
			$info_campo = mysqli_fetch_field_direct($result,$ncol);
			$row = $info_campo->name;
			//$row = mysqli_fetch_field_direct($result,$ncol);
	        break;
	    case 'POSTGRESQL':
			$row = pg_field_name($result,$ncol);
	        break;
	    case 'SQLSERVER':
			$row = sqlsrv_get_field($result,$ncol);
	        break;
	    case 'ORACLE':
			$row = oci_field_name($result,$ncol);
	        break;
		default:
			echo "Problemas con Archivo de Conexion"; //"<script>alert('Problemas al hacer consulta');form1.submit();</script>";
	}
	return($row);
}

?>