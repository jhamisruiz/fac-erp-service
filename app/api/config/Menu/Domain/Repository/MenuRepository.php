<?php

namespace App\config\Menu\Domain\Repository;

use App\config\Menu\Domain\Response\MenuResponse;
use App\config\Menu\Persistence\MenuPersistence;
use App\Utils\Utils;

class MenuRepository
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

    public function Buscar($iduser, $start, $length, $search, $order)
    {
        $data = MenuPersistence::Buscar($iduser, $start, $length, $search, $order);

        $rs = new MenuResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($iduser, $start, $length, $search, $order)
    {
        $data = MenuPersistence::Listar($iduser, $start, $length, $search, $order);

        $rs = new MenuResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = MenuPersistence::Crear($body);

        //$rs = new MenuResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = MenuPersistence::BuscarPorId($id);
        if (isset($res['error']) && isset($res['code'])) {
            return  $res;
        }

        $rs = new MenuResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = MenuPersistence::Actualizar($id, $body);

        //$rs = new MenuResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = MenuPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return MenuPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = MenuPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
