<?php

namespace App\config\Menu\Domain\Repository;

use App\config\Menu\Domain\Response\MenuResponse;
use App\config\Menu\Persistence\MenuPersistence;

class MenuRepository
{
    private $request;
    private $response;
    private $service;

    public function __construct($request = null, $response = null, $service = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->service = $service;
    }

    public function Listar()
    {

        $data = MenuPersistence::Listar();

        $rs = new MenuResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }
}
