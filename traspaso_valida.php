<?php
error_reporting(E_ALL);
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
					(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
					COUNT(DISTINCT BO_COD) AS ARTICULOS,
                    SUM(DISTINCT BO_CANTIDAD) AS CANTIDAD,
					BO_FTRASPASO,BO_STATUS,NVL(D3_ESTORNO,' ') AS D3_ESTORNO
					FROM Z2B_TRASPASO_BODEGA B LEFT JOIN  SD3010 S ON  REPLACE(BO_SECUENCIA,'-','')=D3_DOC
					WHERE  (BO_ORIGEN  like '%$bodega_origen%'
					AND BO_DESTINO  like '%$bodega_destino%')
                    AND B.D_E_L_E_T_<>'*'
					GROUP BY BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO,BO_STATUS,D3_ESTORNO
					ORDER BY substr(BO_SECUENCIA,4,6) DESC";//,,substr(BO_SECUENCIA,4,6),BO_FECHA
					 // echo $querysel;
	}else{
		$querysel = "SELECT BO_SECUENCIA,
					(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
					COUNT(DISTINCT BO_COD) AS ARTICULOS,
                    SUM(DISTINCT BO_CANTIDAD) AS CANTIDAD,
					BO_FTRASPASO,BO_STATUS,NVL(D3_ESTORNO,' ') AS D3_ESTORNO
					FROM Z2B_TRASPASO_BODEGA B LEFT JOIN  SD3010 S ON  REPLACE(BO_SECUENCIA,'-','')=D3_DOC
					WHERE  (BO_ORIGEN  like '%$bodega_origen%'
					AND BO_DESTINO  like '%$bodega_destino%'
					AND BO_STATUS = '$status')
                    AND B.D_E_L_E_T_<>'*'
					GROUP BY BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO,BO_STATUS,D3_ESTORNO
					ORDER BY substr(BO_SECUENCIA,4,6) DESC";//,substr(BO_SECUENCIA,-2),BO_FECHA
					// echo $querysel;
	}
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id_traspaso = $v["BO_SECUENCIA"];
		$cargar[]=array(
					"ID"		=>$v["BO_SECUENCIA"],
					"ORIGEN"	=>$v["BO_ORIGEN"],
					"DESTINO"	=>$v["BO_DESTINO"],
					"ARTICULOS"	=>$v["ARTICULOS"],
					"CANTIDAD"	=>$v["CANTIDAD"],
					"FTRASPASO"	=>formatDate($v["BO_FTRASPASO"]),
					////"FEMISION"	=>formatDate($v["BO_FECHA"]),
					"ESTADO"	=>$v["BO_STATUS"],
					"ESTADO_SD3"	=>$v["D3_ESTORNO"],
					"VER_SOLICITUD" 			=>"<a target='_blank' class='btn btn-block bg-gradient-info btn-sm' href='solicitud_traspaso.php?id=$id_traspaso'>Ver Solicitud</a>"
			);
	}

	echo json_encode($cargar);
}
function lista_traspaso($id_traspaso){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_SECUENCIA,BO_COD,BO_DESCR,BO_CANTIDAD,R_E_C_N_O_
			FROM Z2B_TRASPASO_BODEGA
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
	
	$queryup ="UPDATE Z2B_TRASPASO_BODEGA SET D_E_L_E_T_='*' WHERE R_E_C_N_O_='$recno'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "ARTICULO ELIMINARDO CON EXITO !";
		}else{
			echo "ERROR: ARTICULO NO ELIMINADO !";
		}    
}


function anula_trapaso($secuencia){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$queryup = "UPDATE Z2B_TRASPASO_BODEGA SET D_E_L_E_T_='*', BO_STATUS='30' WHERE BO_SECUENCIA='$secuencia'";
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "TRASPASO ANULADO CON EXITO !";
		}else{
			echo "ERROR: TRASPASO NO ANULADO !";
		}    
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
	
	$querysel = "SELECT distinct BO_STATUS FROM Z2B_TRASPASO_BODEGA WHERE BO_SECUENCIA='$numero'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$status = $v["BO_STATUS"];
	if($status == 20){
		
	$asunto = "ANULACION DE TRASPASO ENTRE BODEGAS # $numero";
    $msj    = "<h3><strong>Se ha anulado un traspaso entre bodegas con ID $numero</strong></h3> ";
			$titulo =" ";
			$pie='<br><br>Este mensaje se ha generado automaticamente, por favor NO RESPONDER.<br><br>Atte. Informática Monarch.';
			$msj=$msj.$titulo.$pie;
			
			$to=array('gpuyol@grupomonarch.cl'/*,'dlacroix@grupomonarch.cl','msotomayor@grupomonarch.cl'*/);
			$adjunto = "traspasos_bodegas/".$numero.".pdf";
			envia_correo_traspaso($to, $asunto, $msj,$adjunto);
	}
}
function existen_codigos_z2b($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_CAMARTICULO,BO_DESTINO FROM Z2B_TRASPASO_BODEGA WHERE BO_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod 		= $v['BO_CAMARTICULO'];
		$bo_destino 	= $v['BO_DESTINO'];
		
		$querysel_1 = "SELECT count(*) AS FILAS FROM SB2010 WHERE B2_COD='$cod' AND B2_LOCAL='$bo_destino' AND D_E_L_E_T_<>'*'";
		//echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss_1, $tipobd_totvs);
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
    global $tipobd_totvs,$conexion_totvs;
	
	
	$secuencia  = str_replace('-','',$numero);
	$querysel_1 = "SELECT count(*) AS FILAS FROM SD3010 WHERE D3_DOC='$secuencia' AND D_E_L_E_T_<>'*'";
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
function busca_segum($articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B1_SEGUM FROM SB1010 WHERE B1_COD='$articulo' AND D_E_L_E_T_<>'*'";
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
						BO_CANT2UM     AS D3_QTSEGUM,
						'PT'         	AS D3_TIPO,
						''          	AS D3_USUARIO,
						'E9'        	AS D3_CHAVE,
                        R_E_C_N_O_ 		AS RECNO
        FROM Z2B_TRASPASO_BODEGA
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
		//echo $querysel."<br>";
		//die();
        //primera unidad de medidas = unidades
        //segunda unidad de medida = bipack, tripack,etc
		while($v = ver_result($rss, $tipobd_totvs)){
			$filia 		= $v["D3_FILIAL"];
			$tm 		= $v["D3_TM"];
			$cod_mch 	= $v["D3_COD"];
            $conversion = busca_conv($cod_mch);
			$um 		= busca_segum($cod_mch);
			$cantidad1um = $v["D3_QUANT2"];//SEGUNDA
            $cantidad = $cantidad1um/$conversion;
			$cant1um	= $v["D3_QUANT2"];//PRIMERA
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
			
			$queryin = "INSERT INTO SD3010 (D3_FILIAL, D3_TM, D3_COD, D3_UM, D3_QUANT, D3_CF,
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
						VALUES ('$filia','$tm','$cod_mch','$um',$cant1um,'RE4',
								'$conta',' ','$bodega','$doc','$emision',' ',
								'$grupo',$custo1,0,0,0,$custo5,
								' ',' ',' ','$secuencia_totvs','$segum',$cantidad,
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
			$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
			//echo "INSERT : ".$queryin."<br>";
			//actualiza numero de secuencia en tabla de origen de traspasos
			$queryup = "UPDATE Z2B_TRASPASO_BODEGA SET BO_NUMSEQ = '$secuencia_totvs',BO_NUMSEQ_ID='$secuencia_totvs_id',
														BO_NUMSEQ_DES='$secuencia_totvs_est' WHERE BO_COD='$cod_mch' AND  BO_SECUENCIA='$numero' and R_E_C_N_O_ = $recno_z2b";//BO_COD='$cod_mch' AND 
			$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
			
			//actualizar cantidad en tabla de stock(SB2010) 
			$queryup_sd2 = "UPDATE SB2010 SET B2_QATU=B2_QATU-$cant1um, B2_QTSEGUM=B2_QTSEGUM-$cantidad WHERE  B2_COD='$cod_mch' AND B2_LOCAL='$bodega' ";
			$rsu_sd2 = querys($queryup_sd2, $tipobd_totvs, $conexion_totvs);
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
        FROM Z2B_TRASPASO_BODEGA
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss2 = querys($querysel2, $tipobd_totvs, $conexion_totvs);
		//echo $querysel2."<br>";
		//die();
		while($v = ver_result($rss2, $tipobd_totvs)){
			$filia 		= $v["D3_FILIAL"];
			$tm 		= $v["D3_TM"];
			$cod_mch 	= $v["D3_COD"];
			$um 		= busca_segum($cod_mch);
			$conversion = busca_conv($cod_mch);
			$cantidad1um = $v["D3_QUANT2"];//SEGUNDA
            $cantidad = $cantidad1um/$conversion;
			$cant1um	= $v["D3_QUANT2"];//PRIMERA
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
			
			$queryin2 = "INSERT INTO SD3010 (D3_FILIAL, D3_TM, D3_COD, D3_UM, D3_QUANT, D3_CF,
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
						VALUES ('$filia','$tm','$cod_mch','$um',$cant1um,'DE4',
								'$conta',' ','$bodega','$doc','$emision',' ',
								'$grupo',$custo1,0,0,0,$custo5,
								' ',' ',' ','$secuencia_t','$segum',$cantidad,
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
			$rsi2 = querys($queryin2, $tipobd_totvs, $conexion_totvs);
			//echo $queryin2."<br>";
			
			$queryup1_sd2 = "UPDATE SB2010 SET B2_QATU=B2_QATU+$cant1um,  B2_QTSEGUM=B2_QTSEGUM+$cantidad WHERE  B2_COD='$cod_mch' AND B2_LOCAL='$bodega'";
			$rsu1_sd2 = querys($queryup1_sd2, $tipobd_totvs, $conexion_totvs);
		}
		
		$queryup_fin = "UPDATE Z2B_TRASPASO_BODEGA SET  BO_STATUS='40', BO_FTRASPASO='$hoy' WHERE  BO_SECUENCIA='$numero' ";
		$rsu_fin = querys($queryup_fin, $tipobd_totvs, $conexion_totvs);
		echo "PROCESO TERMINADO";
	
}
function busca_custo1($origen,$articulo){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo='';
	$queryin = "SELECT B2_CM1 FROM SB2010 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo'";
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
	$queryin = "SELECT B2_CM5 FROM SB2010 WHERE B2_LOCAL='$origen' AND B2_COD='$articulo'";
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
	
	$select = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS R_E_C_N_O_ FROM SD3010";
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