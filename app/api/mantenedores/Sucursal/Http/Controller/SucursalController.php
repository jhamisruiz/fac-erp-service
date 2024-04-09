<?php

namespace Mnt\mantenedores\Sucursal\Http\Controller;

use Mnt\mantenedores\Sucursal\Domain\Models\SucursalModels;
use Mnt\mantenedores\Sucursal\Domain\Repository\SucursalRepository;
use App\Utils\Service\NewController;

class SucursalController
{
    public function Buscar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new SucursalModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new SucursalRepository();
            $data = $repo->Buscar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new SucursalModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new SucursalRepository();
            $data = $repo->Listar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new SucursalModels($request, $response, $service);
            $sv->validateParamsCrear();

            $body = $sv->modelRequestBody();

            $repo = new SucursalRepository();
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
            // example request, args
            // $user=$app->lazyUser;
            // $service->validateParam('param_name1', 'Please enter a valid username s')->isLen(4, 6)->isChars('a-zA-Z0-9-');
            // $service->validateParam('param_name2')->notNull();
            $id = $request->param('id');

            $repo = new SucursalRepository();
            $res = $repo->BuscarPorId($id);

            return $res;
        });
    }

    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new SucursalModels($request, $response, $service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $sv->modelRequestBody();

            $repo = new SucursalRepository();
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

            $repo = new SucursalRepository();
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

            $repo = new SucursalRepository();
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

            $repo = new SucursalRepository();
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
            $id_empresa = $request->param('idempresa');
            $repo = new SucursalRepository();
            return $repo->Codigo($codigo, $id_empresa);
        });
    }

    public function SucursalEmpresa()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new SucursalModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');
            $id = $request->param('cod');
            $id_doc = $request->param('doc');

            $repo = new SucursalRepository();
            $data = $repo->SucursalEmpresa($start, $length, $search, $order, $id, $id_doc);

            return  $data;
        });
    }
}
