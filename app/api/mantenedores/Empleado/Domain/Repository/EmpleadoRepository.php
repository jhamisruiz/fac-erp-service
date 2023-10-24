<?php

namespace Mnt\mantenedores\Empleado\Domain\Repository;

use App\Utils\Utils;
use Mnt\mantenedores\Empleado\Persistence\EmpleadoPersistence;
use Mnt\mantenedores\Empleado\Domain\Response\EmpleadoResponse;

class EmpleadoRepository
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

    public function Listar($start, $length, $search, $order)
    {
        $data = EmpleadoPersistence::Listar($start, $length, $search, $order);

        $rs = new EmpleadoResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = EmpleadoPersistence::Crear($body);

        //$rs = new EmpleadoResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = EmpleadoPersistence::BuscarPorId($id);
        $rs = new EmpleadoResponse($this->service);
        $data = $rs->ListaResponse($res);
        return $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $res = EmpleadoPersistence::Actualizar($id, $body);

        $data = Utils::responseParamsUpdate($res, $id);
        return  $data;
    }

    public function Eliminar($id)
    {
        $res = EmpleadoPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return EmpleadoPersistence::HabilitarDeshabilitar($id, $status);
    }
}
