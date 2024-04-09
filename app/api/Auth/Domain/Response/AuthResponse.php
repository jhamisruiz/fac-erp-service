<?php

namespace App\Auth\Domain\Response;

use App\Utils\Service\NewError;

class AuthResponse
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
            }
        }

        return $data;
    }

    public function LoginResponse($data, $user)
    {
        if (isset($user['code'])) {

            //error de base de datos
            $this->response->code($user['code']);
            return   $user;
        } elseif (isset($user['habilitado'])) {
            if ($user['habilitado'] === 1 || $user['habilitado'] === true) {
                if (password_verify($data['password'], $user['password'])) {

                    return 'ok';
                } else {

                    $this->response->code(202);
                    return  NewError::__Log('La contraseña no coincide...', 202);
                }
            } else {

                $this->response->code(202);
                return  NewError::__Log('Usuario o email esta deshabilitado.', 202);
            }
        } else {

            $this->response->code(202);
            return  NewError::__Log('Usuario o Email incorrecto!', 202);
        }
    }

    public function ruc_dni($body, $token)
    {
        $curl = curl_init();
        if (strlen($body['dni_ruc']) === 8) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $body['dni_ruc'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 2,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: https://apis.net.pe/consulta-dni-api',
                    'Authorization: Bearer ' . $token
                ),
            ));
        }

        if (strlen($body['dni_ruc']) === 11) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $body['dni_ruc'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: http://apis.net.pe/api-ruc',
                    'Authorization: Bearer ' . $token
                ),
            ));
        }

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        // Datos de empresas según padron reducido

        $data_response = json_decode($response);

        if (isset($data_response->{'error'})) {
            return NewError::__Log($data_response->{'error'}, 202);
        }

        if ($data_response === null) {
            return NewError::__Log('No se encontro detalles del documento.', 202);
        }
        return $data_response;
    }
}
