<?php

namespace Mnt\mantenedores\Usuario\Domain\Models;


class UsuarioModels
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

    public function modelRequestBodyDecript()
    {
        $data = json_decode($this->request->body(), true);

        $data['password'] = base64_decode(str_replace(APP_KEY, '', $data['password']));
        $data['rep_password'] = base64_decode(str_replace(APP_KEY, '', $data['rep_password']));

        return $data;
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

    public function password_hash($data)
    {
        if ($data["password"] === $data["rep_password"]) {
            $data["password"]  = password_hash($data["password"], PASSWORD_DEFAULT);
            return $data;
        }

        return 0;
    }
}

//$2y$10$yjMoXOknhEySNaGKHP.OTefnrquVO9TRhrPoXwry57aQuVDmwETTa
//$2y$10$E2yahmzRB07aZiYYBnYDjegQs8QeP3lL23jZDFCOeQNT0yzfzHKYi