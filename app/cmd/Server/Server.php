<?php

namespace Cmd\Server;

class Server
{
    public static $routes = [
        //////////////endpoint login -
        [
            "endpoint" => "/v1/login",
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" => null,
            "url_pattern" => "/login"
        ],
        [
            "endpoint" => "/v1/logout",
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/logout"
        ],
        [
            "endpoint" => "/v1/user-ruc-dni", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" => ["Authorization"],
            "url_pattern" => "/user-ruc-dni"
        ],
        //////////////endpoint menu -
        [
            "endpoint" => "/v1/menu",
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" => null,
            "url_pattern" => "/menu"
        ],
        //////////////endpoint ubigeo -
        [
            "endpoint" => "/v1/ubigeo-departamento",   # Ruta | {id parametro}
            "method" => "GET",         # Metodo de la solicitud     # Nombre del script php que recibe
            "querystring_params" => [], # Parametros adicionales en la ruta
            "headers_to_pass" => null, # Obliga a pasar por la validacion del token
            "url_pattern" => "/ubigeo-departamento" # Ruta sin paratmetro
        ],
        [
            "endpoint" => "/v1/ubigeo-provincia/[i:provincia]",   # Ruta | {id parametro}
            "method" => "GET",         # Metodo de la solicitud
            "querystring_params" => [], # Parametros adicionales en la ruta
            "headers_to_pass" => null, # Obliga a pasar por la validacion del token
            "url_pattern" => "/ubigeo-provincia/[i:provincia]" # Ruta sin paratmetro
        ],
        [
            "endpoint" => "/v1/ubigeo-distrito/[i:distrito]",   # Ruta | {id parametro}
            "method" => "GET",         # Metodo de la solicitud
            "querystring_params" => [], # Parametros adicionales en la ruta
            "headers_to_pass" => null, # Obliga a pasar por la validacion del token
            "url_pattern" => "/ubigeo-distrito/[i:distrito]" # Ruta sin paratmetro
        ],
        //////////////endpoint usuario -
        [
            "endpoint" => "/v1/usuario", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario"
        ],
        [
            "endpoint" => "/v1/usuario", //lista todos los usuario
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario"
        ],
        [
            "endpoint" => "/v1/usuario/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario/[i:id]"
        ],
        [
            "endpoint" => "/v1/usuario/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario/[i:id]"
        ],
        [
            "endpoint" => "/v1/usuario/[i:id]/habilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/usuario/[i:id]/deshabilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/usuario/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario/[i:id]"
        ],
        [
            "endpoint" => "/v1/usuario-password/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario-password/[i:id]"
        ],
        //////////////endpoint producto -
        [
            "endpoint" => "/v1/producto", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto"
        ],
        [
            "endpoint" => "/v1/producto", //lista todos los producto
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/producto"
        ],
        [
            "endpoint" => "/v1/producto/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto/[i:id]"
        ],
        [
            "endpoint" => "/v1/producto/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto/[i:id]"
        ],
        [
            "endpoint" => "/v1/producto/[i:id]/habilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/producto/[i:id]/deshabilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/producto/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto/[i:id]"
        ],
        ///
        [
            "endpoint" => "/v1/cuestionarios", //lista todos los 
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  null, //["Authorization"],
            "url_pattern" => "/cuestionarios"
        ],
        //////////////endpoint empleados -
        [
            "endpoint" => "/v1/empleado", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado"
        ],
        [
            "endpoint" => "/v1/empleado", //lista todos los empleado
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/empleado"
        ],
        [
            "endpoint" => "/v1/empleado/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado/[i:id]"
        ],
        [
            "endpoint" => "/v1/empleado/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado/[i:id]"
        ],
        [
            "endpoint" => "/v1/empleado/[i:id]/habilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/empleado/[i:id]/deshabilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/empleado/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado/[i:id]"
        ],
        //////////////endpoint empleado - asistencias
        [
            "endpoint" => "/v1/empleado-asistencia", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado"
        ],
        [
            "endpoint" => "/v1/empleado-asistencia", //lista todos los empleado
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/empleado"
        ],
        [
            "endpoint" => "/v1/empleado-asistencia/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado/[i:id]"
        ],
        [
            "endpoint" => "/v1/empleado-asistencia/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado-asistencia/[i:id]"
        ],
        [
            "endpoint" => "/v1/empleado-asistencia/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado-asistencia/[i:id]"
        ],
        //facturacion
        [
            "endpoint" => "/v1/facturacion-documento-buscar", //lista todos los sucursal
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-documento-buscar"
        ],
        [
            "endpoint" => "/v1/facturacion-factura", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-factura"
        ],
        [
            "endpoint" => "/v1/facturacion-boleta", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-boleta"
        ],
        [
            "endpoint" => "/v1/facturacion-nota-credito", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-nota-credito"
        ],
        [
            "endpoint" => "/v1/facturacion-nota-debito", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-nota-debito"
        ],
        [
            "endpoint" => "/v1/facturacion-gia-remision", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-gia-remision"
        ],
        [
            "endpoint" => "/v1/facturacion-baja-suntat", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-baja-suntat"
        ],
        [
            "endpoint" => "/v1/facturacion-resumen-boletas", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/facturacion-resumen-boletas"
        ],
        //////////////endpoint EMPRESA -
        [
            "endpoint" => "/v1/empresa", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa"
        ],
        [
            "endpoint" => "/v1/empresa-buscar", //lista todos los empresa
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa-buscar"
        ],
        [
            "endpoint" => "/v1/empresa", //lista todos los empresa
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa"
        ],
        [
            "endpoint" => "/v1/empresa/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa/[i:id]"
        ],
        [
            "endpoint" => "/v1/empresa/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa/[i:id]"
        ],
        [
            "endpoint" => "/v1/empresa/[i:id]/habilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/empresa/[i:id]/deshabilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/empresa/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa/[i:id]"
        ],
        [
            "endpoint" => "/v1/empresa-codigo", //elimina por id
            "method" => "GET",
            "querystring_params" => ['code'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empresa-codigo"
        ],
        //////////////endpoint sucursal -
        [
            "endpoint" => "/v1/sucursal", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal"
        ],
        [
            "endpoint" => "/v1/sucursal-buscar", //lista todos los sucursal
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal-buscar"
        ],
        [
            "endpoint" => "/v1/sucursal", //lista todos los sucursal
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal"
        ],
        [
            "endpoint" => "/v1/sucursal/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal/[i:id]"
        ],
        [
            "endpoint" => "/v1/sucursal/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal/[i:id]"
        ],
        [
            "endpoint" => "/v1/sucursal/[i:id]/habilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/sucursal/[i:id]/deshabilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/sucursal/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal/[i:id]"
        ],
        [
            "endpoint" => "/v1/sucursal-codigo", //elimina por id
            "method" => "GET",
            "querystring_params" => ['code'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal-codigo"
        ],
    ];
}
