<?php

namespace Mnt\mantenedores\Cliente\Domain\Repository;

use Mnt\mantenedores\Cliente\Domain\Response\ClienteResponse;
use Mnt\mantenedores\Cliente\Persistence\ClientePersistence;
use App\Utils\Utils;

class ClienteRepository
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
        $data = ClientePersistence::Buscar($start, $length, $search, $order);

        $rs = new ClienteResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = ClientePersistence::Listar($start, $length, $search, $order);

        $rs = new ClienteResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = ClientePersistence::Crear($body);

        //$rs = new ClienteResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = ClientePersistence::BuscarPorId($id);
        if (isset($res['error']) && isset($res['code'])) {
            return  $res;
        }

        $rs = new ClienteResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = ClientePersistence::Actualizar($id, $body);

        //$rs = new ClienteResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = ClientePersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return ClientePersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = ClientePersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }

    public function BuscarPorDoc($doc)
    {
        $res = ClientePersistence::BuscarPorDoc($doc);

        $rs = new ClienteResponse($this->service);
        $data = $rs->ListaResponse($res);

        if (isset($data[0]['id'])) {
            return  (object)$data[0];
        }
        if ($data === 0 || $data == '0') {
            return 0;
        }
        return $data;
    }
}
