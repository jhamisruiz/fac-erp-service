<?php

namespace Mnt\mantenedores\Sucursal\Domain\Repository;

use App\Utils\Utils;
use Mnt\mantenedores\Sucursal\Persistence\SucursalPersistence;
use Mnt\mantenedores\Sucursal\Domain\Response\SucursalResponse;

class SucursalRepository
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
        $data = SucursalPersistence::Buscar($start, $length, $search, $order);

        $rs = new SucursalResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = SucursalPersistence::Listar($start, $length, $search, $order);

        $rs = new SucursalResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = SucursalPersistence::Crear($body);

        //$rs = new SucursalResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = SucursalPersistence::BuscarPorId($id);

        $rs = new SucursalResponse($this->service);

        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = SucursalPersistence::Actualizar($id, $body);

        $res = Utils::responseParamsUpdate($data, $id);

        return  $res;
    }

    public function Eliminar($id)
    {
        $res = SucursalPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return SucursalPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = SucursalPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
