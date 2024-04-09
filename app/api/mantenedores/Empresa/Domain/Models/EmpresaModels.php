<?php

namespace Mnt\mantenedores\Empresa\Domain\Models;

use App\Utils\Utils;

class EmpresaModels
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

    public function genteraCetificado($data)
    {
        if ($data['certificado']) {
            $file = $data['certificado'];
            $file_name = isset($file['nombre']) ? $file['nombre'] : $data["numero_documento"] . 'pfx';

            $path = dirname(__FILE__) . "/../../../../../../public/certs/" . $data["numero_documento"];
            if (isset($file['delete']) && $file['delete'] === true) {
                if (file_exists("$path/$file_name")) {
                    if (unlink("$path/$file_name")) {
                        return;
                    }
                }
            }
            if (isset($file['create']) && $file['create'] === true) {
                if (file_exists($path)) {
                    Utils::eliminarCarpetaArchivos($path);
                }
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                if (isset($file['data'])) {
                    file_put_contents("$path/$file_name",  base64_decode(preg_replace('#^data:application/x-pkcs12;base64,#i', '', $file['data'])));
                }
            }
        }
    }

    public function deleteCetificado($data)
    {
        $path = dirname(__FILE__) . "/../../../../../../public/certs/" . $data["numero_documento"];
        if (file_exists($path)) {
            Utils::eliminarCarpetaArchivos($path);
        }
    }

    public function validateParamsCrear()
    {
    }

    public function validateParamsActualziar()
    {
    }
}
