<?php

// application
require_once __DIR__ . '/app/autoload.php';


/* 
use Slim\Factory\AppFactory;
use Slim\Middleware\Cache;
use Slim\Middleware\Session;

$app = AppFactory::create();

// Configura el middleware de cacheo HTTP
$app->add(new Cache('private', 86400));

// Configura el middleware de sesiones
$app->add(new Session([
    'name' => 'my-session',
    'autorefresh' => true,
    'lifetime' => '1 hour'
]));

// Define una ruta
$app->get('/hello/{name}', function ($request, $response, $args) {
    // Obtiene la información de la sesión
    $session = $request->getAttribute('session');

    // Agrega un valor a la sesión
    $session->set('name', $args['name']);

    // Obtiene el valor de la sesión
    $name = $session->get('name');

    // Retorna una respuesta con el valor de la sesión
    $response->getBody()->write("Hello, $name!");

    return $response;
});

$app->run();

 */


 /* 
 
 use Klein\Klein;

$klein = new Klein();


// Middleware para validar la conexión a la base de datos

try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=localhost;dbname=embotelladora_cassinelli', 'pandora', 'pandora');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Error al conectar a la base de datos
    $klein->respond(function ($request, $response, $service) use ($e) {
        $response->code(500);
        $response->header('Content-Type', 'application/json');
        $response->header('Access-Control-Allow-Origin', '*');
        $response->body(json_encode(['error' => 'No se pudo conectar a la base de datos' . $e->getMessage()]));
    });
    $klein->dispatch();
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    }
    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
    header($protocol . ' 505 error aql');
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400");
    header("Content-Type: application/json; charset=UTF-8");
    header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,Origin, Accept");
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, PATCH");

    exit();
}


$klein->respond(function ($request, $response) {
    // Permitir el acceso desde cualquier origen
    $response->header('Access-Control-Allow-Origin', '*');

    // Permitir los métodos HTTP que se van a utilizar
    $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

    // Permitir los headers que se van a enviar en la solicitud
    $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    // Devolver la respuesta
    return $response;
});

// Operación GET
$klein->respond('GET', '/v1/usuarios', function ($request, $response) {

    // código para obtener todos los usuarios
    return $response->json(['message' => 'Obtener todos los usuarios']);
});

// Operación POST
$klein->respond('POST', '/v1/usuario', function ($request, $response) {
    // código para crear un nuevo usuario
    return $response->json(['message' => 'Crear un nuevo usuario']);
});

// Operación PUT
$klein->respond('PUT', '/v1/usuario/[i:id]', function ($request, $response) {
    // código para actualizar un usuario existente
    $id = $request->param('id');
    return $response->json(['message' => 'Actualizar el usuario con id ' . $id]);
});

// Operación DELETE
$klein->respond('DELETE', '/usuario/[i:id]', function ($request, $response) {
    // código para eliminar un usuario existente
    $id = $request->param('id');
    return $response->json(['message' => 'Eliminar el usuario con id ' . $id]);
});

// Operación PATCH
$klein->respond('PATCH', '/usuario/[i:id]', function ($request, $response) {
    // código para actualizar parcialmente un usuario existente
    $id = $request->param('id');
    return $response->json(['message' => 'Actualizar parcialmente el usuario con id ' . $id]);
});

$klein->onHttpError(function ($code, $router) use ($klein) {
    if ($code == 404) {
        // Ruta no encontrada
        $klein->response()->code(404);
        $klein->response()->json(['error' => 'Ruta no encontradas']);
        $klein->response()->header('Access-Control-Allow-Origin', '*');

        // Permitir los métodos HTTP que se van a utilizar
        $klein->response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

        // Permitir los headers que se van a enviar en la solicitud
        $klein->response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    } elseif ($code == 405) {
        // Método no permitido
        $klein->response()->code(405);
        $klein->response()->json(['error' => 'Método no permitido']);
        $klein->response()->header('Access-Control-Allow-Origin', '*');

        // Permitir los métodos HTTP que se van a utilizar
        $klein->response()->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

        // Permitir los headers que se van a enviar en la solicitud
        $klein->response()->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
});
// Ejecutar la aplicación
$klein->dispatch();
 
 */