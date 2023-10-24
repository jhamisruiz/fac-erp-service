<?php

namespace Mnt\mantenedores\Empresa\Domain\Repository;

use App\Utils\Utils;
use Mnt\mantenedores\Empresa\Persistence\EmpresaPersistence;
use Mnt\mantenedores\Empresa\Domain\Response\EmpresaResponse;

class EmpresaRepository
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
        $data = EmpresaPersistence::Buscar($start, $length, $search, $order);

        $rsp = new EmpresaResponse($this->service);
        $response = $rsp->ListaResponse($data);

        return  $response;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = EmpresaPersistence::Listar($start, $length, $search, $order);

        $rs = new EmpresaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = EmpresaPersistence::Crear($body);

        //$rs = new EmpresaResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = EmpresaPersistence::BuscarPorId($id);

        $rs = new EmpresaResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $res = EmpresaPersistence::Actualizar($id, $body);

        $data = Utils::responseParamsUpdate($res, $id);
        return  $data;
    }

    public function Eliminar($id)
    {
        $res = EmpresaPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return EmpresaPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = EmpresaPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
