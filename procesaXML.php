<?php
   /*
   * procesa datos de un XML, devuelve un objeto.
   * @ToRo 2017-12-07 https://tar.mx/tema/facturacion-electronica.html
   * soporta CFDi 3.3
   */
   require_once(__DIR__."/cats.php");
   class cfdi {
      public $xml;
      private $cat = [];
      public function parse() {
      }
      public function __construct() {
         @$xml= func_get_arg(0);
         if(!empty($xml)) $this->xml = $xml;
         $this->cat = $GLOBALS['CAT'];
      }
      public function run() {
         $datos = new stdclass;
         if(empty($this->xml)) {
            $datos->error = "debe utilizar el método xml(contenido archivo cfdi)";
            return $datos;
         }
         libxml_use_internal_errors(true);
         $xml=new DOMDocument();
         $ok = $xml->loadXML($this->xml);
         if(empty($ok)) {
            $datos->error = "no se puede leer el XML";
            return $datos;
         }
         $root = $xml->getElementsByTagName('Comprobante')->item(0);
         $datos->id = null;
         $datos->version = $ver = ($root->getAttribute('Version') != null) ? $root->getAttribute('Version') : $root->getAttribute('version');
         // cabeceras
         foreach($this->cat->cabeza["3.3"] AS $tag=>$x) {
            $datos->{$tag} = (string) $root->getAttribute($this->cat->cabeza[$ver][$tag]);
         }
         // emisor
         $emite = $root->getElementsByTagName('Emisor')->item(0);
         $datos->emisor = new stdclass;
         foreach($this->cat->emisor["3.3"] AS $tag=>$x) {
            $datos->emisor->{$tag} = (string) $emite->getAttribute($this->cat->emisor[$ver][$tag]);
         }
         // receptor
         $recibe= $root->getElementsByTagName('Receptor')->item(0);
         $datos->receptor = new stdclass;
         foreach($this->cat->receptor["3.3"] AS $tag=>$x) {
            $datos->receptor->{$tag} = (string) $recibe->getAttribute($this->cat->receptor[$ver][$tag]);
         }
         // impuestos
         /*
         $impuesto = new stdclass;
         $impuesto->trasladados = 
         $impuesto->retenidos = [];
         $imp= $root->getElementsByTagName('Impuestos');
         foreach(['Traslado' => 'trasladados','Retencion' => 'retenidos'] AS $x=>$y) {
            $traslado = $imp->item(0)->getElementsByTagName($x); $i = 0;
            foreach($traslado as $t) {
               $i++; $impuesto->{$y}[$i] = new stdclass;
               foreach($this->cat->impuestos["3.3"] AS $a=>$b) {
                  if(isset($this->cat->impuestos[$ver][$a])) $impuesto->{$y}[$i]->{$a} = $t->getAttribute($this->cat->impuestos[$ver][$a]);
               }
            }
         }
         $datos->impuestos = $impuesto;
         */
         // conceptos
         $losconceptos=[];
         $concepto= $root->getElementsByTagName('Concepto');
         $i=0;
         foreach($concepto AS $t) {
            $i++; 
            $losconceptos[$i] = new stdclass;
            foreach($this->cat->conceptos["3.3"] AS $a=>$b) {
               if(isset($this->cat->conceptos[$ver][$a])) {
                  $losconceptos[$i]->{$a} = $t->getAttribute($this->cat->conceptos[$ver][$a]);
               }
            }
            foreach(['Traslado' => 'trasladados','Retencion' => 'retenidos'] AS $x=>$y) {
               $traslado = $concepto->item(0)->getElementsByTagName($x); 
               $ii = 0;
               foreach($traslado as $t) {
                  $ii++; 
                  $losconceptos[$i]->{$y}[$ii] = new stdclass;
                  foreach($this->cat->ci["3.3"] AS $a=>$b) {
                     if(isset($this->cat->ci[$ver][$a])) {
                        $losconceptos[$i]->{$y}[$ii]->{$a} = $t->getAttribute($this->cat->ci[$ver][$a]);
                     }
                  }
               }
            }
         }
         $datos->conceptos = $losconceptos;
         //impuestos
         /*
         $imp= $root->getElementsByTagName('Impuestos');
         $impuestos = new stdclass;
         foreach($imp AS $t) {
            $i=0;
            foreach(["TotalImpuestosRetenidos","TotalImpuestosTrasladados"]AS $a) {
               $impuestos->{$a} = $t->getAttribute($a);
               foreach(['Traslado' => 'trasladados','Retencion' => 'retenidos'] AS $x=>$y) {
                  $traslado = $imp->item(0)->getElementsByTagName($x); 
                  $ii = 0;
                  foreach($traslado as $t) {
                     $ii++; 
                     $impuestos->{$y}[$ii] = new stdclass;
                     foreach($this->cat->ci["3.3"] AS $a=>$b) {
                        if(isset($this->cat->ci[$ver][$a])) {
                           $impuestos->{$y}[$ii]->{$a} = $t->getAttribute($this->cat->ci[$ver][$a]);
                        }
                     }
                  }
               }
            }
         }
         */
         return $datos;
      }
   }
   /* ejemplo */
   if(isset($argv[1]) && isset($argv[1]) == 'TRUE') {
      $xml= file_get_contents("OO-94525.xml");
      $cfdi = new cfdi();
      $cfdi->xml = $xml;
      $data = $cfdi->run();
      print_r($data);
   }
