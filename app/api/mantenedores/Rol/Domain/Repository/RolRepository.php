<?php

namespace Mnt\mantenedores\Rol\Domain\Repository;

use Mnt\mantenedores\Rol\Domain\Response\RolResponse;
use Mnt\mantenedores\Rol\Persistence\RolPersistence;
use App\Utils\Utils;

class RolRepository
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
        $data = RolPersistence::Buscar($start, $length, $search, $order);

        $rs = new RolResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = RolPersistence::Listar($start, $length, $search, $order);

        $rs = new RolResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        $res = RolPersistence::Crear($body);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = RolPersistence::BuscarPorId($id);

        $rs = new RolResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function BuscarDetallePorId($id)
    {
        $res = RolPersistence::BuscarDetallePorId($id);

        $rs = new RolResponse($this->service);
        $data = $rs->DetalleResponse($res);

        return  $data;
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = RolPersistence::Actualizar($id, $body);

        //$rs = new RolResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = RolPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return RolPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = RolPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
