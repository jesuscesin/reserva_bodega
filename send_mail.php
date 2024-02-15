<?php

function define_smtp(){
    require('./phpmailer/class.phpmailer.php');
    require('./phpmailer/class.smtp.php'); 
    
    $mail = new PHPMailer;                                    // Passing `true` enables exceptions
    $mail->setLanguage('es', '/language/');
    $mail->IsSMTP();
    $mail->SMTPAuth     = true;
    $mail->SMTPSecure   = 'tls';
    $mail->SMTPOptions  = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
    $mail->Host         = 'smtp.turbodal.cl';                   // Specify main and backup SMTP servers
    $mail->SMTPAuth     = true;                                 // Enable SMTP authentication
    $mail->Username     = 'cotizaciones@turbodal.cl';           // SMTP username
    $mail->Password     = '2023..,';                            // SMTP password
    $mail->Port         = 25;
    $lHtml = true;
    $mail->isHTML($lHtml); 
    if(!$lHtml){
    $mail->AltBody = 'Este es el cuerpo en texto plano para clientes de correo no HTML';
    }else{
    $mail->AltBody = 'Correo HTML Standard';
    }
    return $mail;
}

function send_mail($correo,$nombre,$secuencia,$mail,$btype){
 
    ## CONTENIDO ##

    $asunto = 'Reserva I+D #'.$secuencia ; 	//asunto
    $headers = 'Content-type:text/html;charset=UTF-8' . '\r\n';
    $mensaje= bodymail($nombre,$secuencia,$btype);

    ## Configuración y envío de correos ##

    $mail->setFrom($mail->Username, 'Notificaciones PTT');       	//quien envía y su nombre
	// Condicionales para determinar destinatarios según $btype

    $mail->addAddress($correo,$nombre);  							//quien recibe y su nombre 
	if ($btype == 1) {
        // Tipo 1
		$mail->addBCC("jesus.cesin@ptt.cl","Jesus Cesin");	//quien recibe y su nombre copia oculta
		$mail->Subject = $asunto;
		$mail->Body    = utf8_decode($mensaje);
		$mail->addAttachment("traspasos_bodegas/".$secuencia.".pdf"); 
		$mail->addAttachment("r_docs/".mail_doc($secuencia)); 
		//$mail->AddStringAttachment($doc, 'Cot_'.$id.'_'.$cliente.'.pdf', 'base64', 'application/pdf');
    } elseif ($btype == 2) {
        // Tipo 2

		$mail->addBCC("jesus.cesin@ptt.cl","Jesus Cesin");	//quien recibe y su nombre copia oculta
		$mail->addBCC("jesusecesin@gmail.com","Jesus Cesin");	//quien recibe y su nombre copia oculta
		$mail->Subject = $asunto;
		$mail->Body    = utf8_decode($mensaje);
		$mail->addAttachment("traspasos_bodegas/".$secuencia.".pdf"); 
		$mail->addAttachment("r_docs/".mail_doc($secuencia)); 
		//$mail->AddStringAttachment($doc, 'Cot_'.$id.'_'.$cliente.'.pdf', 'base64', 'application/pdf');
    } elseif ($btype == 3) {
        // Tipo 3
		$mail->addBCC("jesus.cesin@ptt.cl","Jesus Cesin");	//quien recibe y su nombre copia oculta
		$mail->Subject = $asunto;
		$mail->Body    = utf8_decode($mensaje);
		$mail->addAttachment("traspasos_bodegas/".$secuencia.".pdf"); 
		$mail->addAttachment("r_docs/".mail_doc($secuencia)); 
		//$mail->AddStringAttachment($doc, 'Cot_'.$id.'_'.$cliente.'.pdf', 'base64', 'application/pdf');
    } else {
        // Otros tipos
		$mail->addBCC("jesus.cesin@ptt.cl","Jesus Cesin");	//quien recibe y su nombre copia oculta
		$mail->Subject = $asunto;
		$mail->Body    = utf8_decode($mensaje);
		$mail->addAttachment("traspasos_bodegas/".$secuencia.".pdf"); 
		$mail->addAttachment("r_docs/".mail_doc($secuencia)); 
		//$mail->AddStringAttachment($doc, 'Cot_'.$id.'_'.$cliente.'.pdf', 'base64', 'application/pdf');
    }

	if(!$mail->Send()) {
	   echo 'PHPMailer error: ' . $mail->ErrorInfo;
	}

    $mail->clearAddresses();
}

function mail_doc($secuencia){
	global $tipobd_totvs,$conexion_totvs;
	
	$query = "SELECT C0_DOC FROM TOTVS12C.ZC0020 WHERE C0_SECUENCIA = '".$secuencia."'";
	$result = querys($query, $tipobd_totvs, $conexion_totvs); 
	$doc_name = ver_result($result, $tipobd_totvs);

	$fec = $doc_name['C0_DOC'];

	if(is_null($fec)){
		$fec = 'NO_DOC';
	}	
	echo $fec;
	return $fec;
}

function bodymail($nombre,$secuencia,$btype){

	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT * FROM ZC0020 WHERE C0_SECUENCIA = '$secuencia'";
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);


	if($btype == 1){
		$msg = "<html>
			    <head>
			    <meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
			    </head>
			    <body>
			    <h2>Se ha <strong>generado</strong> la solicitud de reserva nro <strong>'".$secuencia."'.</strong></h2>
			    <p>
			    <strong>-. De :</strong> Bodega ".$v['C0_ORIGEN']."<br>
			    <strong>-. NParte :</strong> ".$v['C0_COD']."<br> 
			    <strong>-. Descripción :</strong> ".$v['C0_DESCR']."<br> 
			    <strong>-. Cantidad :</strong> ".$v['C0_CANTIDAD']."<br>
			    </p>
			    <br>Dirigete a <strong><a href='http://www.turboptt.com:8082/'> Turboptt.com </a></strong>, ingresa a la opción <strong>Movimientos Stock</strong> y luego a <strong>Validar Reserva</strong>. Revisa la documentación y lleva a cabo la acción correspondiente.
			    <br><br>
			    Atentamente.-
			    <br>Notificaciones PTT 
			    </body>
			    </html>";
	}elseif($btype == 2){
		$msg = "<html>
			    <head>
			    <meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
			    </head>
			    <body>
			    <h2>Se ha <strong>aceptado</strong> la solicitud de reserva nro <strong>'".$secuencia."'.</strong></h2>
			    <p>
				<strong>-. De :</strong> Bodega ".$v['C0_ORIGEN']."<br>
			    <strong>-. NParte :</strong> ".$v['C0_COD']."<br> 
			    <strong>-. Descripción :</strong> ".$v['C0_DESCR']."<br> 
			    <strong>-. Cantidad :</strong> ".$v['C0_CANTIDAD']."<br>
				<strong>-. Reserva :</strong> ".$v['C0_RESERVA']."<br>

			    </p>
			    <br>Dirigete a <strong><a href='http://www.turboptt.com:8082/'> Turboptt.com </a></strong>, ingresa a la opción <strong>Movimientos Stock</strong> y luego a <strong>Devolucion I+D ó Ver Reserva</strong>. Revisa la documentación y lleva a cabo la acción correspondiente.
			    <br><br>
			    Atentamente.-
			    <br>Notificaciones PTT 
			    </body>
			    </html>";
	}elseif($btype == 3){
		$msg = "<html>
			    <head>
			    <meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
			    </head>
			    <body>
			    <h2>Se ha <strong>rechazado</strong> la solicitud de reserva nro <strong>'".$secuencia."'.</strong></h2>
			    <br><br>
			    Atentamente.-
			    <br>Notificaciones PTT 
			    </body>
			    </html>";
	}else{

		$estado = ($v['C0_ESTADO'] == 10) ? 'Bueno' : (($v['C0_ESTADO'] == 20) ? 'Dañado' : 'desconocido');

		$reserva = $v['C0_ORIGEN'];

		$querysel2 = "SELECT * FROM SC0020 WHERE C0_NUM = '$reserva'";
		$rss2 = querys($querysel2, $tipobd_totvs, $conexion_totvs);
		$v2 = ver_result($rss2, $tipobd_totvs); 
		//ECHO $querysel2;
		//echo "<br>";

		$stock = $v2['C0_QUANT'];
		$cantidad = $v['C0_CANTIDAD'];

/* 		echo "<br>";
		echo $stock;
		echo "<br>";
		echo $cantidad; */

		$querysel3 = "SELECT * FROM SC0020 WHERE C0_RESANT = '$reserva'";
		$rss3 = querys($querysel3, $tipobd_totvs, $conexion_totvs);
		$v3 = ver_result($rss3, $tipobd_totvs); 
/* 		ECHO $querysel3;
		echo "<br>"; */

		$reserva_new = $v3['C0_NUM'];

		if($stock == $cantidad){
			if ($estado == 'Bueno'){
			$msg = "<html>
			<head>
			<meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
			</head>
			<body>
			<h1>Reserva Total Buena y Devuelta a Stock</h1>
			<h2>Se ha <strong>generado</strong> movimiento por parte de I+D nro <strong>'".$secuencia."'.</strong></h2>
			<strong>-. De :</strong> Reserva ".$v['C0_ORIGEN']."<br>
			<strong>-. NParte :</strong> ".$v['C0_COD']."<br> 
			<strong>-. Descripción :</strong> ".$v['C0_DESCR']."<br> 
			<strong>-. Estado :</strong> ".$estado."<br> 
			<strong>-. Cantidad :</strong> ".$cantidad."<br>

			Atentamente.-
			<br>Notificaciones PTT 
			</body>
			</html>";
			}
			else{
				$msg = "<html>
				<head>
				<meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
				</head>
				<body>
				<h1>Reserva Totalmente Dañada</h1>
				<h2>Se ha <strong>generado</strong> movimiento por parte de I+D nro <strong>'".$secuencia."'.</strong></h2>
				<strong>-. De :</strong> Reserva ".$v['C0_ORIGEN']."<br>
				<strong>-. NParte :</strong> ".$v['C0_COD']."<br> 
				<strong>-. Descripción :</strong> ".$v['C0_DESCR']."<br> 
				<strong>-. Estado :</strong> ".$estado."<br> 
				<strong>-. Cantidad :</strong> ".$cantidad."<br>
				<strong>-. Nueva Reserva :</strong> ".$reserva_new."<br>
	
				Atentamente.-
				<br>Notificaciones PTT 
				</body>
				</html>";
			}
		}else{

			if ($estado == 'Bueno'){

				$querysel4 = "SELECT * FROM SC0020 WHERE C0_RESANT = '$reserva' AND D_E_L_E_T_ = '*'";
				$rss4 = querys($querysel4, $tipobd_totvs, $conexion_totvs);
				$v4 = ver_result($rss4, $tipobd_totvs); 

				$reserva_devuelta = $v4['C0_NUM'];

				$querysel5 = "SELECT * FROM SC0020 WHERE C0_RESANT = '$reserva' AND D_E_L_E_T_ <> '*'";
				$rss5 = querys($querysel5, $tipobd_totvs, $conexion_totvs);
				$v5 = ver_result($rss5, $tipobd_totvs); 

				$reserva_pendiente = $v5['C0_NUM'];

				$msg = "<html>
				<head>
				<meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
				</head>
				<body>
				<h1>Reserva Parcialmente Buena</h1>
				<h2>Se ha <strong>generado</strong> movimiento por parte de I+D nro <strong>'".$secuencia."'.</strong></h2>
				<strong>-. De :</strong> Reserva ".$v['C0_ORIGEN']."<br>
				<strong>-. NParte :</strong> ".$v['C0_COD']."<br> 
				<strong>-. Descripción :</strong> ".$v['C0_DESCR']."<br> 
				<strong>-. Estado :</strong> ".$estado."<br> 
				<strong>-. Cantidad :</strong> ".$cantidad."<br>
				<strong>-. Reserva con Cantidad Devuelta :</strong> ".$reserva_devuelta."<br> 
				<strong>-. Reserva con Cantidad Pendiente :</strong> ".$reserva_pendiente."<br>
	
				Atentamente.-
				<br>Notificaciones PTT 
				</body>
				</html>";
				}
				else{

				$querysel6 = "SELECT * FROM SC0020 WHERE C0_RESANT = '$reserva' AND D_E_L_E_T_ <> '*' AND C0_CLASIF = 'INVESTIGACION'";
				$rss6 = querys($querysel6, $tipobd_totvs, $conexion_totvs);
				$v6 = ver_result($rss6, $tipobd_totvs); 

				$reserva_pendiente = $v6['C0_NUM'];

				$querysel7 = "SELECT * FROM SC0020 WHERE C0_RESANT = '$reserva' AND D_E_L_E_T_ <> '*' AND C0_CLASIF <> 'INVESTIGACION'";
				$rss7 = querys($querysel7, $tipobd_totvs, $conexion_totvs);
				$v7 = ver_result($rss7, $tipobd_totvs); 

				$reserva_mala = $v7['C0_NUM'];
				
				$msg = "<html>
				<head>
				<meta http-equiv='Content-Type' content='text/html' charset='utf-8'/>
				</head>
				<body>
				<h1>Reserva Parcialmente Dañada</h1>
				<h2>Se ha <strong>generado</strong> movimiento por parte de I+D nro <strong>'".$secuencia."'.</strong></h2>
				<strong>-. De :</strong> Reserva ".$v['C0_ORIGEN']."<br>
				<strong>-. NParte :</strong> ".$v['C0_COD']."<br> 
				<strong>-. Descripción :</strong> ".$v['C0_DESCR']."<br> 
				<strong>-. Estado :</strong> ".$estado."<br> 
				<strong>-. Cantidad :</strong> ".$cantidad."<br>
				<strong>-. Reserva con Cantidad Dañada :</strong> ".$reserva_mala."<br> 
				<strong>-. Reserva con Cantidad Pendiente :</strong> ".$reserva_pendiente."<br>
		
				Atentamente.-
				<br>Notificaciones PTT 
				</body>
				</html>";
				}
			}
}
    

    return $msg;
}

?>