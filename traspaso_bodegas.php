<?php
//error_reporting(E_ALL);

require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
require_once "./fpdf185/fpdf.php";
include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";


function cmb_origen($bodega){
    global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT DISTINCT B2_COD FROM SB2010 WHERE b2_LOCAL='$bodega' AND D_E_L_E_T_<>'*'  ORDER BY B2_COD";
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
//function cmb_vendedor(){
//	global $conexion2;//APUNTA A 100.230 totvs moch
//	
//      $user       = $_SESSION["user"];
//      $usercod    = $_SESSION["usercod"];
//      $seguridad  = $_SESSION["seguridad"];	
//	
//	if($seguridad == 5){
//		$querysel = "SELECT COD_VENDEDOR,NOMBRE 
//				FROM PVUSUARIO
//				WHERE COD_VENDEDOR IN ('000007','000033','000035','000020','000034','000008','000037','000017','000018','000006')
//				ORDER BY NOMBRE";
//	}else{
//		
//		$querysel1 = "SELECT USUARIO, COD_BODEGA
//                  FROM TOTVS.Z2B_USUARIO_ECOM
//                  WHERE SEGURIDAD ='2'";
//      $rss1 = db_exec($conexion2,$querysel1);
//      $x = db_fetch_array($rss1);
//      $usuario = $x["USUARIO"];
//      $cod_bodega = $x["COD_BODEGA"];
//      $cod = explode(",",$cod_bodega
//      
//      
//      $querysel2 = "SELECT * FROM ";
//	}
//	$rss = db_exec($conexion2,$querysel);
//	while($v = db_fetch_array($rss)){
//		$cargar[]=array(
//					"COD_VENDEDOR"=>$v["COD_VENDEDOR"],
//					"NOMBRE"=>$v["COD_VENDEDOR"].' - '.$v["NOMBRE"]
//			);
//	}
//
//	echo json_encode($cargar);
//	
//}

function ver_pedidos($canal_venta,$nro_boleta){
    global $tipobd_totvs,$conexion_totvs;
	
	
	
	$hoy = date('Ymd');
    $checked = false;
    $querysel = "SELECT B1_COD,B1_DESC,B1_CODBAR FROM SB1010 WHERE 
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
function cargarInput($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT B1_COD,B1_DESC
					FROM SB1010 WHERE B1_COD LIKE '%$articulo%' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT B2_QATU FROM SB2010 WHERE B2_COD='$articulo' AND B2_LOCAL='$bodega_origen'";
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
	global $tipobd_totvs,$conexion_totvs;
    
    //$nro = substr($numTrab,0,stripos($numTrab,'-'));
    $querysel = "SELECT BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO
				FROM Z2B_TRASPASO_BODEGA 
			   WHERE BO_SECUENCIA='$numero'
			   AND D_E_L_E_T_<>'*'
			   AND BO_STATUS IN ('10','20')
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
	global $tipobd_totvs,$conexion_totvs;
    
    $querysel = "SELECT BO_SECUENCIA,
				(SELECT ZZ1_BODEGA||' - '||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT ZZ1_BODEGA||' - '||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
				BO_COD,BO_CAMARTICULO,BO_DESCR,BO_FTRASPASO,BO_CANTIDAD,R_E_C_N_O_
				FROM Z2B_TRASPASO_BODEGA 
			   WHERE BO_SECUENCIA='$numero'
			   AND D_E_L_E_T_<>'*'
			   AND BO_STATUS IN ('10','20')
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
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT NVL(SUBSTR(MAX(BO_SECUENCIA),4,7)+1,1) AS BO_TRASPASO
					FROM Z2B_TRASPASO_BODEGA where BO_ORIGEN ='$bodega_origen'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT NVL(MAX(BO_RSECUENCIA),0)+1 AS CORRELATIVO FROM Z2B_TRASPASO_BODEGA WHERE BO_ORIGEN='$bodega_origen'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_UM FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_SEGUM FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss =querys($queryin, $tipobd_totvs, $conexion_totvs);
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_CONV FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_CODBAR FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_CONTA FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_DESC FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_GRUPO FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B2_CM1 FROM SB2010 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B2_CM5 FROM SB2010 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	global $tipobd_totvs,$conexion_totvs;
	
		
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
	global $tipobd_totvs,$conexion_totvs;

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
		$recno_traspaso	= recno_secuencia_s($origen);

		
		//$custo1 = busca_custo1($origen,$articulo);
		//$custo1_save = $custo1*$cantidad;
		$custo1_save = 0;
		
		//$custo5 = busca_custo5($origen,$articulo);
		//$custo5_save = $custo5*$cantidad;
		$custo5_save = 0;
		
		
		
		
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
			

				if($existe_codigo == 0){
					$queryin = "INSERT INTO Z2B_TRASPASO_BODEGA(BO_SECUENCIA, BO_COD,BO_CAMARTICULO, BO_DESCR, BO_ORIGEN, BO_DESTINO, BO_CANTIDAD, BO_FTRASPASO, BO_FECHA, R_E_C_N_O_, D_E_L_E_T_,
									BO_USUARIO,BO_STATUS,BO_UM,BO_CODBAR,BO_CONV,BO_SEGUM,BO_RSECUENCIA,BO_CONTA,BO_GRUPO,BO_CUSTO1,BO_CUSTO5,BO_NUMSEQ,BO_CANT2UM) 
									VALUES('$numero', '$articulo','$cambio_articulo', '$descripcion', '$origen', '$destino', $cantidad, '$ftraspaso', '$hoy', $recno, ' ',
									'$user','10','$um','$cod_barra',$conv,'$segum',$recno_traspaso,'$conta','$grupo',$custo1_save,$custo5_save,'0',$cant2um)";
					$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
					//echo $queryin;
				}
				
			//echo $queryin;
			/*estados
			10 => PROCESANDO
			20 => INGRESO TERMINADO
			30 => PICKING TERMINADO
			40 => TERMINADO*/
    //}
//			if(db_num_rows($rsi)<>false and db_num_rows($rsi)<>0){
//                echo "ASIGNACION CON NRO. DE TRABAJADOR : $trabajador Y ASIGNACION : $correlativo   INGRESADA CON EXITO ! <br>";
//            }else{
                //echo "INGRESADO <br>";
            //}
}
function existe_codigo($articulo,$cambio_articulo,$numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querycount = "SELECT count(*) as FILAS FROM Z2B_TRASPASO_BODEGA where BO_COD='$articulo' and BO_CAMARTICULO='$cambio_articulo' and BO_SECUENCIA='$numero' and D_E_L_E_T_<>'*'";
	$rsc = querys($querycount, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rsc, $tipobd_totvs);
    if($fila["FILAS"]>0){
        return 1;
    }else{
        return 0;
    }
}
function existe_codigo_bodega($articulo,$bodega){
	global $tipobd_totvs,$conexion_totvs;
	
	$querycount = "SELECT COUNT(B2_COD) AS FILAS FROM SB2010 WHERE B2_LOCAL='$bodega' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
   //echo $querycount;
	$rsc = querys($querycount, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rsc, $tipobd_totvs)){
		$codigo[]=array(
            "FILAS"		=>trim($fila["FILAS"])
			
                     );
    }
    echo json_encode($codigo);
	
}
function eliminar_articulo($articulo,$numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$queryup ="UPDATE Z2B_TRASPASO_BODEGA SET D_E_L_E_T_='*' WHERE BO_COD='$articulo' AND BO_SECUENCIA='$numero'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
  
}
function revisa_confirmacion($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_COD,BO_CAMARTICULO FROM Z2B_TRASPASO_BODEGA WHERE BO_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod_salida = $v["BO_COD"];
		$cod_entrada = $v["BO_CAMARTICULO"];
		
		$queryexist = "SELECT COUNT(*) AS FILAS FROM SB1010 WHERE B1_COD ='$cod_salida' AND D_E_L_E_T_<>'*'";
		$rse = querys($queryexist, $tipobd_totvs, $conexion_totvs);
		$v = ver_result($rse, $tipobd_totvs);
		$filas_salida = $v["FILAS"];
		if($filas_salida == 0){
			echo "ARTICULO DE SALIDA <strong> $cod_salida </strong> NO EXISTE EN MAESTRA DE MONARCH";
			die();
		}
		
	}
}
function confirmar_traspaso($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	//revisa_confirmacion($numero);
	
	//$recno_secuencia = recno_secuencia_s($numero);
	$queryup ="UPDATE Z2B_TRASPASO_BODEGA SET BO_STATUS='20' WHERE  BO_SECUENCIA='$numero'";///PREGUHTAR ESTADOS
	$rsu =querys($queryup, $tipobd_totvs, $conexion_totvs);
	//echo $queryup;
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "TRASPASO CONFIRMADO ! <br> <br> <br> <a target='_blank' class='btn btn-danger' href='solicitud_traspaso.php?id=$numero'><strong>Descarga Aqui Ficha de Traspaso</a></strong> ";
		}else{
			echo "ERROR: TRASPASO NO CONFIRMADO !";
		}    
}
function count_filas($numero){
	global $tipobd_totvs,$conexion_totvs;

	
	$select = "SELECT COUNT(*) AS FILAS FROM Z2B_TRASPASO_BODEGA WHERE BO_SECUENCIA='$numero'";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rs, $tipobd_totvs)){
		$codigo[]=array(
            "FILAS"		=>trim($fila["FILAS"])
			
                     );
    }
    echo json_encode($codigo);
	
}
function recno_traspaso(){
	global $tipobd_totvs,$conexion_totvs;

	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS CORRELATIVO FROM Z2B_TRASPASO_BODEGA";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
	
}
function recno_secuencia_s($bodega){
	global $tipobd_totvs,$conexion_totvs;

	
	$select = "SELECT NVL(MAX(BO_RSECUENCIA),0)+1 AS CORRELATIVO FROM Z2B_TRASPASO_BODEGA WHERE BO_ORIGEN='$bodega' ";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
	
}

function fechas(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT TO_CHAR(SYSDATE,'DD/MM/YYYY') AS FECHA1 FROM DUAL";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
			$fec[] = array(
			'FECHA1' => $v['FECHA1'],
			//'FECHA2' => $v['FECHA2'],
			
		);
	}
	echo json_encode($fec);
}
function recno_secuencia(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT MAX(R_E_C_N_O_)+1 AS RECNO FROM Z2B_TRASPASO_BODEGA";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
			$fec[] = array(
			'RECNO' => $v['RECNO']
			//'FECHA2' => $v['FECHA2'],
			
		);
	}
	echo json_encode($fec);
}
function envia_correo_traspaso($to, $asunto, $msj, $adjunto) {
    

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
    global $tipobd_totvs,$conexion_totvs;
	
	$s = "SELECT BO_DESTINO FROM Z2B_TRASPASO_BODEGA 
			WHERE BO_SECUENCIA='$numero'
			GROUP BY BO_DESTINO";
	$rss = querys($s, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$bodega = $v["BO_DESTINO"];
	if($bodega == '01'){
		$to = array('imendoza@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '02'){
		$to = array('macuna@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '04'){
		$to = array('pcarrasco@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '05' or $bodega == '15' or $bodega=='31'){
		$to = array('jadillegrez@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '11'){
		$to = array('jromani@grupomonarch.cl','dlacroix@grupomonarch.cl');
		//$to = array('gpuyol@grupomonarch.cl');
	}elseif($bodega == '50'){
		$to = array('rinfante@grupomonarch.cl','dlacroix@grupomonarch.cl');
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
	global $tipobd_totvs,$conexion_totvs;
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
				FROM Z2B_TRASPASO_BODEGA
				WHERE BO_SECUENCIA='$id_traspaso'
				AND D_E_L_E_T_<>'*'";
				//echo $querysel;
	$n=0;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
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
							FROM Z2B_TRASPASO_BODEGA
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
			
			$rss1 = querys($querysel2, $tipobd_totvs, $conexion_totvs);
			$pdf->ln();
			$i = 0;
			
			
		while($v1 = ver_result($rss1, $tipobd_totvs)){
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
							FROM Z2B_TRASPASO_BODEGA
							WHERE BO_SECUENCIA='$id_traspaso'
					AND D_E_L_E_T_<>'*'";
		$rss3 = querys($querysum, $tipobd_totvs, $conexion_totvs);
			$v = ver_result($rss3, $tipobd_totvs);
			$cantidad = $v["CANTIDAD"];
			$cant2um = $v["CANT2UM"];
			$pdf->SetX(160);
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,5,number_format($cantidad,'0',',','.'),1,0,'C');
			$pdf->SetX(180);
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,5,number_format($cant2um,'0',',','.'),1,0,'C');
	}
	//$queryup = "UPDATE Z2B_TRASPASO_BODEGA SET BO_STATUS='30' WHERE BO_SECUENCIA='$id_traspaso'";
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
	$bodega = $_GET['bodega_origen'];
    existe_codigo_bodega($articulo,$bodega);
}
if(isset($_GET["recno"])){
	//$bodega = $_GET['bodega_origen'];
	//tmp_nrotrabajador();
    recno_secuencia();
}
if(isset($_GET["cargar"])){
   $articulo = $_GET['articulo'];
    cargarInput($articulo);
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
	
		$articulo = $_GET["articulo"];	
	$numero 	= $_GET["numero"];//secuencia	
    eliminar_articulo($articulo,$numero);	
}
if(isset($_GET["confima_traspaso"])){
	
	//$articulo = $_GET["articulo"];	
	$numero 	= $_GET["numero"];//secuencia	
    confirmar_traspaso($numero);
	ver_pedido_impreso($numero);
	confirmacion_correo($numero);
}
?>