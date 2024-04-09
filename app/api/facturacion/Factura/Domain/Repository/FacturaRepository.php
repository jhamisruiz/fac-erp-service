<?php

namespace Mnt\facturacion\Factura\Domain\Repository;

use App\Utils\Utils;
use App\Utils\Sunat\Sunat;
use App\Utils\Sunat\XmlFactura;
use Mnt\facturacion\Factura\Persistence\FacturaPersistence;
use Mnt\facturacion\Factura\Domain\Response\FacturaResponse;

class FacturaRepository
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

    public function Buscar($start, $length, $search, $order)
    {
        $data = FacturaPersistence::Buscar($start, $length, $search, $order);

        $rs = new FacturaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order, $user)
    {
        $data = FacturaPersistence::Listar($start, $length, $search, $order, $user);

        $rs = new FacturaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body, $fact)
    {
        if ($body['solo_guardar']) {
            $fact['hash'] = NULL;
            $fact['mensaje_sunat'] = NULL;
            $fact['codigo_sunat'] = NULL;
            $fact['enviar_sunat'] = '';
            $res_sunat = NULL;
        }

        if ($body['enviar_sunat']) {
            $fact['solo_guardar'] = '';

            //genera xml
            $xml = XmlFactura::Factura(
                $fact['nombre_documento'],
                $fact['emisor'],
                $fact['cliente'],
                $fact['cabecera'],
                $fact['cabecera']['cuotas'],
                $fact['detalle']
            );
            //return $fact;
            //firma xml
            $xmlsgn  = Utils::XMLSignature($fact['emisor'], $xml->file, $xml->xml_name);
            if (isset($xmlsgn->code)) {
                return $xmlsgn; //FIXME: 
            }
            //envia a sunat
            $res_sunat = Sunat::Init($xmlsgn, $fact['emisor']['numero_documento'] . '/' . $fact['cabecera']['anexo_sucursal'] . '/factura/');
            $fact['hash'] = $xmlsgn->hash_cpe;
            $fact['mensaje_sunat'] = isset($res_sunat['message']) ? "ERROR[sunat] " . $res_sunat['message'] : $res_sunat;
            $fact['codigo_sunat'] = isset($res_sunat['code']) ? $res_sunat['code'] : 0;
        }

        $res = FacturaPersistence::Crear($fact);

        //$rs = new FacturaResponse($this->service);
        //$cli = $fact->getCliente();
        //

        return [$res, $res_sunat, $fact];
    }

    public function BuscarPorId($id)
    {
        $res = FacturaPersistence::BuscarPorId($id);
        if (isset($res['error']) && isset($res['code'])) {
            return  $res;
        }

        $rs = new FacturaResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = FacturaPersistence::Actualizar($id, $body);

        //$rs = new FacturaResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = FacturaPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return FacturaPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = FacturaPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
