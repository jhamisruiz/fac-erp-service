<?php

namespace App\config\Menu\Http\Controller;

use App\config\Menu\Domain\Models\MenuModels;
use App\config\Menu\Domain\Repository\MenuRepository;
use App\Utils\Service\NewController;

class MenuController
{
    public function Listar()
    {
        $ctr = new NewController();

        return $ctr->Controller(function ($request, $response, $service) {
            $sv = new MenuModels($request, $response, $service);
            // validators
            $sv->validateParamsCrear();

            $id = $request->param('id');

            $repo = new MenuRepository();
            $data = $repo->Listar($id);

            return  $data;
        });
    }
}
