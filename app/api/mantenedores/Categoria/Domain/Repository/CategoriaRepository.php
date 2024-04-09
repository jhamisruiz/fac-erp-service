<?php

namespace Mnt\mantenedores\Categoria\Domain\Repository;

use Mnt\mantenedores\Categoria\Domain\Response\CategoriaResponse;
use Mnt\mantenedores\Categoria\Persistence\CategoriaPersistence;
use App\Utils\Utils;

class CategoriaRepository
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
        $data = CategoriaPersistence::Buscar($start, $length, $search, $order);

        $rs = new CategoriaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order)
    {
        $data = CategoriaPersistence::Listar($start, $length, $search, $order);

        $rs = new CategoriaResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = CategoriaPersistence::Crear($body);

        //$rs = new CategoriaResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = CategoriaPersistence::BuscarPorId($id);
        if (isset($res['error']) && isset($res['code'])) {
            return  $res;
        }
        $rs = new CategoriaResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = CategoriaPersistence::Actualizar($id, $body);

        //$rs = new CategoriaResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = CategoriaPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return CategoriaPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = CategoriaPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }
}
