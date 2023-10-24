<?php

namespace Mnt\mantenedores\Usuario\Domain\Response;

class UsuarioResponse
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

    /**
     * @param $data type array
     * @return array
     */
    public function ListaResponse($data)
    {
        if (count($data)) {
            foreach ($data as $key => $value) {
                $data[$key]['habilitado'] = boolval($value['habilitado']);
                $data[$key]['index'] = ($key + 1);
                if (strlen($value['password']) > 10) {
                    $data[$key]['password'] = null; //'...' . substr($value['password'], -8);
                    $data[$key]['rep_password'] = null;
                }
            }
        }

        return $data;
    }
}
