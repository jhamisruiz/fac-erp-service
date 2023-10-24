<?php

namespace Cmd\Middleware;

use PDOException;
use DB\Conexion\Conexion;
use Cmd\Middleware\RouterMiddleware;

class OnHttpError
{

    public static function errors($klein)
    {
        return function ($code, $router) use ($klein) {

            $routes = $klein->routes();

            if (1) {
                try {
                    Conexion::validar();
                } catch (PDOException $e) {

                    // Método no permitido
                    $klein->response()->code(500);
                    $klein->response()->json('Error de conexión a la base de datos: ' . $e->getMessage());
                    $klein->response()->header('Access-Control-Allow-Origin', '*');

                    // Permitir los métodos HTTP que se van a utilizar
                    $klein->response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

                    // Permitir los headers que se van a enviar en la solicitud
                    $klein->response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
                    die();
                }
            }
            if (RouterMiddleware::getMatch($routes) === 0 || $code == 404) {
                // Ruta no encontrada
                $klein->response()->code(404);
                $klein->response()->json('Not Found');
                $klein->response()->header('Access-Control-Allow-Origin', '*');

                // Permitir los métodos HTTP que se van a utilizar
                $klein->response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

                // Permitir los headers que se van a enviar en la solicitud
                $klein->response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            } elseif ($code == 405) {
                // Método no permitido
                $klein->response()->code(405);
                $klein->response()->json('Method Not Allowed');
                $klein->response()->header('Access-Control-Allow-Origin', '*');

                // Permitir los métodos HTTP que se van a utilizar
                $klein->response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

                // Permitir los headers que se van a enviar en la solicitud
                $klein->response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }
        };
    }
}
