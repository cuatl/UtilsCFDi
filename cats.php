<?php
//catálogos de estructura de archivo. 
//ver procesaXML.php
$CAT =new stdclass;
$csvimpuestos = [
   'trasladados' => [ 'impuesto', 'importe' ],
   'retenidos'   => [ 'impuesto', 'importe' ], //base, factor, tasacuota
];
//cabecera 
$CAT->cabeza = [
   '3.2' => [
      'fecha'      => 'fecha',
      'tipo'       => 'tipoDeComprobante',
      'lugar'      => 'LugarExpedicion',
      'metodopago' => 'metodoDePago',
      'subtotal'   => 'subTotal',
      'total'      => 'total',
      'moneda'     => 'moneda',
      'serie'      => 'serie',
      'folio'      => 'folio',
   ],
   '3.3'        => [
      'fecha'      => 'Fecha',
      'tipo'       => 'TipoDeComprobante',
      'lugar'      => 'LugarExpedicion',
      'metodopago' => 'MetodoPago',
      'formapago'  => 'FormaPago',
      'subtotal'   => 'SubTotal',
      'total'      => 'Total',
      'moneda'     => 'Moneda',
      'serie'      => 'Serie',
      'folio'      => 'Folio',
      'certificado'=> 'Certificado',
      'nocertifica'=> 'NoCertificado',
      'sello'      => 'Sello',
      'condiciones'=> 'CondicionesDePago',
      'version'    => 'Version',
   ],
   ];
//emisor
$CAT->emisor = [
   '3.2' => [
      'rfc'        => 'rfc',
      'nombre'     => 'nombre',
      'regimen'    => 'RegimenFiscal',
   ],
   '3.3' => [
      'rfc'        => 'Rfc',
      'nombre'     => 'Nombre',
      'regimen'    => 'RegimenFiscal',
   ]
];
//receptor
$CAT->receptor = [
   '3.2' => [
      'rfc'        => 'rfc',
      'nombre'     => 'nombre',
      'usocfdi'    => 'UsoCFDI',
   ],
   '3.3' => [
      'rfc'        => 'Rfc',
      'nombre'     => 'Nombre',
      'usocfdi'    => 'UsoCFDI',
   ]
];
//impuestos
$CAT->impuestos = [
   '3.2' => [
      'impuesto'   => 'impuesto',
      'importe'    => 'importe',
   ],
   '3.3' => [
      'impuesto'   => 'Impuesto',
      'importe'    => 'Importe',
      'base'       => 'Base',
      'factor'     => 'TipoFactor',
      'tasacuota'  => 'TasaOCuota',
   ],
];
//conceptos
$CAT->conceptos = [
   "3.2" => [
      "cantidad"   => "cantidad",
      "unidad"     => "unidad",
      "descripcion"=> "descripcion",
      "valorunitario"=> "valorUnitario",
      "importe"    => "importe",
   ],
   "3.3" => [
      "clave"      => "ClaveProdServ",
      "cantidad"   => "Cantidad",
      "unidad"     => "ClaveUnidad",
      "descripcion"=> "Descripcion",
      "valorunitario"=>"ValorUnitario",
      "importe"    => "Importe",
      "noidentifica"=>"NoIdentificacion",
      "lunidad"    => "Unidad",
   ],
];
//conceptos impuestos
$CAT->ci = [
   "3.3" => [
      'impuesto'   => 'Impuesto',
      'importe'    => 'Importe',
      'base'       => 'Base',
      'factor'     => 'TipoFactor',
      'tasacuota'  => 'TasaOCuota',
   ]
];
//EOF
