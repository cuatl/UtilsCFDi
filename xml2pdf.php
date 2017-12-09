<?php
   setlocale(LC_ALL,'es_MX.UTF-8');
   require_once(__DIR__."/procesaXML.php");
   require_once(__DIR__."/fpdf181/fpdf.php");
   class PDF extends FPDF {
      // Cabecera de página
      function Header() {
         $this->SetFont('Helvetica','B',14);
         /*
         $this->SetDrawColor(0,0,0);
         $this->SetLineWidth(.05);
         */
         $d = $GLOBALS['data'];
         $this->SetLineWidth(.05);
         if(isset($d->serie) && !empty($d->serie)) {
            $factura = utf8_decode($d->serie.$d->folio);
            $ln = $this->GetStringWidth($factura);
            $this->Cell((193-$ln),6,"FACTURA",0,0,'R');
            $this->setTextColor(255,0,0);
            $this->Cell(null,6,$factura,0,1,'R');
         } else {
            $this->Cell(0,6,"FACTURA",null,1,'R');
         } 
         $this->setY(10);
         $this->Image("siglo.png",null,null,60);
         // Line break
         $this->Ln(1);
         //emite
         $ancho=60;
         $this->setTextColor(0,0,0);
         $this->SetFont('Helvetica','B',8);
         //primera fila
         $this->Cell($ancho,5,strtoupper(utf8_decode($d->emisor->nombre)),0,0,null,0);
         $this->SetFont('Helvetica','',7);
         $this->Cell($ancho-5);
         $this->Cell(20,5,utf8_decode('Folio fiscal:'),'B',0,'R');
         $this->SetFont('Helvetica','B',8);
         $this->SetTextColor(153,0,0);
         $this->Cell($ancho,5,strtoupper($d->id),'B',0,'L');
         $this->ln();
         //segunda fila
         $this->SetFont('Helvetica','',7);
         $this->SetTextColor(0,0,0);
         $this->Cell(10,5,"RFC",0,0,null,0);
         $this->SetFont('Helvetica','B',8);
         $this->Cell($ancho-5,5,utf8_decode($d->emisor->rfc),0,0,null,0);
         $this->SetFont('Helvetica','',7);
         $this->Cell($ancho-10);
         $this->Cell(25,5,utf8_decode('No. de serie del CSD:'),'B',0,'R');
         $this->Cell($ancho-5,5,$d->nocertifica,'B',0,'L');
         $this->ln();
         //tercera fila
         $this->Cell($ancho,5,utf8_decode("Av. Matamoros 1056 Pte"),0,0,null,0);
         $this->Cell($ancho-5);
         $this->Cell(25,5,utf8_decode('Fecha/hora emisión:'),'B',0,'R');
         $this->Cell($ancho-5,5,utf8_decode($d->fecha),'B',0,null,0);
         $this->ln();
         //cuarta fila
         $this->Cell($ancho,5,utf8_decode("Col. Centro, CP. 27000"),0,0,null,0);
         $this->Cell($ancho-5);
         $this->Cell(25,5,utf8_decode('Lugar de expedición:'),'B',0,'R');
         $this->Cell($ancho-5,5,utf8_decode($d->lugar),'B',0,null,0);
         $this->ln();
         //quinta línea
         $this->Cell($ancho,5,utf8_decode("Torreón, Coahuila, México"),0,0,null,0);
         $this->Cell($ancho-5);
         $this->Cell(25,5,utf8_decode('Fecha/hora timbrado:'),'B',0,'R');
         $this->Cell($ancho-5,5,utf8_decode($d->fechatimbrado),'B',0,null,0);
         $this->ln();
         //sexta línea
         $this->Cell($ancho,5,utf8_decode("Régimen fiscal ").$d->emisor->regimen,0,0,null,0);
         $this->Cell($ancho-5);
         $this->Cell(25,5,utf8_decode('Efecto de comprobante:'),'B',0,'R');
         $this->Cell($ancho-5,5,utf8_decode($d->tipo),'B',0,null,0);
         $this->ln(8);
         //receptor
         $this->SetFillColor(247,247,249);
         $this->SetDrawColor(80,96,119);
         $this->SetFont('Helvetica','',7);
         $this->Cell(null,5,utf8_decode("RECEPTOR DEL COMPROBANTE FISCAL"),'T',0,'C',1);
         $this->ln();
         $this->SetFont('Helvetica','B',9);
         $this->Cell($ancho,7,utf8_decode("RFC: ").$d->receptor->rfc,'B',0,null,0);
         $this->Cell($ancho,7,utf8_decode($d->receptor->nombre),'B',0,null,0);
         $this->Cell(null,7,utf8_decode("Uso de CFDI: ".$d->receptor->usocfdi),'B',0,null,0);
         $this->ln();
      }
      // Pie de página
      function footer() {
         $this->SetY(-15);
         $this->SetDrawColor(176,28,34);
         $this->SetLineWidth(.05);
         //$this->SetTextColor(190,190,190);
         $this->SetFont('Helvetica','',6);
         $this->Cell(30,8,utf8_decode('ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI'),'T',0);
         $this->SetTextColor(0,0,0);
         $this->SetFont('Helvetica','',7);
         $this->Cell(0,8,'https://www.elsiglodetorreon.com.mx','T',0,'C');
         $this->Cell(0,8,utf8_decode('Página ').$this->PageNo().' de {nb}',0,0,'R');
      }
   }
   //procesamos XML
   $xml= file_get_contents("OO-94525.xml");
   $cfdi = new cfdi();
   $cfdi->xml = $xml;
   $data = $cfdi->run();
   //print_r($data); die();
   // Creación del objeto de la clase heredada
   $pdf = new PDF("P","mm","Letter");
   $pdf->AliasNbPages();
   $pdf->AddPage();
   //
   //
   // body
   $pdf->ln();
   $pdf->SetFillColor(247,247,249);
   $pdf->SetDrawColor(80,96,119);
   $pdf->SetFont('Helvetica','',7);
   $pdf->Cell(null,5,utf8_decode("CONCEPTOS"),'T',1,'C',1);
   $a=4;
   //cabecera conceptos
   $pdf->SetFont('Helvetica','B',6);
   $pdf->Cell(16,$a,utf8_decode('Clave producto'),'B',0,'C',1);
   $pdf->Cell(15,$a,utf8_decode('Cantidad'),'B',0,'R',1);
   $pdf->Cell(15,$a,utf8_decode('Clave Unidad'),'B',0,'R',1);
   $pdf->Cell(17,$a,utf8_decode('Unidad'),'B',0,'R',1);
   $pdf->Cell(17,$a,utf8_decode('Valor unitario'),'B',0,'R',1);
   $pdf->Cell(17,$a,utf8_decode('Importe'),'B',0,'R',1);
   $pdf->Cell(null,$a,utf8_decode('Descripción'),'B',0,'C',1);
   $pdf->ln();
   $pdf->SetFont('Helvetica','',6);
   foreach($data->conceptos AS $k) {
      $pdf->Cell(16,$a,utf8_decode($k->clave),"R",0);
      $pdf->Cell(15,$a,number_format($k->cantidad,2),"R",0,'R');
      $pdf->Cell(15,$a,utf8_decode($k->unidad),"R",0,'R');
      $pdf->Cell(17,$a,utf8_decode($k->lunidad),"R",0,'R');
      $pdf->Cell(17,$a,"$".number_format($k->valorunitario,2),"R",0,'R');
      $pdf->Cell(17,$a,"$".number_format($k->valorunitario,2),"R",0,'R');
      $pdf->multicell(100,$a,utf8_decode($k->descripcion),0);
      if(isset($k->trasladados) && !empty($k->trasladados)) {
         foreach($k->trasladados AS $imp) {
            $pdf->Cell(40,5,utf8_decode("IMPUESTO TRASLADADO"),0,0,'R',1);
            $pdf->Cell(30,5,utf8_decode("Base: $".number_format($imp->base,2)),0,0,null,1);
            $pdf->Cell(30,5,utf8_decode("Impuesto: ".$imp->impuesto),0,0,null,1);
            $pdf->Cell(30,5,utf8_decode("Tipo o factor: ".$imp->factor),0,0,null,1);
            $pdf->Cell(30,5,utf8_decode("Tasa o cuota: ".$imp->tasacuota),0,0,null,1);
            $pdf->Cell(null,5,utf8_decode("Importe: ".number_format($imp->importe,2)),0,0,null,1);
            $pdf->ln();
         }
      }
      $pdf->ln();
   }
   /* [1] => stdClass Object
   (
      [impuesto] => 002
      [importe] => 2097.56
      [base] => 13109.76
      [factor] => Tasa
      [tasacuota] => 0.160000
   )
   */
   $pdf->ln();
   //
   // end body
   //
   $pdf->Output();
