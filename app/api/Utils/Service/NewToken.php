<?php

namespace App\Utils\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use ReallySimpleJWT\Token;

class NewToken
{
    public static function generaToken($data)
    {
        ///
        ini_set('date.timezone', 'America/Lima');
        $time = time();
        $_data = [
            "iat" => $time,
            "exp" => $time + (60 * 60 * HR_KEY_EXP * DD_KEY_EXP),
            "data" => [
                "id" => $data['id'],
                "email" => $data['email']
            ]
        ];
        $jwt = JWT::encode($_data, APP_KEY, 'HS512');

        return ["token" => $jwt, "data" => $_data, "sid" => APP_KEY];
    }

    public static function validaToken($response, $user)
    {
        ini_set('date.timezone', 'America/Lima');
        $time = time();
        $_token = null;
        if (isset($_SERVER['Authorization'])) {

            $_token = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI

            $_token = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {

            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);

            if (isset($requestHeaders['Authorization'])) {
                $_token = trim($requestHeaders['Authorization']);
            }
        }
        //echo 'ok';
        if (!empty($_token)) {
            if (preg_match('/Bearer\s(\S+)/', $_token, $matches)) {
                $_token = $matches[1];
                //return $_token;

                $data = JWT::decode($_token, new key(APP_KEY, 'HS512'));
                if (isset($data->{'error'})) {
                    $response->code(403);
                }

                //mostrar error si no hay data token
                if (!isset($data->{'data'})) {
                    $response->code(401);
                    return 'Missing token.';
                }

                //$resp = ControllerQueryes::SELECT($select, $tables, $where);

                //SI NO HAY TOKEN --(!isset($resp[0]))
                if (isset($resp[0])) {
                    $response->code(401);
                    return 'Missing token.';
                }

                if ($data->{"data"}->{'id'} === $user['id']) {
                    if ($user['expire'] <= $time || $user['token'] != $_token) {
                        $response->code(403);
                        return 'Token is expired / Forbidden authentication.';
                    }
                    $_token = "ok";
                } else {
                    $response->code(403);
                    return 'Token is expired / Forbidden authentication.';
                }
            }
        } else {
            $response->code(401);
            return 'Missing token.';
        }
        return $_token;
    }
}
