<?php

namespace Mnt\mantenedores\Empleado\Http\Controller;

use Mnt\mantenedores\Empleado\Domain\Models\EmpleadoModels;
use Mnt\mantenedores\Empleado\Domain\Repository\EmpleadoRepository;
use App\Utils\Service\NewController;

class EmpleadoController
{
    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new EmpleadoModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new EmpleadoRepository();
            $data = $repo->Listar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new EmpleadoModels($request, $response, $service);
            $body = $sv->modelRequestBody();

            $repo = new EmpleadoRepository();
            $id = $repo->Crear($body);

            $res = $repo->BuscarPorId($id);

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

            $repo = new EmpleadoRepository();
            $res = $repo->BuscarPorId($id);

            return $res;
        });
    }

    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new EmpleadoModels($service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $request->body();

            $repo = new EmpleadoRepository();
            $res = $repo->Actualizar($id, $body);;

            return  $res;
        });
    }

    public function Eliminar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new EmpleadoRepository();
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

            $repo = new EmpleadoRepository();
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

            $repo = new EmpleadoRepository();
            $res =  $repo->HabilitarDeshabilitar($id, 'false');

            return  $res;
        });
    }
}
