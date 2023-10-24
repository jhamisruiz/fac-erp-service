<?php

namespace Mnt\mantenedores\Usuario\Http\Controller;

use App\Utils\Service\NewError;
use App\Utils\Service\NewController;
use Mnt\mantenedores\Usuario\Domain\Models\UsuarioModels;
use Mnt\mantenedores\Usuario\Domain\Repository\UsuarioRepository;

class UsuarioController
{
    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new UsuarioModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new UsuarioRepository();
            $data = $repo->Listar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new UsuarioModels($request, $response, $service);
            $body = $sv->modelRequestBodyDecript();
            if ($body["password"] != $body["rep_password"]) {
                return NewError::__Log('Las contraseñas no coinciden.', 200);
            }

            $body =  $sv->password_hash($sv->modelRequestBody());

            $repo = new UsuarioRepository();
            $res = $repo->Crear($body);

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

            $repo = new UsuarioRepository();
            $res = $repo->BuscarPorId($id);

            return $res;
        });
    }

    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new UsuarioModels($request, $response, $service);

            // example request, args
            $id = $request->param('id');
            $body = $sv->modelRequestBodyDecript();

            if ($body["password"] != $body["rep_password"]) {
                return NewError::__Log('Las contraseñas no coinciden.', 200);
            }

            $data =  $sv->password_hash($body);

            $repo = new UsuarioRepository();
            $res = $repo->Actualizar($id, $data);;
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

            $repo = new UsuarioRepository();
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

            $repo = new UsuarioRepository();
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

            $repo = new UsuarioRepository();
            $res =  $repo->HabilitarDeshabilitar($id, 'false');

            return  $res;
        });
    }
}
