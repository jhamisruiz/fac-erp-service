<?php
namespace Mnt\facturacion\Boleta\Http\Controller;

use Mnt\facturacion\Boleta\Domain\Models\BoletaModels;
use Mnt\facturacion\Boleta\Domain\Repository\BoletaRepository;
use App\Utils\Service\NewController;

class BoletaController
{
    public function Buscar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new BoletaModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new BoletaRepository();
            $data = $repo->Buscar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new BoletaModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new BoletaRepository();
            $data = $repo->Listar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new BoletaModels($request, $response, $service);
            $sv->validateParamsCrear();

            // example
            // $user=$app->lazyUser;
            // $service->validateParam('param_name1', 'Please enter a valid username s')->isLen(4, 6)->isChars('a-zA-Z0-9-');
            // $service->validateParam('param_name2')->notNull();
            $body = $sv->modelRequestBody();

            $repo = new BoletaRepository();
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

            $repo = new BoletaRepository();
            $res = $repo->BuscarPorId($id);
        
            return $res;
        });
    }

    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new BoletaModels($request, $response, $service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $sv->modelRequestBody();

            $repo = new BoletaRepository();
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

            $repo = new BoletaRepository();
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

            $repo = new BoletaRepository();
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

            $repo = new BoletaRepository();
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

            $repo = new BoletaRepository();
            return $repo->Codigo($codigo);
        });
    }
}
