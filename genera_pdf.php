<?php
require('fpdf17/fpdf.php');
require('Pruebas.php');
require_once "config.php";
//AddPage(orientacion[PORTRAIT - LANDSCAPE], tamaño[A3 - A4 - A5 - LETTER - LEGAL])
//SetFont(tipo[COURIER - HELVETICA - ARIAL - TIMES - SYMBOL], estilo[normal - B - I - U], tamaño)
//Cell(ancho, alto, 'texto', bordes, ¿posicion?, alineacion, rellenar, link1)
//OutPut(destino[I, D, F, S], nombre del archivo, utf8)
//Image(ruta, posicionX, posicionY, alto, ancho, tipo, link)
//RoundedRect(x, y, ancho, altura, radio esquinas, estilo[F, D (default value), FD or DF.])
class PDF extends FPDF{
    
    //ENCABEZADO PARA CADA PAGINA DE LA ORDEN DE COMPRA
    function header(){
        $this->Image('img/monarch_esencial.png', 0, -20, 70, 60,'png');
        $this->SetDrawColor(0, 145, 255);
        $this->Line(75,20,180,20);

        global $title;
        $this->Ln(5);
        $this->SetFont('Arial','B',8);
        $w = $this->GetStringWidth($title)+6;
        $this->setY(5);

        $this->setX(80);
        $this->Cell($w,5,$title,0,0,'',false);
        $this->SetFont('Arial','B',8);
        $this->Cell(50,5,'RUT: 90.991.000-5',0,1,'',false);
        $this->setX(80);
        $this->Cell(50,5,utf8_decode('DIRECCIÓN: Marathon 2239, Macul'),0,1,'',false);
        $this->setX(80);

        $this->Cell(50,5,utf8_decode('TELEFONO: (56-2)24789101'),0,1,'',false);
        $this->Ln(10);
    }

    //CUERPO DE ORDEN DE COMPRA - DATOA GENERALES DE PROVEEDOR 
    function OC_cuerpo($numOC){
        global $sol, $observ, $tipo, $tipoDesc;
        $modelo = new OC_CONSULTAS();

        $cuerpos = $modelo->Oc_encabezado($numOC);
        //RECTANGULO CON BORDES REDONDEADOS   
        $this->SetFillColor(238, 238, 238);
        $this->RoundedRect(135, 30, 65, 27, 3.5, 'DF');     
        $this->SetTextColor(0, 96, 170);
        $this->SetFont('Arial','',14);
        $this->Ln(2);

        $this->SetX(135);
        $this->Cell(65,5,utf8_decode("ORDEN DE COMPRA"),0,1,'C',false);
        $this->Ln(3);

        //CONTENIDO DE RECTANGULO CON BORDES REDONDEADOS
        foreach($cuerpos as $cuerpo){
            $this->SetTextColor(0,0,0);
            $this->SetX(140);
            $this->SetDrawColor(0, 145, 255);

            $this->SetFont('Arial','B',10);
            $this->Cell(25,5,utf8_decode("Número"),0,0,'L',false);
            $this->SetFont('Arial','',10);

            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetX(168);

            $this->Cell(10,5,$numOC,0,1,'L',false);
            $this->Ln(1);

            $this->SetFont('Arial','B',10);
            $this->SetX(140);

            $this->Cell(25,5,utf8_decode("Fecha"),0,0,'L',false);

            $this->SetFont('Arial','',10);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetX(168);

            $this->Cell(20,5, formatDate($cuerpo["FECHA"]),0,0,'L',false);
            $this->Ln(6);       
        }

        //DATOS DE PROVEEDOR
        foreach($cuerpos as $cuerpo){            
            $this->SetY(30);
            $this->SetFont('Arial','',10);
            $this->SetFillColor(238, 238, 238);
            $this->Cell(115,5,"Proveedor",0,1,'L',true);
            $this->Ln(2);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("Nombre"),0,0,'L',false);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->MultiCell(90,5, utf8_decode($cuerpo["PROVEEDOR"]),0,'L',false);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("RUT"),0,0,'L',false);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->Cell(90,5,formatRut($cuerpo["RUT"]),0,1,'L',false);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("Dirección"),0,0,'L',false);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->MultiCell(90,5,utf8_decode($cuerpo["DIRECCION"]),0,'L',false);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("Telefono"),0,0,'L',false);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->Cell(90,5,$cuerpo["CONTACTO"],0,1,'L',false);
            $this->Ln(5);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("Fecha Despacho "),0,0,'L',false);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->Cell(90,5, formatDate($cuerpo["FECHADESPACHO"]),0,1,'L',false);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("Cond. de Pago"),0,0,'L',false);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->Cell(90,5,utf8_decode($cuerpo["CONDIPAGO"]),0,1,'L',false);

            $this->SetFont('Arial','B',10);
            $this->Cell(30,5,utf8_decode("Solicitada por"),0,0,'L',false);
            $this->SetFont('Arial','',10);
            $this->Cell(2,5,":",0,0,'L',false);
            $this->Cell(90,5,utf8_decode($cuerpo["SOLICITANTE"]),0,1,'L',false);
            $sol = utf8_decode($cuerpo["SOLICITANTE"]);           
            $observ = utf8_decode($cuerpo["OBSERVAC"]);
            $tipo = $cuerpo["TIPO"];        //TIPO DE DOCUMENTO [       1               -    2   ]
            $tipoDesc = $cuerpo["TIPODESC"];//TIPO DE DOCUMENTO [BOLETA DE HONORARIOS   - FACTURA]
        }
        
    }

    //DETALLE DE ORDEN DE COMPRA
    //LLENADO DE TABLA
    function OC_detalle($numOC){
        global $observ, $tipo;
        $modelo = new OC_CONSULTAS();//LLAMADA DE CLASE DE CONSULTAS DE PRUEBAS.PHP

        $this->Ln(10);
        $this->SetTextColor(255,255,255);
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(0, 96, 170);
        $this->Cell(5, 5, '#', 1, 0, 'C', true);
        $this->Cell(31, 5, 'COD PROD', 1, 0, 'C', true);
        $this->Cell(91, 5, 'PRODUCTO', 1, 0, 'C', true);
        $this->Cell(11, 5, 'CANT', 1, 0, 'C', true);
        $this->Cell(15, 5, 'P.UNI', 1, 0, 'C', true);
        $this->Cell(14, 5, 'DSCTO', 1, 0, 'C', true);
        $this->Cell(23, 5, 'SUB-TOTAL', 1, 0, 'C', true);

        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(248, 250, 255);
        $this->SetTextColor(0,0,0);
        $this->Ln();
        $tablas = $modelo->OC_detalle($numOC);
        $i=1;
        $SUMTOTALNETO =0.0;//SUMA TOTAL DE VALORES NETOS INDIVIDUALES (POR CADA ARTICULO DE LA ORDEN DE COMPRA)
        $DESCUENTOT = 0.0;//SUMA DE DESCUENTOS INDIVIDUALES (POR CADA ARTICULO DE LA ORDEN DE COMPRA)
        
        $TTOTAL =0.0;//SUMA DE TOTALES CON IVA/RETENCIONES INCLUIDAS
        foreach($tablas as $tabla){
            $y2 = $this->GetY();
            $w2 = $this->GetStringWidth($tabla["PRODUCTO"]);
            //echo "resultado----".$w2."----resultado";
            $w3 = $w2/70;//POR CADA 70 CARACTERES EN LA CELDA LA ALTURA DE ESTA AUMENTA EN 6
            if($w3 < 1){//CALCULO DE ALTURA DE CELDA
                $w3 = 6;
            }else{
                $w3 = $w3*6;
            }
            
            $this->SetFont('Arial', '', 7);
            $this->setY($y2);
            $this->setX(10);
            $this->MultiCell(5, $w3, $i, 1, 'C', true);
            $this->setY($y2);

            $this->SetFont('Arial', '', 9);
            $this->setX(15);
            $this->MultiCell(31, $w3, utf8_decode($tabla["CODIGO"]), 1, 'L', true);
            $this->setY($y2);

            $this->setX(46);
            $this->MultiCell(91, 6, utf8_decode($tabla["PRODUCTO"]), 1, 'L', true);
            $y = $this->GetY();

            $this->setY($y2);
            $this->setX(137);
            $this->SetFont('Arial', '', 10);
            
            $this->MultiCell(11, $w3, $tabla["CANTIDAD"], 1, 'R', true);
            $this->setY($y2);
            $this->setX(148);

            $this->MultiCell(15, $w3, number_format($tabla["UNITARIO"],'0','.','.'), 1, 'R', true);
            $this->setY($y2);
            $this->setX(163);

            $this->MultiCell(14, $w3, $tabla["DECUENTO"], 1, 'R', true);
            $this->setY($y2);
            $this->setX(177);

            $this->MultiCell(23, $w3, number_format($tabla["SUBTOTAL"],'0','.','.'), 1, 'R', true);
            $this->Ln(0);
            $i++;
            $SUMTOTALNETO = $SUMTOTALNETO + ((int)$tabla["SUBTOTAL"]);
            $DESCUENTOT += ( (((int)$tabla["UNITARIO"])*((int)$tabla["CANTIDAD"]))- ((int)$tabla["SUBTOTAL"]) );
        } 

        if($tipo == 1){//CALCULO DE RETENCION Y TOTAL PARA CASO EN EL QUE EL DOCUMETO ES UNA BOLETA DE HONORARIOS
            $IVATOTAL = $SUMTOTALNETO*0.13;//RETENCION DE 13%
            $TTOTAL = $SUMTOTALNETO/0.87;

        }else if($tipo == 2){//CALCULO DE IVA Y TOTAL PARA CASO EN EL QUE EL DOCUMETO ES UNA FACTURA
            $IVATOTAL = $SUMTOTALNETO*0.19;
            $TTOTAL = $IVATOTAL + $SUMTOTALNETO;
        }

        if($i<8){// SI LA ORDEN DE COMPRA TIENE MENOS DE 8 FILAS DE DETALLE SE COMPLETA CON FILAS VACIAS HASTA TENER UN TOTAL DE 8
            for($g = $i; $g<9; $g++){
                $this->Cell(5, 6, "", 1, 0, 'R', true);
                $this->Cell(31, 6, "", 1, 0, 'L', true);
                $this->Cell(91, 6, "", 1, 0, 'L', true);
                $this->Cell(11, 6, "", 1, 0, 'R', true);
                $this->Cell(15, 6, "", 1, 0, 'R', true);
                $this->Cell(14, 6, "", 1, 0, 'R', true);
                $this->Cell(23, 6, "", 1, 0, 'R', true);
                $this->Ln();
            }
        }

        $y = $this->GetY();
        $this->SetFillColor(226, 242, 255);
        //$this->SetFillColor(190,190,190);
        $this->SetFont('Arial', 'B', 10);    
        $this->MultiCell(127, 6,"OBSERVACIONES: $observ", 1, 'L', true);

        $this->setY($y);
        $this->setX(137);

        $this->Cell(40, 6, "VALOR NETO $", 1, 0, 'L', true);
        $this->Cell(23, 6, number_format($SUMTOTALNETO,'0','.','.'), 1, 0, 'R', true);
        $this->Ln();


        $this->setX(137);
        $this->Cell(40, 6, "DESCUENTO $", 1, 0, 'L', true);
        $this->Cell(23, 6, number_format($DESCUENTOT,'0','.','.'), 1, 0, 'R', true);
        $this->Ln();

        if($tipo == 1){//CASO EN EL QUE EL DOCUMETO ES UNA BOLETA DE HONORARIOS
            $this->setX(137);
            $this->Cell(40, 6, utf8_decode("RETENCIÓN $"), 1, 0, 'L', true);
            $this->Cell(23, 6, number_format($IVATOTAL,'0','.','.'), 1, 0, 'R', true);
            $this->Ln();
        }else if($tipo == 2){//CASO EN EL QUE EL DOCUMETO ES UNA FACTURA
            $this->setX(137);
            $this->Cell(40, 6, "IVA 19% $", 1, 0, 'L', true);
            $this->Cell(23, 6, number_format($IVATOTAL,'0','.','.'), 1, 0, 'R', true);
            $this->Ln();
        }
        

        $this->setX(137);
        $this->Cell(40, 6, "VALOR TOTAL $", 1, 0, 'L', true);
        $this->Cell(23, 6, number_format($TTOTAL,'0','.','.'), 1, 0, 'R', true);
        $this->Ln();
    }

    //PIE DE PAGINA DE ORDEN DE COMPRA
    //INCLUYE LOGOS DE EMPRESAS Y SECCION PARA FIRMA DE SOLICITANTE Y APROBADOR
    function Footer(){
        global $sol;
        $modelo = new OC_CONSULTAS();
        $solis = $modelo->OC_solicitante($sol);
        $this->SetDrawColor(0, 145, 255);

        foreach($solis as $soli){
            $this->SetY(-36);
            $this->SetX(15);
            $this->Cell(75,5,utf8_decode($soli["CARGO"]),0,0,'C',false);
            $this->Ln(4);
            $this->SetX(15);
            $this->Cell(75,5,utf8_decode($soli["NOMBRE"]),0,0,'C',false);
        }
        $this->SetY(-36);
        $this->SetX(120);
        $this->Cell(75,5,utf8_decode("CARGO AUTORIZADOR"),0,0,'C',false);
        $this->Ln(4);
        $this->SetX(120);
        $this->Cell(75,5,utf8_decode("NOMBRE AUTORIZADOR"),0,0,'C',false);

        $this->Line(15,260,90,260);
        $this->Line(120,260,195,260);
        //$this->Line(105,0,105,297); //LINEA CENTRAL (PAGINA DIVIDIDA EN 2 VERTICALMENTE)

        //LOGOS EN PIE DE PAGINA
        $this->Image('img/dicotex-logo.png' , 15, 275, 50, 15,'png');
        $this->Image('img/voila-logo.png'   , 80, 275, 50, 15,'png');
        $this->Image('img/minsa-logo.png'   , 145, 275, 50, 15,'png');
    }


    function Imprimir_doc($num)
    {
        $this->SetCreator("Informática Monarch", true);
        $this->AddPage('PORTRAIT','A4');
        $this->OC_cuerpo($num);
        $this->OC_detalle($num);        
    }

}

$pdf = new PDF();
$title = 'INDUSTRIA TEXTIL MONARCH S.A.';

$numOrd = $_GET['num'];
$pdf-> SetAutoPageBreak(true,40);

$pdf->Imprimir_doc($numOrd);
$nombre = "Orden de compra N° ".$numOrd.".pdf";
$pdf->SetMargins(10, 10); 
$pdf->Output($nombre,'I');


?>