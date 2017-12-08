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
         return $datos;
      }
   }
   /* ejemplo */
   $xml= file_get_contents("test.xml");
   $cfdi = new cfdi();
   $cfdi->xml = $xml;
   $data = $cfdi->run();
   print_r($data);
