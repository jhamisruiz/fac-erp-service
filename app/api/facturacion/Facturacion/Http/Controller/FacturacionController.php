<?php

namespace Mnt\facturacion\Facturacion\Http\Controller;

use Mnt\facturacion\Facturacion\Domain\Models\FacturacionModels;
use Mnt\facturacion\Facturacion\Domain\Repository\FacturacionRepository;
use App\Utils\Service\NewController;

class FacturacionController
{
    public function Factura()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new FacturacionModels($request, $response, $service);
            // example request
            $body = $sv->modelRequestBody();

            $repo = new FacturacionRepository();
            $data = $repo->Factura($body);

            return  $data;
        });
    }

    public function Boleta()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new FacturacionModels($request, $response, $service);
            // example request
            $body = $sv->modelRequestBody();

            $repo = new FacturacionRepository();
            $res = $repo->Boleta($body);

            return $res;
        });
    }

    public function NotaCredito()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // example request, args
            // $user=$app->lazyUser;
            // $service->validateParam('param_name1', 'Please enter a valid username s')->isLen(4, 6)->isChars('a-zA-Z0-9-');
            // $service->validateParam('param_name2')->notNull();
            $id = $request->param('id');

            $repo = new FacturacionRepository();
            $res = $repo->NotaCredito($id);

            return $res;
        });
    }

    public function NotaDebito()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            // validators
            $sv = new FacturacionModels($service);
            $sv->validateParamsActualziar();

            // example request, args
            $id = $request->param('id');
            $body = $request->body();

            $repo = new FacturacionRepository();
            $res = $repo->NotaDebito($id, $body);;

            return  $res;
        });
    }

    public function GiraRemision()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new FacturacionRepository();
            $res = $repo->GiraRemision($id, $s = 0);

            return  $res;
        });
    }

    public function BajaSuna()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new FacturacionRepository();
            $res =  $repo->BajaSuna($id, true);

            return  $res;
        });
    }

    public function ResumenBoletas()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // example request, args
            $id = $request->param('id');

            $repo = new FacturacionRepository();
            $res =  $repo->ResumenBoletas($id, 'false');

            return  $res;
        });
    }

    public function Buscar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new FacturacionModels($service);
            $sv->validateParamsLista();

            // example request
            $start = $request->param('start');
            $length = $request->param('length');
            $search = $request->param('search');
            $order = $request->param('order');

            $repo = new FacturacionRepository();
            $data = $repo->Buscar($start, $length, $search, $order);

            return  $data;
        });
    }
}
