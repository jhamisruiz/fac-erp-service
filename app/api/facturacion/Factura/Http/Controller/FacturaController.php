<?php

namespace Mnt\facturacion\Factura\Http\Controller;

use App\Utils\Service\NewController;
use App\Utils\FacturacionMdl\FacturacionMdl;
use Mnt\facturacion\Factura\Domain\Models\FacturaModels;
use Mnt\facturacion\Factura\Domain\Repository\FacturaRepository;
use Mnt\mantenedores\Cliente\Domain\Repository\ClienteRepository;

class FacturaController
{
    public function Buscar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new FacturaModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new FacturaRepository();
            $data = $repo->Buscar($start, $length, $search, $order);

            return  $data;
        });
    }

    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new FacturaModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new FacturaRepository();
            $data = $repo->Listar($start, $length, $search, $order, $app->getUser());

            return  $data;
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new FacturaModels($request, $response, $service);
            $body = $sv->modelRequestBody();

            $fact = new FacturacionMdl($body);
            $fact = $fact->getData();

            //return [$fact];
            ///cliente
            $cli = new ClienteRepository();
            if ($fact['cliente']['cliente_id'] > 0) {
                $cli->Actualizar($fact['cliente']['cliente_id'], $body['cliente']);
            } else {
                $id_cli = $cli->Crear($body['cliente']);
                if ($id_cli > 0) {
                    $fact['cliente']['cliente_id'] = ((int)$id_cli);
                }
            }

            ///Factura Repository
            $repo = new FacturaRepository();
            $res = $repo->Crear($body, $fact);

            //$res = $repo->BuscarPorId($id);
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

            $repo = new FacturaRepository();
            $res = $repo->BuscarPorId($id);

            return $res;
        });
    }

    public function Actualizar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new FacturaModels($request, $response, $service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $sv->modelRequestBody();

            /* $repo = new FacturaRepository();
            $res = $repo->Actualizar($id, $body);

            $res->data = $repo->BuscarPorId($id); */
            return  $body['id'];
        });
    }

    public function Eliminar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new FacturaRepository();
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

            $repo = new FacturaRepository();
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

            $repo = new FacturaRepository();
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

            $repo = new FacturaRepository();
            return $repo->Codigo($codigo);
        });
    }
}
