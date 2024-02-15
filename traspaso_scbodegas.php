<?php
//error_reporting(E_ALL);

require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
require_once "./fpdf17/fpdf.php";
include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";


function cmb_origen($bodega){
    global $sb2;
    global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT DISTINCT B2_COD FROM $sb2 WHERE b2_LOCAL='$bodega' AND D_E_L_E_T_<>'*'  ORDER BY B2_COD";
					//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
					//$nro = $v["RUT_TRABAJADOR"];
		$cargar[]=array(
					"CODIGO"=>$v["B2_COD"]
			);
	}

	echo json_encode($cargar);
}


function ver_pedidos($canal_venta,$nro_boleta){
    global $sb1;
    global $tipobd_totvs,$conexion_totvs;
	
	
	
	$hoy = date('Ymd');
    $checked = false;
    $querysel = "SELECT B1_COD,B1_DESC,B1_CODBAR FROM $sb1 WHERE 
				B1_COD LIKE '%$articulo%' AND 
				B1_DESC NOT IN ('*******   NO USAR   *******','NO USAR') AND 
				 D_E_L_E_T_ <> '*'";
		   //echo $querysel;
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    while($fila = ver_result($rss, $tipobd_totvs)){
        $codigo[]=array(
            "CODIGO"		=>trim($fila["B1_COD"]),
            "DESCR"		=>trim($fila["B1_DESC"]),
            "BARRA"		=>trim($fila["B1_CODBAR"]),
            
                     );
    }
    echo json_encode($codigo);
    
}

function cargarInput($barra){
	global $sb1;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$querysel = "SELECT B1_COD,B1_DESC
					FROM $sb1 WHERE B1_CODBAR LIKE '%$barra%' AND D_E_L_E_T_<>'*'";
					//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
					$articulo = $v["B1_COD"];
		$cargar["CODIGO"]=array(
					"COD"	=>$v["B1_COD"],
					"DESCR"	=>utf8_encode($v["B1_DESC"])
			);
	}

	echo json_encode($cargar);
}
function cargar_stock($articulo,$bodega_origen){
	global $sb2;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$querysel = "SELECT B2_QATU FROM $sb2 WHERE B2_COD='$articulo' AND B2_LOCAL='$bodega_origen'";
					//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$csk[]=array(
					"STOCK"	=>$v["B2_QATU"]
			);
	}

	echo json_encode($csk);
}
function cargar_traspaso($numero){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
    
    //$nro = substr($numTrab,0,stripos($numTrab,'-'));
    $querysel = "SELECT BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO
				FROM $traspaso_bodega 
			   WHERE BO_SECUENCIA='$numero'
			   AND D_E_L_E_T_<>'*'
			   GROUP BY BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO";
		//echo $querysel;
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    while($fila=ver_result($rss, $tipobd_totvs)){
        $num["NUMERO"]=array(
            "BO_SECUENCIA"          =>$fila["BO_SECUENCIA"],//
            "BO_ORIGEN"            	=>$fila["BO_ORIGEN"],//
			"BO_DESTINO"		 	=>$fila["BO_DESTINO"],//
            "BO_FTRASPASO"          =>formatDate($fila["BO_FTRASPASO"]),//
           
        );
    }

    $dataAsig      = get_articulos($numero);
    if(count($dataAsig)>0){
        $num["ASIGNACION"]=$dataAsig;
    }

    //echo "<pre>";
    //print_r($trab);
    //echo "</pre>";
    //$dpto["success"]=true;
    echo json_encode($num);
}
function get_articulos($numero){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
    
    $querysel = "SELECT BO_SECUENCIA,
				(SELECT ZZ1_BODEGA||' - '||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT ZZ1_BODEGA||' - '||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
				BO_COD,BO_CAMARTICULO,BO_DESCR,BO_FTRASPASO,BO_CANTIDAD,R_E_C_N_O_
				FROM $traspaso_bodega 
			   WHERE BO_SECUENCIA='$numero'
			   AND D_E_L_E_T_<>'*'
			   AND BO_STATUS NOT IN ('30')
			   ORDER BY R_E_C_N_O_";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	//echo $querysel;
    while($fila=ver_result($rss, $tipobd_totvs)){
        $get_art[]=[
            "BO_SECUENCIA"		=>$fila["BO_SECUENCIA"],
            "BO_ORIGEN"			=>$fila["BO_ORIGEN"],
            "BO_DESTINO"		=>$fila["BO_DESTINO"],
            "BO_COD"			=>$fila["BO_COD"],
            "BO_CAMARTICULO"	=>$fila["BO_CAMARTICULO"],
            "BO_DESCR"			=>utf8_encode($fila["BO_DESCR"]),
            "BO_FTRASPASO"		=>formatDate($fila["BO_FTRASPASO"]),
            "BO_CANTIDAD"		=>$fila["BO_CANTIDAD"],
            "RECNO"				=>$fila["R_E_C_N_O_"]
            
        ];
    }
   return $get_art;
}
function get_secuencia($bodega_origen){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$querysel = "SELECT NVL(SUBSTR(MAX(BO_SECUENCIA),4,7)+1,1) AS BO_TRASPASO
					FROM $traspaso_bodega where BO_ORIGEN ='$bodega_origen' and D_E_L_E_T_<>'*'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rss, $tipobd_totvs)){
		$secuencia = $fila['BO_TRASPASO'];
		$secuencia = 	str_pad($secuencia,6,'0', STR_PAD_LEFT);
		$secuencia = $bodega_origen."-".$secuencia;
		$nro[] = array(
		'BO_TRASPASO' => $secuencia
		);
	}
	//echo "Nro_trabajador ".$querysel;
	echo json_encode($nro);
}
function getrecno_secuencia($bodega_origen){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$querysel = "SELECT NVL(MAX(BO_RSECUENCIA),0)+1 AS CORRELATIVO FROM $traspaso_bodega WHERE BO_ORIGEN='$bodega_origen'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rss, $tipobd_totvs)){
	
		$nro[] = array(
		'RECNO' => $fila["CORRELATIVO"]
		);
	}
	//echo "Nro_trabajador ".$querysel;
	echo json_encode($nro);
}
function generaInsert($frm){
    //global $conexion;
   	global $articulo,$destino;
    
    //echo"<pre>";
    //print_r($frm);
    //echo"</pre>";

   	//$existe_codigo_bodega 	= existe_codigo_bodega($articulo,$destino);
    $asig = [];
    foreach($frm as $clave => $valor){
        if($clave<>'insertar' and trim($valor)<>''  and substr($clave,0,3)<>'asg'){
            $var = $clave;
            ${$var} = $valor;//se definen variables		
			
        }

			if(substr($clave,0,3)=='asg'){
				$asig[substr($clave,4)]=trim($valor);
				//echo $clave."<br>";
			}
	
	}

   
	if(count($asig)>0){//el array $haberes por lo menos siempre tendra el valor de cantidad de filas de la tabla haberes
		insertar_traspaso($asig);
	}

}
function busca_um($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_UM FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){   
       $codigo=trim($row2['B1_UM']);
    }

	if($codigo==""){
		return $codigo="99";
	}else{
		return $codigo;
	}
}
function busca_segum($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_SEGUM FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){ 
       $codigo=trim($row2['B1_SEGUM']);
    }

	if($codigo==""){
		return $codigo="99";
	}else{
		return $codigo;
	}
}
function busca_conv($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_CONV FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){ 
       $codigo=trim($row2['B1_CONV']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
}
function busca_codbar($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_CODBAR FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){      
       $codigo=trim($row2['B1_CODBAR']);
    }

	if($codigo==""){
		return $codigo="99";
	}else{
		return $codigo;
	}
}
function busca_conta($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_CONTA FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){
       $codigo=trim($row2['B1_CONTA']);
    }

	if($codigo==""){
		return $codigo="99";
	}else{
		return $codigo;
	}
}
function busca_desc($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_DESC FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){
       $codigo=trim(str_replace("'",'',$row2['B1_DESC']));
    }

	if($codigo==""){
		return $codigo="99";
	}else{
		return $codigo;
	}
}
function busca_grupo($articulo){
	global $sb1;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B1_GRUPO FROM $sb1 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){
       $codigo=trim($row2['B1_GRUPO']);
    }

	if($codigo==""){
		return $codigo="99";
	}else{
		return $codigo;
	}
}
function busca_custo1($origen,$articulo){
	global $sb2;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B2_CM1 FROM $sb2 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
	//echo "QUERY  : ".$queryin;
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){    
       $codigo=trim($row2['B2_CM1']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
}
function busca_custo5($origen,$articulo){
	global $sb2;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B2_CM5 FROM $sb2 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){
       $codigo=trim($row2['B2_CM5']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
}
function existen_codigos_z2b($cod,$bo_origen){
	//global $conexion;//apunta  a la 230 BD PTL 
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
    //global $conexion3;//apunta  a la 45 BD TOTVS DESARRROLLO
	
		
		$querysel_1 = "SELECT count(*) AS FILAS FROM SB2010 WHERE B2_COD='$cod' AND B2_LOCAL='$bo_origen' AND D_E_L_E_T_<>'*'";
		//echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		while($v = ver_result($rss_1, $tipobd_totvs)){
		$ex_filas[]=array(
					"FILAS"	=>$v["FILAS"]
			);
	}

	echo json_encode($ex_filas);
}
function insertar_traspaso($post){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS

//	echo"<pre> externo:";
//    print_r($post);
//    echo"</pre>";
	
	//$bodega = $post["bodega_origen1"];
	//$bodega = substr($bodega,0,12);
	//echo "numero   : ".$numerog."<br>";
	//foreach($post as $clave => $valor){
	//	
	//	echo $clave. '=>' .$valor."<br>";
	//}
	//$seq_totvs=consultaseq();
	//	$secuencia_totvs = $seq_totvs[2];
	$recno			= recno_traspaso();
    //for($i=1;$i<=$recno;){
		
        $numero  			= $post["numero"];
        //$barra  			= $post["barra"];
        $origen 			= $post["bodega_origen"];
        $destino    		= $post["bodega_destino"];
        $articulo   		= $post["articulo"];
        $cambio_articulo    = $post["cambio_articulo"];
		if($cambio_articulo == ''){
			$cambio_articulo=$post["articulo"];
		}
        $descr      		= $post["descripcion_articulo"];
			$descr = str_replace("'",'',$descr);
        $ftraspaso  		= $post["fec_traspaso"];
        $cantidad   		= $post["cantidad"];
        $hoy         		= date('Ymd');
		$hora		 		= date('His');
		$mes		 		= date('m');
		$ano		 		= date('Y');
		$um 		   		= busca_um($articulo);	
		$descripcion   		= busca_desc($articulo);	
		$segum 		   		= busca_segum($articulo);	
		$cod_barra 	   		= busca_codbar($articulo);	
		$conv 	   	   		= busca_conv($articulo);	
		$conta 	   	   		= busca_conta($articulo);	
		$grupo 	   	   		= busca_grupo($articulo);
		$existe_codigo 			= existe_codigo($articulo,$cambio_articulo,$numero);
		//$existe_codigo_bodega   = existen_codigos_z2b($cambio_articulo,$destino);
		$cant2um			= $cantidad * $conv;
		$recno_traspaso	= recno_secuencia_s($numero);

		
		$custo1 = busca_custo1($origen,$articulo);
		$custo1_save = $custo1*$cantidad;
		//$custo1_save = 0;
		
		$custo5 = busca_custo5($origen,$articulo);
		$custo5_save = $custo5*$cantidad;
		//$custo5_save = 0;
		
		
		
		
		$ftraspaso = str_replace(array('/'),'',$ftraspaso);
		$parte1 = substr($ftraspaso,4,8); //12
		$parte2 = substr($ftraspaso,2,2); //345
		$parte3 = substr($ftraspaso,0,2); //456
		$ftraspaso = $parte1.$parte2.$parte3;
		//if($cambio_articulo==""){$cambio_articulo==" ";}
		//die();
			/**
			  * Insertar Asignacion
			  */
			$user = $_SESSION["user"];
			//echo $user;
			

				//if($existe_codigo == 0){
					$queryin = "INSERT INTO $traspaso_bodega(BO_SECUENCIA, BO_COD,BO_CAMARTICULO, BO_DESCR, BO_ORIGEN, BO_DESTINO, BO_CANTIDAD, BO_FTRASPASO, BO_FECHA, R_E_C_N_O_, D_E_L_E_T_,
									BO_USUARIO,BO_STATUS,BO_UM,BO_CODBAR,BO_CONV,BO_SEGUM,BO_RSECUENCIA,BO_CONTA,BO_GRUPO,BO_CUSTO1,BO_CUSTO5,BO_NUMSEQ,BO_CANT2UM) 
									VALUES('$numero', '$articulo','$cambio_articulo', '$descripcion', '$origen', '$destino', $cantidad, '$ftraspaso', '$hoy', $recno, ' ',
									'$user','10','$um','$cod_barra',$conv,'$segum',$recno_traspaso,'$conta','$grupo',$custo1_save,$custo5_save,'0',$cant2um)";
					$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
					//echo $queryin;
				//}
}
function existe_codigo($articulo,$cambio_articulo,$numero){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$querycount = "SELECT count(*) as FILAS FROM $traspaso_bodega where BO_COD='$articulo' and BO_CAMARTICULO='$cambio_articulo' and BO_SECUENCIA='$numero' and D_E_L_E_T_<>'*'";
	$rsc = querys($querycount, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rsc, $tipobd_totvs);
    if($fila["FILAS"]>0){
        return 1;
    }else{
        return 0;
    }
}
function existe_codigo_bodega($articulo,$bodega){
	global $sb2;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$querycount = "SELECT COUNT(B2_COD) AS FILAS FROM $sb2 WHERE B2_LOCAL='$bodega' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rsc = querys($querycount, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rsc, $tipobd_totvs)){
		$codigo[]=array(
            "FILAS"		=>trim($fila["FILAS"])
			
                     );
    }
    echo json_encode($codigo);
	
}
function eliminar_articulo($articulo,$numero,$rsecuencia){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$queryup ="UPDATE $traspaso_bodega SET D_E_L_E_T_='*' WHERE BO_COD=trim('$articulo') AND BO_SECUENCIA='$numero' and BO_RSECUENCIA='$rsecuencia'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	//echo $queryup;
  
}
function revisa_confirmacion($numero){
	global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	global $traspaso_bodega;
	global $sb1;
	
	$querysel = "SELECT BO_COD,BO_CAMARTICULO FROM $traspaso_bodega WHERE BO_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod_salida = $v["BO_COD"];
		$cod_entrada = $v["BO_CAMARTICULO"];
		
		$queryexist = "SELECT COUNT(*) AS FILAS FROM $sb1 WHERE B1_COD ='$cod_salida' AND D_E_L_E_T_<>'*'";
		$rse = querys($queryexist, $tipobd_totvs, $conexion_totvs);
		$v = ver_result($rse, $tipobd_totvs);
		$filas_salida = $v["FILAS"];
		if($filas_salida == 0){
			echo "ARTICULO DE SALIDA <strong> $cod_salida </strong> NO EXISTE EN MAESTRA DE MONARCH";
			die();
		}
		
	}
}
function existen_codigos_2($numero){
	global $traspaso_bodega;
	global $sb2;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
    //global $conexion3;//apunta  a la 45 BD TOTVS DESARRROLLO
	
	$querysel = "SELECT BO_CAMARTICULO,BO_DESTINO FROM $traspaso_bodega WHERE BO_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod 		= $v['BO_CAMARTICULO'];
		$bo_destino 	= $v['BO_DESTINO'];
		
		$querysel_1 = "SELECT count(*) AS FILAS FROM $sb2 WHERE B2_COD='$cod' AND B2_LOCAL='$bo_destino' AND D_E_L_E_T_<>'*'";
		//echo $querysel_1."<br>";
		$rss_1 = db_exec($conexion2,$querysel_1);
		$v1 = db_fetch_array($rss_1);
		$filas = $v1["FILAS"];
		if($filas == 0){
			//echo '<script language="javascript">alert("ERROR : CODIGO '.$cod.' NO EXISTEN EN BODEGA DE DESTINO");</script>';
			echo "<font color=red>ERROR : CODIGO <strong>$cod</strong> NO EXISTE EN BODEGA DE DESTINO</font>";
			die();
		}
		
	}
	//return $filas;
}
function existe_traspaso($numero){	
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	global $sd3;
	
	$secuencia  = str_replace('-','',$numero);
	$querysel_1 = "SELECT count(*) AS FILAS FROM $sd3 WHERE D3_DOC='$secuencia' AND D_E_L_E_T_<>'*'";
		//echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss_1, $tipobd_totvs);
		$filas = $v1["FILAS"];
		if($filas > 0){
			//echo '<script language="javascript">alert("ERROR : CODIGO '.$cod.' NO EXISTEN EN BODEGA DE DESTINO");</script>';
			echo "ERROR : NUMERO DE DOCUMENTO $secuencia YA EXISTE, TRASPASO NO FUE CARGADO";
			die();
		}
}
function confirmar_traspaso($numero){
	global $traspaso_bodega;
	global $sd3;
	global $sb2;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	//PASO 1 : CONFIRMACION DE TRASPASO
	$queryup ="UPDATE $traspaso_bodega SET BO_STATUS='20' WHERE  BO_SECUENCIA='$numero'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	//********************************************************************************************
	
	//PASO 2: EJECUTAR TRASPASO
	$hoy 	= date('Ymd');

	existen_codigos_2($numero);
   
   //echo "MUERTO";
   //die();
	existe_traspaso($numero);
	
	$querysel = "SELECT '01'         	AS D3_FILIAL,
						'999'       	AS D3_TM,
						BO_COD 			AS D3_COD,
						BO_UM       	AS D3_UM,
						BO_CANTIDAD 	AS D3_QUANT,
						BO_CANT2UM  	AS D3_QUANT2,
						BO_CONTA    	AS D3_CONTA,
						BO_ORIGEN   	AS D3_LOCAL,
						BO_SECUENCIA 	AS D3_DOC,
						BO_FECHA 		AS D3_EMISSAO,
						BO_GRUPO    	AS D3_GRUPO,
						''          	AS D3_CUSTO1,
						''          	AS D3_CUSTO5,
						''          	AS D3_NUMSEQ,
						BO_SEGUM    	AS D3_SEGUM,
						BO_CANTIDAD 	AS D3_QTSEGUM,
						'PT'         	AS D3_TIPO,
						''          	AS D3_USUARIO,
						'E9'        	AS D3_CHAVE,
                        R_E_C_N_O_ 		AS RECNO
        FROM $traspaso_bodega
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss = db_exec($conexion2,$querysel);
		//echo $querysel."<br>";
		//die();
		while($v = db_fetch_array($rss)){
			$filia 		= $v["D3_FILIAL"];
			$tm 		= $v["D3_TM"];
			$cod_mch 	= $v["D3_COD"];
			$um 		= $v["D3_UM"];
			$cantidad	= $v["D3_QUANT"];
			$cant2um	= $v["D3_QUANT2"];
			$conta		= $v["D3_CONTA"];
			$bodega		= $v["D3_LOCAL"];
			$doc 		= $v["D3_DOC"];
				$doc = str_replace('-','',$doc);
			$emision 	= $v["D3_EMISSAO"];
			$grupo 		= $v["D3_GRUPO"];
			$emision 	= $v["D3_EMISSAO"];
			$custo1 	= busca_custo1_2($bodega,$cod_mch);
				$custo1 = $custo1*$cantidad;
			$custo5 	= busca_custo5_2($bodega,$cod_mch);
				$custo5 = $custo5*$cantidad;
			$segum 		= $v["D3_SEGUM"];
			$tcantidad 	= $v["D3_QTSEGUM"];
			$tipo 		= $v["D3_TIPO"];
			$chave 		= $v["D3_CHAVE"];
			$recno_z2b 		= $v["RECNO"];
			$seq_totvs=consultaseq();
			$recno_sd3 = recno_sd3();
			$secuencia_totvs_id 	= $seq_totvs[0];
			$secuencia_totvs_est 	= $seq_totvs[1];
				$secuencia_totvs_est = substr($secuencia_totvs_est,0,199);
			$secuencia_totvs 		= $seq_totvs[2];
			
			$queryin = "INSERT INTO $sd3 (D3_FILIAL, D3_TM, D3_COD, D3_UM, D3_QUANT, D3_CF,
												D3_CONTA, D3_OP, D3_LOCAL, D3_DOC, D3_EMISSAO, D3_MCANAL,
												D3_GRUPO, D3_CUSTO1, D3_CUSTO2, D3_CUSTO3, D3_CUSTO4, D3_CUSTO5,
												D3_CC, D3_PARCTOT, D3_ESTORNO, D3_NUMSEQ, D3_SEGUM, D3_QTSEGUM,
												D3_TIPO, D3_NIVEL, D3_USUARIO, D3_REGWMS, D3_PERDA, D3_DTLANC,
												D3_TRT, D3_CHAVE, D3_IDENT, D3_SEQCALC, D3_RATEIO, D3_LOTECTL,
												D3_NUMLOTE, D3_DTVALID, D3_LOCALIZ, D3_NUMSERI, D3_CUSFF1, D3_CUSFF2,
												D3_CUSFF3, D3_CUSFF4, D3_CUSFF5, D3_ITEM, D3_OK, D3_ITEMCTA,
												D3_CLVL, D3_PROJPMS, D3_TASKPMS, D3_ORDEM, D3_SERVIC, D3_STSERV,
												D3_OSTEC, D3_POTENCI, D3_TPESTR, D3_REGATEN, D3_ITEMSWN, D3_CC2,
												D3_DOCSWN, D3_MDOCORI, D3_ITEMGRD, D3_BORRAR, D3_STATUS, D3_CUSRP1,
												D3_CUSRP2, D3_CUSRP3, D3_CUSRP4, D3_CUSRP5, D3_CMRP, D3_MOEDRP,
												D3_MOEDA, D3_EMPOP, D3_DIACTB, D3_PMICNUT, D3_CMFIXO, D3_NODIA,
												D3_GARANTI, D3_PMACNUT, D3_NRBPIMS, D3_CODLAN, D_E_L_E_T_, R_E_C_N_O_)
						VALUES ('$filia','$tm','$cod_mch','$um',$cant2um,'RE4',
								'$conta',' ','$bodega','$doc','$emision',' ',
								'$grupo',$custo1,0,0,0,$custo5,
								' ',' ',' ','$secuencia_totvs','$segum',$tcantidad,
								'$tipo',' ','AUTOCONECTOR',' ',0,' ',
								' ','$chave',' ',' ',0,' ',
								' ',' ',' ',' ',0,0,
								0,0,0,' ',' ',' ',
								' ',' ',' ',' ',' ',' ',
								' ',0,' ',' ',' ',' ',
								' ',' ',' ',' ',' ',0,
								0,0,0,0,0,' ',
								' ',' ',' ',0,0,' ',
								' ',0,' ',' ',' ',$recno_sd3)";
			$rsi = db_exec($conexion2,$queryin);
			//echo "INSERT : ".$queryin."<br>";
			//actualiza numero de secuencia en tabla de origen de traspasos
			$queryup = "UPDATE $traspaso_bodega SET BO_NUMSEQ = '$secuencia_totvs',BO_NUMSEQ_ID='$secuencia_totvs_id',
														BO_NUMSEQ_DES='$secuencia_totvs_est' WHERE  BO_SECUENCIA='$numero' and R_E_C_N_O_ = $recno_z2b";//BO_COD='$cod_mch' AND 
			$rsu = db_exec($conexion2,$queryup);//DESCOMENTAR
			
			//actualizar cantidad en tabla de stock($sb2) 
			$queryup_sd2 = "UPDATE $sb2 SET B2_QATU=B2_QATU-$cant2um WHERE  B2_COD='$cod_mch' AND B2_LOCAL='$bodega' ";
			$rsu_sd2 = db_exec($conexion2,$queryup_sd2);
		}
		
		$querysel2 = "SELECT '01'         	AS D3_FILIAL,
						'499'       		AS D3_TM,
						BO_CAMARTICULO      AS D3_COD,
						BO_UM       		AS D3_UM,
						BO_CANTIDAD 		AS D3_QUANT,
						BO_CANT2UM  		AS D3_QUANT2,
						BO_CONTA    		AS D3_CONTA,
						bo_destino   		AS D3_LOCAL,
						BO_SECUENCIA 		AS D3_DOC,
						BO_FECHA	 		AS D3_EMISSAO,
						BO_GRUPO    		AS D3_GRUPO,
						''          		AS D3_CUSTO1,
						''          		AS D3_CUSTO5,
						BO_NUMSEQ   		AS D3_NUMSEQ,
						BO_SEGUM    		AS D3_SEGUM,
						BO_CANT2UM 			AS D3_QTSEGUM,
						'PT'         		AS D3_TIPO,
						''          		AS D3_USUARIO,
						'E0'        		AS D3_CHAVE
        FROM $traspaso_bodega
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss2 = db_exec($conexion2,$querysel2);
		//echo $querysel2."<br>";
		//die();
		while($v = db_fetch_array($rss2)){
			$filia 		= $v["D3_FILIAL"];
			$tm 		= $v["D3_TM"];
			$cod_mch 	= $v["D3_COD"];
			$um 		= $v["D3_UM"];
			$cantidad	= $v["D3_QUANT"];
			$cant2um	= $v["D3_QUANT2"];
			$conta		= $v["D3_CONTA"];
			$bodega		= $v["D3_LOCAL"];
			$doc 		= $v["D3_DOC"];
				$doc = str_replace('-','',$doc);
			$emision 	= $v["D3_EMISSAO"];
			$grupo 		= $v["D3_GRUPO"];
			$emision 	= $v["D3_EMISSAO"];
			$custo1 	= busca_custo1($bodega,$cod_mch);
				$custo1 = $custo1*$cantidad;
			$custo5 	= busca_custo5($bodega,$cod_mch);
				$custo5 = $custo5*$cantidad;
			$secuencia_t 		= $v["D3_NUMSEQ"];
			$segum 		= $v["D3_SEGUM"];
			$tcantidad 	= $v["D3_QTSEGUM"];
			$tipo 		= $v["D3_TIPO"];
			$chave 		= $v["D3_CHAVE"];
			//$seq_totvs=consultaseq();
			$recno_sd3 = recno_sd3();
			//$secuencia_totvs = $seq_totvs[2];
			
			$queryin2 = "INSERT INTO $sd3 (D3_FILIAL, D3_TM, D3_COD, D3_UM, D3_QUANT, D3_CF,
												D3_CONTA, D3_OP, D3_LOCAL, D3_DOC, D3_EMISSAO, D3_MCANAL,
												D3_GRUPO, D3_CUSTO1, D3_CUSTO2, D3_CUSTO3, D3_CUSTO4, D3_CUSTO5,
												D3_CC, D3_PARCTOT, D3_ESTORNO, D3_NUMSEQ, D3_SEGUM, D3_QTSEGUM,
												D3_TIPO, D3_NIVEL, D3_USUARIO, D3_REGWMS, D3_PERDA, D3_DTLANC,
												D3_TRT, D3_CHAVE, D3_IDENT, D3_SEQCALC, D3_RATEIO, D3_LOTECTL,
												D3_NUMLOTE, D3_DTVALID, D3_LOCALIZ, D3_NUMSERI, D3_CUSFF1, D3_CUSFF2,
												D3_CUSFF3, D3_CUSFF4, D3_CUSFF5, D3_ITEM, D3_OK, D3_ITEMCTA,
												D3_CLVL, D3_PROJPMS, D3_TASKPMS, D3_ORDEM, D3_SERVIC, D3_STSERV,
												D3_OSTEC, D3_POTENCI, D3_TPESTR, D3_REGATEN, D3_ITEMSWN, D3_CC2,
												D3_DOCSWN, D3_MDOCORI, D3_ITEMGRD, D3_BORRAR, D3_STATUS, D3_CUSRP1,
												D3_CUSRP2, D3_CUSRP3, D3_CUSRP4, D3_CUSRP5, D3_CMRP, D3_MOEDRP,
												D3_MOEDA, D3_EMPOP, D3_DIACTB, D3_PMICNUT, D3_CMFIXO, D3_NODIA,
												D3_GARANTI, D3_PMACNUT, D3_NRBPIMS, D3_CODLAN, D_E_L_E_T_, R_E_C_N_O_)
						VALUES ('$filia','$tm','$cod_mch','$um',$cant2um,'DE4',
								'$conta',' ','$bodega','$doc','$emision',' ',
								'$grupo',$custo1,0,0,0,$custo5,
								' ',' ',' ','$secuencia_t','$segum',$tcantidad,
								'$tipo',' ','AUTOCONECTOR',' ',0,' ',
								' ','$chave',' ',' ',0,' ',
								' ',' ',' ',' ',0,0,
								0,0,0,' ',' ',' ',
								' ',' ',' ',' ',' ',' ',
								' ',0,' ',' ',' ',' ',
								' ',' ',' ',' ',' ',0,
								0,0,0,0,0,' ',
								' ',' ',' ',0,0,' ',
								' ',0,' ',' ',' ',$recno_sd3)";
			$rsi2 = db_exec($conexion2,$queryin2);
			//echo $queryin2."<br>";
			
			$queryup1_sd2 = "UPDATE $sb2 SET B2_QATU=B2_QATU+$cant2um WHERE  B2_COD='$cod_mch' AND B2_LOCAL='$bodega'";
			$rsu1_sd2 = db_exec($conexion2,$queryup1_sd2);
		}
		
		$queryup_fin = "UPDATE $traspaso_bodega SET  BO_STATUS='40', BO_FTRASPASO='$hoy' WHERE  BO_SECUENCIA='$numero' ";
		$rsu_fin = db_exec($conexion2,$queryup_fin);
		echo "PROCESO TERMINADO <br>";
		
		
	
	//echo $queryup;
	if(db_num_rows($rsu)<>0 or db_num_rows($rsu)<>null){
			echo "TRASPASO # <strong>$numero</strong> CONFIRMADO Y VALIDADO DE FORMA AUTOMATICA ! <br> <br> <br> <a target='_blank' class='btn btn-danger' href='solicitud_traspaso.php?id=$numero'><strong>Descarga Aqui Ficha de Traspaso</a></strong> ";
		}else{
			echo "ERROR: TRASPASO NO CONFIRMADO !";
		}    
}
function busca_custo1_2($origen,$articulo){
	global $sb2;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B2_CM1 FROM $sb2 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo'";
	//echo "QUERY busca_custo1 : ".$queryin."<br>";
	$rss = db_exec($conexion2,$queryin);
	while ($row2 =db_fetch_array($rss)) {                
       $codigo=trim($row2['B2_CM1']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
}
function busca_custo5_2($origen,$articulo){
	global $sb2;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$codigo='';
	$queryin = "SELECT B2_CM5 FROM $sb2 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo'";
	$rss = db_exec($conexion2,$queryin);
	while ($row2 =db_fetch_array($rss)) {                
       $codigo=trim($row2['B2_CM5']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
}
function recno_sd3(){
    global $sd3;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
    //global $conexion3;//apunta  a la 45 BD TOTVS DESARRROLLO	
	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS R_E_C_N_O_ FROM $sd3";
	$rs = db_exec($conexion2, $select);
	$fila = db_fetch_array($rs);
	$recno = $fila['R_E_C_N_O_'];
	return $recno;
}
function count_filas($numero){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS

	
	$select = "SELECT NVL(MAX(BO_RSECUENCIA),0)+1 AS CORRELATIVO FROM $traspaso_bodega WHERE BO_SECUENCIA='$numero' ";
	$rs = db_exec($conexion2, $select);
	//echo $select;
	while($fila = db_fetch_array($rs)){
		$codigo[]=array(
            "FILAS"		=>trim($fila["CORRELATIVO"])
			
                     );
    }
    echo json_encode($codigo);
	
}
function recno_traspaso(){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS

	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS CORRELATIVO FROM $traspaso_bodega";
	$rs = db_exec($conexion2, $select);
	$fila = db_fetch_array($rs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
	
}
function recno_secuencia_s($numero){
	global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS

	
	$select = "SELECT NVL(MAX(BO_RSECUENCIA),0)+1 AS CORRELATIVO FROM $traspaso_bodega WHERE BO_SECUENCIA='$numero' ";
	$rs = db_exec($conexion2, $select);
	$fila = db_fetch_array($rs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
	
}

function fechas(){
	//global $conexion; //APUNTA A 100.230 PTL
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$querysel = "SELECT TO_CHAR(SYSDATE,'DD/MM/YYYY') AS FECHA1 FROM DUAL";
	$rss = db_exec($conexion2,$querysel);
	while($v = db_fetch_array($rss)){
			$fec[] = array(
			'FECHA1' => $v['FECHA1'],
			//'FECHA2' => $v['FECHA2'],
			
		);
	}
	echo json_encode($fec);
}
function recno_secuencia(){
	global $traspaso_bodega;
	global $tipobd_totvs,$conexion_totvs;//APUNTA A 100.232 TOTVS
	
	$querysel = "SELECT MAX(R_E_C_N_O_)+1 AS RECNO FROM $traspaso_bodega";
	$rss = db_exec($conexion2,$querysel);
	while($v = db_fetch_array($rss)){
			$fec[] = array(
			'RECNO' => $v['RECNO']
			//'FECHA2' => $v['FECHA2'],
			
		);
	}
	echo json_encode($fec);
}
function envia_correo_traspaso($to, $asunto, $msj, $adjunto) {
    global $tipobd_totvs,$conexion_totvs;

    $mail = new PHPMailer();        // crea un nuevo object
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();                // habilita SMTP
    $mail->SMTPDebug = 0;           // depuración: 1 = errores y messages, 2 = mensajes solamente
    $mail->Debugoutput = 'html';
    $mail->SMTPAuth = true;         // autenticación habilitada
    $mail->SMTPSecure = 'ssl';      // transferencia segura habilitada requerida por Gmail
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->Username = C_USER_MCH; 
    $mail->Password = C_PASS_MCH;
    
    $mail->SetFrom(FROM_MCH, FROM_NAME_MCH);  //remitente
    $mail->Subject = $asunto;          //asunto
    if(is_array($to)){
        foreach($to as $destinatario){
        $mail->AddAddress($destinatario);             //destinatario
        //echo "ENVIANDO MAIL A: ".$destinatario."<br>\n";
        }
    }else{
        $mail->AddAddress($to);
    }
    
    if($adjunto <> ''){
		
        $mail->addAttachment($adjunto);     //archivo adjunto
    }
    
    $mail->Body = $msj;                //cuerpo mensaje
    $mail->AltBody = $msj;             //cuerpo mensaje no html

    if($mail->Send()){
        return true;
    }else{
        echo "Mailer Error: " . $mail->ErrorInfo ."<br>";
    }
}
function confirmacion_correo($numero){
    global $traspaso_bodega;
    global $tipobd_totvs,$conexion_totvs;//apunta  a la 232 BD TOTVS
	
	$s = "SELECT BO_DESTINO FROM $traspaso_bodega 
			WHERE BO_SECUENCIA='$numero'
			GROUP BY BO_DESTINO";
	$rss = db_exec($conexion2,$s);
	$v = db_fetch_array($rss);
	$bodega = $v["BO_DESTINO"];
	switch($bodega){
		case '01':
			$to = array('imendoza@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '02':
			$to = array('macuna@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '04':
			$to = array('pcarrasco@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '05':
		case '15':
		case '31':
			$to = array('jadillegrez@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '11':
			$to = array('jromani@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '50':
			$to = array('rinfante@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '14':
			$to = array('splacencia@dicotex.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
			break;
		case '42':
			$to = array('gpuyol@grupomonarch.cl');
			break;
	}
	
	$asunto = "TRASPASOS ENTRE BODEGAS # $numero";
    $msj    = "<h3><strong>Se ha generado un traspaso entre bodegas con ID $numero</strong></h3> ";
			$titulo =" ";
			$pie='<br><br>Este mensaje se ha generado automaticamente, por favor NO RESPONDER.<br><br>Atte. Informática Monarch.';
			$msj=$msj.$titulo.$pie;
			
			//$to=array('gpuyol@grupomonarch.cl'/*,'dlacroix@grupomonarch.cl'*//*,'msotomayor@grupomonarch.cl'*/);
			$adjunto = "traspasos_bodegas/".$numero.".pdf";
			envia_correo_traspaso($to, $asunto, $msj,$adjunto);
}
function ver_pedido_impreso($numero){

	
	//global $conexion;
	global $tipobd_totvs,$conexion_totvs;
	global $traspaso_bodega;
	//Titulos
$pdf=new FPDF();

//*************************************************************************************************
//*************************************************************************************************
//Datos Personales
	
	$id_traspaso = $numero;
	
	$hoy = date('d/m/Y');
	$pdf->SetTitle('TRASPASOS ENTRE BODEGAS '.$id_traspaso);	
	$querysel ="SELECT DISTINCT  BO_SECUENCIA,
				BO_FTRASPASO,
				(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
				(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
				BO_USUARIO
				FROM $traspaso_bodega
				WHERE BO_SECUENCIA='$id_traspaso'
				AND D_E_L_E_T_<>'*'";
				//echo $querysel;
	$n=0;
	$rss = db_exec($conexion2,$querysel);
	while($v = db_fetch_array($rss)){
		$id	 		 = $v["BO_SECUENCIA"];
		$ftrapaso	 = $v["BO_FTRASPASO"];
		$origen	 	 = $v["BO_ORIGEN"];
		$destino	 = $v["BO_DESTINO"];
		$ususario	 = $v["BO_USUARIO"];
		
	////Empresa
	$pdf->AddPage();
	$pdf->Cell(2);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(30,6,'MONARCH.',0,0,'C');
	$pdf->Cell(120);
	$pdf->SetFont('Arial','B',9);
	$pdf->Cell(30,6,'Fec. Impresion : '.$hoy,0,0,'C');
	$pdf->ln();
	$pdf->Cell(80);
	$pdf->SetFont('Arial','BU',16);
	$pdf->Cell(30,9,'TRASPASOS ENTRE BODEGAS',0,1,'C');
	//$pdf->ln();
	$pdf->Cell(80);
	$pdf->SetFont('Arial','BU',16);
	$pdf->Cell(30,9,'#'.$id.'',0,0,'C');
	
	$pdf->ln();
	$pdf->ln();
	

	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(20,6,'FEC. TRASPASO',0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,6,formatDate($ftrapaso),0,0);
	$pdf->SetX(73);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(20,6,'B. ORIGEN',0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,6,$origen,0,1);
	
	//$pdf->ln();
	//nro_trabajador
	//$pdf->SetX(105);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(20,6,'SOLICITANTE',0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,6,$ususario,0,0);
	$pdf->SetX(73);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(20,6,'B. DESTINO',0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(20,6,$destino,0,1);
	
	$pdf->ln();	
	
			$querysel2 = "SELECT BO_COD,BO_CAMARTICULO,BO_SEGUM,BO_DESCR,BO_CANTIDAD,BO_CANT2UM,R_E_C_N_O_
							FROM $traspaso_bodega
							WHERE BO_SECUENCIA='$id_traspaso'
							AND D_E_L_E_T_<>'*'
							ORDER BY R_E_C_N_O_";
			//echo $querysel;
		
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(5,7,'#',1,0,'C');
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(35,7,'SALIDA B.'.substr($origen,0,2),1,0,'C');
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(35,7,'ENTRADA B.'.substr($destino,0,2),1,0,'C');
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(10,7,'UM',1,0,'C');
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(65,7,'DESCRIPCION',1,0,'C');
		//*******************************************
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,7,'UNID. 2UM',1,0,'C');
		//*******************************************
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,7,'UNID. ',1,0,'C');
		//*******************************************
			
			$rss1 = db_exec($conexion2,$querysel2);
			$pdf->ln();
			$i = 0;
			
			
		while($v1 = db_fetch_array($rss1)){
			$i=$i+1;
			$codigo 		= $v1["BO_COD"];
			$codigo_cam		= $v1["BO_CAMARTICULO"];
			$um				= $v1["BO_SEGUM"];
			$desc 			= $v1["BO_DESCR"];
			$cantidad 		= $v1["BO_CANTIDAD"];
			$cant2um 		= $v1["BO_CANT2UM"];
			if($codigo==$codigo_cam){
				$pdf->SetFont('Arial','',8);
				$pdf->cell(5,5,$i,1,0,'C');
			//*******************************************
				$pdf->SetFont('Arial','',8);
				$pdf->cell(35,5,$codigo,1,0,'C');
			//*******************************************
				$pdf->SetFont('Arial','',8);
				$pdf->cell(35,5,$codigo_cam,1,0,'C');
			//*******************************************
				//incentivo
				$pdf->SetFont('Arial','',8);
				$pdf->cell(10,5,$um,1,0,'C');
			//*******************************************
				//incentivo
				$pdf->SetFont('Arial','',8);
				$pdf->cell(65,5,$desc,1,0,'C');
			//*******************************************
				//departamento
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,$cantidad,1,0,'C');
			//*******************************************
				//departamento
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,$cant2um,1,0,'C');
			}else{
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(5,5,$i,1,0,'C',true);
			//*******************************************
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(35,5,$codigo,1,0,'C',true);
			//*******************************************
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(35,5,$codigo_cam,1,0,'C',true);
			//*******************************************
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(10,5,$um,1,0,'C',true);
			//*******************************************
				//incentivo
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(65,5,$desc,1,0,'C',true);
			//*******************************************
				//departamento
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,$cantidad,1,0,'C',true);
			//*******************************************
				//departamento
				$pdf->SetFillColor(190);
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,$cant2um,1,0,'C',true);
				
			}
				$pdf->ln();	
		
		}
		$querysum = "SELECT SUM(BO_CANTIDAD) AS CANTIDAD,SUM(BO_CANT2UM) AS CANT2UM
							FROM $traspaso_bodega
							WHERE BO_SECUENCIA='$id_traspaso'
					AND D_E_L_E_T_<>'*'";
		$rss3 = db_exec($conexion2,$querysum);
			$v = db_fetch_array($rss3);
			$cantidad = $v["CANTIDAD"];
			$cant2um = $v["CANT2UM"];
			$pdf->SetX(160);
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,5,number_format($cantidad,'0',',','.'),1,0,'C');
			$pdf->SetX(180);
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,5,number_format($cant2um,'0',',','.'),1,0,'C');
	}
	//$queryup = "UPDATE $traspaso_bodega SET BO_STATUS='30' WHERE BO_SECUENCIA='$id_traspaso'";
	//$rsu = db_exec($conexion2,$queryup);
	$pdf->ln();	$pdf->ln();	
	$pdf->cell(30,7,'______________________',0,0,'L');
            $pdf->SetX(75);
			$pdf->cell(30,7,'______________________',0,1,'L');
			$pdf->SetFont('Arial','',8);
			$pdf->cell(30,7,'Emisor',0,0,'L');			
            $pdf->SetX(75);
			$pdf->cell(30,7,'Receptor',0,1,'L');

	
	
	$hoy = date('Ymd');
	$url = "./traspasos_bodegas/".$id_traspaso.".pdf";
    $pdf->Output($url,'F');
	//$pdf->Output();

}	
//getUsers();
/*MAIN*/
if(isset($_GET["fecha"])){
	//tmp_nrotrabajador();
    fechas();
}
if(isset($_GET["existe_articulo_bodega"])){
	//tmp_nrotrabajador();
	
	$articulo = $_GET['articulo'];
	$bodega = $_GET['bodega_destino'];
    existe_codigo_bodega($articulo,$bodega);
}
if(isset($_GET["recno"])){
	//$bodega = $_GET['bodega_origen'];
	//tmp_nrotrabajador();
    recno_secuencia();
}
if(isset($_GET["cargar"])){
   $barra = $_GET['barra'];
    cargarInput($barra);
}
if(isset($_GET["carga_stock"])){
   $articulo 	  = $_GET['articulo'];
   $bodega_origen = $_GET['bodega_origen'];
    cargar_stock($articulo,$bodega_origen);
}
if(isset($_GET["existe_codigo"])){
   $articulo 	   = $_GET['articulo'];
   $bodega_origen = $_GET['bodega_origen'];
    existen_codigos_z2b($articulo,$bodega_origen);
}
if(isset($_GET["cargar_inputs"])){
   $numero = $_GET['numero'];
    cargar_traspaso($numero);
}
if(isset($_GET["count"])){
   $numero = $_GET['numero'];
    count_filas($numero);
}
if(isset($_GET["origen"])){
   $bodega = $_GET['bodega_origen'];
    cmb_origen($bodega);
}
if(isset($_GET["bo_secuencia"])){
	//tmp_nrot
   $bodega = $_GET['bodega_origen'];
	
    get_secuencia($bodega);
}
if(isset($_POST["insertar"])){
    /**
      *  Imprime valores de formulario rellenado, útil para debug 
      */
    //echo"<pre>";
    //print_r($_POST);
    //echo"</pre>";
  //  $nro_trabajador = $_POST['nro_trabajador'];
    generaInsert($_POST);
}
if(isset($_GET["eliminar_articulo"])){
	
	$articulo 	= $_GET["articulo"];	
	$numero 	= $_GET["numero"];//secuencia	
	$rsecuencia = $_GET["numFila"];//secuencia	
    eliminar_articulo($articulo,$numero,$rsecuencia);	
}
if(isset($_GET["confima_traspaso"])){
	
	//$articulo = $_GET["articulo"];	
	$numero 	= $_GET["numero"];//secuencia	
    confirmar_traspaso($numero);
	ver_pedido_impreso($numero);
	confirmacion_correo($numero);
}
?>