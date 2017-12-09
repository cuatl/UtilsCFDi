<?php
   require_once(__DIR__."/procesaXML.php");
   require_once(__DIR__."/fpdf181/fpdf.php");
   class PDF extends FPDF {
      // Cabecera de página
      function Header() {
         $this->SetFont('Helvetica','B',15);
         $this->SetDrawColor(0,0,0);
         $this->SetLineWidth(.05);
         $this->Cell(0,6,'FACTURA','B',0,'R');
         $this->setY(10);
         // $this->Image("logos.png",null,null,80);
         // Line break
         $this->Ln(5);

      }
      // Pie de página
      function footer() {
         $this->SetY(-15);
         $this->SetDrawColor(176,28,34);
         $this->SetLineWidth(.05);
         $this->SetTextColor(190,190,190);
         $this->SetFont('Helvetica','',6);
         $this->Cell(30,8,utf8_decode('ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI'),'T',0);
         $this->SetTextColor(0,0,0);
         $this->SetFont('Helvetica','',7);
         $this->Cell(0,8,'https://www.elsiglodetorreon.com.mx','T',0,'C');
         $this->Cell(0,8,utf8_decode('Página ').$this->PageNo().' de {nb}',0,0,'R');
      }
   }
   //procesamos XML
   $xml= file_get_contents("test2.xml");
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
   $pdf->SetFont('Helvetica','B',9);
   $pdf->setTextColor(0,0,0);
   //$pdf->SetFillColor(70,0,255);
   $pdf->SetFillColor(176,28,34);
   $ancho = 70;
   $pdf->Cell($ancho,7,"NOMBRE CIA",0,0,null,0);
   $pdf->Cell(4);
   $pdf->setTextColor(255,255,255);
   $pdf->Cell(0,7,utf8_decode('Factura tal'),0,0,'C',1);
   $pdf->ln();

   $pdf->setTextColor(0,0,0);
   $pdf->SetFont('Helvetica','',8);
   $pdf->Cell($ancho,5,utf8_decode('RFC cia'),0,0,null,0);
   $pdf->Cell(4);
   $pdf->Cell(32,5,utf8_decode('Fecha:'),'B',0,'R');
   $pdf->Cell(34,5,utf8_decode(strftime("%d-%b-%Y")),'B',0,'L');
   $pdf->ln();
   //
   //
   $pdf->Output();
