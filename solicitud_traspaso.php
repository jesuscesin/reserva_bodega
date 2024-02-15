<?php
require_once "conexion.php";
require_once "config.php";

require_once "./fpdf185/fpdf.php";
//***********************************************************************************************
//***********************************************************************************************
function pdf(){
	global $tipobd_totvs,$conexion_totvs;
	//Titulos
$pdf=new FPDF();

//*************************************************************************************************
//*************************************************************************************************
//Datos Personales
	
	$id_traspaso = $_GET["id"];
	
	$hoy = date('d/m/Y');
	$pdf->SetTitle('TRASPASOS ENTRE BODEGAS '.$id_traspaso);	
	$querysel ="SELECT DISTINCT  BO_SECUENCIA,
				BO_FTRASPASO,
				(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_ORIGEN) AS BO_ORIGEN,
				(SELECT ZZ1_BODEGA||'_'||ZZ1_DESCRI FROM ZZ1010 WHERE ZZ1_FILIAL='01' AND ZZ1_BODEGA=BO_DESTINO) AS BO_DESTINO,
				NVL(BO_USUARIO,'**') AS BO_USUARIO
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
			
			
		while($v1 =ver_result($rss1, $tipobd_totvs)){
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
	$pdf->Output();
}	

pdf();
?>
