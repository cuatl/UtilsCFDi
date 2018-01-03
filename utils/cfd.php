<?php
   /* utilidades cfd https://tar.mx/conta/ */
   class cfdi {
      private $xmlo; //text
      private $xml;  //objeto XML
      private $c;    //catálogos
      private $ver;  //versión CFDi
      private $impuestos=0;
      public function __construct() {
         $this->xmlo = func_get_arg(0);
         libxml_use_internal_errors(true);
         if(empty($this->xmlo)) { die("debe especificar un archivo XML\n"); }
         try {
            $this->xml = simplexml_load_string(str_replace("terceros:","",str_replace("tfd:","",str_replace("cfdi:","",$this->xmlo))));
         } catch(Exception $e) {
            die("Error ".$e->getMessage()."\n");
         }
         $this->c = $GLOBALS['CAT'];
         if(empty($this->c)) die("Faltan catálogos de esqueleto XML\n");
      }
      public function run() {
         //print_r($this->xml);
         $data = new stdclass;
         $data->cabeza = new stdclass;   //cabeza
         foreach($this->xml->attributes() AS $k=>$v) {
            if(array_search($k,$this->c->cabeza)) {
               $key = array_search($k,$this->c->cabeza);
               $data->cabeza->{$key} = (string)$v;
            }
         }
         //emisor, receptor
         $nodos = ['receptor'=>'Receptor','emisor'=>'Emisor'];
         foreach($nodos AS $n=>$nodo) {
            $data->{$n} = new stdclass;
            foreach($this->xml->{$nodo}->attributes() as $k=>$v) {
               if(array_search($k,$this->c->{$n})) {
                  $key = array_search($k,$this->c->{$n});
                  $data->{$n}->{$key} = (string)$v;
               }
            }
         }
         //
         $data->impuestos = new stdclass;
         foreach($this->xml->Impuestos->attributes() AS $k=>$v) {
            if(array_search($k,$this->c->impuestos)) {
               $key = array_search($k,$this->c->impuestos);
               $data->impuestos->{$key} = (float)$v;
            }
         }
         //conceptos e impuestos
         $conceptos = [];
         $ii=$i=1;
         $impuesto = new stdclass;
         foreach($this->xml->Conceptos->Concepto AS $k=>$v) {
            foreach($v->attributes() AS $a=>$b) {
               if(array_search($a,$this->c->concepto)) {
                  $key = array_search($a,$this->c->concepto);
                  @$conceptos[$i]->{$key} = (string)$b;
               }
            }
            $conceptos[$i]->impuestos = new stdclass;
            if(isset($v->Impuestos->Traslados->Traslado)) {
               foreach($v->Impuestos->Traslados->Traslado as $traslado) {
                  foreach($traslado->attributes() AS $a=>$b) {
                     if(array_search($a,$this->c->impuestoTraslado)) {
                        $key = array_search($a,$this->c->impuestoTraslado);
                        @$conceptos[$i]->impuestos->traslado[$ii]->{$key} = (string)$b;
                     }
                  }
                  $ii++;
               }
            }
            if(isset($v->Impuestos->Retenciones->Retencion)) {
               foreach($v->Impuestos->Retenciones->Retencion AS $retencion) {
                  foreach($retencion->attributes() AS $a=>$b) {
                     if(array_search($a,$this->c->impuestoTraslado)) {
                        $key = array_search($a,$this->c->impuestoTraslado);
                        @$conceptos[$i]->impuestos->retencion[$ii]->{$key} = (string)$b;
                     }
                  }
               $ii++;
               }
            }
            if(isset($v->ComplementoConcepto->PorCuentadeTerceros)) {
               foreach($v->ComplementoConcepto->PorCuentadeTerceros AS $tercero) {
                  foreach($tercero->attributes() AS $a=>$b) {
                     if(array_search($a,$this->c->terceros)) {
                        $key = array_search($a,$this->c->terceros);
                        @$conceptos[$i]->terceros[$i]->{$key} = (string)$b;
                     }
                  }
               }
            }
            $i++;
         }
         $data->conceptos = $conceptos;
         $data->complemento = new stdclass;
         foreach($this->xml->Complemento->TimbreFiscalDigital->Attributes() AS $a=>$b) {
            if(array_search($a,$this->c->complemento)) {
               $key = array_search($a,$this->c->complemento);
               $data->complemento->{$key} = (string)$b;
            }
         }
         $data->id = strtoupper($data->complemento->uuid);
         //
         //print_r($data);
         return $data;
      }
   }
