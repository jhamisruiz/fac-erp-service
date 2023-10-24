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
            // validators

            $repo = new MenuRepository();
            $data = $repo->Listar();

            return  $data;
        });
    }
}
