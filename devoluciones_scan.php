<?php
//error_reporting(E_ALL);

require_once "conexion.php";
require_once "config.php";
//require_once "generar_insert.php";
require_once "./PHPMailer/PHPMailerAutoload.php";
require_once "./fpdf17/fpdf.php";
//include('WS_totvs_mch.php');
$sid=session_id();
if (!ini_get('session.auto_start') and empty($sid)) {session_start();}
//require_once "page.ext";


function lee_codigo($upc){
    global $tipobd_ptl,$conexion_ptl;
	
	$count_filas = existe_articulo_equicod($upc);
	//echo "COUNT FILAS : ".$count_filas;
	
	if($count_filas == 99){
		
		$querysel = "SELECT 99 as UPC,99 AS VALIDA FROM dual";
						//echo $querysel;
		$rss = querys($querysel, $tipobd_ptl, $conexion_ptl);
		while($v = ver_result($rss, $tipobd_ptl)){
			$cargar["CODIGO"]=array(
						"COD"			=>$v["UPC"],
						"VALIDA"		=>$v["VALIDA"]
				);
		}
		// $link = "sonido/error.wav";
		// echo $link;
	}else{
	
		$querysel = "SELECT * FROM PTL_EQUICOD WHERE EQU_UPC='$upc'";
						//echo $querysel;
		$rss = querys($querysel, $tipobd_ptl, $conexion_ptl);
		while($v = ver_result($rss, $tipobd_ptl)){
						//$nro = $v["RUT_TRABAJADOR"];
			$cargar["CODIGO"]=array(
						"EQU_UPC"			=>$v["EQU_UPC"],
						"EQU_INTCOD"		=>$v["EQU_INTCOD"],
						"EQU_BARCOD"		=>$v["EQU_BARCOD"],
						"EQU_DCANAL"		=>$v["EQU_DCANAL"],
						"EQU_RUT"			=>$v["EQU_RUT"]
				);
		}
	}
	echo json_encode($cargar);
//		    echo"<pre>";
//    print_r($cargar);
//    echo"</pre>";
}
function existe_articulo_equicod($upc){
	global $tipobd_ptl,$conexion_ptl;
	
	$filas = 0;
	$querycount = "SELECT COUNT(*) AS FILAS 
						FROM PTL_EQUICOD 
						WHERE EQU_UPC='$upc'";
	$rsc = querys($querycount, $tipobd_ptl, $conexion_ptl);
	while ($v =ver_result($rsc, $tipobd_ptl)){         
       $filas = $v['FILAS'];
    }

	if($filas==0){
		return $filas=99;
	}else{
		return $filas;
	}
}
function suma_cantidades($numero){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysum = "SELECT nvl(SUM(RD_CANTIDAD),0) as SUMA 
					FROM Z2B_RECEP_DEVOLUCIONES 
					where RD_ID='$numero'  
					and D_E_L_E_T_<>'*'";
	// echo $querysum;
	$rss = querys($querysum, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rss, $tipobd_totvs)){
	
		$nro[] = array(
		"SUMA" => $fila["SUMA"]
		);
	}
	//echo "Nro_trabajador ".$querysel;
	echo json_encode($nro);
	
}
function get_secuencia(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT TOTVS.CID_ID.NEXTVAL AS RD_ID FROM DUAL";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($fila = ver_result($rss, $tipobd_totvs)){
		$secuencia = $fila['RD_ID'];
		$secuencia = 	str_pad($secuencia,6,'0', STR_PAD_LEFT);
		$secuencia = "R".$secuencia;
		$nro[] = array(
		'ID' => $secuencia
		);
	}
	//echo "Nro_trabajador ".$querysel;
	echo json_encode($nro);
}
function get_sesion(){
	global $tipobd_ptl,$conexion_ptl;
	
	$querysel = "SELECT PTL.SALIDA_BODEGA.NEXTVAL AS SA_SESION
					FROM DUAL";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_ptl, $conexion_ptl);
	while($fila = ver_result($rss, $tipobd_ptl)){
	
		$nro[] = array(
		"SESION" => $fila["SA_SESION"]
		);
	}
	//echo "Nro_trabajador ".$querysel;
	echo json_encode($nro);
}
function getrecno_secuencia(){
	global $tipobd_totvs, $conexion_totvs;
	
	$querysel = "SELECT NVL(MAX(R_E_C_N_O_),0)+1 AS CORRELATIVO FROM Z2B_RECEP_DEVOLUCIONES";
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
function existe_codigo($numero){
	global $tipobd_ptl,$conexion_ptl;
	
	$querycount = "SELECT count(*) as FILAS 
					FROM MVE_SALIDA_BODEGA 
					where SA_CODEMV='$numero'  
					and D_E_L_E_T_<>'*'";
	$rsc = querys($querycount, $tipobd_ptl, $conexion_ptl);
	$fila = ver_result($rsc, $tipobd_ptl);
    if($fila["FILAS"]>0){
        return 1;
    }else{
        return 0;
    }
}
function generaInsert($frm){
    //global $conexion;
    
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

		insertar_salida($asig);
	}

}
function busca_precio($sku_monarch){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$querysel = "SELECT NVL(MAX(DA1_PRCVEN),0) AS DA1_PRCVEN FROM DA1010 WHERE DA1_CODTAB='E23' AND  DA1_CODPRO='$sku_monarch'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$precio = $v["DA1_PRCVEN"];
	
	return $precio;
	
}
function busca_conv($sku_monarch){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$querysel = "SELECT NVL(MAX(B1_CONV),0) AS B1_CONV FROM SB1010 WHERE B1_COD='$sku_monarch'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$conv = $v["B1_CONV"];
	
	return $conv;
	
}
function busca_descripcion($upc){
	global $tipobd_ptl, $conexion_ptl;
	
	
	$querysel = "SELECT EQU_CLIDES FROM PTL_EQUICOD WHERE EQU_UPC='$upc'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_ptl, $conexion_ptl);
	$v = ver_result($rss, $tipobd_ptl);
	$descripcion = $v["EQU_CLIDES"];
	
	return $descripcion;
	
}
function busca_ultimo_precio_facturado($rut, $codigo){
	global $tipobd_totvs, $conexion_totvs;
	
	
	$querysel = "SELECT MAX(D2_EMISSAO),NVL(MAX(D2_PRCVEN),0) as PR_ULT FROM SD2010 WHERE D2_CLIENTE ='$rut' 
					AND D2_EMISSAO BETWEEN '20221115' AND '20230210'
					AND D2_COD ='$codigo' and D2_SERIE='FEA'";
	// echo $querysel;	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss,$tipobd_totvs);
	$ultimo_precio = $v["PR_ULT"];
	
	if($ultimo_precio == 0){
		$querysel = "SELECT MAX(D2_EMISSAO),NVL(MAX(D2_PRCVEN),0) as PR_ULT FROM SD2010 WHERE D2_CLIENTE ='$rut' 
					AND D2_COD ='$codigo' and D2_SERIE='FEA'";
	// echo $querysel;
	
		$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
		$v = ver_result($rss,$tipobd_totvs);
		$ultimo_precio = $v["PR_ULT"];
		return $ultimo_precio;
		
	}else{
		return $ultimo_precio;
		
	}
	
	
}
function insertar_salida($post){
	global $tipobd_totvs, $conexion_totvs;

//	echo"<pre> externo:";
//    print_r($post);
//    echo"</pre>";
	
	
	
    //for($i=1;$i<=$recno;){
		
        $fila 				= $post["filas"];
        $secuencia			= $post["secuencia"];
        $numero  			= trim($post["upc"]);
        $codigo			    = trim($post["intcod"]);
        $barcod   			= trim($post["barcod"]);
        $origen			    = trim($post["origen"]);
        $rut			    = trim($post["rut"]);
        $cantidad   		= $post["cantidad"];
		$conversion			= busca_conv($codigo);
		$cant2um			= $cantidad*$conversion;
		$precio				= busca_precio($codigo);
		$ult_precio_uni		= busca_ultimo_precio_facturado($rut, $codigo);
		$ult_precio 		= $ult_precio_uni*$conversion; 
		$guia				= $_POST["guia"];
        $hoy         		= date('Ymd');
		$hora		 		= date('His');
		
		$existe_codigo 			= existe_codigo($codigo);
		//$existe_codigo_bodega   = existen_codigos_z2b($cambio_articulo,$destino);
	
			/**
			  * Insertar Asignacion
			  */
			$user = $_SESSION["user"];
			//echo $user;
			

				// if($existe_codigo == 0){
					// $queryin = "INSERT INTO PTL.MVE_SALIDA_BODEGA(SA_ID,SA_ORIGEN, SA_CODEMV, SA_CODEMP, SA_CANTIDAD, SA_FECHA,SA_HORA, R_E_C_N_O_, D_E_L_E_T_, SA_IDVENTA,SA_USER,SA_ESTADO) 
																// VALUES('$secuencia','$origen', '$codigo', '$codigomkp', $cantidad, '$hoy','$hora', $fila, ' ', '$numero','$user',10)";
					// querys($queryin, $tipobd_ptl, $conexion_ptl);	
					
					$queryin = "INSERT INTO TOTVS.Z2B_RECEP_DEVOLUCIONES(RD_ID, RD_ORIGEN, RD_CODBAR_FB, RD_COD_MCH, RD_CODBAR_MCH, RD_CANTIDAD, RD_FECHA, RD_HORA, R_E_C_N_O_, RD_GUIA, RD_ESTADO, RD_PRECIO, RD_CONV, RD_CANT2UM, RD_RUT, RD_ULT_PR) 
							VALUES('$secuencia', '$origen', '$numero', '$codigo', '$barcod', $cantidad, '$hoy', '$hora', $fila, '$guia', '10', $precio, $conversion, $cant2um, '$rut',$ult_precio)";
					querys($queryin, $tipobd_totvs, $conexion_totvs);
					// echo $queryin;
			

}
function confirmar_escaneo($numero){
	global $tipobd_totvs, $conexion_totvs;
	
	$nuevo_id= obtener_id();

	$queryup_1 ="UPDATE Z2B_RECEP_DEVOLUCIONES SET RD_ESTADO='20' WHERE  RD_ID='$numero'";///PREGUHTAR ESTADOS
	$rsu_1 = querys($queryup_1, $tipobd_totvs, $conexion_totvs);
	
	$queryup_1 ="UPDATE Z2B_RECEP_DEVOLUCIONES SET RD_ID='$nuevo_id' WHERE  RD_ID='$numero'";///PREGUHTAR ESTADOS
	$rsu_1 = querys($queryup_1, $tipobd_totvs, $conexion_totvs);
	
	
	
	if(oci_num_rows($rsu_1)<>0 or oci_num_rows($rsu_1)<>null){
			echo "PROCESO $nuevo_id CONFIRMADO ! <br> <br> <br> <a target='_blank' class='btn btn-danger' href='devoluciones/devoluciones_escaniadas.php?escaneo_id=$nuevo_id'><strong>Descarga Aqui Informe</a></strong> ";
		}else{
			echo "ERROR: PROCESO NO CONFIRMADO !";
		}    
}
function obtener_id(){
	global $tipobd_totvs,$conexion_totvs;
	
	$querysel = "SELECT SUBSTR(MAX(RD_ID),-6)+1 AS ID
					FROM Z2B_RECEP_DEVOLUCIONES
					where substr(RD_ID,0,1) <>'R'";
	//echo $querysel;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$fila = ver_result($rss, $tipobd_totvs);
		$secuencia = $fila['ID'];
		$secuencia = 	str_pad($secuencia,6,'0', STR_PAD_LEFT);
		
		return $secuencia;

}
function envia_correo_traspaso($to, $asunto, $msj, $adjunto) {
    // global $conexion2;

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
		$to = array('imendoza@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '02'){
		$to = array('macuna@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '04'){
		$to = array('pcarrasco@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '05' or $bodega == '15' or $bodega=='31'){
		$to = array('jadillegrez@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '11'){
		$to = array('jromani@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
	}elseif($bodega == '50'){
		$to = array('rinfante@grupomonarch.cl','gpuyol@grupomonarch.cl','dlacroix@grupomonarch.cl');
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
function eliminar_linea($recno){
	global $tipobd_totvs,$conexion_totvs;
	
	// $codigo_mv =trim($codigo_mv);
	
	$queryup ="UPDATE Z2B_RECEP_DEVOLUCIONES SET D_E_L_E_T_='*' WHERE R_E_C_N_O_='$recno'";///PREGUHTAR ESTADOS
	$rsu = querys($queryup, $tipobd_totvs, $conexion_totvs);
	//echo $queryup;
  
}
//getUsers();
/*MAIN*/
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
if(isset($_GET["lee_pedido"])){
	//tmp_nrotrabajador();
	
	$numero = $_GET['numero'];
    lee_codigo($numero);
}
if(isset($_GET["recno"])){

    getrecno_secuencia();
}
if(isset($_GET["secuencia"])){

    get_secuencia();
}
if(isset($_GET["totales"])){
	$numero 	= $_GET["numero"];//secuencia	
    suma_cantidades($numero);
}
if(isset($_GET["sesion"])){

    get_sesion();
}
if(isset($_GET["confirma_scan"])){
	
	//$articulo = $_GET["articulo"];	
	$numero 	= $_GET["numero"];//secuencia	
    confirmar_escaneo($numero);
	//ver_pedido_impreso($numero);
	//confirmacion_correo($numero);
}
if(isset($_GET["eliminar_linea"])){
	
	$recno = $_GET["recno"];	
	//$numero 	= $_GET["numero"];//secuencia	
   	
    eliminar_linea($recno);	
}
?>