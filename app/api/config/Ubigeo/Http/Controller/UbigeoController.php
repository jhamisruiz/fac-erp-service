<?php

namespace App\config\Ubigeo\Http\Controller;

use App\config\Ubigeo\Domain\Repository\UbigeoRepository;
use App\Utils\Service\NewController;

class UbigeoController
{
    public function Departamento()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $repo = new UbigeoRepository();
            $res = $repo->Departamento();

            return  $res;
        });
    }

    public function Provincia()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            // validators
            $provincia = $request->param('provincia');

            $repo = new UbigeoRepository();
            $res = $repo->Provincia($provincia);

            return $res;
        });
    }

    public function Distrito()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service, $app) {
            $distrito = $request->param('distrito');

            $repo = new UbigeoRepository();
            $res = $repo->Distrito($distrito);

            return $res;
        });
    }
}
