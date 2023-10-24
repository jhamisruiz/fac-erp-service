<?php

namespace App\config\Ubigeo\Domain\Repository;

use App\config\Ubigeo\Persistence\UbigeoPersistence;

class UbigeoRepository
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


    public function Departamento()
    {
        $res = UbigeoPersistence::Departamento();

        return  $res;
    }
    public function Provincia($prov)
    {
        $res = UbigeoPersistence::Provincia($prov);

        return  $res;
    }
    public function Distrito($dist)
    {
        $res = UbigeoPersistence::Distrito($dist);

        return  $res;
    }
}
