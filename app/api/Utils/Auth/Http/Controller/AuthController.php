<?php

namespace App\Utils\Auth\Http\Controller;

use App\Utils\Auth\Domain\Models\AuthModels;
use App\Utils\Auth\Domain\Repository\AuthRepository;
use App\Utils\Service\NewController;

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

            return $res;
        });
    }
}
