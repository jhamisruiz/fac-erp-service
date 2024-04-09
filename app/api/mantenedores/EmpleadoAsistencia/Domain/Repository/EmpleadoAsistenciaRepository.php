<?php

namespace Mnt\mantenedores\EmpleadoAsistencia\Domain\Repository;

use Mnt\mantenedores\EmpleadoAsistencia\Domain\Response\EmpleadoAsistenciaResponse;
use Mnt\mantenedores\EmpleadoAsistencia\Persistence\EmpleadoAsistenciaPersistence;
use App\Utils\Utils;

class EmpleadoAsistenciaRepository
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
        $data = EmpleadoAsistenciaPersistence::Buscar($start, $length, $search, $order);

        $rs = new EmpleadoAsistenciaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = EmpleadoAsistenciaPersistence::Listar($start, $length, $search, $order);

        $rs = new EmpleadoAsistenciaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = EmpleadoAsistenciaPersistence::Crear($body);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = EmpleadoAsistenciaPersistence::BuscarPorId($id);

        $rs = new EmpleadoAsistenciaResponse($this->service);
        $data = $rs->modelIDResponse($res);

        if (count($data)) {
            return $data[0];
        }

        return $data;
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = EmpleadoAsistenciaPersistence::Actualizar($id, $body);

        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = EmpleadoAsistenciaPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return EmpleadoAsistenciaPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = EmpleadoAsistenciaPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
