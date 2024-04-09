<?php

namespace Mnt\mantenedores\Rol\Domain\Models;

class RolModels
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

    public function permisosCrear($arr)
    {
        $permisos = [];
        if (is_array($arr) && count($arr)) {
            foreach ($arr as $key => $value) {
                if (is_array($value) && isset($value['selected']) && isset($value['indeterminate'])) {
                    if ($value['selected'] === true || $value['indeterminate'] === true) {
                        if (isset($value['componentes']) && is_array($value['componentes'])) {
                            $permisos = array_merge($permisos, $value['componentes']);
                        }
                    }
                }
            }
        }

        return array_values(array_filter($permisos, array(self::class, 'filtroSeleccion')));
    }

    function filtroSeleccion($obj)
    {
        return isset($obj['selected']) && $obj['selected'] === true ||
            isset($obj['indeterminate']) && $obj['indeterminate'] === true;
    }

    public function validateParamsActualziar()
    {
    }
}
