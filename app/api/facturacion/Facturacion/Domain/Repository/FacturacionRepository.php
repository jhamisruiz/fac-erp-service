<?php

namespace Mnt\facturacion\Facturacion\Domain\Repository;

use App\Utils\Utils;
use App\Utils\Sunat\Sunat;
use App\Utils\Sunat\XmlBoleta;
use App\Utils\Sunat\XmlFactura;
use App\Utils\Sunat\XmlNotaDebito;
use App\Utils\Sunat\XmlNotaCredito;
use Mnt\facturacion\Facturacion\Persistence\FacturacionPersistence;
use Mnt\facturacion\Facturacion\Domain\Response\FacturacionResponse;

class FacturacionRepository
{
    private $request;
    private $response;
    private $service;

    public function __construct($request = null, $response = null, $service = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->service = $service;
    }

    public function Factura($data)
    {
        $emisor = array(
            "tipo_documento" => 6,
            "numero_documento" => "20607599727",
            "razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
            "nombre_comercial" => "ACADEMIA DE SOFTWARE",
            "departamento" => "LAMBAYEQUE",
            "provincia" => "CHICLAYO",
            "distrito" => "CHICLAYO",
            "direccion" => "CALLE OCHO DE OCTUBRE 123",
            "ubigeo" => "140101",
            "usuario_emisor" => "MODDATOS",
            "clave_emisor" => "MODDATOS",
            "clave_certificado" => "prueba123",
        );
        $cliente = array(
            "tipo_documento" => "6",
            "numero_documento" => "20605145648",
            "razon_social" => "AGROINVERSIONES Y SERVICIOS AJINOR S.R.L. - AGROSERVIS AJINOR S.R.L.",
            "direccion" => "MZA. C LOTE. 46 URB. SAN ISIDRO LA LIBERTAD - TRUJILLO - TRUJILLO"
        );
        $cuotas = array();
        $cabecera = array(
            "tipo_operacion"  => "0101",
            "tipo_comprobante" => "01",
            "codigo_moneda"         => "PEN",
            "serie"           => "F001",
            "correlativo"     => 176,
            "total_op_gravadas" => 50.17,
            "igv"          => 9.03,
            "icbper"       => 0.50,
            "total_op_exoneradas" => 140.00,
            "total_op_inafectas" => 270.00,
            "total_antes_impuestos" => 460.17,
            "total_impuestos"    => 9.53,
            "total_despues_impuestos" => 469.70,
            "total_a_pagar"      => 469.70,
            "fecha_emision"      => "2023-02-02",
            "hora_emision"    => "19:48:00",
            "fecha_vencimiento" => "2023-02-02",
            "forma_pago"      => "Contado",
            "monto_credito"   => 0.00,
            "anexo_sucursal"  => "0000"
        );
        $items = [
            [
                "item"   => 1,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "MOCHILA",
                "valor_unitario" => 50.00,
                "precio_lista" => 59.00,
                "valor_total" => 50.00,
                "igv"  => 9.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 50.00,
                "total_impuestos" => 9.00,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ],
            [
                "item"   => 2,
                "cantidad"   => 2,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "LIBRO COQUITO",
                "valor_unitario" => 70.00,
                "precio_lista" => 70.00,
                "valor_total" => 140.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 140.00,
                "total_impuestos" => 0.00,
                "codigos" => array("E", "20", "9997", "EXO", "VAT")
            ], [
                "item"   => 3,
                "cantidad"   => 3,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "MANZANA",
                "valor_unitario" => 90.00,
                "precio_lista" => 90.00,
                "valor_total" => 270.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 270.00,
                "total_impuestos" => 0.00,
                "codigos" => array("O", "30", "9998", "INA", "FRE")
            ],
            [
                "item"   => 4,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "BOLSA PLÁSTICA",
                "valor_unitario" => 0.17,
                "precio_lista" => 0.70,
                "valor_total" => 0.17,
                "igv"  => 0.03,
                "icbper"  => 0.50,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 0.17,
                "total_impuestos" => 0.53,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ]
        ];

        //genera xml
        $xml = XmlFactura::Factura($emisor, $cliente, $cabecera, $cuotas, $items);
        //firma xml
        $xmlsgn  = Utils::XMLSignature($emisor, $xml->{'file'}, $xml->{'xml_name'});
        //envia a sunat
        $res_sunat = Sunat::Init($xmlsgn, 'factura/');
        //$data = FacturacionPersistence::Listar($body);
        $rs = new FacturacionResponse($this->service);
        $response = $rs->SenatResponse($res_sunat);

        return  $response;
    }

    public function Boleta($body)
    {
        $emisor = array(
            "tipo_documento" => 6,
            "numero_documento" => "20607599727",
            "razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
            "nombre_comercial" => "ACADEMIA DE SOFTWARE",
            "departamento" => "LAMBAYEQUE",
            "provincia" => "CHICLAYO",
            "distrito" => "CHICLAYO",
            "direccion" => "CALLE OCHO DE OCTUBRE 123",
            "ubigeo" => "140101",
            "usuario_emisor" => "MODDATOS",
            "clave_emisor" => "MODDATOS",
            "clave_certificado" => "prueba123",
        );

        $cliente = array(
            "tipo_documento" => "0",
            "numero_documento" => "00000000",
            "razon_social" => "CLIENTE VARIOS",
            "direccion" => "-"
        );

        $cabecera = array(
            "tipo_operacion"  => "0101",
            "tipo_comprobante" => "03",
            "codigo_moneda"         => "PEN",
            "serie"           => "B001",
            "correlativo"     => 444,
            "total_op_gravadas" => 50.17,
            "igv"          => 9.03,
            "icbper"       => 0.50,
            "total_op_exoneradas" => 140.00,
            "total_op_inafectas" => 270.00,
            "total_antes_impuestos" => 460.17,
            "total_impuestos"    => 9.53,
            "total_despues_impuestos" => 469.70,
            "total_a_pagar"      => 469.70,
            "fecha_emision"      => "2023-02-02",
            "hora_emision"    => "19:48:00",
            "fecha_vencimiento" => "2023-02-02",
            "forma_pago"      => "Contado",
            "monto_credito"   => 0.00,
            "anexo_sucursal"  => "0000"
        );

        $cuotas = array();
        $items = [
            [
                "item"   => 1,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "MOCHILA",
                "valor_unitario" => 50.00,
                "precio_lista" => 59.00,
                "valor_total" => 50.00,
                "igv"  => 9.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 50.00,
                "total_impuestos" => 9.00,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ],
            [
                "item"   => 2,
                "cantidad"   => 2,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "LIBRO COQUITO",
                "valor_unitario" => 70.00,
                "precio_lista" => 70.00,
                "valor_total" => 140.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 140.00,
                "total_impuestos" => 0.00,
                "codigos" => array("E", "20", "9997", "EXO", "VAT")
            ],
            [
                "item"   => 3,
                "cantidad"   => 3,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "MANZANA",
                "valor_unitario" => 90.00,
                "precio_lista" => 90.00,
                "valor_total" => 270.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 270.00,
                "total_impuestos" => 0.00,
                "codigos" => array("O", "30", "9998", "INA", "FRE")
            ],
            [
                "item"   => 4,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "BOLSA PLÁSTICA",
                "valor_unitario" => 0.17,
                "precio_lista" => 0.70,
                "valor_total" => 0.17,
                "igv"  => 0.03,
                "icbper"  => 0.50,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 0.17,
                "total_impuestos" => 0.53,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ]
        ];
        //genera xml
        $xml = XmlBoleta::Boleta($emisor, $cliente, $cabecera, $cuotas, $items);
        //firma xml
        $xmlsgn  = Utils::XMLSignature($emisor, $xml->{'file'}, $xml->{'xml_name'});
        //envia a sunat
        $res_sunat = Sunat::Init($xmlsgn, 'botela/');

        $rs = new FacturacionResponse($this->service);
        $response = $rs->SenatResponse($res_sunat);

        return  $response;
    }

    public function NotaCredito($id)
    {
        $emisor = array(
            "tipo_documento" => 6,
            "numero_documento" => "20607599727",
            "razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
            "nombre_comercial" => "ACADEMIA DE SOFTWARE",
            "departamento" => "LAMBAYEQUE",
            "provincia" => "CHICLAYO",
            "distrito" => "CHICLAYO",
            "direccion" => "CALLE OCHO DE OCTUBRE 123",
            "ubigeo" => "140101",
            "usuario_emisor" => "MODDATOS",
            "clave_emisor" => "MODDATOS",
            "clave_certificado" => "prueba123",
        );

        $cliente = array(
            "tipo_documento" => "6",
            "numero_documento" => "20605145648",
            "razon_social" => "AGROINVERSIONES Y SERVICIOS AJINOR S.R.L. - AGROSERVIS AJINOR S.R.L.",
            "direccion" => "MZA. C LOTE. 46 URB. SAN ISIDRO LA LIBERTAD - TRUJILLO - TRUJILLO"
        );
        $cabecera = array(
            "tipo_comprobante" => "07",
            "codigo_moneda"         => "PEN",
            "serie"           => "FN01",
            "correlativo"     => 234,
            "total_op_gravadas" => 50.17,
            "igv"          => 9.03,
            "icbper"       => 0.50,
            "total_op_exoneradas" => 140.00,
            "total_op_inafectas" => 270.00,
            "total_antes_impuestos" => 460.17,
            "total_impuestos"    => 9.53,
            "total_despues_impuestos" => 469.70,
            "total_a_pagar"      => 469.70,
            "fecha_emision"      => "2022-08-16",
            "hora_emision"    => "20:14:00",
            "codigo_motivo"   => "01",
            "descripcion_motivo" => "ANULACION DE LA OPERACION",
            "tipo_comp_ref"   => "01",
            "serie_comp_ref"  => "F001",
            "correlativo_comp_ref"  => 111,
            "anexo_sucursal"  => "0000"
        );
        $items = array(
            [
                "item"   => 1,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "codigo" => "M001",
                "codigo_unspsc" => "10191509",
                "nombre" => "MOCHILA",
                "valor_unitario" => 50.00,
                "precio_lista" => 59.00,
                "valor_total" => 50.00,
                "igv"  => 9.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 50.00,
                "total_impuestos" => 9.00,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ],
            [
                "item"   => 2,
                "cantidad"   => 2,
                "unidad"   => "NIU",
                "codigo" => "L001",
                "codigo_unspsc" => "10191509",
                "nombre" => "LIBRO COQUITO",
                "valor_unitario" => 70.00,
                "precio_lista" => 70.00,
                "valor_total" => 140.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 140.00,
                "total_impuestos" => 0.00,
                "codigos" => array("E", "20", "9997", "EXO", "VAT")
            ],
            [
                "item"   => 3,
                "cantidad"   => 3,
                "unidad"   => "NIU",
                "codigo" => "MZ001",
                "codigo_unspsc" => "10191509",
                "nombre" => "MANZANA",
                "valor_unitario" => 90.00,
                "precio_lista" => 90.00,
                "valor_total" => 270.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 270.00,
                "total_impuestos" => 0.00,
                "codigos" => array("E", "30", "9998", "INA", "FRE")
            ],
            [
                "item"   => 4,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "codigo" => "BP001",
                "codigo_unspsc" => "10191509",
                "nombre" => "BOLSA PLÁSTICA",
                "valor_unitario" => 0.17,
                "precio_lista" => 0.70,
                "valor_total" => 0.17,
                "igv"  => 0.03,
                "icbper"  => 0.50,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 0.17,
                "total_impuestos" => 0.53,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ]
        );

        //genera xml
        $xml = XmlNotaCredito::XMLNotaCredito($emisor, $cliente, $cabecera,  $items);
        //firma xml
        $xmlsgn  = Utils::XMLSignature($emisor, $xml->{'file'}, $xml->{'xml_name'});
        //envia a sunat
        $res_sunat = Sunat::Init($xmlsgn, 'nota_credito/');

        $rs = new FacturacionResponse($this->service);
        $response = $rs->SenatResponse($res_sunat);

        return  $response;
    }
    public function NotaDebito($id)
    {
        $emisor = array(
            "tipo_documento" => 6,
            "numero_documento" => "20607599727",
            "razon_social" => "INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.",
            "nombre_comercial" => "ACADEMIA DE SOFTWARE",
            "departamento" => "LAMBAYEQUE",
            "provincia" => "CHICLAYO",
            "distrito" => "CHICLAYO",
            "direccion" => "CALLE OCHO DE OCTUBRE 123",
            "ubigeo" => "140101",
            "usuario_emisor" => "MODDATOS",
            "clave_emisor" => "MODDATOS",
            "clave_certificado" => "prueba123",
        );

        $cliente = array(
            "tipo_documento" => "6",
            "numero_documento" => "20605145648",
            "razon_social" => "AGROINVERSIONES Y SERVICIOS AJINOR S.R.L. - AGROSERVIS AJINOR S.R.L.",
            "direccion" => "MZA. C LOTE. 46 URB. SAN ISIDRO LA LIBERTAD - TRUJILLO - TRUJILLO"
        );

        $cabecera = array(
            "tipo_comprobante" => "08",
            "codigo_moneda"         => "PEN",
            "serie"           => "FD05",
            "correlativo"     => 244,
            "total_op_gravadas" => 50.17,
            "igv"          => 9.03,
            "icbper"       => 0.50,
            "total_op_exoneradas" => 140.00,
            "total_op_inafectas" => 270.00,
            "total_antes_impuestos" => 460.17,
            "total_impuestos"    => 9.53,
            "total_despues_impuestos" => 469.70,
            "total_a_pagar"      => 469.70,
            "fecha_emision"      => "2023-08-16",
            "hora_emision"    => "21:24:00",
            "codigo_motivo"   => "02",
            "descripcion_motivo" => "AUMENTO EN EL VALOR",
            "tipo_comp_ref"   => "01",
            "serie_comp_ref"  => "F001",
            "correlativo_comp_ref"  => 345,
        );

        $items = array(
            [
                "item"   => 1,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "nombre" => "MOCHILA",
                "valor_unitario" => 50.00,
                "precio_lista" => 59.00,
                "valor_total" => 50.00,
                "igv"  => 9.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 50.00,
                "total_impuestos" => 9.00,
                "porcentaje_igv" => 18,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ],
            [
                "item"   => 2,
                "cantidad"   => 2,
                "unidad"   => "NIU",
                "nombre" => "LIBRO COQUITO",
                "valor_unitario" => 70.00,
                "precio_lista" => 70.00,
                "valor_total" => 140.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 140.00,
                "total_impuestos" => 0.00,
                "porcentaje_igv" => 0,
                "codigos" => array("E", "20", "9997", "EXO", "VAT")

            ],
            [
                "item"   => 3,
                "cantidad"   => 3,
                "unidad"   => "NIU",
                "nombre" => "MANZANA",
                "valor_unitario" => 90.00,
                "precio_lista" => 90.00,
                "valor_total" => 270.00,
                "igv"  => 0.00,
                "icbper"  => 0.00,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 270.00,
                "total_impuestos" => 0.00,
                "porcentaje_igv" => 0,
                "codigos" => array("E", "30", "9998", "INA", "FRE")
            ],
            [
                "item"   => 4,
                "cantidad"   => 1,
                "unidad"   => "NIU",
                "nombre" => "BOLSA PLÁSTICA",
                "valor_unitario" => 0.17,
                "precio_lista" => 0.70,
                "valor_total" => 0.17,
                "igv"  => 0.03,
                "icbper"  => 0.50,
                "factor_icbper"   => 0.50,
                "total_antes_impuestos" => 0.17,
                "total_impuestos" => 0.53,
                "porcentaje_igv" => 18,
                "codigos" => array("S", "10", "1000", "IGV", "VAT")
            ]
        );


        //genera xml
        $xml = XmlNotaDebito::XMLNotaDebito($emisor, $cliente, $cabecera,  $items);
        //firma xml
        $xmlsgn  = Utils::XMLSignature($emisor, $xml->{'file'}, $xml->{'xml_name'});
        //envia a sunat
        $res_sunat = Sunat::Init($xmlsgn, 'nota_debito/');

        $rs = new FacturacionResponse($this->service);
        $response = $rs->SenatResponse($res_sunat);

        return  $response;
    }

    public function GiraRemision($id, $body)
    {
        // validators
        $res = FacturacionPersistence::Actualizar($id, $body);

        //$rs = new FacturacionResponse($this->service);

        return  $res;
    }

    public function BajaSuna($id)
    {
        $res = FacturacionPersistence::Eliminar($id);

        return  $res;
    }

    public function ResumenBoletas($id, $status)
    {
        return FacturacionPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Buscar($start, $length, $search, $order)
    {
        $data = FacturacionPersistence::Buscar($start, $length, $search, $order);

        $rsp = new FacturacionResponse($this->service);
        $response = $rsp->ListaResponse($data);

        return  $response;
    }

    public function AfectacionBuscar($start, $length, $search, $order)
    {
        $data = FacturacionPersistence::AfectacionBuscar($start, $length, $search, $order);

        $rsp = new FacturacionResponse($this->service);
        $response = $rsp->ListaResponse($data);

        return  $response;
    }
}
