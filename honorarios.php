<?php
error_reporting();

require_once "config.php";
require_once "conexion.php";
//require_once "generar_insert.php";
require 'PHPExcel-1.8/Classes/PHPExcel.php';

function subirArchivo(){
	global $tipobd_ptl,$conexion_ptl;
	
	$dir_subida = './archivos_subidos/honorarios/';
	$fichero_subido = $dir_subida.basename($_FILES['file_cventas']['name']);
	$nombre = $_FILES['file_cventas']['tmp_name'];	
	
	if (move_uploaded_file($_FILES['file_cventas']['tmp_name'], $fichero_subido)) {
		
		$error = $_FILES['file_cventas']['error'];		 
		$type  = $_FILES['file_cventas']['type'];

		if($error == 1){
			echo "TAMAÑO ARCHIVO EXCEDE MAXIMO PERMITIDO";
		}else{
			echo "El fichero es valido y subido con Exito !\n<br>";
			//echo "Tipo Archivo : ".$type."<br>";
		}	

	}

}
function datos_subidos(){
	global $tipobd_ptl,$conexion_ptl;
	
	$querysel = "SELECT NOM_ARCHIVO, NRO_OC, FECHA_SUBIDA, 
				 SUM(UNIDADES) AS UNIDADES, COUNT(DISTINCT SKU) AS ARTICULOS 
				 FROM B2B_DIST_FALABELLA 
				 GROUP BY NOM_ARCHIVO, NRO_OC, FECHA_SUBIDA";	
	$rss = querys($querysel,$tipobd_ptl,$conexion_ptl);
	while($v = ver_result($rss, $tipobd_ptl)){
		$oc = $v["NRO_OC"];
		$datos[]=array(
					"NOM_ARCHIVO" 	=> $v["NOM_ARCHIVO"],
					"NRO_OC"   		=> $v["NRO_OC"], 
					"FECHA_SUBIDA" 	=> formatDate($v["FECHA_SUBIDA"]), 
					"UNIDADES" 		=> $v["UNIDADES"],
					"EMPAQUES" 		=> $v["ARTICULOS"]
				);
	}
	echo json_encode($datos);
	
	// cierra_conexion($tipobd_ptl,$conexion_ptl);
}

function leer_archivo($archivo){
	global $tipobd_winper,$conexion_winper;
	
	$path="./archivos_subidos/honorarios/";
	
    $nombreArchivo=$path.$archivo;
	
	//echo "LEER ARCHIVO : ".$nombreArchivo."<br>";
	// Cargo la hoja de cÃ¡lculo
	$objPHPExcel = PHPExcel_IOFactory::load($nombreArchivo);
	
	//Asigno la hoja de calculo activa
	$objPHPExcel->setActiveSheetIndex(0);
	//Obtengo el numero de filas del archivo
	$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	
	$hoy = date('Ymd');
	$query_fact="select mes_tributacion mes, factor_tributacion factor from factor_tributacion where ano_tributacion=2022";
	$result_fact=querys($query_fact,$tipobd_winper,$conexion_winper);
	while ($vfact=ver_result($result_fact,$tipobd_winper)){
		$factor[$vfact["mes"]]=$vfact["factor"];
	}
	// print_r($factor[$vfact["mes"]]);
	
	// die();
	$bod_item = 0;
	for ($i = 2; $i <= $numRows; $i++) {

		
		$empresa 			= $objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		$planilla 			= trim($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue());
		$Nombre 			= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
		$rut 				= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
		$digito 			= $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
		$bol 				= trim($objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue());
		$fecha_bol 			= substr($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue(),0,2) . "-" . substr($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue(),3,2) ."-".substr($objPHPExcel->getActiveSheet()->getCell("G".$i)->getValue(),6,4). " 12:00:00";
		//echo $fecha_bol . "<br>";
		$tipo_bol 			= $objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue();
		$monto_bol 			= $objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
		$moneda				= trim($objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue());
		$monto_bol_pesos 	= $objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
		$ret_11 			= round($objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue());
		$ret_3 				= round($objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue());
		$tot_bol 			= trim($objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue());
		$glosa 				= $objPHPExcel->getActiveSheet()->getCell("O".$i)->getValue();
		$estado 			= $objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
		$ano_per 			= $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getValue();
		$mes_per 			= trim($objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue());
		$usuario 			= $objPHPExcel->getActiveSheet()->getCell("S".$i)->getValue();
		$fecha_proc 		= $objPHPExcel->getActiveSheet()->getCell("T".$i)->getValue();
		$hora_proc 			= $objPHPExcel->getActiveSheet()->getCell("U".$i)->getValue();	
		$hono_pub 			= trim($objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue());
		$nula 				= $objPHPExcel->getActiveSheet()->getCell("W".$i)->getValue();
		$fecha_pago 		= substr($objPHPExcel->getActiveSheet()->getCell("X".$i)->getValue(),0,2) . "-" . substr($objPHPExcel->getActiveSheet()->getCell("X".$i)->getValue(),3,2) ."-".substr($objPHPExcel->getActiveSheet()->getCell("X".$i)->getValue(),6,4). " 12:00:00";
		$cod_sucu 			= $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getValue();	
		
		
		// echo "$empresa <br>"; 		
		// echo "$planilla 		<br>"; 	
		// echo "$Nombre 		<br>"; 	
		// echo "$rut 			<br>"; 	
		// echo "$digito 		<br>"; 	
		// echo "$bol 			<br>"; 	
		// echo "$fecha_bol 		<br>"; 	
		// echo "//echo $fecha_bo<br>"; 	
		// echo "$tipo_bol 		<br>"; 	
		// echo "$monto_bol 		<br>"; 	
		// echo "$moneda			<br>"; 	
		// echo "$monto_bol_pesos<br>"; 	
		// echo "$ret_11 		<br>"; 	
		// echo "$ret_3 			<br>"; 	
		// echo "$tot_bol 		<br>"; 	
		// echo "$glosa 			<br>"; 	
		// echo "$estado 		<br>"; 	
		// echo "$ano_per 		<br>"; 	
		// echo "$mes_per 		<br>"; 	
		// echo "$usuario 		<br>"; 	
		// echo "$fecha_proc 	<br>"; 	
		// echo "$hora_proc 		<br>"; 	
		// echo "$hono_pub 		<br>"; 	
		// echo "$nula 			<br>"; 	
		// echo "$fecha_pago 	<br>"; 	
		// echo "$cod_sucu 		<br>"; 	
		
		// die();
		
		
  
		$query_rut="select count(rut_honorario) as VAL from hono_personal where rut_honorario=". trim($rut) ." and cod_empresa='". trim($empresa) ."' ";
//		 echo $query_rut . "<br>";
			$result_rut=querys($query_rut,$tipobd_winper,$conexion_winper);
			if ($vrut=ver_result($result_rut,$tipobd_winper)){

			if  ($vrut["VAL"] == 0){
			echo $query_rut ."---".$vrut['val']. "<br>";						 
				echo "RUT :" .trim($rut)." No esta en Hono_personal.<br> ";
			}
			else {

			$qi = "insert into hono_encabezado
				(cod_empresa,cod_planta,rut_honorario,dv_honorario,nro_boleta,fecha_boleta,tipo_boleta,monto_boleta,moneda,monto_bol_pesos,monto_retencion,total_boleta,glosa_boleta,estado,ano_periodo,mes_periodo,usuario,fecha_proceso,hora_proceso,hono_publica,nula,cod_sucursal,fecha_pago)
				values('".trim($empresa)."','".trim($planilla)."','".trim($rut)."','".trim($digito)."','".trim($bol)."','".trim($fecha_bol)."','".trim($tipo_bol)."','".trim($monto_bol)."','".trim($moneda)."',
				'".trim($monto_bol_pesos)."','".trim($ret_11)."','".trim($tot_bol)."','".trim($glosa)."','','".trim($ano_per)."','".trim($mes_per)."','".trim($usuario)."','".trim($fecha_proc)."',
				'','','".trim($nula)."','".trim($cod_sucu)."','".trim($fecha_pago)."')";//,'A2_NOME','A2_END','A2_BAIRRO','MONOTO_BOL_PESOS'
				// echo $qi."<br>";				
			if (!querys($qi,$tipobd_winper,$conexion_winper)){
				 echo $qi . "<br>";
			}
				
			}
				if ($ret_3 <> '')
				{
				$qdesc="INSERT INTO hono_descuentos 
				(cod_empresa, cod_planta, rut_honorario, dv_honorario, cod_descuento, correlativo, monto, moneda, cuotas, cod_centro_costo, tipo_descuento, fec_aplicacion, nro_boleta) 
				VALUES('".trim($empresa)."','".trim($planilla)."','".trim($rut)."','".trim($digito)."', 503380, '".trim($bol)."', '". $ret_3 ."', '$', 1, 101, 'F', '".trim($fecha_bol)."', '".trim($bol)."')";	
				
				echo $qdesc."<br>";				
			if (!querys($qdesc,$tipobd_winper,$conexion_winper)){
				 echo $qdesc . "<br>";
			}
				$qhist_desc="INSERT INTO hono_hist_descue
				(cod_empresa, cod_planta, rut_honorario, dv_honorario, ano_periodo, mes_periodo, nro_boleta, cod_descuento, correlativo, monto_original, moneda, monto_pesos, cuotas, cod_centro_costo, tipo_descuento, fec_aplicacion, cod_proyecto, cod_sucursal, cod_unidad_adminis, cod_concep_contabl, tipo_boleta) 
				VALUES('".trim($empresa)."','".trim($planilla)."','".trim($rut)."','".trim($digito)."', '".trim($ano_per)."','".trim($mes_per)."', '".trim($bol)."', 503380, '".trim($bol)."', '". round($factor[trim($mes_per)] * $ret_3,0)."', '$', '".round($factor[trim($mes_per)] * $ret_3,0)."', 1, 101, 'F', '".trim($fecha_bol)."', '1', '".trim($cod_sucu)."', 0, '4110012', '1')";
			 echo $qhist_desc."<br>";
				if (!querys($qhist_desc,$tipobd_winper,$conexion_winper)){
				echo $qhist_desc . "<br>";
				}
				// echo $rut . "--" . $bol ." Tiene retencion 3 porciento <br>";	
					
				}
			}

		
	}
		
		
}


//============================================================================================
//============================================================================================
//============================================================================================

if(isset($_FILES['file_cventas']['name'])){
	
	$nombre_archivo = $_FILES['file_cventas']['name'];
    subirArchivo();
	//$nombre_archivo = 'corona.csv';
	leer_archivo($nombre_archivo);
	

}


if(isset($_GET["borrarPlanilla"])){
	$archivo = $_GET["archivo"];
    borrarPlanilla($archivo);
}


// if(isset($_GET["ver"])){
    // datos_subidos();
// }





?>