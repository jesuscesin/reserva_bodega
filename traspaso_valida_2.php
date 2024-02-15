<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";



function ver_traspasos($bodega_origen,$bodega_destino,$status){
	global $tipobd_totvs,$conexion_totvs;
	
	if($status == 'todos'){
		$querysel = "SELECT BO_SECUENCIA,
					(SELECT NNR_CODIGO||'_'||NNR_DESCRI FROM NNR020 WHERE NNR_FILIAL='01' AND NNR_CODIGO=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT NNR_CODIGO||'_'||NNR_DESCRI FROM NNR020 WHERE NNR_FILIAL='01' AND NNR_CODIGO=BO_DESTINO) AS BO_DESTINO,
					(SELECT MAX(BD.BO_DOC) FROM ZD3020 BD WHERE BD.BO_SECUENCIA = B.BO_SECUENCIA) AS BO_DOC,
					COUNT(BO_COD) AS ARTICULOS,
                    SUM(BO_CANTIDAD) AS CANTIDAD,
                    SUM(BO_CANTEN) AS CANTIDAD_ENTRADA,
					BO_FTRASPASO,BO_STATUS, NVL((SELECT DISTINCT D3_ESTORNO FROM SD3010 WHERE REPLACE(BO_SECUENCIA,'-','')=D3_DOC),' ') AS D3_ESTORNO
					FROM ZD3020 B
					WHERE  (BO_ORIGEN  like '%$bodega_origen%'
					AND BO_DESTINO  like '%$bodega_destino%')
                    AND B.D_E_L_E_T_<>'*'
					GROUP BY BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO,BO_STATUS
					ORDER BY substr(BO_SECUENCIA,4,6) DESC";//,,substr(BO_SECUENCIA,4,6),BO_FECHA
					 // echo $querysel;
	}else{
		$querysel = "SELECT BO_SECUENCIA,
					(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1020 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1020 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
					(SELECT MAX(BD.BO_DOC) FROM ZD3020 BD WHERE BD.BO_SECUENCIA = B.BO_SECUENCIA) AS BO_DOC,
					COUNT(BO_COD) AS ARTICULOS,
                    SUM(BO_CANTIDAD) AS CANTIDAD,
                    SUM(BO_CANTEN) AS CANTIDAD_ENTRADA,
					BO_FTRASPASO,BO_STATUS, NVL((SELECT DISTINCT D3_ESTORNO FROM SD3010 WHERE REPLACE(BO_SECUENCIA,'-','')=D3_DOC),' ') AS D3_ESTORNO
					FROM ZD3020 B
					WHERE  (BO_ORIGEN  like '%$bodega_origen%'
					AND BO_DESTINO  like '%$bodega_destino%'
					AND BO_STATUS = '$status')
                    AND B.D_E_L_E_T_<>'*'
					GROUP BY BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO,BO_STATUS
					ORDER BY substr(BO_SECUENCIA,4,6) DESC";//,substr(BO_SECUENCIA,-2),BO_FECHA
					// echo $querysel;
	}

	 //echo "<pre>".$querysel."</pre>";
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id_traspaso = $v["BO_SECUENCIA"];
		$cargar[]=array(
					"ID"		=>$v["BO_SECUENCIA"],
					"ORIGEN"	=>$v["BO_ORIGEN"],
					"DESTINO"	=>$v["BO_DESTINO"],
					"ARTICULOS"	=>$v["ARTICULOS"],
					"CANTIDAD"	=>$v["CANTIDAD"],
					"DOC"		=>$v["BO_DOC"],
					"CANTIDAD_ENTRADA"	=>$v["CANTIDAD_ENTRADA"],
					"FTRASPASO"	=>formatDate($v["BO_FTRASPASO"]),
					////"FEMISION"	=>formatDate($v["BO_FECHA"]),
					"ESTADO"	=>$v["BO_STATUS"],
					"ESTADO_SD3"	=>$v["D3_ESTORNO"],
					"VER_SOLICITUD" 			=>"<a target='_blank' class='btn btn-block bg-gradient-info btn-sm' href='solicitud_transferencia.php?id=$id_traspaso'>Ver Solicitud</a>"
			);
	}

	echo json_encode($cargar);
}
function lista_traspaso($id_traspaso){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_SECUENCIA,BO_COD,BO_DESCR,BO_CANTIDAD,R_E_C_N_O_
			FROM ZD3020
			WHERE BO_SECUENCIA='$id_traspaso'
			AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	//echo $querysel;
	while($v = ver_result($rss, $tipobd_totvs)){
		$ver[]=array(
					"ID"		=>$v["BO_SECUENCIA"],					
					"ARTICULOS"	=>$v["BO_COD"],
					"DESCR"		=>$v["BO_DESCR"],
					"CANTIDAD"	=>$v["BO_CANTIDAD"],
					"RECNO"		=>$v["R_E_C_N_O_"]
					
			);
	}
	echo json_encode($ver);
}
function eliminar_articulo($recno){
	global $tipobd_totvs,$conexion_totvs;
	
	$queryup ="UPDATE ZD3020 SET D_E_L_E_T_='*' WHERE R_E_C_N_O_='$recno'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "ARTICULO ELIMINARDO CON EXITO !";
		}else{
			echo "ERROR: ARTICULO NO ELIMINADO !";
		}    
}


function anula_trapaso($secuencia){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$queryup = "UPDATE ZD3020 SET D_E_L_E_T_='*', BO_STATUS='30' WHERE BO_SECUENCIA='$secuencia'";
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "TRASPASO ANULADO CON EXITO !";
		}else{
			echo "ERROR: TRASPASO NO ANULADO !";
		}    
}

function enviar_notificacion($email,$nombre,$secuencia){

    $url  = "http://200.27.195.145:8080/mail/solicitud_transferencia.php?mail=".urlencode($email)."&nombre=".urlencode($nombre)."&secuencia=".urlencode($secuencia);
    $curl  = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $exec  = curl_exec($curl);
    echo $exec;
    curl_close($curl);  
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
	
	$querysel = "SELECT distinct BO_STATUS FROM ZD3020 WHERE BO_SECUENCIA='$numero'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$status = $v["BO_STATUS"];
	if($status == 20){
		
	$asunto = "ANULACION DE TRASPASO ENTRE BODEGAS # $numero";
    $msj    = "<h3><strong>Se ha anulado un traspaso entre bodegas con ID $numero</strong></h3> ";
			$titulo =" ";
			$pie='<br><br>Este mensaje se ha generado automaticamente, por favor NO RESPONDER.<br><br>Atte. Informática Monarch.';
			$msj=$msj.$titulo.$pie;
			
			$to=array('carlos.escobar@turbodal.cl'/*,'dlacroix@grupomonarch.cl','msotomayor@grupomonarch.cl'*/);
			$adjunto = "traspasos_bodegas/".$numero.".pdf";
			envia_correo_traspaso($to, $asunto, $msj,$adjunto);
	}
}

function existen_codigos_z2b($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_CAMARTICULO,BO_DESTINO,BO_ORIGEN FROM ZD3020 WHERE BO_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod 		= $v['BO_CAMARTICULO'];
		$bo_destino 	= $v['BO_DESTINO'];
		$bodega_origen  = $v['BO_ORIGEN'];
		
		$querysel_1 = "SELECT count(*) AS FILAS FROM SB2020 WHERE B2_COD='$cod' AND B2_LOCAL='$bo_destino' AND D_E_L_E_T_<>'*'";
		//echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss_1, $tipobd_totvs);
		$filas = $v1["FILAS"];
		if($filas == 0){

			$insert = "INSERT INTO TOTVS12C.SB2020
						SELECT B2_FILIAL, B2_COD, B2_QFIM,'$bo_destino' AS B2_LOCAL,0 AS B2_QATU, B2_VFIM1, B2_VATU1, B2_CM1, B2_VFIM2, B2_VATU2, B2_CM2, B2_VFIM3, B2_VATU3, B2_CM3, B2_VFIM4, B2_VATU4, B2_CM4, B2_VFIM5, B2_VATU5, B2_CM5, B2_QEMP, B2_QEMPN, B2_QTSEGUM, B2_USAI, B2_RESERVA, B2_QPEDVEN, B2_LOCALIZ, B2_NAOCLAS, B2_SALPEDI, B2_DINVENT, B2_DINVFIM, B2_QTNP, B2_QNPT, B2_QTER, B2_QFIM2, B2_QACLASS, B2_DTINV, B2_CMFF1, B2_CMFF2, B2_CMFF3, B2_CMFF4, B2_CMFF5, B2_VFIMFF1, B2_VFIMFF2, B2_VFIMFF3, B2_VFIMFF4, B2_VFIMFF5, B2_QEMPSA, B2_QEMPPRE, B2_SALPPRE, B2_QEMP2, B2_QEMPN2, B2_RESERV2, B2_QPEDVE2, B2_QEPRE2, B2_QFIMFF, B2_SALPED2, B2_QEMPPRJ, B2_QEMPPR2, B2_STATUS, B2_CMFIM1, B2_CMFIM2, B2_CMFIM3, B2_CMFIM4, B2_CMFIM5, B2_TIPO, B2_USERLGI, B2_USERLGA, D_E_L_E_T_, (SELECT MAX(R_E_C_N_O_) + 1 FROM TOTVS12C.SB2020) AS R_E_C_N_O_ , R_E_C_D_E_L_, B2_CMRP1, B2_VFRP1, B2_CMRP2, B2_VFRP2, B2_CMRP3, B2_VFRP3, B2_CMRP4, B2_VFRP4, B2_CMRP5, B2_VFRP5, B2_QULT, B2_DULT, B2_BLOQUEI, B2_MSEXP, B2_ECSALDO, B2_XDTFIN, B2_XDTINI, B2_UBIC, B2_UBICXD 
						FROM TOTVS12C.SB2020
						WHERE B2_LOCAL = '$bodega_origen'
						AND B2_COD = '$cod'
						AND D_E_L_E_T_ <> '*'";

			//die();
		}
		
	}
	//return $filas;
}

function existe_traspaso($numero){	
    global $tipobd_totvs,$conexion_totvs;
	
	
	$secuencia  = str_replace('-','',$numero);
	$querysel_1 = "SELECT count(*) AS FILAS FROM SD3020 WHERE D3_DOC='$secuencia' AND D_E_L_E_T_<>'*'";
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
/*
	$codigo='';
	$queryin = "SELECT B1_FACTOR FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	while ($row2 =ver_result($rss, $tipobd_totvs)){  
       $codigo=trim($row2['B1_FACTOR']);
    }

	if($codigo==""){
		return $codigo="0";
	}else{
		return $codigo;
	}
*/
	return $codigo="0";
}
function busca_segum($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_SEGUM FROM SB1020 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
function valida_traspaso($numero){
    global $tipobd_totvs,$conexion_totvs;
	
	//$recno_sd3 = recno_sd3();
	$hoy 	= date('Ymd');

	existen_codigos_z2b($numero);
	existe_traspaso($numero);
	
	$querysel = "SELECT '01'         	AS D3_FILIAL,
						'999'       	AS D3_TM,
						BO_COD 			AS D3_COD,
						'UM'	       	AS D3_UM,
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
						'  '	    	AS D3_SEGUM,
						0 		    	AS D3_QTSEGUM,
						''          	AS D3_USUARIO,
						'E0'        	AS D3_CHAVE,
                        R_E_C_N_O_ 		AS RECNO,
                        (SELECT SB1.B1_TIPO FROM SB1020 SB1 WHERE TRIM(SB1.B1_COD) = TRIM(BO_COD))         	AS D3_TIPO
        FROM ZD3020
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
		//echo $querysel."<br><br>";
		//die();
        //primera unidad de medidas = unidades
        //segunda unidad de medida = bipack, tripack,etc
		while($v = ver_result($rss, $tipobd_totvs)){
			$filia 		= $v["D3_FILIAL"];
			$tm 		= $v["D3_TM"];
			$cod_mch 	= $v["D3_COD"];
            $conversion = busca_conv($cod_mch);
            $factor 	= busca_factor($cod_mch);
			$um 		= busca_segum($cod_mch);
			$cantidad 	= $v["D3_QUANT"];//SEGUNDA
            //$cantidad = $cantidad1um/$conversion;
			$cant1um	= $v["D3_QUANT2"];//PRIMERA
			$conta		= $v["D3_CONTA"];
			$bodega		= $v["D3_LOCAL"];
			$doc 		= $v["D3_DOC"];
			$doc 		= 'TB'.substr($doc,-6);
			//$doc 		= str_replace('-','',$doc);
			$emision 	= $v["D3_EMISSAO"];
			$grupo 		= $v["D3_GRUPO"];
			$emision 	= $v["D3_EMISSAO"];
			//$custo1 	= busca_custo1($bodega,$cod_mch);
			$custo1 	= valor_custo1($cod_mch);
			$custo1 	= $custo1*$cantidad;
			$custo5 	= busca_custo5($bodega,$cod_mch);
			$custo5 	= $custo5*$cantidad;
			$segum 		= $v["D3_SEGUM"];
			$tcantidad 	= $v["D3_QTSEGUM"];
			$tipo 		= $v["D3_TIPO"];
			$chave 		= $v["D3_CHAVE"];
			$recno_z2b 	= $v["RECNO"];
			$seq_totvs	=consultaseq();
			$recno_sd3 	= recno_sd3();
			$secuencia_totvs_id 	= $seq_totvs[0];
			$secuencia_totvs_est 	= $seq_totvs[1];
			$secuencia_totvs_est 	= substr($secuencia_totvs_est,0,199);
			$secuencia_totvs 		= $seq_totvs[2];
			$sec 					= explode('-',$seq_totvs);
			$seq_id					= substr($sec[0], 3);
			$seq_ds					= substr($sec[1], 3);
			$seq_sc					= substr($sec[2], 3);
			
			
			
			$queryin = "INSERT INTO SD3020 (D3_FILIAL, D3_TM, D3_COD, D3_UM, D3_QUANT, D3_CF,
												D3_CONTA, D3_OP, D3_LOCAL, D3_DOC, D3_EMISSAO,
												D3_GRUPO, D3_CUSTO1, D3_CUSTO2, D3_CUSTO3, D3_CUSTO4, D3_CUSTO5,
												D3_CC, D3_PARCTOT, D3_ESTORNO, D3_NUMSEQ, D3_SEGUM, D3_QTSEGUM,
												D3_TIPO, D3_NIVEL, D3_USUARIO, D3_REGWMS, D3_PERDA, D3_DTLANC,
												D3_TRT, D3_CHAVE, D3_IDENT, D3_SEQCALC, D3_RATEIO, D3_LOTECTL,
												D3_NUMLOTE, D3_DTVALID, D3_LOCALIZ, D3_NUMSERI, D3_CUSFF1, D3_CUSFF2,
												D3_CUSFF3, D3_CUSFF4, D3_CUSFF5, D3_ITEM, D3_OK, D3_ITEMCTA,
												D3_CLVL, D3_PROJPMS, D3_TASKPMS, D3_ORDEM, D3_SERVIC, D3_STSERV,
												D3_OSTEC, D3_POTENCI, D3_TPESTR, D3_REGATEN, D3_ITEMSWN,
												D3_DOCSWN, D3_ITEMGRD, D3_STATUS, D3_CUSRP1,
												D3_CUSRP2, D3_CUSRP3, D3_CUSRP4, D3_CUSRP5, D3_CMRP, D3_MOEDRP,
												D3_MOEDA, D3_EMPOP, D3_DIACTB, D3_PMICNUT, D3_CMFIXO, D3_NODIA,
												D3_GARANTI, D3_PMACNUT, D3_NRBPIMS, D3_CODLAN, D_E_L_E_T_, R_E_C_N_O_)
						VALUES ('$filia','$tm','$cod_mch','UM',$cant1um,'RE4',
								'$conta',' ','$bodega','$doc','$emision',
								'$grupo',$custo1,0,0,0,$custo5,
								' ',' ',' ','$seq_sc','$segum',$cantidad,
								'$tipo',' ','AUTOCONECTOR',' ',0,' ',
								' ','$chave',' ',' ',0,' ',
								' ',' ',' ',' ',0,0,
								0,0,0,' ',' ',' ',
								' ',' ',' ',' ',' ',' ',
								' ',0,' ',' ',' ',
								' ',' ',' ',0,
								0,0,0,0,0,' ',
								' ',' ',' ',0,0,' ',
								' ',0,' ',' ',' ',$recno_sd3)";
			$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
			//echo $queryin."<br><br>";
			//actualiza numero de secuencia en tabla de origen de traspasos
			$queryup = "UPDATE ZD3020 SET BO_NUMSEQ = '$seq_sc',BO_NUMSEQ_ID='$seq_id',
														BO_NUMSEQ_DES='$seq_ds' WHERE BO_COD='$cod_mch' AND  BO_SECUENCIA='$numero' and R_E_C_N_O_ = $recno_z2b";//BO_COD='$cod_mch' AND 
			$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
			
			//actualizar cantidad en tabla de stock(SB2010) 
			$queryup_sd2 = "UPDATE SB2020 SET B2_QATU=B2_QATU-$cant1um, B2_QTSEGUM=B2_QTSEGUM-$cantidad WHERE  B2_COD='$cod_mch' AND B2_LOCAL='$bodega' ";
			$rsu_sd2 = querys($queryup_sd2, $tipobd_totvs, $conexion_totvs);
		}
		
		$querysel2 = "SELECT '01'         	AS D3_FILIAL,
						'499'       		AS D3_TM,
						BO_CAMARTICULO      AS D3_COD,
						BO_COD      		AS D3_CODSALIDA,
						'UM'	       		AS D3_UM,
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
						'  '	    		AS D3_SEGUM,
						0 		 			AS D3_QTSEGUM,
						''          		AS D3_USUARIO,
						'E9'        		AS D3_CHAVE,
						BO_CANTEN			AS D3_CANTEN,
						(SELECT SB1.B1_TIPO FROM SB1020 SB1 WHERE TRIM(SB1.B1_COD) = TRIM(BO_COD))         	AS D3_TIPO
        FROM ZD3020
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss2 = querys($querysel2, $tipobd_totvs, $conexion_totvs);
		//echo $querysel2."<br><br>";
		//die();
		while($v = ver_result($rss2, $tipobd_totvs)){
			$filia 		= $v["D3_FILIAL"];
			$tm 		= $v["D3_TM"];
			$cod_mch 	= $v["D3_COD"];
			$cod_salida 	= $v["D3_CODSALIDA"];
			$um 		= busca_segum($cod_mch);
			$conversion = busca_conv($cod_mch);
			$factor 	= busca_factor($cod_mch);
			$factor_salida 	= busca_factor($cod_salida);
			$cantidad = $v["D3_QUANT"];//SEGUNDA
			$cant1um	= $v["D3_QUANT2"];//PRIMERA
			$conta		= $v["D3_CONTA"];
			$bodega		= $v["D3_LOCAL"];
			$doc 		= $v["D3_DOC"];
			$doc 		= 'TB'.substr($doc,-6);
			//$doc 		= str_replace('-','',$doc);
			$emision 	= $v["D3_EMISSAO"];
			$grupo 		= $v["D3_GRUPO"];
			$emision 	= $v["D3_EMISSAO"];
			$secuencia_t 		= $v["D3_NUMSEQ"];
			$segum 		= $v["D3_SEGUM"];
			$tcantidad 	= $v["D3_QTSEGUM"];
			$tipo 		= $v["D3_TIPO"];
			$chave 		= $v["D3_CHAVE"];
			$cantidad_ent 		= $v["D3_CANTEN"];
			//$custo1 	= busca_custo1($bodega,$cod_mch);
			$custo1 	= valor_custo1($cod_mch);
			$custo1 	= $custo1*$cantidad_ent;
			$custo5 	= busca_custo5($bodega,$cod_mch);
			$custo5 	= $custo5*$cantidad_ent;
			//$seq_totvs=consultaseq();
			$recno_sd3 = recno_sd3();
			//$secuencia_totvs = $seq_totvs[2];
			
			//********************************proceso nuevo******************
			
			$cantidad = ($cantidad);
			
			
			
			///******************************//
			
			$queryin2 = "INSERT INTO SD3020 (D3_FILIAL, D3_TM, D3_COD, D3_UM, D3_QUANT, D3_CF,
												D3_CONTA, D3_OP, D3_LOCAL, D3_DOC, D3_EMISSAO,
												D3_GRUPO, D3_CUSTO1, D3_CUSTO2, D3_CUSTO3, D3_CUSTO4, D3_CUSTO5,
												D3_CC, D3_PARCTOT, D3_ESTORNO, D3_NUMSEQ, D3_SEGUM, D3_QTSEGUM,
												D3_TIPO, D3_NIVEL, D3_USUARIO, D3_REGWMS, D3_PERDA, D3_DTLANC,
												D3_TRT, D3_CHAVE, D3_IDENT, D3_SEQCALC, D3_RATEIO, D3_LOTECTL,
												D3_NUMLOTE, D3_DTVALID, D3_LOCALIZ, D3_NUMSERI, D3_CUSFF1, D3_CUSFF2,
												D3_CUSFF3, D3_CUSFF4, D3_CUSFF5, D3_ITEM, D3_OK, D3_ITEMCTA,
												D3_CLVL, D3_PROJPMS, D3_TASKPMS, D3_ORDEM, D3_SERVIC, D3_STSERV,
												D3_OSTEC, D3_POTENCI, D3_TPESTR, D3_REGATEN, D3_ITEMSWN,
												D3_DOCSWN, D3_ITEMGRD, D3_STATUS, D3_CUSRP1,
												D3_CUSRP2, D3_CUSRP3, D3_CUSRP4, D3_CUSRP5, D3_CMRP, D3_MOEDRP,
												D3_MOEDA, D3_EMPOP, D3_DIACTB, D3_PMICNUT, D3_CMFIXO, D3_NODIA,
												D3_GARANTI, D3_PMACNUT, D3_NRBPIMS, D3_CODLAN, D_E_L_E_T_, R_E_C_N_O_)
						VALUES ('$filia','$tm','$cod_mch','UM',$cantidad_ent,'DE4',
								'$conta',' ','$bodega','$doc','$emision',
								'$grupo',$custo1,0,0,0,$custo5,
								' ',' ',' ','$secuencia_t','$segum',$cantidad_ent,
								'$tipo',' ','AUTOCONECTOR',' ',0,' ',
								' ','$chave',' ',' ',0,' ',
								' ',' ',' ',' ',0,0,
								0,0,0,' ',' ',' ',
								' ',' ',' ',' ',' ',' ',
								' ',0,' ',' ',' ',
								' ',' ',' ',0,
								0,0,0,0,0,' ',
								' ',' ',' ',0,0,' ',
								' ',0,' ',' ',' ',$recno_sd3)";

						
			$rsi2 = querys($queryin2, $tipobd_totvs, $conexion_totvs);
			//echo $queryin2."<br><br>";
			
			$queryup1_sd2 = "UPDATE SB2020 SET B2_QATU=B2_QATU+$cantidad_ent,  B2_QTSEGUM=B2_QTSEGUM+$cantidad_ent WHERE  B2_COD='$cod_mch' AND B2_LOCAL='$bodega'";
			$rsu1_sd2 = querys($queryup1_sd2, $tipobd_totvs, $conexion_totvs);
		}
		
		$queryup_fin = "UPDATE ZD3020 SET  BO_STATUS='40', BO_FTRASPASO='$hoy' WHERE  BO_SECUENCIA='$numero' ";
		$rsu_fin = querys($queryup_fin, $tipobd_totvs, $conexion_totvs);
		echo "PROCESO TERMINADO /".$seq."/id:".$seq_id."/status:".$seq_ds."/sec:".$seq_sc;
	
}
function valor_custo1($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$queryin = "SELECT 
				CASE WHEN 
				(SELECT NVL(ROUND((TOTAL/CANT),2),0) AS B9_VALOR FROM(
				SELECT B9_COD, NVL(SUM(B9_QINI),1) AS CANT, SUM(B9_VINI1) AS TOTAL FROM SB9020 WHERE TRIM(B9_COD) = TRIM('$articulo')  GROUP BY B9_COD)) = 0
				THEN
				(SELECT NVL(ROUND((TOTAL/CANT),2),0) AS B2_VALOR FROM(
				SELECT B2_COD, NVL(SUM(B2_QATU),1) AS CANT, SUM(B2_VATU1) AS TOTAL FROM SB2020 WHERE TRIM(B2_COD) = TRIM('$articulo')  GROUP BY B2_COD))
				ELSE
				(SELECT NVL(ROUND((TOTAL/CANT),2),0) AS B9_VALOR FROM(
				SELECT B9_COD, NVL(SUM(B9_QINI),1) AS CANT, SUM(B9_VINI1) AS TOTAL FROM SB9020 WHERE TRIM(B9_COD) = TRIM('$articulo')  GROUP BY B9_COD))
				END AS VALOR
				FROM DUAL";

	$rss = querys($queryin, $tipobd_totvs, $conexion_totvs);
	$row =ver_result($rss, $tipobd_totvs);

    $codigo=$row['VALOR'];
    
	return $codigo;
}

function busca_custo1($origen,$articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B2_CM1 FROM SB2020 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo'";
	//echo "QUERY busca_custo1 : ".$queryin."<br>";
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
	$queryin = "SELECT B2_CM5 FROM SB2020 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo'";
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

function recno_sd3(){
    global $tipobd_totvs,$conexion_totvs;
	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS R_E_C_N_O_ FROM SD3020";
	$rs = querys($select, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rs, $tipobd_totvs);
	$recno = $fila['R_E_C_N_O_'];
	return $recno;
}
//getUsers();
/*MAIN*/

if(isset($_GET["ver"])){
   $bodega_origen =  $_GET["bodega_origen"];
   $bodega_destino =  $_GET["bodega_destino"];
   $status 			=  $_GET["status"];
    ver_traspasos($bodega_origen,$bodega_destino,$status);
}
if(isset($_GET["ver_traspaso"])){
   $id_traspaso = $_GET["id_traspaso"];
    lista_traspaso($id_traspaso);
}
if(isset($_GET["eliminar_articulo"])){
	
	$recno = $_GET["recno"];	
    eliminar_articulo($recno);	
}
if(isset($_GET["anula_traspaso"])){
	
	$secuencia = $_GET["secuencia"];
	confirmacion_correo($secuencia);
    anula_trapaso($secuencia);	
}
if(isset($_GET["valida_ok"])){
	
	$secuencia = $_GET["secuencia"];	
    valida_traspaso($secuencia);
	

}
?>