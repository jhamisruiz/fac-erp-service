<?php

namespace App\Auth\Domain\Repository;

use App\Utils\Service\NewToken;
use App\Auth\Persistence\AuthPersistence;
use App\Auth\Domain\Response\AuthResponse;

class AuthRepository
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

    public function Login($data)
    {
        $user = AuthPersistence::Select($data);

        $rs = new AuthResponse($this->request, $this->response, $this->service);
        $res = $rs->LoginResponse($data, $user);

        if ($res === 'ok') {

            $jwt = NewToken::generaToken(["id" => $user['id'], "email" => $user['email']]);
            $tk = null;
            if ($user['id_token']) {

                //actualiza token
                $tk = AuthPersistence::Update($jwt, $user['id']);
            } else {

                //inserta
                $tk = AuthPersistence::Insert($jwt, $user['id']);
            }
            error_log("[LOGIN]-----------Token Agregado: {$tk}");

            $select_user = self::selectUser($user['id']);

            return $select_user;
        }

        return  $res;
    }

    public static function selectUser($userId)
    {
        $user = AuthPersistence::selectId($userId);
        unset($user['password']);
        return $user;
    }

    public function Logout($body)
    {
        // validators
        $res = AuthPersistence::Logout($body);

        return $res;
    }

    public function RucDni($body)
    {
        $token = 'apis-token-1423.LhRaF5vpvwyiAZo-M0E0eKq0ULnxEHUj';
        // validators
        $sv = new AuthResponse($this->service);
        // Iniciar llamada a API
        $res = $sv->ruc_dni($body, $token);

        return $res;
    }
}
