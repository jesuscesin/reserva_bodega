<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require_once "conexion.php";
require_once "config.php";
require_once "generar_insert.php";
include('send_mail.php');
include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";



function ver_traspasos($bodega_origen,$bodega_destino,$status){
	global $tipobd_totvs,$conexion_totvs;
	
	if($status == 'todos'){
		$querysel = "SELECT C0_SECUENCIA,
					C0_ORIGEN,
					C0_DESTINO,
					COUNT(C0_COD) AS ARTICULOS,
                    SUM(C0_CANTIDAD) AS CANTIDAD,
                    SUM(C0_CANTEN) AS CANTIDAD_ENTRADA,
					C0_FTRASPASO,C0_STATUS, NVL((SELECT DISTINCT D3_ESTORNO FROM SD3010 WHERE REPLACE(C0_SECUENCIA,'-','')=D3_DOC),' ') AS D3_ESTORNO,
					(SELECT MAX(ZC.C0_DOC) FROM ZC0020 ZC WHERE B.C0_SECUENCIA = ZC.C0_SECUENCIA ) AS C0_DOC 
					FROM ZC0020 B
					WHERE  C0_SECUENCIA like '$bodega_origen%'
					AND C0_SECUENCIA NOT LIKE 'XX%'
					AND C0_DESTINO  like '%$bodega_destino%'
                    AND B.D_E_L_E_T_<>'*'
					GROUP BY C0_SECUENCIA,C0_ORIGEN,C0_DESTINO,C0_FTRASPASO,C0_STATUS
					ORDER BY substr(C0_SECUENCIA,4,6) DESC";//,,substr(C0_SECUENCIA,4,6),C0_FECHA
					//echo $querysel;
	}elseif ($status <> 'todos') {
		$querysel = "SELECT C0_SECUENCIA,
					C0_ORIGEN,
					C0_DESTINO,
					COUNT(C0_COD) AS ARTICULOS,
					SUM(C0_CANTIDAD) AS CANTIDAD,
					SUM(C0_CANTEN) AS CANTIDAD_ENTRADA,
					C0_FTRASPASO,C0_STATUS, NVL((SELECT DISTINCT D3_ESTORNO FROM SD3010 WHERE REPLACE(C0_SECUENCIA,'-','')=D3_DOC),' ') AS D3_ESTORNO,
					(SELECT MAX(ZC.C0_DOC) FROM ZC0020 ZC WHERE B.C0_SECUENCIA = ZC.C0_SECUENCIA ) AS C0_DOC 
					FROM ZC0020 B
					WHERE  C0_SECUENCIA like '$bodega_origen%'
					AND C0_SECUENCIA NOT LIKE 'XX%'
					AND C0_DESTINO  like '%$bodega_destino%'
					AND C0_STATUS = '$status'
					AND B.D_E_L_E_T_<>'*'
					GROUP BY C0_SECUENCIA,C0_ORIGEN,C0_DESTINO,C0_FTRASPASO,C0_STATUS
					ORDER BY substr(C0_SECUENCIA,4,6) DESC";//,,substr(C0_SECUENCIA,4,6),C0_FECHA
	}

	 //echo "<pre>".$querysel."</pre>";
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$id_traspaso = $v["C0_SECUENCIA"];
		$cargar[]=array(
					"ID"		=>$v["C0_SECUENCIA"],
					"ORIGEN"	=>$v["C0_ORIGEN"],
					"DESTINO"	=>$v["C0_DESTINO"],
					"ARTICULOS"	=>$v["ARTICULOS"],
					"CANTIDAD"	=>$v["CANTIDAD"],
					"CANTIDAD_ENTRADA"	=>$v["CANTIDAD_ENTRADA"],
					"FTRASPASO"		=>formatDate($v["C0_FTRASPASO"]),
					////"FEMISION"	=>formatDate($v["C0_FECHA"]),
					"ESTADO"		=>$v["C0_STATUS"],
					"ESTADO_SD3"	=>$v["D3_ESTORNO"],
					"DOC"			=>$v["C0_DOC"],
					"VER_SOLICITUD" 			=>"<a target='_blank' class='btn btn-block bg-gradient-info btn-sm' href='solicitud_reserva.php?id=$id_traspaso'>Ver Solicitud</a>"
			);
	}

	echo json_encode($cargar);
}
function lista_traspaso($id_traspaso){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT C0_SECUENCIA,C0_COD,C0_DESCR,C0_CANTIDAD,R_E_C_N_O_
			FROM ZC0020
			WHERE C0_SECUENCIA='$id_traspaso'
			AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	//echo $querysel;
	while($v = ver_result($rss, $tipobd_totvs)){
		$ver[]=array(
					"ID"		=>$v["C0_SECUENCIA"],					
					"ARTICULOS"	=>$v["C0_COD"],
					"DESCR"		=>$v["C0_DESCR"],
					"CANTIDAD"	=>$v["C0_CANTIDAD"],
					"RECNO"		=>$v["R_E_C_N_O_"]
					
			);
	}
	echo json_encode($ver);
}
function eliminar_articulo($recno){
	global $tipobd_totvs,$conexion_totvs;
	
	$queryup ="UPDATE ZC0020 SET D_E_L_E_T_='*' WHERE R_E_C_N_O_='$recno'";///PREGUNTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "ARTICULO ELIMINARDO CON EXITO !";
		}else{
			echo "ERROR: ARTICULO NO ELIMINADO !";
		}    
}


function anula_trapaso($secuencia){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$queryup = "UPDATE ZC0020 SET D_E_L_E_T_='*', C0_STATUS='30' WHERE C0_SECUENCIA='$secuencia'";
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "RESERVA ANULADA CON EXITO !";
		}else{
			echo "ERROR: RESERVA NO ANULADA !";
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
	
	$querysel = "SELECT distinct C0_STATUS FROM ZC0020 WHERE C0_SECUENCIA='$numero'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$status = $v["C0_STATUS"];
	if($status == 20){
		
	$asunto = "ANULACION DE RESERVA ENTRE BODEGAS # $numero";
    $msj    = "<h3><strong>Se ha anulado una reserva entre bodegas con ID $numero</strong></h3> ";
			$titulo =" ";
			$pie='<br><br>Este mensaje se ha generado automaticamente, por favor NO RESPONDER.<br><br>Atte. Informática Monarch.';
			$msj=$msj.$titulo.$pie;
			
			$to=array('gpuyol@grupomonarch.cl'/*,'dlacroix@grupomonarch.cl','msotomayor@grupomonarch.cl'*/);
			$adjunto = "traspasos_bodegas/".$numero.".pdf";
			envia_correo_traspaso($to, $asunto, $msj,$adjunto);
	}
}
//////////////////////////////FUNCION VALIDAR STOCK PARA VALIDAR RESERVA/////////////////////////////////////////////////////JC
function validar_stock($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT C0_CAMARTICULO, C0_CANTIDAD, C0_ORIGEN FROM ZC0020 WHERE C0_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod 		= $v['C0_CAMARTICULO'];
		$C0_origen 	= $v['C0_ORIGEN'];
		$cant 		= $v['C0_CANTIDAD'];
		
		$querysel_1 = "SELECT B2_QATU AS CANT FROM SB2020 WHERE B2_COD='$cod' AND (B2_QATU - B2_RESERVA) > $cant AND D_E_L_E_T_<>'*' AND B2_LOCAL = '$C0_origen'";
		//echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss_1, $tipobd_totvs);
		$filas = $v1["CANT"];
		if(empty($filas) ){
			//echo '<script language="javascript">alert("ERROR : CODIGO '.$cod.' NO EXISTEN EN BODEGA DE DESTINO");</script>';
			echo "<font color=red>ERROR : CODIGO <strong>$cod</strong> NO HAY STOCK SUFICIENTE PARA VALIDAR LA RESERVA</font>";
			die();
		}
		
	}
	//return $filas;
}
//////////////////////////////FUNCION VALIDAR SI N° RESERVA EXISTE/////////////////////////////////////////////////////JC
function existe_reserva($numero){	
    global $tipobd_totvs,$conexion_totvs;
	
	
	//$secuencia  = substr($numero, -6);
	$num_reserva = substr($numero, -6); 
	//echo $num_reserva;

	$querysel_1 = "SELECT count(*) AS FILAS FROM SC0020 WHERE C0_NUM='$num_reserva' --AND D_E_L_E_T_<>'*'";
		//echo $querysel_1."<br>";
		$rss_1 = querys($querysel_1, $tipobd_totvs, $conexion_totvs);
		$v1 = ver_result($rss_1, $tipobd_totvs);
		$filas = $v1["FILAS"];
		if($filas > 0){
			//echo '<script language="javascript">alert("ERROR : CODIGO '.$cod.' NO EXISTEN EN BODEGA DE DESTINO");</script>';
			echo "<font color=red>ERROR : NUMERO DE RESERVA <strong>$num_reserva</strong> YA EXISTE, RESERVA NO FUE CARGADA</font>";
			die();
		}
}


//////////////////////////////FUNCION CREA RESERVA/////////////////////////////////////////////////////JC

function valida_reserva($numero){
    global $tipobd_totvs,$conexion_totvs;
	
	//$recno_sd3 = recno_sd3();
	$hoy 	= date('Ymd');

	// Sumar 10 días a la fecha actual
    $fecha_valida = date('Ymd', strtotime($hoy . ' +10 days'));

	// Modificación para mantener los ceros a la izquierda y solo los 6 ultimos numeros
	$num_reserva = substr($numero, -6); // Obtener los últimos 6 caracteres
    $num_reserva_char = str_pad(substr($numero, -6), 6, '0', STR_PAD_LEFT);
	

	//echo $num_reserva;
	//echo $num_reserva_char;
	
	
	$querysel = "SELECT *
        FROM ZC0020
		WHERE C0_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
		//echo $querysel."<br>";
		//die();
        //primera unidad de medidas = unidades
        //segunda unidad de medida = bipack, tripack,etc



			//SELECCIONO N° RESERVA
			$query_num_rsv = "SELECT LPAD(MAX(C0_NUM) + 1, 6, 0) as N_RESERVA  FROM SC0020";
			$stid = oci_parse($conexion_totvs, $query_num_rsv);
			oci_execute($stid);
			//ECHO $query_num_rsv;

			// Verificar si la consulta se ejecutó correctamente y obtener el valor
			if ($row = oci_fetch_assoc($stid)) {
				$num_reserva = $row['N_RESERVA'];
			} else {
				// Manejo de errores si la consulta no se ejecuta correctamente
				echo "Error en la consulta SQL.";
				exit();
			}
			
		while($v = ver_result($rss, $tipobd_totvs)){
			$filial 	= $v["C0_ORIGEN"];
			$np_cod 	= $v["C0_COD"];
			$np_descri 	= $v["C0_DESCR"];
			$cant		= $v["C0_CANTIDAD"];
			$usuario	= $v["C0_USUARIO"];
			$oc			= $v["C0_OC"];
			$factura	= $v["C0_FACTURA"];
	
			
			if ($filial === '01') {
				
				

				validar_stock($numero);
				//existe_reserva($numero);



			
			$queryin = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
										   C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
										   C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
										   C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
										   C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
										   R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS,C0_OC,C0_FACTURA) 
						VALUES(	'$filial', ' ', ' ', '$num_reserva', 'RPNC01', 'VD', ' ', '$usuario', '01', 
								'$np_cod', '01', ' ', $cant, 0, 0, 0, 0,
						 		0, '$fecha_valida', 0, 0, '$hoy', ' ', ' ', ' ', 
						 		' ', 0, ' ', ' ',0, 0, 0, 0,
						  		' ', ' ', '$np_cod', '01', ' ', ' ', ' ', ' ',
								(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, 'INVESTIGACION', 'ID', '$oc', $factura)";
					
			$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
			//echo "INSERT : ".$queryin."<br>";

			//actualizar cantidad en tabla de stock(SB2020) 
			$queryup_sd2 = "UPDATE SB2020 SET B2_RESERVA=(B2_RESERVA+$cant) WHERE  B2_COD='$np_cod' AND B2_LOCAL='$filial' ";
			$rsu_sd2 = querys($queryup_sd2, $tipobd_totvs, $conexion_totvs);

			//numero de reserva en tabla ZC0
			$queryup_sd3 = "UPDATE ZC0020 SET C0_RESERVA = '$num_reserva'  WHERE C0_SECUENCIA='$numero' ";
			$rsu_sd3 = querys($queryup_sd3, $tipobd_totvs, $conexion_totvs);

			}else {


			//cuando se reserva desde una reserva, PRIMERO ELIMINAR LA RESERVA DEL PRODUCTO SELECCIONADO
			$queryup_1 = "UPDATE SC0020 SET D_E_L_E_T_ = '*' , R_E_C_D_E_L_ = (SELECT R_E_C_N_O_ FROM SC0020 WHERE C0_NUM = '".$filial."' and C0_PRODUTO='".$np_cod."') WHERE C0_NUM = '".$filial."' and C0_PRODUTO='".$np_cod."' ";
			$rsu_1 = querys($queryup_1, $tipobd_totvs, $conexion_totvs);
			//echo $queryup_1;

			//numero de reserva en tabla ZC0
			$queryup_sd3 = "UPDATE ZC0020 SET C0_RESERVA = '$num_reserva'  WHERE C0_SECUENCIA='$numero' ";
			$rsu_sd3 = querys($queryup_sd3, $tipobd_totvs, $conexion_totvs);

			$query_stock = "SELECT C0_QUANT AS STOCK FROM SC0020 WHERE C0_NUM = '".$filial."' and C0_PRODUTO='".$np_cod."'";
			// echo $querysel;
			$rss = querys($query_stock, $tipobd_totvs, $conexion_totvs);
			$v = ver_result($rss, $tipobd_totvs);
			$stock =  $v["STOCK"];

			if($cant == $stock){

			//CREA RESERVA PARA I+D
			$query_03 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
			R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT, C0_OC, C0_FACTURA)
			SELECT C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, (SELECT LPAD(MAX(C0_NUM) +1,6,0) FROM SC0020), C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA,".$cant." , C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, ' ', 
			(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, 'INVESTIGACION', 'ID', C0_NUM, C0_OC, C0_FACTURA
			FROM SC0020 WHERE C0_NUM = '".$filial."' and C0_PRODUTO='".$np_cod."' ";
			//echo $query_03;
			$rsu_3 = querys($query_03, $tipobd_totvs, $conexion_totvs);
			
			
			}else{

			//CREA RESERVA PARA I+D
			$query_02 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
			R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT, C0_OC, C0_FACTURA)
			SELECT C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, (SELECT LPAD(MAX(C0_NUM) +1,6,0) FROM SC0020), C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA,".$cant." , C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, ' ',
			(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, 'INVESTIGACION', 'ID', C0_NUM, C0_OC, C0_FACTURA
			FROM SC0020 WHERE C0_NUM = '".$filial."' and C0_PRODUTO='".$np_cod."' ";
			//echo $query_03;
			$rsu_2 = querys($query_02, $tipobd_totvs, $conexion_totvs);


			//CREA RESERVA CON RESTO
			$query_03 = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
			R_E_C_N_O_, R_E_C_D_E_L_, C0_CLASIF, C0_CODCLAS, C0_RESANT, C0_OC, C0_FACTURA)
			SELECT C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, (SELECT LPAD(MAX(C0_NUM) +1,6,0) FROM SC0020), C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
			C0_PRODUTO, C0_LOCAL, C0_XUBICA,(C0_QUANT-$cant) , C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
			C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
			C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
			C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, ' ', 
			(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, C0_CLASIF, C0_CODCLAS, C0_NUM, C0_OC, C0_FACTURA
			FROM SC0020 WHERE C0_NUM = '".$filial."' and C0_PRODUTO='".$np_cod."' ";
			//echo $query_03;
			$rsu_3 = querys($query_03, $tipobd_totvs, $conexion_totvs);
			}


			if (!$rsu_3){
				echo "Error";
				echo "<br>";
				echo $query_03;
				exit();
			}

			}	
			


		}
	
		$queryup_fin = "UPDATE ZC0020 SET  C0_STATUS='40', C0_FTRASPASO='$hoy' WHERE  C0_SECUENCIA='$numero' ";
		$rsu_fin = querys($queryup_fin, $tipobd_totvs, $conexion_totvs);

		echo "PROCESO TERMINADO";
}
		


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
    anula_trapaso($secuencia);	
	send_mail('jesus.cesin@ptt.cl','Jesus Cesin',$_GET["secuencia"], define_smtp(),3);
}
if(isset($_GET["valida_ok"])){
	
	$secuencia = $_GET["secuencia"];	
    valida_reserva($secuencia);
	send_mail('jesus.cesin@ptt.cl','Jesus Cesin',$_GET["secuencia"], define_smtp(),2);
	

}
?>