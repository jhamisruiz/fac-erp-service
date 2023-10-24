<?php

namespace Cmd\Middleware;

class CorsMiddleware
{
    public static function headers()
    {
        return function ($request, $response) {
            $response->header('Access-Control-Allow-Origin', '*');
            $response->header('Access-Control-Allow-Credentials', true);
            $response->header('Access-Control-Max-Age', MAX_TIME_HTTP_CACHE);
            // Permitir los métodos HTTP que se van a utilizar
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');
            $response->header('Content-Type', 'application/json; charset=UTF-8');
            $response->header('P3P', 'CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
            // Permitir los headers que se van a enviar en la solicitud
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,Origin, Accept');

            // Devolver la respuesta
            return $response;
        };
    }
}
