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

            if (isset($file['create']) && $file['create'] === true) {
                $path = dirname(__FILE__) . "/../../../../../../public/certs/" . $data["id"];
                if (file_exists($path)) {
                    Utils::eliminarCarpetaYArchivos($path);
                }
                if (!file_exists($path)) {

                    mkdir($path, 0777, true);
                }
                $file_name = isset($file['nombre']) ? $file['nombre'] : $data["numero_documento"] . 'pfx';
                if (isset($file['data'])) {
                    file_put_contents("$path/$file_name",  base64_decode($file['data']));
                }
            }
        }
    }

    public function deleteCetificado($id)
    {
        $path = dirname(__FILE__) . "/../../../../../../public/certs/" . $id;
        if (file_exists($path)) {
            Utils::eliminarCarpetaYArchivos($path);
        }
    }

    public function validateParamsCrear()
    {
    }

    public function validateParamsActualziar()
    {
    }
}
