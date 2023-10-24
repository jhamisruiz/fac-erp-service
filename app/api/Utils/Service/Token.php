<?php

namespace App\Utils\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Utils\Auth\Domain\Repository\AuthRepository;

class Token
{
    public static function validaToken($request, $response, $service, $app)
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
        $_token = $_token ?  $_token : ($request->headers())['Authorization'];

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

                $user = AuthRepository::selectUser($data->{"data"}->{'id'});

                //SI NO HAY TOKEN --(!isset($resp[0]))
                if (!isset($user['id'])) {
                    $response->code(401);
                    return 'Missing token.';
                }

                if ($data->{"data"}->{'id'} === $user['id']) {
                    $data->{"data"}->{'id'};

                    if ($user['expire'] <= $time || $user['token'] != $_token) {
                        $response->code(403);
                        return 'Token is expired / Forbidden authentication.';
                    }

                    $app->register('lazyUser', function () use ($user) {

                        return $user;
                    });

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
