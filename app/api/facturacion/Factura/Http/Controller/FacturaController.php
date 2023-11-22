<?php

namespace Mnt\facturacion\Factura\Http\Controller;

use App\Utils\Service\NewController;
use App\Utils\FacturacionMdl\FacturacionMdl;
use Mnt\facturacion\Factura\Domain\Models\FacturaModels;
use Mnt\facturacion\Factura\Domain\Repository\FacturaRepository;

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
            $data = $repo->Listar($start, $length, $search, $order);

            return  [
                [
                    'factura' => 1,
                    'pdf' => 1,
                    'xml' => 1,
                    'cdr' => 1,
                    'sunat' => 1,
                    'correo' => 1,
                ]
            ];
        });
    }

    public function Crear()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new FacturaModels($request, $response, $service);
            $sv->validateParamsCrear();

            // example
            // $user=$app->lazyUser;
            // $service->validateParam('param_name1', 'Please enter a valid username s')->isLen(4, 6)->isChars('a-zA-Z0-9-');
            // $service->validateParam('param_name2')->notNull();
            $body = $sv->modelRequestBody();
            $fact = new FacturacionMdl($body);

            $repo = new FacturaRepository();
            $id = $repo->Crear($body);

            //$res = $repo->BuscarPorId($id);
            return [
                'g_Sub_total' => $fact->getSub_total(),
                'g_Descuento' => $fact->getDescuento(),
                'g_Subtotal_con_dscto' => $fact->getSubtotal_con_dscto(),
                'g_Igv' => $fact->getIgv(),
                'g_Icbper' => $fact->getIcbper(),
                'g_Op_exoneradas' => $fact->getOp_exoneradas(),
                'g_Op_inafectas' => $fact->getOp_inafectas(),
                'g_Total' => $fact->getTotal()
            ];
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
