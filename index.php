<?php

// application
require_once __DIR__ . '/app/autoload.php';

// if (isset($_SERVER['Authorization'])) {

//     $_token = trim($_SERVER["Authorization"]);
// } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI

//     $_token = trim($_SERVER["HTTP_AUTHORIZATION"]);
// } elseif (function_exists('apache_request_headers')) {

//     $requestHeaders = apache_request_headers();
//     // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
//     $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
//     //print_r($requestHeaders);

//     if (isset($requestHeaders['Authorization'])) {
//         $_token = trim($requestHeaders['Authorization']);
//     }
//     echo json_encode($requestHeaders);
// }
// header('Content-Type: application/json; charset=UTF-8');
// $_token = '{
//     "user_id": 9,
//     "perfil_id": 1015,
//     "name": "Administrador",
//     "company_id": 106,
//     "corporation_id": "1",
//     "roles": [
//         "admin"
//     ],
//     "email": "administrador@nisira.com.pe",
//     "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJjb21wYW55X2lkIjoxMDYsImNvcnBvcmF0aW9uX2lkIjoiMSIsImV4cCI6MTcwODg5ODQxNSwiaGFzaCI6IjM3YTMwN2RiODMzN2RjM2M3NzI3MDY0ZjVmY2I4ZDE1IiwicGVyZmlsX2lkIjoxMDE1LCJyb2xlcyI6WyJhZG1pbiJdLCJzaWQiOiJFOEMzMUFDMzJFRTk0RkU1QTlFNzBEREQxRUFBNDIwQiIsInVzZXJfaWQiOjksInVzZXJuYW1lIjoiYWRtaW4ifQ.b4NXogTLct5m8hV2P9Wf-xKtcDslITuRZyZwvnrtqe4QY1TBjo3bLQ6DvXyGDJb2cZ-yOOFVLIjtgqwRrpRqn8JSp6-Ruia-PCujZz_zzHESwF3DUyIlJxI8vGTp6f82NG6cM2jcfG8CSygj1NuRZ1isBA1D9H_4VTpQkZOE46I",
//     "expire": 1708898415,
//     "sid": "E8C31AC32EE94FE5A9E70DDD1EAA420B"
// }';
// $_token = json_decode($_token);

// Función para imprimir el token Bearer y los headers en formato JSON
// function handleRequest()
// {
//     // Obtener el token Bearer del header Authorization
//     $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

//     // Obtener todos los headers en un array
//     $headers = [];
//     foreach ($_SERVER as $name => $value) {
//         if (substr($name, 0, 5) == 'HTTP_') {
//             $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
//         }
//     }

//     // Escribir el token Bearer y los headers en formato JSON
//     $requestHeaders = apache_request_headers();
//     // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
//     $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

//     $headers = getallheaders();

//     // Convertir los encabezados a JSON
//     $json_headers = json_encode($headers);
//     $responseData = [
//         "token" => $token,
//         "headers" => $headers,
//         "rh" => $requestHeaders,
//         "sv" => $_SERVER,
//         "saph" => $headers
//     ];

//     header('Content-Type: application/json');
//     echo json_encode($responseData);
// }

// // Manejar la solicitud
// handleRequest();
