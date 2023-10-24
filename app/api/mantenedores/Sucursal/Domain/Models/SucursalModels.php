<?php
namespace Mnt\mantenedores\Sucursal\Domain\Models;

class SucursalModels
{
    private $request;
    private $response;
    private $service;
    private $app;
    private $model;

    public function __construct($request = null, $response = null, $service = null, $app = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->service = $service;
        $this->app = $app;

        $this->model = $request ?? $response ?? $service ?? $app;
    }

     public function modelRequestBody()
    {
        return json_decode($this->request->body(), true);
    }

    public function validateParamsLista()
    {
        $this->model->validateParam('start', 'require start')->isInt();
        $this->model->validateParam('length', 'require length')->isInt();
        $this->model->validateParam('search');
        $this->model->validateParam('order', 'require asc|desc')->isOrder();
    }

    public function validateParamsCrear()
    {
    }
    
    public function validateParamsActualziar()
    {
    }
}
