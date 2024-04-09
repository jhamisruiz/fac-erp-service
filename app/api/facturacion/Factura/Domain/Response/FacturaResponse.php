<?php

namespace Mnt\facturacion\Factura\Domain\Response;

class FacturaResponse
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
                $data[$key]['enviar_sunat'] = boolval($value['enviar_sunat']);
                $data[$key]['solo_guardar'] = boolval($value['solo_guardar']);
                $data[$key]['index'] = ($key + 1);
            }
        }

        return $data;
    }
}
