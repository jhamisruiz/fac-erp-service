<?php

namespace Mnt\mantenedores\Rol\Http\Controller;

use Mnt\mantenedores\Rol\Domain\Models\RolModels;
use Mnt\mantenedores\Rol\Domain\Repository\RolRepository;
use App\Utils\Service\NewController;

class RolController
{
    public function Buscar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new RolModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new RolRepository();
            $data = $repo->Buscar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new RolModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new RolRepository();
            $data = $repo->Listar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new RolModels($request, $response, $service);

            $body = $sv->modelRequestBody();
            $detalle = $sv->permisosCrear($body['detalle']);

            $repo = new RolRepository();
            $body['detalle'] = (array)$detalle;
            $res = $repo->Crear($body);

            if (isset($res[0]['id'])) {
                $resp = $repo->BuscarPorId($res[0]['id']);
                return $resp;
            }

            return $res;
        });
    }

    public function BuscarPorId()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {

            $id = $request->param('id');

            $repo = new RolRepository();
            $res = $repo->BuscarPorId($id);

            return $res;
        });
    }

    public function BuscarDetallePorId()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {

            $id = (int)($request->param('id'));

            $repo = new RolRepository();
            $res = $repo->BuscarDetallePorId($id);

            return $res;
        });
    }
    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new RolModels($request, $response, $service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $sv->modelRequestBody();
            $detalle = $sv->permisosCrear($body['detalle']);

            $repo = new RolRepository();
            $body['detalle'] = (array)$detalle;
            $res = $repo->Actualizar($id, $body);

            $res->data = $repo->BuscarPorId($id);
            return  $res;
        });
    }

    public function Eliminar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new RolRepository();
            $res = $repo->Eliminar($id);

            return  $res;
        });
    }

    public function Habilitar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new RolRepository();
            $res =  $repo->HabilitarDeshabilitar($id, true);

            return  $res;
        });
    }

    public function Deshabilitar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new RolRepository();
            $res =  $repo->HabilitarDeshabilitar($id, 'false');

            return  $res;
        });
    }

    public function Codigo()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validator
            // example request
            $codigo = $request->param('code');

            $repo = new RolRepository();
            return $repo->Codigo($codigo);
        });
    }
}
