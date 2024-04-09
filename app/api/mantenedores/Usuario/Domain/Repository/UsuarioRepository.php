<?php

namespace Mnt\mantenedores\Usuario\Domain\Repository;

use App\Utils\Utils;
use Mnt\mantenedores\Usuario\Persistence\UsuarioPersistence;
use Mnt\mantenedores\Usuario\Domain\Response\UsuarioResponse;

class UsuarioRepository
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
        $data = UsuarioPersistence::Listar($start, $length, $search, $order);

        $rs = new UsuarioResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = UsuarioPersistence::Crear($body);

        //$rs = new UsuarioResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = UsuarioPersistence::BuscarPorId($id);

        $rs = new UsuarioResponse($this->service);
        $data = $rs->ListaResponse($res);
        return $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $res = UsuarioPersistence::Actualizar($id, $body);

        $data = Utils::responseParamsUpdate($res, $id);

        return  $data;
    }

    public function Eliminar($id)
    {
        $res = UsuarioPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return UsuarioPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function UsuarioEmpresa($body)
    {
        // validators
        $res = UsuarioPersistence::UsuarioEmpresa($body);

        //$rs = new UsuarioResponse($this->service);
        if ($res === 1 || $res === '1' || $res === 0 || $res === '0') {
            return (object)[
                'id' => (int)$body['id'] ?? null,
                'rowCount' => $res,
                'message' => 'Datos de Empresa Actualizados.',
                'data' => $body,
            ];
        }

        return $res;
    }
}
