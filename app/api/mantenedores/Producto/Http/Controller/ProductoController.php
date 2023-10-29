<?php

namespace Mnt\mantenedores\Producto\Http\Controller;

use Mnt\mantenedores\Producto\Domain\Models\ProductoModels;
use Mnt\mantenedores\Producto\Domain\Repository\ProductoRepository;
use App\Utils\Service\NewController;

class ProductoController
{
    public function Buscar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new ProductoModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new ProductoRepository();
            $data = $repo->Buscar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new ProductoModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new ProductoRepository();
            $data = $repo->Listar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new ProductoModels($request, $response, $service);
            $sv->validateParamsCrear();

            // example
            // $user=$app->lazyUser;
            // $service->validateParam('param_name1', 'Please enter a valid username s')->isLen(4, 6)->isChars('a-zA-Z0-9-');
            // $service->validateParam('param_name2')->notNull();
            $body = $request->body();

            $repo = new ProductoRepository();
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

            $repo = new ProductoRepository();
            $res = $repo->BuscarPorId($id);

            return $res;
        });
    }

    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new ProductoModels($request, $response, $service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $sv->modelRequestBody();

            $repo = new ProductoRepository();
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

            $repo = new ProductoRepository();
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

            $repo = new ProductoRepository();
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

            $repo = new ProductoRepository();
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

            $repo = new ProductoRepository();
            return $repo->Codigo($codigo);
        });
    }

    public function Segmentos()
    {
        $ctr = new NewController();
        return $ctr->Controller(function ($request, $response, $service, $app) {

            $repo = new ProductoRepository();
            $res = $repo->Segmentos();

            return $res;
        });
    }
    public function Familias()
    {
        $ctr = new NewController();
        return $ctr->Controller(function ($request, $response, $service, $app) {
            $codigo = $request->param('codigo');

            $repo = new ProductoRepository();
            $res = $repo->Familias($codigo);

            return $res;
        });
    }
    public function Clases()
    {
        $ctr = new NewController();
        return $ctr->Controller(function ($request, $response, $service, $app) {
            $codigo = $request->param('codigo');

            $repo = new ProductoRepository();
            $res = $repo->Clases($codigo);

            return $res;
        });
    }

    public function Productos()
    {
        $ctr = new NewController();
        return $ctr->Controller(function ($request, $response, $service, $app) {
            // example request
            $codigo = $request->param('codigo');
            $desc = $request->param('descripcion');

            $repo = new ProductoRepository();
            $res = $repo->Productos($codigo, $desc);

            return $res;
        });
    }
}
