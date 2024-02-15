<?php
require_once "../conexion.php";
require_once "../config.php";

require_once "../fpdf185/fpdf.php";
//***********************************************************************************************
//***********************************************************************************************
function pdf(){
	global $tipobd_totvs,$conexion_totvs;
	//Titulos
$pdf=new FPDF();

//*************************************************************************************************
//*************************************************************************************************
//Datos Personales
	$pdf->AddPage();
	$id_escaneo = $_GET["escaneo_id"];
	$pdf->Image('../img/monarch_esencial.png', 0, -20, 70, 60,'png');
        // $pdf->SetDrawColor(0, 145, 255);
        // $pdf->Line(75,20,180,20);

        global $title;
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',8);
        $w = $pdf->GetStringWidth($title)+6;
        $pdf->setY(5);

        $pdf->setX(80);
        $pdf->Cell(50,5,'INDUSTRIA TEXTIL MONARCH S.A.',0,0,'',false);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5,'RUT: 90.991.000-5',0,1,'',false);
        $pdf->setX(80);
        $pdf->Cell(50,5,utf8_decode('DIRECCIÓN: Marathon 2239, Macul'),0,1,'',false);
        $pdf->setX(80);

        $pdf->Cell(50,5,utf8_decode('TELEFONO: (56-2)24789101'),0,1,'',false);
        $pdf->Ln(1);	
		
		$pdf->SetY(30);
            $pdf->SetFont('Arial','B',11);
            $pdf->SetFillColor(238, 238, 238);
            $pdf->Cell(115,5,"INFORME DE RECEPCION DE DEVOLUCIONES # $id_escaneo",0,1,'L',true);
            // $pdf->Ln();
			
		
	//$pdf->ln();
	// $pdf->Cell(80);
	// $pdf->SetFont('Arial','BU',12);
	// $pdf->Cell(30,9.'',0,0,'C');
	
	// $pdf->ln();
	$pdf->ln();


	$hoy = date('d/m/Y');
	
	$querysel ="SELECT distinct  RD_GUIA,RD_ORIGEN, RD_FECHA
				FROM Z2B_RECEP_DEVOLUCIONES 
				WHERE RD_ID='$id_escaneo'";
	$rssc = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$vc = ver_result($rssc, $tipobd_totvs);
	$guia = $vc["RD_GUIA"];
	$origen = $vc["RD_ORIGEN"];
	$fecha = formatDate($vc["RD_FECHA"]);
	$pdf->SetX(10);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(15,6,utf8_decode('Guia'),0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,6,strtoupper($guia),0,1);	
	
	$pdf->SetX(10);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(15,6,utf8_decode('Origen'),0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,6,strtoupper($origen),0,1);	
	
	$pdf->SetX(10);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(15,6,utf8_decode('Fecha'),0);
	$pdf->Cell(5,6,':',0);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(20,6,strtoupper($fecha),0,1);

	
	
	////// INICIO DEVOLUCIONES
	$pdf->SetTitle('Informe Devoluciones #'.$id_escaneo);	
	$querysel ="SELECT RD_GUIA,RD_ORIGEN,RD_CODBAR_FB,RD_COD_MCH,RD_CODBAR_MCH,RD_CODBAR_FB, SUM(RD_CANTIDAD) AS CANTIDAD ,
				RD_FECHA,RD_ULT_PR, RD_PRECIO, RD_CONV
				FROM Z2B_RECEP_DEVOLUCIONES 
				WHERE RD_ID='$id_escaneo'
				AND D_E_L_E_T_<>'*'
				GROUP BY  RD_GUIA,RD_ORIGEN,RD_CODBAR_FB,RD_COD_MCH,RD_CODBAR_MCH,RD_CODBAR_FB, RD_FECHA,RD_ULT_PR,RD_PRECIO, RD_CONV";
				//echo $querysel;
	$n=0;
	
	

			
			//$pdf->SetFont('Arial','B',8);
			//$pdf->Cell(30,5,'Fecha',0);
			//$pdf->Cell(5,5,':',0);
			//$pdf->SetFont('Arial','',8);
			//$pdf->Cell(20,5,formatDate($fec_venta),0,1);
			
			$pdf->ln(10);	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(5,7,'#',1,0,'C',true);
		//*******************************************	
			// $pdf->SetFont('Arial','B',8);
			// $pdf->cell(35,7,'GUIA',1,0,'C');
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(238, 238, 238);
			$pdf->cell(30,7,'ARTICULO MCH',1,0,'C',true);
			//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(238, 238, 238);
			$pdf->cell(30,7,'UPC',1,0,'C',true);
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(238, 238, 238);
			$pdf->cell(70,7,'DESCRIPCION',1,0,'C',true);	
			//*******************************************	
		//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,7,'CANTIDAD',1,0,'C',true);
			//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,7,'Pr.Lista',1,0,'C',true);
			//*******************************************	
			$pdf->SetFont('Arial','B',8);
			$pdf->cell(20,7,'Pr. Fact',1,0,'C',true);
			$pdf->ln();
			$i = 0;
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	while($v = ver_result($rss, $tipobd_totvs)){
			$i=$i+1;
		$guia	 		 = trim($v["RD_GUIA"]);
		$origen	 		 = trim($v["RD_ORIGEN"]);
		$barra_cli		 = trim($v["RD_CODBAR_FB"]);
		$cod_mch		 = trim(strtoupper(utf8_decode($v["RD_COD_MCH"])));
		$barra_mch		 = trim($v["RD_CODBAR_MCH"]);
		// $upc		 	= trim($v["RD_CODBAR_MCH"]);
		$cantidad		 = $v["CANTIDAD"];
		$precio_fact		 = $v["RD_ULT_PR"];
		// $precio = busca_precio($cod_mch);
		$precio = $v["RD_PRECIO"];
		$descripcion 	 = busca_descripcion_mch($cod_mch);
		$conv 	 		 = busca_conv($cod_mch);
		$prlis			= $precio*$conv;
		$prfact			= ($precio_fact)*$cantidad;
		

		
			
		
				$pdf->SetFont('Arial','',8);
				$pdf->cell(5,5,$i,1,0,'C');
			//*******************************************
				// $pdf->SetFont('Arial','',8);
				// $pdf->cell(35,5,$guia,1,0,'C');
			//*******************************************
				$pdf->SetFont('Arial','',8);
				$pdf->cell(30,5,$cod_mch,1,0,'C');
				//*******************************************
				$pdf->SetFont('Arial','',8);
				$pdf->cell(30,5,$barra_cli,1,0,'C');
			//*******************************************
				//incentivo
				$pdf->SetFont('Arial','',8);
				$pdf->cell(70,5,substr(trim($descripcion),0,40),1,0,'C');
				//*******************************************
			//*******************************************
				//incentivo
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,$cantidad,1,0,'C');	
				// *******************************************//*******************************************
				// incentivo
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,number_format($precio_fact,'0',',','.'),1,0,'C');
				//incentivo
				$pdf->SetFont('Arial','',8);
				$pdf->cell(20,5,number_format($prfact,'0',',','.'),1,1,'C');	
				// *******************************************

		

	
	}
				$pdf->SetX(10);
				$pdf->SetFont('Arial','B',9);
				$pdf->cell(135,6,'TOTAL UNIDADES',1,0,'R',true);
				$querysel3 = "SELECT  SUM(RD_CANTIDAD) AS CANTIDAD,
								   SUM(RD_ULT_PR * RD_CONV) AS PRECIO_TOTAL
								FROM Z2B_RECEP_DEVOLUCIONES 
								WHERE  RD_ID='$id_escaneo'
								AND D_E_L_E_T_<>'*'";
				$rss3 = querys($querysel3, $tipobd_totvs, $conexion_totvs);
				$v3 = ver_result($rss3, $tipobd_totvs);
					$tot_uni 		= $v3["CANTIDAD"];
					// $um2 			= $v3["UM"];
					$precio_total 	= $v3["PRECIO_TOTAL"];
					$pdf->SetX(145);
					$pdf->SetFont('Arial','B',9);
					$pdf->cell(20,6,number_format($tot_uni,'0',',','.'),1,0,'C',true);
					
					// $pdf->SetX(165);
					// $pdf->SetFont('Arial','B',9);
					// $pdf->cell(20,6,number_format($um2,'0',',','.'),1,0,'C',true)
					
					// ;$pdf->SetX(185);
					// $pdf->SetFont('Arial','B',9);
					// $pdf->cell(20,6,number_format($precio_total,'0',',','.'),1,0,'C',true);
	//$queryup = "UPDATE Z2B_TRASPASO_BODEGA SET BO_STATUS='30' WHERE BO_SECUENCIA='$id_traspaso'";
	//$rsu = db_exec($conexion2,$queryup);
	
	$pdf->ln();	$pdf->ln();
	// $pdf->Multicell(160, 10, "NOTA : Al firmar este documento todas las ordenes son resposabilidad de la persona que firma", 0, 'L', 0);
	$pdf->cell(30,7,'______________________',0,0,'L');
            $pdf->SetX(75);
			$pdf->cell(30,7,'______________________',0,1,'L');
			$pdf->SetFont('Arial','',8);
			$pdf->cell(30,7,'Emisor',0,0,'L');			
            $pdf->SetX(75);
			$pdf->cell(30,7,'Receptor',0,1,'L');


	




	
	
	$hoy = date('Ymd');
	$pdf->Output();
	$url = "./devoluciones/PDF/".$id_escaneo.".pdf";
    $pdf->Output($url,'F');
}	
function busca_precio($sku_monarch){
	global $tipobd_totvs,$conexion_totvs;
	
	
	$querysel = "SELECT DA1_PRCVEN FROM DA1010 WHERE DA1_CODTAB='009' AND  DA1_CODPRO='$sku_monarch'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);
	$precio = $v["DA1_PRCVEN"];
	
	return $precio;
	
}
function busca_conv($sku_monarch){
	global $tipobd_totvs,$conexion_totvs;
	
	$conv=0;
	$querysel = "SELECT NVL(B1_CONV,0) AS B1_CONV FROM SB1010 WHERE B1_COD='$sku_monarch'";
	// echo $querysel;
	
	$rss = querys($querysel, $tipobd_totvs, $conexion_totvs);
	$v = ver_result($rss, $tipobd_totvs);	
	$conv = $v["B1_CONV"];
	//echo $sku_monarch." - ".$conv."<br>";
	
	
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
function busca_descripcion_mch($cod_monarch){
	global $tipobd_totvs,$conexion_totvs;
	
	$codigo = "";
	$queryin = "SELECT B1_DESC FROM SB1010 WHERE B1_COD='$cod_monarch'";
	$rss = querys($queryin,$tipobd_totvs,$conexion_totvs);
	while ($row2 = ver_result($rss, $tipobd_totvs)) {                
       	$codigo = trim($row2['B1_DESC']);
    }
	if($codigo==''){
		return $codigo='9999999999999';
	}else{
		return $codigo;
	}

	cierra_conexion($tipobd_totvs, $conexion_totvs);
}



pdf();
?>
