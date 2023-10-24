<?php

namespace App\Utils\Service;

use App\Utils\Service\Token;
use Cmd\Middleware\RouterMiddleware;

class NewController
{
    function Controller($function)
    {
        return function ($request, $response, $service, $app) use ($function) {

            // add validator
            $aut = RouterMiddleware::getMatchPath($request);

            if ($aut === 0 || $aut === 'error') {
                $aut === 0 ?
                    $response->code(500)->json('Error: endpoint not match')
                    : $response->code(404)->json('Not Found');
                return $response;
            }

            if ($aut === "Authorization") {

                $res =  Token::validaToken($request, $response, $service, $app);
                if ($res !== 'ok') {
                    return $response->json($res);
                }
            }

            $service->addValidator('order', function ($str) {
                return preg_match('/^(asc|desc)$/i', $str);
            });
            $body = $function($request, $response, $service, $app);

            return $response->json($body);
        };
    }
}
