<?php
//error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
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
	
	$querysel = "SELECT DISTINCT B2_COD FROM SB2020 WHERE b2_LOCAL='$bodega' AND D_E_L_E_T_<>'*'  ORDER BY B2_COD";
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
    $querysel = "SELECT B1_COD,B1_DESC,B1_CODBAR FROM SB1020 WHERE 
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
					FROM SB1020 WHERE B1_COD LIKE '%$articulo%' AND D_E_L_E_T_<>'*'";
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
	
	$querysel = "SELECT C0_QUANT - C0_RESERVA AS STOCK FROM SC0020 WHERE C0_PRODUTO='$articulo' AND C0_NUM='$bodega_origen' AND D_E_L_E_T_ <> '*'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$csk[]=array(
					"STOCK"	=>$v["STOCK"]
			);
	}

	if ($csk == null){
		$csk[]=array(
			"STOCK"	=> 0
	);
	}

	echo json_encode($csk);
	return $v["STOCK"];
}

function cargar_traspaso($numero){
	global $tipobd_totvs,$conexion_totvs;
    
    //$nro = substr($numTrab,0,stripos($numTrab,'-'));
    $querysel = "SELECT C0_SECUENCIA,C0_ORIGEN,C0_DESTINO,C0_FTRASPASO
				FROM ZC0020 
			   WHERE C0_SECUENCIA='$numero'
			   AND D_E_L_E_T_<>'*'
			   AND C0_STATUS IN ('10','20')
			   GROUP BY C0_SECUENCIA,C0_ORIGEN,C0_DESTINO,C0_FTRASPASO";
		//echo $querysel;
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
    while($fila=ver_result($rss, $tipobd_totvs)){
        $num["NUMERO"]=array(
            "C0_SECUENCIA"          =>$fila["C0_SECUENCIA"],//
            "C0_ORIGEN"            	=>$fila["C0_ORIGEN"],//
			"C0_DESTINO"		 	=>$fila["C0_DESTINO"],//
            "C0_FTRASPASO"          =>formatDate($fila["C0_FTRASPASO"]),//
           
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
    
    $querysel = "SELECT C0_SECUENCIA,
				(SELECT ZZ1_BODEGA||' - '||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=C0_ORIGEN) AS C0_ORIGEN,
					(SELECT ZZ1_BODEGA||' - '||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=C0_DESTINO) AS C0_DESTINO,
				C0_COD,C0_CAMARTICULO,C0_DESCR,C0_FTRASPASO,C0_CANTIDAD,R_E_C_N_O_
				FROM ZC0020 
			   WHERE C0_SECUENCIA='$numero'
			   AND D_E_L_E_T_<>'*'
			   AND C0_STATUS IN ('10','20')
			   ORDER BY R_E_C_N_O_";
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	//echo $querysel;
    while($fila=ver_result($rss, $tipobd_totvs)){
        $get_art[]=[
            "C0_SECUENCIA"		=>$fila["C0_SECUENCIA"],
            "C0_ORIGEN"			=>$fila["C0_ORIGEN"],
            "C0_DESTINO"		=>$fila["C0_DESTINO"],
            "C0_COD"			=>$fila["C0_COD"],
            "C0_CAMARTICULO"	=>$fila["C0_CAMARTICULO"],
            "C0_DESCR"			=>utf8_encode($fila["C0_DESCR"]),
            "C0_FTRASPASO"		=>formatDate($fila["C0_FTRASPASO"]),
            "C0_CANTIDAD"		=>$fila["C0_CANTIDAD"],
            "RECNO"				=>$fila["R_E_C_N_O_"]
            
        ];
    }
   return $get_art;
}
function get_secuencia($bodega_origen){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT NVL(SUBSTR(MAX(C0_SECUENCIA),4,7)+1,1) AS C0_TRASPASO
					FROM ZC0020 where C0_ORIGEN <>'$bodega_origen'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rss, $tipobd_totvs)){
		$secuencia = $fila['C0_TRASPASO'];
		$secuencia = 	str_pad($secuencia,6,'0', STR_PAD_LEFT);
		$secuencia = "XX-".$secuencia;
		$nro[] = array(
		'C0_TRASPASO' => $secuencia
		);
	}
	//echo "Nro_trabajador ".$querysel;
	echo json_encode($nro);
}
function getrecno_secuencia($bodega_origen){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT NVL(MAX(C0_RSECUENCIA),0)+1 AS CORRELATIVO FROM ZC0020 WHERE C0_ORIGEN='$bodega_origen'";
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
    global $tipobd_totvs,$conexion_totvs;
   	global $articulo,$destino;
    global $r;

    //echo"<pre>";
    //print_r($frm);
    //echo"</pre>";
    
   	$articulo   		= $frm["articulo"];
   	$cambio_articulo    = $frm["articulo"];
   	$cantidad   		= $frm["cantidad"];
   	$factor_salida 		= 1;	
	$factor_entrada		= 1;
   	$x = $cantidad*$factor_salida;
	$y = $factor_entrada;
	$r = fmod($x, $y);
         
        // echo "FACTOR ENTRADA : ".$factor_entrada."<br>";
        // echo "FACTOR SALIDA : ".$factor_salida."<br>";
        // echo "X : ".$x."<br>";
        // echo "Y : ".$y."<br>";
		// echo "resto : ".$r."<br>";
    
    if($r == 0){
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
            $recno				= recno_traspaso();
            $numero  			= $frm["numero"];
            $origen 			= $frm["bodega_origen"];
            $destino    		= $frm["bodega_destino"];
            $articulo   		= $frm["articulo"];
            $cambio_articulo	= $frm["articulo"];
            $descr      		= $frm["descripcion_articulo"];
            $descr 				= str_replace("'",'',$descr);
            $ftraspaso  		= $frm["fec_traspaso"];
            $cantidad   		= $frm["cantidad"];
			$cantidad_tipo 		= $frm["cantidad_tipo"];
            $hoy         		= date('Ymd');
            $hora		 		= date('His');
            $mes		 		= date('m');
            $ano		 		= date('Y');
            $um 		   		= busca_um($articulo);	
            $descripcion   		= busca_desc($articulo);	
            $segum 		   		= busca_segum($articulo);	
            $cod_barra 	   		= busca_codbar($articulo);	
            $conv 	   	   		= 1;	
            $factor_salida 		= 1;	
            $factor_entrada		= 1;	
            $conta 	   	   		= busca_conta($articulo);	
            $grupo 	   	   		= busca_grupo($articulo);
            $existe_codigo 		= existe_codigo($articulo,$cambio_articulo,$numero);
            $cant2um			= $cantidad * $conv;
            $recno_traspaso		= recno_secuencia_s($origen);
            $custo1_save 		= 0;
            $custo5_save 		= 0;	
            $cantidad_entrada 	= ($cantidad*$factor_salida)/ $factor_entrada;
            $ftraspaso 			= str_replace(array('/'),'',$ftraspaso);
            $parte1 			= substr($ftraspaso,4,8); //12
            $parte2 			= substr($ftraspaso,2,2); //345
            $parte3 			= substr($ftraspaso,0,2); //456
            $ftraspaso 			= $parte1.$parte2.$parte3;	
            $user 				= $_SESSION["user"];
            //$custo1 			= busca_custo1($origen,$articulo);
            //$custo1_save 		= $custo1*$cantidad;	
            //$custo5 			= busca_custo5($origen,$articulo);
            //$custo5_save 		= $custo5*$cantidad;
            //$existe_codigo_bodega   = existen_codigos_z2b($cambio_articulo,$destino);

			if($existe_codigo == 0){
				$queryin = "INSERT INTO ZC0020(C0_SECUENCIA, C0_COD,C0_CAMARTICULO, C0_DESCR, C0_ORIGEN, C0_DESTINO, C0_CANTIDAD, C0_FTRASPASO, C0_FECHA, R_E_C_N_O_, D_E_L_E_T_,
								C0_USUARIO,C0_STATUS,C0_UM,C0_CODBAR,C0_CONV,C0_SEGUM,C0_RSECUENCIA,C0_CONTA,C0_GRUPO,C0_CUSTO1,C0_CUSTO5,C0_NUMSEQ,C0_CANT2UM, C0_CANTEN) 
								VALUES('$numero', '$articulo','$cambio_articulo', '$descripcion', '$origen', 01 , $cantidad, '$ftraspaso', '$hoy', $recno, ' ',
								'$user','40','$um','$cod_barra',$conv,'$segum',$recno_traspaso,'$conta','$grupo',$custo1_save,$custo5_save,'0',$cant2um, $cantidad_entrada)";
				$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
				//echo $queryin;
			}
         
			//echo $queryin;
			/*estados
			10 => PROCESANDO
			20 => INGRESO TERMINADO
			30 => PICKING TERMINADO
			40 => TERMINADO*/
      }
      
   }else{
      echo "ERROR";
   }

}

function busca_um($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_UM FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	$queryin = "SELECT B1_SEGUM FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	$queryin = "SELECT B1_CONV FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
function busca_factor($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_FACTOR FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
   //echo $queryin."<br>";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){   
       $codigo=trim($row2['B1_FACTOR']);
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
	$queryin = "SELECT B1_CODBAR FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	$queryin = "SELECT B1_CONTA FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	$queryin = "SELECT B1_DESC FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
	$queryin = "SELECT B1_GRUPO FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
function existen_codigos_z2b($cod,$C0_origen){
	global $tipobd_totvs,$conexion_totvs;
	
		
		$querysel_1 = "SELECT count(*) AS FILAS FROM SB2010 WHERE B2_COD='$cod' AND B2_LOCAL='$C0_origen' AND D_E_L_E_T_<>'*'";
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
   global $r;

//	echo"<pre> externo:";
//    print_r($post);
//    echo"</pre>";
	

	$recno			= recno_traspaso();
		
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
		$cantidad_tipo  	= $post["cantidad_tipo"];
        $hoy         		= date('Ymd');
		$hora		 		= date('His');
		$mes		 		= date('m');
		$ano		 		= date('Y');
		$um 		   		= busca_um($articulo);	
		$descripcion   		= busca_desc($articulo);	
		$segum 		   		= busca_segum($articulo);	
		$cod_barra 	   		= busca_codbar($articulo);	
		$conv 	   	   		= busca_conv($articulo);	
		$factor_salida 		= busca_factor($articulo);	
		$factor_entrada		= busca_factor($cambio_articulo);	
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
			
			
				
				if($existe_codigo == 0){
					$queryin = "INSERT INTO ZC0020(C0_SECUENCIA, C0_COD,C0_CAMARTICULO, C0_DESCR, C0_ORIGEN, C0_DESTINO, C0_CANTIDAD, C0_FTRASPASO, C0_FECHA, R_E_C_N_O_, D_E_L_E_T_,
									C0_USUARIO,C0_STATUS,C0_UM,C0_CODBAR,C0_CONV,C0_SEGUM,C0_RSECUENCIA,C0_CONTA,C0_GRUPO,C0_CUSTO1,C0_CUSTO5,C0_NUMSEQ,C0_CANT2UM) 
									VALUES('$numero', '$articulo','$cambio_articulo', '$descripcion', '$origen', '$destino', $cantidad, '$ftraspaso', '$hoy', $recno, ' ',
									'$user','40','$um','$cod_barra',$conv,'$segum',$recno_traspaso,'$conta','$grupo',$custo1_save,$custo5_save,'0',$cant2um)";
					$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
					echo $queryin;
				}

         
			//echo $queryin;
			/*estados
			10 => PROCESANDO
			20 => INGRESO TERMINADO
			30 => PICKING TERMINADO
			40 => TERMINADO*/

}
function valida_resto($articulo, $cambio_articulo,$cantidad){
   global $tipobd_totvs,$conexion_totvs;
   
   $factor_salida 		= busca_factor($articulo);	
	$factor_entrada		= busca_factor($cambio_articulo);
   
   $x = $cantidad*$factor_salida;
	$y = $factor_entrada;
	$r = fmod($x, $y);
   $ex_filas[]=array(
					"VALIDA"	=>$r
			);
	

	echo json_encode($ex_filas);
   
}
function existe_codigo($articulo,$cambio_articulo,$numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querycount = "SELECT count(*) as FILAS FROM ZC0020 where C0_COD='$articulo' and C0_CAMARTICULO='$cambio_articulo' and C0_SECUENCIA='$numero' and D_E_L_E_T_<>'*'";
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
	
	$querycount = "SELECT COUNT(B2_COD) AS FILAS FROM SB2020 WHERE B2_LOCAL='$bodega' AND B2_COD='$articulo' AND D_E_L_E_T_<>'*'";
   	echo $querycount;
	$rsc = querys($querycount, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rsc, $tipobd_totvs)){
		$codigo[]=array(
            "FILAS"		=> 1			
                     );
    }
    echo json_encode($codigo);
	
}
function eliminar_articulo($articulo,$numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$queryup ="UPDATE ZC0020 SET D_E_L_E_T_='*' WHERE C0_COD='$articulo' AND C0_SECUENCIA='$numero'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
  
}
function revisa_confirmacion($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT C0_COD,C0_CAMARTICULO FROM ZC0020 WHERE C0_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod_salida = $v["C0_COD"];
		$cod_entrada = $v["C0_CAMARTICULO"];
		
		$queryexist = "SELECT COUNT(*) AS FILAS FROM SB1020 WHERE B1_COD ='$cod_salida' AND D_E_L_E_T_<>'*'";
		$rse = querys($queryexist, $tipobd_totvs, $conexion_totvs);
		$v = ver_result($rse, $tipobd_totvs);
		$filas_salida = $v["FILAS"];
		if($filas_salida == 0){
			echo "ARTICULO DE SALIDA <strong> $cod_salida </strong> NO EXISTE EN MAESTRA DE MONARCH";
			die();
		}
		
	}
}

function delete_reserva($bodega_origen,$articulo){
	global $tipobd_totvs,$conexion_totvs;
		//borrar la reserva
		$queryup_1 = "UPDATE SC0020 SET D_E_L_E_T_ = '*', R_E_C_D_E_L_ = (SELECT R_E_C_N_O_ FROM SC0020  WHERE C0_NUM = '".$bodega_origen."' and C0_PRODUTO='".$articulo."')
		WHERE C0_NUM = '".$bodega_origen."' and C0_PRODUTO='".$articulo."' ";
		//echo $queryup_1;
		$rsu_1 = querys($queryup_1, $tipobd_totvs, $conexion_totvs);

		echo "reserva '".$bodega_origen."'con NParte ".$articulo." eliminada";
		echo "<br>";
}

function actualiza_stock($cantidad,$articulo, $bodega_origen){

	global $tipobd_totvs,$conexion_totvs;
		//quitar la cantidad reservada del sb2
		$queryup_sd2 = "UPDATE SB2020 SET B2_RESERVA=(B2_RESERVA-$cantidad) WHERE  B2_COD='$articulo' AND B2_LOCAL= (SELECT C0_FILIAL FROM SC0020 WHERE C0_NUM ='".$bodega_origen."')";
		//echo $queryup_sd2;
		$rsu_sd2 = querys($queryup_sd2, $tipobd_totvs, $conexion_totvs);

		echo "Se Devuelve a Bodega la cantidad:  ".$cantidad." ";
		echo "<br>";
		
}

function ver_traspasos(){
	global $tipobd_totvs,$conexion_totvs;
	
		$querysel = "SELECT C0_NUM, C0_OS, C0_SOLICIT, C0_PRODUTO, C0_QUANT, C0_CLASIF, C0_CODCLAS, 
					D_E_L_E_T_,R_E_C_D_E_L_, R_E_C_N_O_, C0_RESANT, C0_EMISSAO, C0_VALIDA
					FROM SC0020 
					WHERE C0_CODCLAS ='ID' and D_E_L_E_T_ <> '*'";
					
		//echo $querysel;

	 	//echo "<pre>".$querysel."</pre>";
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id_traspaso = $v["C0_SECUENCIA"];
		$cargar[]=array(
					"ID"		=>$v["C0_NUM"],
					"ORIGEN"	=>$v["C0_OS"],
					"ARTICULOS"	=>$v["C0_PRODUTO"],
					"CANTIDAD"	=>$v["C0_QUANT"],
					"FEMISION"	=>$v["C0_EMISSAO"],
					"FVALIDA"	=>$v["C0_VALIDA"],
					"ESTADO"		=>$v["C0_CODCLAS"],
					"ESTADO_SD3"	=>$v["C0_CODCLAS"],
					"DOC"			=>$v["C0_DOC"],
					"VER_SOLICITUD" 			=>"<a target='_blank' class='btn btn-block bg-gradient-info btn-sm' href='solicitud_reserva.php?id=$id_traspaso'>Ver Solicitud</a>"
			);
	}

	echo json_encode($cargar);
}

function repuestos_malos($articulo, $cantidad, $bodega_origen){

	global $tipobd_totvs,$conexion_totvs;
	
	$cant_mala =  $cantidad;

	//echo $cantidad_tipo;

	//$recno_sd3 = recno_sd3();
	$hoy 	= date('Ymd');

	// Sumar 10 días a la fecha actual
	$fecha_valida = date('Ymd', strtotime($hoy . ' +10 days'));


	$queryin1 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
	C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
	C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
	C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
	C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
	R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT) 
	VALUES(	'01', ' ', ' ', (SELECT LPAD(MAX(C0_NUM) + 1, 6, 0) as N_RESERVA  FROM SC0020), 'RPNC01', 'VD', ' ', 'I+D', '01', 
	'$articulo', '01', ' ', $cant_mala, 0, 0, 0, 0,
	0, '$fecha_valida', 0, 0, '$hoy', ' ', ' ', ' ', 
	' ', 0, ' ', ' ',0, 0, 0, 0,
	' ', ' ', '$articulo', '01', ' ', ' ', ' ', ' ',
	(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, 'RPNC', 'NC', '$bodega_origen')";

	$rsi = querys($queryin1, $tipobd_totvs, $conexion_totvs);

	echo "Se Declaran Repuestos Fallados la cantidad:  ".$cant_mala." ";
	echo "<br>";
}

function repuestos_buenos($articulo, $cantidad, $bodega_origen){

	global $tipobd_totvs,$conexion_totvs;

	//$recno_sd3 = recno_sd3();
	$hoy 	= date('Ymd');

	// Sumar 10 días a la fecha actual
	$fecha_valida = date('Ymd', strtotime($hoy . ' +10 days'));


	$queryin3 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
	C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
	C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
	C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
	C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
	R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT) 
	VALUES(	'01', ' ', ' ', (SELECT LPAD(MAX(C0_NUM) + 1, 6, 0) as N_RESERVA  FROM SC0020), 'RPNC01', 'VD', ' ', 'I+D', '01', 
	'$articulo', '01', ' ', $cantidad, 0, 0, 0, 0,
	0, '$fecha_valida', 0, 0, '$hoy', ' ', ' ', ' ', 
	' ', 0, ' ', ' ',0, 0, 0, 0,
	' ', ' ', '$articulo', '01', ' ', ' ', ' ', '*',
	(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), (SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 'INVESTIGACION', 'ID', '$bodega_origen')";

	$rsi = querys($queryin3, $tipobd_totvs, $conexion_totvs);
}

function repuestos_pendientes($articulo, $cantidad, $bodega_origen){

	global $tipobd_totvs,$conexion_totvs;

	$querysel = "SELECT C0_QUANT - C0_RESERVA AS STOCK FROM SC0020 WHERE C0_PRODUTO='$articulo' AND C0_NUM='$bodega_origen'";
    // echo $querysel;
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$stock =  $v["STOCK"];

	$new_cant = $stock - $cantidad;

	//$recno_sd3 = recno_sd3();
	$hoy 	= date('Ymd');

	// Sumar 10 días a la fecha actual
	$fecha_valida = date('Ymd', strtotime($hoy . ' +10 days'));


	$queryin = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
	C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
	C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
	C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
	C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
	R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT) 
	VALUES(	'01', ' ', ' ', (SELECT LPAD(MAX(C0_NUM) + 1, 6, 0) as N_RESERVA  FROM SC0020), 'RPNC01', 'VD', ' ', 'I+D', '01', 
	'$articulo', '01', ' ', $new_cant, 0, 0, 0, 0,
	0, '$fecha_valida', 0, 0, '$hoy', ' ', ' ', ' ', 
	' ', 0, ' ', ' ',0, 0, 0, 0,
	' ', ' ', '$articulo', '01', ' ', ' ', ' ', ' ',
	(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, 'INVESTIGACION', 'ID', '$bodega_origen')";

	$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);

	echo "Se Declaran Repuestos Pendientes por analizar la cantidad:  ".$new_cant."";
	echo "<br>";

}

function confirmar_traspaso($numero, $articulo, $bodega_origen, $cantidad, $cantidad_tipo){
	global $tipobd_totvs,$conexion_totvs;

    $querysel = "SELECT C0_QUANT - C0_RESERVA AS STOCK FROM SC0020 WHERE C0_PRODUTO='$articulo' AND C0_NUM='$bodega_origen'";
    // echo $querysel;
    $rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$stock =  $v["STOCK"];


/* 	$querysel1 = "SELECT C0_USUARIO AS USUARIO FROM SC0020 WHERE C0_PRODUTO='$articulo' AND C0_NUM='$bodega_origen'";
    // echo $querysel;
    $rss1 = querys($querysel1, $tipobd_totvs, $conexion_totvs);
	$v1 = ver_result($rss1, $tipobd_totvs);
	$usuario =  $v1["USUARIO"]; */

    // Inicializa el stock en 0 en caso de que no se encuentre en la base de datos.
/* 	echo $numero;
	echo $articulo;
	echo $bodega_origen;
	echo $cantidad; */
	//$cant_reserva = cargar_stock($articulo,$bodega_origen);
	//echo $stock;


	if($cantidad_tipo == 10){

		if($stock == $cantidad){
		delete_reserva($bodega_origen,$articulo);
		actualiza_stock($cantidad,$articulo, $bodega_origen);
		}elseif($stock > $cantidad){
		delete_reserva($bodega_origen,$articulo);
		actualiza_stock($cantidad,$articulo, $bodega_origen);
		repuestos_buenos($articulo, $cantidad, $bodega_origen);
		repuestos_pendientes($articulo, $cantidad, $bodega_origen);
		}

	}elseif($cantidad_tipo == 20){
		if ($stock == $cantidad){
			delete_reserva($bodega_origen,$articulo);
			repuestos_malos($articulo, $cantidad, $bodega_origen);
		}elseif($stock > $cantidad_tipo){
			delete_reserva($bodega_origen,$articulo);
			repuestos_malos($articulo, $cantidad, $bodega_origen);
			repuestos_pendientes($articulo, $cantidad, $bodega_origen);
		}
	}
}
	
	

/* 			actualiza_stock($cantidad,$articulo, $bodega_origen);
		}elseif($cantidad == 0){
			delete_reserva($bodega_origen,$articulo);
			repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		}else{
			delete_reserva($bodega_origen,$articulo);
			actualiza_stock($cantidad,$articulo, $bodega_origen);
			repuestos_buenos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
			repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		}
	}elseif ($stock > $cantidad_tipo){
		if($cantidad_tipo == $cantidad){
			delete_reserva($bodega_origen,$articulo);
			actualiza_stock($cantidad,$articulo, $bodega_origen);
			repuestos_buenos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
			repuestos_pendientes($articulo, $cantidad_tipo, $bodega_origen);
		}elseif($cantidad == 0){
			delete_reserva($bodega_origen,$articulo);
			repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
			repuestos_pendientes($articulo, $cantidad_tipo, $bodega_origen);
		}else{
			delete_reserva($bodega_origen,$articulo);
			actualiza_stock($cantidad,$articulo, $bodega_origen);
			repuestos_buenos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
			repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
			repuestos_pendientes($articulo, $cantidad_tipo, $bodega_origen);
		}
	}





if ($stock == $cantidad_tipo){
	
	if($cantidad_tipo == $cantidad){
		delete_reserva($bodega_origen,$articulo);
		actualiza_stock($cantidad,$articulo, $bodega_origen);
	}elseif($cantidad == 0){
		delete_reserva($bodega_origen,$articulo);
		repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
	}else{
		delete_reserva($bodega_origen,$articulo);
		actualiza_stock($cantidad,$articulo, $bodega_origen);
		repuestos_buenos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
	}
}elseif ($stock > $cantidad_tipo){
	if($cantidad_tipo == $cantidad){
		delete_reserva($bodega_origen,$articulo);
		actualiza_stock($cantidad,$articulo, $bodega_origen);
		repuestos_buenos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		repuestos_pendientes($articulo, $cantidad_tipo, $bodega_origen);
	}elseif($cantidad == 0){
		delete_reserva($bodega_origen,$articulo);
		repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		repuestos_pendientes($articulo, $cantidad_tipo, $bodega_origen);
	}else{
		delete_reserva($bodega_origen,$articulo);
		actualiza_stock($cantidad,$articulo, $bodega_origen);
		repuestos_buenos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		repuestos_malos($articulo, $cantidad_tipo, $cantidad, $bodega_origen);
		repuestos_pendientes($articulo, $cantidad_tipo, $bodega_origen);
	}
}
 	 */



function count_filas($numero){
	global $tipobd_totvs,$conexion_totvs;

	
	$select = "SELECT COUNT(*) AS FILAS FROM ZC0020 WHERE C0_SECUENCIA='$numero'";
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

	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS CORRELATIVO FROM ZC0020";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['CORRELATIVO'];
	return $recno;
	
}
function recno_secuencia_s($bodega){
	global $tipobd_totvs,$conexion_totvs;

	
	$select = "SELECT NVL(MAX(C0_RSECUENCIA),0)+1 AS CORRELATIVO FROM ZC0020 WHERE C0_ORIGEN='$bodega' ";
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
	
	$querysel = "SELECT MAX(R_E_C_N_O_)+1 AS RECNO FROM ZC0020";
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
	
	$s = "SELECT C0_DESTINO FROM ZC0020 
			WHERE C0_SECUENCIA='$numero'
			GROUP BY C0_DESTINO";
	$rss = querys($s, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$bodega = $v["C0_DESTINO"];
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
	$querysel ="SELECT DISTINCT  C0_SECUENCIA,
				C0_FTRASPASO,
				(SELECT NNR_CODIGO||'_'||NNR_DESCRI FROM NNR020 WHERE NNR_FILIAL='01' AND NNR_CODIGO=C0_ORIGEN) AS C0_ORIGEN,
				(SELECT NNR_CODIGO||'_'||NNR_DESCRI FROM NNR020 WHERE NNR_FILIAL='01' AND NNR_CODIGO=C0_DESTINO) AS C0_DESTINO,
				C0_USUARIO
				FROM ZC0020
				WHERE C0_SECUENCIA='$id_traspaso'
				AND D_E_L_E_T_<>'*'";
				//echo $querysel;
	$n=0;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id	 		 = $v["C0_SECUENCIA"];
		$ftrapaso	 = $v["C0_FTRASPASO"];
		$origen	 	 = $v["C0_ORIGEN"];
		$destino	 = ' ';
		$ususario	 = $v["C0_USUARIO"];
		
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
	
			$querysel2 = "SELECT C0_COD,C0_CAMARTICULO,C0_SEGUM,C0_DESCR,C0_CANTIDAD,C0_CANT2UM,R_E_C_N_O_
							FROM ZC0020
							WHERE C0_SECUENCIA='$id_traspaso'
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
			$codigo 		= $v1["C0_COD"];
			$codigo_cam		= $v1["C0_CAMARTICULO"];
			$um				= $v1["C0_SEGUM"];
			$desc 			= $v1["C0_DESCR"];
			$cantidad 		= $v1["C0_CANTIDAD"];
			$cant2um 		= $v1["C0_CANT2UM"];
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
		$querysum = "SELECT SUM(C0_CANTIDAD) AS CANTIDAD,SUM(C0_CANT2UM) AS CANT2UM
							FROM ZC0020
							WHERE C0_SECUENCIA='$id_traspaso'
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
	//$queryup = "UPDATE ZC0020 SET C0_STATUS='30' WHERE C0_SECUENCIA='$id_traspaso'";
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

if(isset($_GET["ver"])){

	 ver_traspasos();
 }

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
if(isset($_GET["valida_resto"])){
	//tmp_nrotrabajador();
	
	$articulo_salida  = $_GET['articulo'];
	$articulo_entrada = $_GET['cambio_articulo'];
	$cantidad         = $_GET['cantidad'];
	$cantidad_tipo         = $_GET['cantidad_tipo'];
    valida_resto($articulo_salida,$articulo_entrada,$cantidad,$cantidad_tipo);
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
if(isset($_GET["C0_secuencia"])){
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
    eliminar_articulo($articulo,$numero);	
}
if(isset($_GET["confirma_traspaso"])){
	
	//$articulo = $_GET["articulo"];	
	$numero 		= $_GET["numero"];//secuencia	
	$articulo 	   	= $_GET['articulo'];
	$bodega_origen 	= $_GET['bodega_origen'];
	$cantidad       = $_GET['cantidad'];
	$cantidad_tipo  = $_GET['cantidad_tipo'];
    confirmar_traspaso($numero, $articulo, $bodega_origen,  $cantidad, $cantidad_tipo);
	//ver_pedido_impreso($numero, $articulo, $bodega_origen,  $cantidad, $cantidad_tipo);
	//confirmacion_correo($numero);
}
?>