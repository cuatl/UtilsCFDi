<?php
   /*
   * lee información de CFDi y genera salida en csv delimitado por comas
   * (configurable), útil para generar reportes para excel.
   * @ToRo 2016-11-15 https://tar.mx/tema/facturacion-electronica.html
   */
   // Configuración
   if(isset($argv[1]) && is_dir($argv[1])) $path = $argv[1];
   else $path="directorio/facturas"; // el directorio donde se tienen los archivos XML
   if(!is_dir($path)) die("Estableza la ruta de sus XML o ejecute php ".$argv[0]." DIRECTORIO\n".$path." no existe\n\n");
   $archivo= "resumen.csv";      //nombre del archivo a generar
   //
   $saltolinea = "\r\n";         //para windows usar \r\n, para mac o linux \n
   $separador  = ",";            //separador de campos
   if(!is_dir($path)) die("\nError: No existe el directorio ".$path.$saltolinea);
   //
   //
   // lee 3 niveles de profundidad
   // d1/d2/d3
   //
   $pathd=dir($path);
   $t=0;
   while(false !== ($e0 = $pathd->read())) {
      if(preg_match("/^\./",$e0)) continue;
      elseif(preg_match("/\.xml/i",$e0)) { $archivos["/"][] = $e0;$t++; }
      elseif(is_dir($path."/".$e0)) {
         $pathd1 = dir($path."/".$e0);
         while(false !== ($e1 = $pathd1->read())) {
            if(preg_match("/^\./",$e1)) continue;
            elseif(preg_match("/\.xml/i",$e1)) { $archivos["/".$e0."/"][] = $e1;$t++; }
            elseif(is_dir($path."/".$e0."/".$e1)) {
               $pathd2 = dir($path."/".$e0."/".$e1);
               while(false !== ($e2 = $pathd2->read())) {
                  if(preg_match("/^\./",$e2)) continue;
                  elseif(preg_match("/\.xml/i",$e2)) { $archivos["/".$e0."/".$e1."/"][] = $e2; $t++; }
               }
               $pathd2->close();
            }
         }
         $pathd1->close();
      }
   }
   $pathd->close();
   //
   $no=strlen($t)+1;
   //encabezados
   $resumend  = "FACTURA".$separador."FOLIO FISCAL".$separador."SUBTOTAL".$separador."TOTAL".$separador."FECHA";
   $resumend .= $separador."MÉTODO PAGO";
   $resumend .= $separador."EMITE RFC".$separador."EMITE RAZÓN".$separador."RECEPTOR RFC".$separador."RECEPTOR RAZÓN";
   $resumend .= $separador."IMPUESTOS TRASLADADOS".$separador."IMPUESTOS RETENIDOS".$separador."CONCEPTOS";
   $resumend .= $saltolinea;
   if(!empty($archivos)) {
      $i=0;
      foreach($archivos AS $dir=>$files) {
         foreach($files AS $file) {
            $xmlfile = $path.$dir.$file;
            if(is_file($xmlfile)) {
               $xmldata = parseCFDi($xmlfile);
               if(!isset($xmldata['id'])) continue;
               $i++;
               printf("%".$no."d",$i);
               echo " ".$dir.$file." ";
               $resumend .= $xmldata['serie'].$xmldata['folio'].$separador.$xmldata['id'].$separador;
               $resumend .= $xmldata['subtotal'].$separador.$xmldata['total'].$separador;
               $resumend .= substr($xmldata['fecha'],0,10);
               $resumend .= $separador.str_replace(","," ",$xmldata['metodopago']);
               $resumend .= $separador.$xmldata['emisor'][0].$separador.str_replace(","," ",$xmldata['emisor'][1]);
               $resumend .= $separador.$xmldata['receptor'][0].$separador.str_replace(","," ",$xmldata['receptor'][1]);
               $resumend .= $separador.$xmldata['impuestos'][0].$separador.($xmldata['impuestos'][1]+0).$separador;
               if(!empty($xmldata['concepto'])) {
                  foreach($xmldata['concepto'] AS $con) {
                     $resumend .= str_replace(","," ",$con[4])." - ";
                  }
               }
               $resumend .= $saltolinea;
               //print_r($xmldata);
               echo " ok!\n";
            }
         }
      }
   }
   if(file_put_contents($archivo,$resumend)) {
      echo $saltolinea.$saltolinea."Se almacenó el archivo $archivo".$saltolinea.$saltolinea;
   } else {
      echo $saltolinea."NO SE PUDO ALMACENAR EL ARCHIVO $archivo, verifique que pueda escribir ahí.".$saltolinea;
   }
   /*
   * lee el cfdi y regresa un arreglo de datos.
   */
   function parseCFDi() {
      @$tmp = func_get_arg(0);
      @$xml = simplexml_load_file($tmp);
      @$ns = $xml->getNamespaces(true);
      @$xml->registerXPathNamespace('c', $ns['cfdi']);
      @$xml->registerXPathNamespace('t', $ns['tfd']);
      if(!isset($ns['cfdi'])) {
         echo "=== no pude leer $tmp como CFDi :(\n";
         return;
      }
      $cfdiComprobante = $xml->xpath('//cfdi:Comprobante');
      $datos = Array('id'=>'');
      foreach ($xml->xpath('//cfdi:Comprobante') as $cfdiComprobante) {
         $fecha = strtotime($cfdiComprobante['fecha']);
         $datos['fecha'] = date('Y-m-d H:i:s',$fecha);
         $datos['total']= (string)$cfdiComprobante['total'];
         $datos['subtotal']=(string)$cfdiComprobante['subTotal'];
         $datos['descuento']=(string)$cfdiComprobante['descuento'];
         $datos['formapago']=(string)$cfdiComprobante['formaDePago'];
         $datos['tipo']=(string)$cfdiComprobante['tipoDeComprobante'];
         $datos['expedido']=(string)$cfdiComprobante['LugarExpedicion'];
         $datos['metodopago']=(string)$cfdiComprobante['metodoDePago'];
         $datos['nocertificado']=(string)$cfdiComprobante['noCertificado'];
         $datos['serie']=(string)$cfdiComprobante['serie'];
         $datos['folio']=(string)$cfdiComprobante['folio'];
         foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor) {
            $datos['emisor'] = array((string)$Emisor['rfc'],(string)$Emisor['nombre']);
         }
         foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor){
            $datos['receptor']=array((string)$Receptor['rfc'],(string)$Receptor['nombre']);
         }
         foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos') as $Receptor){
            $datos['impuestos']=array((string)$Receptor['totalImpuestosTrasladados'],(string)$Receptor['totalImpuestosRetenidos']);
         }
         $i=0;
         foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto) {
            $i++;
            $datos['concepto'][$i] = array((string)$Concepto['unidad'], (string)$Concepto['importe'], (string)$Concepto['cantidad'], (string)$Concepto['valorUnitario'], (string)$Concepto['descripcion']);
         }
         foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto) {
            $i++;
            @$datos['traslado'][$tmp] = array((string)$Traslado['tasa'],(string)$Traslado['importe']);
         }
         foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Impuestos//cfdi:Retenciones//cfdi:Retencion') as $Traslado) {
            $tmp = (string) $Traslado['impuesto'];
            $datos['retencion'][$tmp] = (string)$Traslado['importe'];
         }
         foreach ($xml->xpath('//t:TimbreFiscalDigital') as $tfd) {
            $datos['id'] = (string)$tfd['UUID'];
            $datos['id'] = strtoupper($datos['id']);
            $datos['timbrado'] = date('Y-m-d H:i:s',strtotime($tfd['FechaTimbrado']));
         }
      }
      return $datos;
   }
