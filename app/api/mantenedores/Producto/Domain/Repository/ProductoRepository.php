<?php
namespace Mnt\mantenedores\Producto\Domain\Repository;

use Mnt\mantenedores\Producto\Domain\Response\ProductoResponse;
use Mnt\mantenedores\Producto\Persistence\ProductoPersistence;
use App\Utils\Utils;

class ProductoRepository
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
        $data = ProductoPersistence::Buscar($start, $length, $search, $order);

        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = ProductoPersistence::Listar($start, $length, $search, $order);

        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = ProductoPersistence::Crear($body);

        //$rs = new ProductoResponse($this->service);
        
        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = ProductoPersistence::BuscarPorId($id);
    
        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = ProductoPersistence::Actualizar($id, $body);
        
        //$rs = new ProductoResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = ProductoPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return ProductoPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = ProductoPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }

}
