<?php

namespace App\Auth\Http\Controller;

use App\Utils\Service\NewController;
use App\Auth\Domain\Models\AuthModels;
use App\Auth\Domain\Repository\AuthRepository;
use Mnt\mantenedores\Cliente\Domain\Repository\ClienteRepository;

class AuthController
{
    public function Login()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $model = new AuthModels($request, $response, $service);

            $data = $model->modelRequestBody();

            $repo = new AuthRepository($request, $response, $service);
            $data = $repo->login($data);

            return  $data;
        });
    }

    public function Logout()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            $model = new AuthModels($request, $response, $service);

            $data = $model->modelRequestBody();

            $repo = new AuthRepository();
            $res = $repo->Logout($data);

            return $res;
        });
    }

    public function RucDni()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $sv = new AuthModels($request, $response, $service);
            $body =  $sv->modelRequestBody();

            $repo = new AuthRepository();
            $res = $repo->RucDni($body);
            /**/
            if (isset($res->ubigeo)) {
                if (strlen($body['dni_ruc']) === 8 || strlen($body['dni_ruc']) === 11) {
                    $repcli = new ClienteRepository();
                    $cliente = $repcli->BuscarPorDoc($body['dni_ruc']);
                    if (isset($cliente->id)) {
                        $res->id = $cliente->id;
                        $res->email = $cliente->email;
                    } else {
                        $res->id = null;
                        $res->email = null;
                    }
                }
            }
            return $res;
        });
    }
}
