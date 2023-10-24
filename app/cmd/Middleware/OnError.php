<?php

namespace Cmd\Middleware;

class OnError
{
    public static function ValidationException($klein)
    {
        //echo json_encode($klein->routes());
        return function ($app, $exception) use ($klein) {

            //| VAlidar Parametros definidos correctamente
            if (!$exception) {
                $exception = 'Parametro Requerido.';
            }
            try {
                throw new \Klein\Exceptions\ValidationException($exception);
                //code...
            } catch (\Klein\Exceptions\ValidationException $th) {
                //return $app->response()->json(['error' => $th->getMessage()], 400);
                if ($exception === 'Expired token') {

                    $app->response()->code(403);
                    $app->response()->json('AS: Token is expired / Forbidden authentication');
                } else {

                    $app->response()->code(400);
                    $app->response()->json($th->getMessage());
                }
                $app->response()->header('Access-Control-Allow-Origin', '*');

                // Permitir los métodos HTTP que se van a utilizar
                $app->response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

                // Permitir los headers que se van a enviar en la solicitud
                $app->response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            }
        };
    }
}
