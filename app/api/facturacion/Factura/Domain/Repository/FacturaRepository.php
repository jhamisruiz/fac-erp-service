<?php
namespace Mnt\facturacion\Factura\Domain\Repository;

use Mnt\facturacion\Factura\Domain\Response\FacturaResponse;
use Mnt\facturacion\Factura\Persistence\FacturaPersistence;
use App\Utils\Utils;

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

    public function Listar($start, $length, $search, $order)
    {
        $data = FacturaPersistence::Listar($start, $length, $search, $order);

        $rs = new FacturaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = FacturaPersistence::Crear($body);

        //$rs = new FacturaResponse($this->service);
        
        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = FacturaPersistence::BuscarPorId($id);
    
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
