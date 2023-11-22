<?php
namespace Mnt\facturacion\Boleta\Domain\Repository;

use Mnt\facturacion\Boleta\Domain\Response\BoletaResponse;
use Mnt\facturacion\Boleta\Persistence\BoletaPersistence;
use App\Utils\Utils;

class BoletaRepository
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
        $data = BoletaPersistence::Buscar($start, $length, $search, $order);

        $rs = new BoletaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = BoletaPersistence::Listar($start, $length, $search, $order);

        $rs = new BoletaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = BoletaPersistence::Crear($body);

        //$rs = new BoletaResponse($this->service);
        
        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = BoletaPersistence::BuscarPorId($id);
    
        $rs = new BoletaResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = BoletaPersistence::Actualizar($id, $body);
        
        //$rs = new BoletaResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = BoletaPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return BoletaPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = BoletaPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }

}
