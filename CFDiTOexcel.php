<?php
   /*
   * Lee información de CFDi y genera salida en csv delimitado por comas
   * (configurable), útil para generar reportes para excel.
   * @ToRo 2016-11-15 https://tar.mx/tema/facturacion-electronica.html
   * soporta CFDi 3.2 y 3.3.
   */
   // Configuración
   require_once("catalogos.php");//catálogo de conceptos CFD 3.2/3.3
   if(isset($argv[1]) && is_dir($argv[1])) $path = $argv[1];
   else $path="facturas/";       // el directorio donde se tienen los archivos XML
   if(!is_dir($path)) die("Estableza la ruta de sus XML o ejecute php ".$argv[0]." DIRECTORIO\n - ".$path." no existe\n\n");
   $archivo= "resumen.csv";      // nombre del archivo a generar
   //
   $salto = "\r\n";              // para windows usar \r\n, para mac o linux \n
   $sep  = ", ";                 // separador de campos
   if(!is_dir($path)) die("\nError: No existe el directorio ".$path.$salto);
   // 3 niveles de profundidad   // d1/d2/d3
   $pathd=dir($path); $t=0;
   while(false !== ($e0 = $pathd->read())) {
      if(preg_match("/^\./",$e0)) continue;
      elseif(preg_match("/\.xml$/i",$e0)) { $archivos["/"][] = $e0;$t++; }
      elseif(is_dir($path."/".$e0)) {
         $pathd1 = dir($path."/".$e0);
         while(false !== ($e1 = $pathd1->read())) {
            if(preg_match("/^\./",$e1)) continue;
            elseif(preg_match("/\.xml$/i",$e1)) { $archivos["/".$e0."/"][] = $e1;$t++; }
            elseif(is_dir($path."/".$e0."/".$e1)) {
               $pathd2 = dir($path."/".$e0."/".$e1);
               while(false !== ($e2 = $pathd2->read())) {
                  if(preg_match("/^\./",$e2)) continue;
                  elseif(preg_match("/\.xml$/i",$e2)) { $archivos["/".$e0."/".$e1."/"][] = $e2; $t++; }
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
   //encabezado csv
   $salida = null;
   foreach($csv AS $k=>$v) { $salida .= $k.$sep; }
   $salida .= "Concepto 1";
   $salida .= $salto;
   //
   if(!empty($archivos)) {
      $i=0;
      foreach($archivos AS $dir=>$files) {
         foreach($files AS $file) {
            $xmlfile = $path.$dir.$file;
            if(is_file($xmlfile)) {
               $xml= parse($xmlfile);
               if(!isset($xml->id)) { printf("El archivo %s no parece ser un CFDi válido\n",$xmlfile); continue; }
               $i++;
               printf("%".$no."d",$i);
               echo " ".$dir.$file." ";
               foreach($csv AS $a=>$b) {
                  if(empty($b)) continue;
                  elseif(is_array($b)) {
                     foreach($b AS $z) { $salida .= $xml->{$z}; }
                  } elseif(preg_match("/\//",$b)) {
                     $tmp = explode("/",$b);
                     $salida .= str_replace(","," ",$xml->{$tmp[0]}->{$tmp[1]});
                  } else {
                     $salida .= str_replace(","," ",$xml->{$b});
                  }
                  $salida .= $sep;
               }
               $tmp = 0; //impuestos trasladados
               if(isset($xml->impuestos->trasladados)) {
                  foreach($xml->impuestos->trasladados AS $imp) { $tmp += $imp->importe; }
                  $salida .= $tmp.$sep;
               } else $salida .= "0".$sep;
               $tmp = 0; //impuestos retenidos
               if(isset($xml->impuestos->retenidos)) {
                  foreach($xml->impuestos->retenidos AS $imp) { $tmp += $imp->importe; }
                  $salida .= $tmp.$sep;
               } else $salida .= "0".$sep;
               //vamos a tomar nadamás un concepto de referencia
               $salida .= str_replace(","," ",$xml->conceptos[1]->descripcion);
               $salida .= $salto;
               echo " ok!\n";
            }
         }
      }
   }
   if(file_put_contents($archivo,$salida)) {
      echo $salto.$salto."Se almacenó el archivo $archivo".$salto.$salto;
   } else {
      echo $salto."NO SE PUDO ALMACENAR EL ARCHIVO $archivo, verifique que pueda escribir ahí.".$salto;
   }
   /*
   * lee el cfdi y regresa un arreglo de datos.
   * v3.2 y v3.3
   */
   function parse($file) {
      global $cabeza,$emisor,$receptor,$impuestos,$conceptos;
      $datat = file_get_contents($file);
      @$xml= simplexml_load_file($file);
      @$ns = $xml->getNamespaces(true);
      @$xml->registerXPathNamespace('c', $ns['cfdi']);
      $xml->registerXPathNamespace('t', $ns['tfd']);
      $cfdi = $xml->xpath('//cfdi:Comprobante');
      $datos = new stdclass;
      $datos->id = null;
      //
      $xml2=new DOMDocument();
      $ok = $xml2->loadXML($datat);
      $root = $xml2->getElementsByTagName('Comprobante')->item(0);
      libxml_use_internal_errors(true);   // Gracias a Salim Giacoman
      $xml2 = new DOMDocument();
      $datos->version = $ver = ($root->getAttribute('Version') != null) ? $root->getAttribute('Version') : $root->getAttribute('version');
      // cabeceras
   foreach($cabeza["3.3"] AS $tag=>$x) {
      $datos->{$tag} = (string) $root->getAttribute($cabeza[$ver][$tag]);
      }
      // emisor
      $emite = $root->getElementsByTagName('Emisor')->item(0);
      $datos->emisor = new stdclass;
      foreach($emisor["3.3"] AS $tag=>$x) {
         $datos->emisor->{$tag} = (string) $emite->getAttribute($emisor[$ver][$tag]);
      }
      // emisor.regimen
      if($ver=="3.2") {
         $tmp = $root->getElementsByTagName('RegimenFiscal')->item(0);
         $datos->emisor->regimen = (string)$tmp->getAttribute('Regimen');
      }
      // receptor
      $recibe= $root->getElementsByTagName('Receptor')->item(0);
      $datos->receptor = new stdclass;
      foreach($receptor["3.3"] AS $tag=>$x) {
         $datos->receptor->{$tag} = (string) $recibe->getAttribute($receptor[$ver][$tag]);
      }
      // impuestos
      $impuesto = new stdclass;
      $impuesto->trasladados = $impuesto->retenidos = [];
      $imp= $root->getElementsByTagName('Impuestos');
      foreach(['Traslado' => 'trasladados','Retencion' => 'retenidos'] AS $x=>$y) {
         $traslado = $imp->item(0)->getElementsByTagName($x); $i = 0;
         foreach($traslado as $t) {
            $i++; $impuesto->{$y}[$i] = new stdclass;
            foreach($impuestos["3.3"] AS $a=>$b) {
               if(isset($impuestos[$ver][$a])) $impuesto->{$y}[$i]->{$a} = $t->getAttribute($impuestos[$ver][$a]);
            }
         }
      }
      $datos->impuestos = $impuesto;
      // conceptos
      $losconceptos=[];
      $concepto= $root->getElementsByTagName('Concepto');
      $i=0;
      foreach($concepto AS $t) {
         $i++; $losconceptos[$i] = new stdclass;
         foreach($conceptos["3.3"] AS $a=>$b) {
            if(isset($conceptos[$ver][$a])) $losconceptos[$i]->{$a} = $t->getAttribute($conceptos[$ver][$a]);
         }
      }
      $datos->conceptos = $losconceptos;
      $tfd = $root->getElementsByTagName('TimbreFiscalDigital')->item(0);
      $datos->id = $tfd->getAttribute('UUID');
      $datos->fechatimbrado = date('Y-m-d',strtotime($tfd->getAttribute('FechaTimbrado')));
      $datos->fecha = date('Y-m-d',strtotime($datos->fecha));
      //
      unset($tfd,$root,$imp,$tmp);
      return $datos;
   }
   //EOF v2.0
