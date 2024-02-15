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
					(SELECT NNR_CODIGO||'_'||NNR_DESCRI FROM NNR020 WHERE NNR_FILIAL='01' AND NNR_CODIGO=BO_ORIGEN) AS BO_ORIGEN,
					(SELECT NNR_CODIGO||'_'||NNR_DESCRI FROM NNR020 WHERE NNR_FILIAL='01' AND NNR_CODIGO=BO_DESTINO) AS BO_DESTINO,
					COUNT(BO_COD) AS ARTICULOS,
                    SUM(BO_CANTIDAD) AS CANTIDAD,
                    SUM(BO_CANTEN) AS CANTIDAD_ENTRADA,
					BO_FTRASPASO,BO_STATUS, NVL((SELECT DISTINCT D3_ESTORNO FROM SD3010 WHERE REPLACE(BO_SECUENCIA,'-','')=D3_DOC),' ') AS D3_ESTORNO
					FROM ZC0020 B
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
					COUNT(BO_COD) AS ARTICULOS,
                    SUM(BO_CANTIDAD) AS CANTIDAD,
                    SUM(BO_CANTEN) AS CANTIDAD_ENTRADA,
					BO_FTRASPASO,BO_STATUS, NVL((SELECT DISTINCT D3_ESTORNO FROM SD3010 WHERE REPLACE(BO_SECUENCIA,'-','')=D3_DOC),' ') AS D3_ESTORNO
					FROM ZC0020 B
					WHERE  (BO_ORIGEN  like '%$bodega_origen%'
					AND BO_DESTINO  like '%$bodega_destino%'
					AND BO_STATUS = '$status')
                    AND B.D_E_L_E_T_<>'*'
					GROUP BY BO_SECUENCIA,BO_ORIGEN,BO_DESTINO,BO_FTRASPASO,BO_STATUS
					ORDER BY substr(BO_SECUENCIA,4,6) DESC";//,substr(BO_SECUENCIA,-2),BO_FECHA
					echo $querysel;
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
					"CANTIDAD_ENTRADA"	=>$v["CANTIDAD_ENTRADA"],
					"FTRASPASO"	=>formatDate($v["BO_FTRASPASO"]),
					////"FEMISION"	=>formatDate($v["BO_FECHA"]),
					"ESTADO"	=>$v["BO_STATUS"],
					"ESTADO_SD3"	=>$v["D3_ESTORNO"],
					"VER_SOLICITUD" 			=>"<a target='_blank' class='btn btn-block bg-gradient-info btn-sm' href='solicitud_reserva.php?id=$id_traspaso'>Ver Solicitud</a>"
			);
	}

	echo json_encode($cargar);
}
function lista_traspaso($id_traspaso){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_SECUENCIA,BO_COD,BO_DESCR,BO_CANTIDAD,R_E_C_N_O_
			FROM ZC0020
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
	
	$queryup ="UPDATE ZC0020 SET D_E_L_E_T_='*' WHERE R_E_C_N_O_='$recno'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	if(oci_num_rows($rsu)<>0 or oci_num_rows($rsu)<>null){
			echo "ARTICULO ELIMINARDO CON EXITO !";
		}else{
			echo "ERROR: ARTICULO NO ELIMINADO !";
		}    
}


function anula_trapaso($secuencia){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$queryup = "UPDATE ZC0020 SET D_E_L_E_T_='*', BO_STATUS='30' WHERE BO_SECUENCIA='$secuencia'";
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
	
	$querysel = "SELECT distinct BO_STATUS FROM ZC0020 WHERE BO_SECUENCIA='$numero'";
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
//////////////////////////////FUNCION VALIDAR STOCK PARA VALIDAR RESERVA/////////////////////////////////////////////////////JC
function validar_stock($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT BO_CAMARTICULO, BO_CANTIDAD, BO_ORIGEN FROM ZC0020 WHERE BO_SECUENCIA='$numero' AND D_E_L_E_T_<>'*'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
		$cod 		= $v['BO_CAMARTICULO'];
		$bo_origen 	= $v['BO_ORIGEN'];
		$cant 		= $v['BO_CANTIDAD'];
		
		$querysel_1 = "SELECT B2_QATU AS CANT FROM SB2020 WHERE B2_COD='$cod' AND (B2_QATU - B2_RESERVA) > $cant AND D_E_L_E_T_<>'*' AND B2_LOCAL = '$bo_origen'";
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

	validar_stock($numero);
	existe_reserva($numero);
	// Modificación para mantener los ceros a la izquierda y solo los 6 ultimos numeros
	$num_reserva = substr($numero, -6); // Obtener los últimos 6 caracteres
    $num_reserva_char = str_pad(substr($numero, -6), 6, '0', STR_PAD_LEFT);

	//echo $num_reserva;
	//echo $num_reserva_char;
	
	
	$querysel = "SELECT *
        FROM ZC0020
		WHERE BO_SECUENCIA='$numero'
		AND D_E_L_E_T_<>'*'";
		$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
		//echo $querysel."<br>";
		//die();
        //primera unidad de medidas = unidades
        //segunda unidad de medida = bipack, tripack,etc
		while($v = ver_result($rss, $tipobd_totvs)){
			$filial 		= $v["BO_ORIGEN"];
			$np_cod 	= $v["BO_COD"];
			$np_descri 	= $v["BO_DESCR"];
			$cant		= $v["BO_CANTIDAD"];
	
			
			
			
			$queryin = "INSERT INTO SC0020(C0_FILIAL, C0_NUMSCP, C0_ITEMSCP, C0_NUM, C0_OS, C0_TIPO, C0_DOCRES, C0_SOLICIT, C0_FILRES, 
										   C0_PRODUTO, C0_LOCAL, C0_XUBICA, C0_QUANT, C0_XCONSUM, C0_PENDIEN, C0_XSALDOA, C0_QSOLICI, 
										   C0_SALDOAC, C0_VALIDA, C0_QD3DOC, C0_D3QUANT, C0_EMISSAO, C0_NUMLOTE, C0_LOTECTL, C0_LOCALIZ, 
										   C0_NUMSERI, C0_QTDPED, C0_OBS, C0_UBICA, C0_QTDELIM, C0_QTDORIG, C0_RESERVA, C0_RECNOCP, 
										   C0_NUMREQ, C0_ITEMREQ, C0_CODREQ, C0_FILDES, C0_ORIGEN, C0_TABORI, C0_RECORI, D_E_L_E_T_, 
										   R_E_C_N_O_, R_E_C_D_E_L_, C0_USERLGI, C0_USERLGA) 
						VALUES(	'$filial', ' ', ' ', '$num_reserva', ' ', ' ', ' ', ' ', ' ', 
								'$np_cod', ' ', ' ', $cant, 0, 0, 0, 0,
						 		0, ' ', 0, 0, ' ', ' ', ' ', ' ', 
						 		' ', 0, ' ', ' ',0, 0, 0, 0,
						  		' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
								(SELECT MAX(R_E_C_N_O_) +1 FROM SC0020), 0, ' ', ' ')";
					
			$rsi = querys($queryin, $tipobd_totvs, $conexion_totvs);
			//echo "INSERT : ".$queryin."<br>";

			//actualizar cantidad en tabla de stock(SB2020) 
			$queryup_sd2 = "UPDATE SB2020 SET B2_RESERVA=(B2_RESERVA+$cant) WHERE  B2_COD='$np_cod' AND B2_FILIAL='$filial' ";
			$rsu_sd2 = querys($queryup_sd2, $tipobd_totvs, $conexion_totvs);
		}
	
		$queryup_fin = "UPDATE ZC0020 SET  BO_STATUS='40', BO_FTRASPASO='$hoy' WHERE  BO_SECUENCIA='$numero' ";
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
	confirmacion_correo($secuencia);
    anula_trapaso($secuencia);	
}
if(isset($_GET["valida_ok"])){
	
	$secuencia = $_GET["secuencia"];	
    valida_reserva($secuencia);
	

}
?>