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
         $this->Ln(3);
         //emite
         $ancho=105;
         $this->setTextColor(0,0,0);
         $this->SetFont('Helvetica','B',10);
         //primera fila
         $this->Cell($ancho,5,strtoupper(utf8_decode($d->emisor->nombre)),0,0,null,0);
         $this->SetFont('Helvetica','',7);
         $this->Cell(30,5,utf8_decode('Folio fiscal:'),'B',0,'R');
         $this->SetFont('Helvetica','B',8);
         $this->SetTextColor(153,0,0);
         $this->Cell(null,5,$d->id,'B',0,'L');
         $this->ln();
         //segunda fila
         $this->SetFont('Helvetica','',7);
         $this->SetTextColor(0,0,0);
         $this->Cell(10,5,"RFC",0,0,null,0);
         $this->SetFont('Helvetica','B',8);
         $this->Cell($ancho-10,5,utf8_decode($d->emisor->rfc),0,0,null,0);
         $this->SetFont('Helvetica','',7);
         $this->Cell(30,5,utf8_decode('No. de serie del CSD:'),'B',0,'R');
         $this->Cell(null,5,$d->nocertifica,'B',0,'L');
         $this->ln();
         //tercera fila
         $this->Cell($ancho,5,utf8_decode("Av. Matamoros 1056 Pte"),0,0,null,0);
         $this->Cell(30,5,utf8_decode('Fecha/hora emisión:'),'B',0,'R');
         $this->Cell(null,5,utf8_decode($d->fecha),'B',0,null,0);
         $this->ln();
         //cuarta fila
         $this->Cell($ancho,5,utf8_decode("Col. Centro, CP. 27000"),0,0,null,0);
         $this->Cell(30,5,utf8_decode('Lugar de expedición:'),'B',0,'R');
         $this->Cell(null,5,utf8_decode($d->lugar),'B',0,null,0);
         $this->ln();
         //quinta línea
         $this->Cell($ancho,5,utf8_decode("Torreón, Coahuila, México"),0,0,null,0);
         $this->Cell(30,5,utf8_decode('Fecha/hora timbrado:'),'B',0,'R');
         $this->Cell(null,5,utf8_decode($d->fechatimbrado),'B',0,null,0);
         $this->ln();
         //sexta línea
         $this->Cell($ancho,5,utf8_decode("Régimen fiscal ").$d->emisor->regimen,0,0,null,0);
         $this->Cell(30,5,utf8_decode('Efecto de comprobante:'),null,0,'R');
         $this->Cell(null,5,utf8_decode($d->tipo),null,0,null,0);
         $this->ln(8);
         //receptor
         $this->SetFillColor(255,218,218);
         //$this->SetDrawColor(80,96,119);
         $this->SetDrawColor(191,84,84);
         $this->SetFont('Helvetica','',7);
         $this->Cell(null,5,utf8_decode("RECEPTOR DEL COMPROBANTE FISCAL"),'T',0,'C',1);
         $this->ln();
         $this->SetFont('Helvetica','B',9);
         $this->Cell(50,7,utf8_decode("RFC: ").$d->receptor->rfc,'B',0,null,1);
         $this->Cell(110,7,utf8_decode($d->receptor->nombre),'B',0,null,1);
         $this->Cell(null,7,utf8_decode("Uso de CFDI: ".$d->receptor->usocfdi),'B',0,null,1);
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
   function qr($uuid, $rfcemisor, $rfcreceptor, $total, $sello) {
      $url="https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?";
      $cadena = sprintf("%s&id=%s&re=%s&rr=%s&tt=%s&fe=%s",$url,$uuid,$rfcemisor,$rfcreceptor,$total,substr($sello,-8));
      $cmd = "qrencode -o /tmp/".escapeshellcmd($uuid).".png -l M \"$cadena\"";
      $cmd = `$cmd`;
      return ["/tmp/".escapeshellcmd($uuid).".png",$cadena];
   }
   //procesamos XML
   $tmp = (isset($_GET['id'])&&!empty($_GET['id'])) ? escapeshellcmd($_GET['id']) : "WW-9434";
   $xml= file_get_contents($tmp.".xml");
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
   $pdf->SetFillColor(239,249,255);
   $pdf->SetDrawColor(80,96,119);
   $pdf->SetFont('Helvetica','',7);
   $pdf->Cell(null,5,utf8_decode("C O N C E P T O S"),'B',1,'C',0);
   $a=5;
   //cabecera conceptos
   $pdf->SetFont('Helvetica','B',6);
   $pdf->Cell(18,$a,utf8_decode('Clave producto'),'B',0,'C',1);
   $pdf->Cell(15,$a,utf8_decode('Cantidad'),'B',0,'R',1);
   $pdf->Cell(15,$a,utf8_decode('Clave Unidad'),'B',0,'R',1);
   $pdf->Cell(17,$a,utf8_decode('Unidad'),'B',0,'R',1);
   $pdf->Cell(17,$a,utf8_decode('Valor unitario'),'B',0,'R',1);
   $pdf->Cell(17,$a,utf8_decode('Importe'),'B',0,'R',1);
   $pdf->Cell(null,$a,utf8_decode('Descripción'),'B',0,'C',1);
   $pdf->ln();
   $impuestos=0;
   $pdf->SetFillColor(247,247,249);
   foreach($data->conceptos AS $k) {
      $pdf->SetFont('Helvetica','',8);
      $pdf->setTextColor(0,0,0);
      $pdf->Cell(18,$a,utf8_decode($k->clave),"R",0,'C');
      $pdf->Cell(15,$a,number_format($k->cantidad,2),"R",0,'R');
      $pdf->Cell(15,$a,utf8_decode($k->unidad),"R",0,'R');
      $pdf->Cell(17,$a,utf8_decode($k->lunidad),"R",0,'R');
      $pdf->Cell(17,$a,"$".number_format($k->valorunitario,2),"R",0,'R');
      $pdf->Cell(17,$a,"$".number_format($k->valorunitario,2),"R",0,'R');
      $pdf->multicell(100,$a,utf8_decode($k->descripcion),0);
      if(isset($k->trasladados) && !empty($k->trasladados)) {
         $pdf->SetFont('Helvetica','',6);
         $pdf->setTextColor(102,102,102);
         foreach($k->trasladados AS $imp) {
            $pdf->Cell(38,$a,utf8_decode("IMPUESTO TRASLADADO"),0,0,'R',1);
            $pdf->Cell(30,$a,utf8_decode("Base: $".number_format($imp->base,2)),0,0,null,1);
            $pdf->Cell(30,$a,utf8_decode("Impuesto: ".$imp->impuesto),0,0,null,1);
            $pdf->Cell(30,$a,utf8_decode("Tipo o factor: ".$imp->factor),0,0,null,1);
            $pdf->Cell(32,$a,utf8_decode("Tasa o cuota: ".$imp->tasacuota),0,0,null,1);
            $pdf->Cell(null,$a,utf8_decode("Importe: $".number_format($imp->importe,2)),0,0,null,1);
            $impuestos += $imp->importe;
            $pdf->ln();
         }
      } else $pdf->ln();
   }
   $pdf->ln(4);
   /// moneda, totales
   //moneda , subtotal
   $pdf->setTextColor(0,0,0);
   $pdf->Cell(25,$a,'Moneda:',0,0,'R');
   $pdf->Cell(40,$a,utf8_decode($data->moneda),0,0);
   $pdf->Cell(70);
   $pdf->Cell(40,$a,utf8_decode("Subtotal:"),0,0,'R');
   $pdf->Cell(null,$a,utf8_decode("$".number_format($data->subtotal,2)),'B',0,'R');
   $pdf->ln();
   //forma de pago, impuestos
   $pdf->Cell(25,$a,'Forma de pago:',0,0,'R');
   $pdf->Cell(40,$a,utf8_decode($data->formapago),0,0);
   if(isset($data->conceptos[1]->trasladados[1])) {
      $pdf->Cell(70);
      $pdf->Cell(null,$a,utf8_decode("Impuestos trasladados"),0,0,'C',1);
   }
   $pdf->ln();
   // método de pago
   $pdf->Cell(25,$a,utf8_decode('Método de pago:'),0,0,'R');
   $pdf->Cell(40,$a,utf8_decode($data->metodopago),0,0);
   if(isset($data->conceptos[1]->trasladados[1])) {
      $pdf->Cell(70);
      $pdf->Cell(40,$a,utf8_decode("IVA ".$data->conceptos[1]->trasladados[1]->tasacuota."%:"),0,0,'R');
      $pdf->Cell(null,$a,utf8_decode("$".number_format($impuestos,2)),'B',0,'R');
   }
   $pdf->ln();
   // total
   $pdf->Cell(25+40+70);
   $pdf->Cell(40,$a,utf8_decode("TOTAL:"),0,0,'R');
   $pdf->SetFont('Helvetica','B',8);
   $pdf->Cell(null,$a,utf8_decode("$".number_format($data->total,2)),'B',0,'R');
   $pdf->SetFont('Helvetica','',6);
   $pdf->ln();
   // letra
   $pdf->SetFont('Helvetica','B',7);
   $pdf->cell(null,$a,utf8_decode("Total con letra: "),0,1);
   $pdf->SetFont('Helvetica','',6);
   $pdf->multicell(null,2.5,utf8_decode("Total con letra"),0);
   $pdf->SetFont('Helvetica','B',7);
   $pdf->cell(null,$a,utf8_decode("Sello digital del CFDI: "),0,1);
   $pdf->SetFont('Helvetica','',6);
   $pdf->multicell(null,2.5,utf8_decode($data->sello),0);
   $pdf->SetFont('Helvetica','B',7);
   $pdf->cell(null,$a,utf8_decode("Sello digital del SAT: "),0,1);
   $pdf->SetFont('Helvetica','',6);
   $pdf->multicell(null,2.5,utf8_decode($data->sellosat),0);
   $pdf->ln();
   // QR
   $qr = qr($data->id, $data->emisor->rfc, $data->receptor->rfc, $data->total, $data->sello);
   $pdf->Cell(40,$a, $pdf->Image($qr[0],$pdf->GetX(), $pdf->GetY()-1, 40),0,0,0,false);
   $pdf->SetFont('Helvetica','B',7);
   $pdf->Cell(40,$a, utf8_decode("Cadena Original del complemento de certificación digital del SAT"),0,1);
   $pdf->SetFont('Helvetica','',6);
   $pdf->Cell(40);
   $data->cadena = sprintf("||%s|%s|%s|%s|%s|%s||",$data->tfdversion,$data->id,$data->fechatimbrado,$data->tfdrfc,$data->sello,$data->tfdnosat);
   $pdf->multicell(null,2.5,utf8_decode($data->cadena),0);
   //
   $pdf->ln();
   $pdf->SetFont('Helvetica','',5);
   $pdf->setTextColor(160,160,160);
   $pdf->cell(null,3,$qr[1],0,0,'C');
   //
   // end body
   //
   $pdf->Output();
