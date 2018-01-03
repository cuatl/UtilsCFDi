<?php
   /* catálogos 3.3 */
   $CAT = new stdclass;
   $CAT->cabeza = [
   'certificado'  => 'Certificado',
   'condiciones'  => 'CondicionesDePago',
   'descuento'    => 'Descuento',
   'fecha'        => 'Fecha',
   'folio'        => 'Folio',
   'forma'        => 'FormaPago',
   'lugar'        => 'LugarExpedicion',
   'metodo'       => 'MetodoPago',
   'moneda'       => 'Moneda',
   'nocertifica'  => 'NoCertificado',
   'sello'        => 'Sello',
   'serie'        => 'Serie',
   'subtotal'     => 'SubTotal',
   'tipo'         => 'TipoDeComprobante',
   'total'        => 'Total',
   'version'      => 'Version',
   ];
   $CAT->emisor = [
   'nombre'       => 'Nombre',
   'regimen'      => 'RegimenFiscal',
   'rfc'          => 'Rfc',
   ];
   $CAT->receptor = [
   'nombre'       => 'Nombre',
   'usocfdi'      => 'UsoCFDI',
   'rfc'          => 'Rfc',
   ];
   $CAT->concepto = [
   'cantidad'     => 'Cantidad',
   'claveproducto'=> 'ClaveProdServ',
   'claveunidad'  => 'ClaveUnidad',
   'noidentifica' => 'NoIdentificacion',
   'unidad'       => 'Unidad',
   'valorunitario'=> 'ValorUnitario',
   'descuento'    => 'Descuento',
   'descripcion'  => 'Descripcion',
   'importe'      => 'Importe',
   ];
$CAT->impuestoTraslado = [
   'base'         => 'Base',
   'importe'      => 'Importe',
   'impuesto'     => 'Impuesto',
   'tasaocuota'   => 'TasaOCuota',
   'tipofactor'   => 'TipoFactor',
];
$CAT->terceros = [
   'version'      => 'version',
   'nombre'       => 'nombre',
   'rfc'          => 'rfc',
];
$CAT->complemento = [
   'fecha'        => 'FechaTimbrado',
   'nocertifica'  => 'NoCertificadoSAT',
   'rfc'          => 'RfcProvCertif',
   'sellocfd'     => 'SelloCFD',
   'sellosat'     => 'SelloSAT',
   'version'      => 'Version',
   'uuid'         => 'UUID',
];
$CAT->imptipos = [
   'traslado'     => 'Impuestos trasladados',
   'retencion'    => 'Impuestos retenidos',
];
$CAT->impuestos = [
   'traslado'     => 'TotalImpuestosTrasladados',
   'retencion'    => 'TotalImpuestosRetenidos',
];
$CAT->impuestosd = [
   'traslado'     => 'Trasladados',
   'retencion'    => 'Retenidos',
];
$CAT->general = [
   'I'              => 'Ingreso',
   'E'              => 'Egreso',

   'MXN'            => 'Peso mexicano',
   'PUE'            => 'Pago en una sola exhibición',
];
