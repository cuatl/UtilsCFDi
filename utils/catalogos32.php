<?php
   /* catálogos 3.3 */
   $CAT = new stdclass;
   $CAT->cabeza = [
   'certificado'  => 'certificado',
   'condiciones'  => 'condicionesDePago',
   'descuento'    => 'Descuento',
   'fecha'        => 'fecha',
   'folio'        => 'folio',
   'forma'        => 'formaDePago',
   'lugar'        => 'LugarExpedicion',
   'metodo'       => 'metodoDePago',
   'nocertifica'  => 'noCertificado',
   'sello'        => 'sello',
   'serie'        => 'serie',
   'subtotal'     => 'subTotal',
   'tipo'         => 'tipoDeComprobante',
   'total'        => 'total',
   'version'      => 'version',
   ];
   $CAT->emisor = [
   'nombre'       => 'nombre',
   'regimen'      => 'RegimenFiscal',
   'rfc'          => 'rfc',
   ];
   $CAT->receptor = [
   'nombre'       => 'nombre',
   'usocfdi'      => 'UsoCFDI',
   'rfc'          => 'rfc',
   ];
   $CAT->concepto = [
   'cantidad'     => 'cantidad',
   'unidad'       => 'unidad',
   'noidentifica' => 'noIdentificacion',
   'valorunitario'=> 'valorUnitario',
   'descuento'    => 'descuento',
   'descripcion'  => 'descripcion',
   'importe'      => 'importe',
   ];
$CAT->impuestoTraslado = [
   'base'         => 'Base',
   'importe'      => 'Importe',
   'impuesto'     => 'Impuesto',
   'tasaocuota'   => 'TasaOCuota',
   'tipofactor'   => 'TipoFactor',
];
$CAT->complemento = [
   'fecha'        => 'FechaTimbrado',
   'nocertifica'  => 'noCertificadoSAT',
   'rfc'          => 'RfcProvCertif',
   'sellocfd'     => 'selloCFD',
   'sellosat'     => 'selloSAT',
   'version'      => 'version',
   'uuid'         => 'UUID',
];
$CAT->impuestos = [
   'traslado'     => 'totalImpuestosTrasladados',
   'retencion'    => 'totalImpuestosRetenidos',
];
$CAT->impuestosd = [
   'traslado'     => 'Trasladados',
   'retencion'    => 'Retenidos',
];
