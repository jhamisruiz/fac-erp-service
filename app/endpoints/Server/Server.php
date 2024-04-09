<?php

namespace Sv\Server;

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
            "endpoint" => "/v1/menu", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/menu"
        ],
        [
            "endpoint" => "/v1/menu/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/menu/[i:id]"
        ],
        [
            "endpoint" => "/v1/menu/[i:id]",
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" => ["Authorization"],
            "url_pattern" => "/menu/[i:id]"
        ],
        [
            "endpoint" => "/v1/menu-buscar", //lista todos los producto
            "method" => "GET",
            "querystring_params" => ['userid', 'start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/menu-buscar"
        ],
        [
            "endpoint" => "/v1/menu", //lista todos los producto
            "method" => "GET",
            "querystring_params" => ['userid', 'start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
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
        [
            "endpoint" => "/v1/usuario-empresa-sucursal", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/usuario-empresa-sucursal"
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
            "endpoint" => "/v1/producto-buscar", //lista todos los producto
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/producto-buscar"
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
        [
            "endpoint" => "/v1/producto-unspsc-segmentos",
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto-unspsc-segmentos"
        ],
        [
            "endpoint" => "/v1/producto-unspsc-familias/[i:codigo]",
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto-unspsc-familias/[i:codigo]"
        ],
        [
            "endpoint" => "/v1/producto-unspsc-clases/[i:codigo]",
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto-unspsc-clases/[i:codigo]"
        ],
        [
            "endpoint" => "/v1/producto-unspsc-productos",
            "method" => "GET",
            "querystring_params" => ['codigo', 'descripcion'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/producto-unspsc-productos"
        ],
        [
            "endpoint" => "/v1/producto-unidad-medida-buscar", //lista todos los producto
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/producto-unidad-medida-buscar"
        ],
        ///
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
            "url_pattern" => "/empleado-asistencia"
        ],
        [
            "endpoint" => "/v1/empleado-asistencia", //lista todos los empleado
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"], //["Authorization"],
            "url_pattern" => "/empleado-asistencia"
        ],
        [
            "endpoint" => "/v1/empleado-asistencia/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/empleado-asistencia/[i:id]"
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
        //documento
        [
            "endpoint" => "/v1/documento-tipo-buscar", //lista todos los
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-tipo-buscar"
        ],
        [
            "endpoint" => "/v1/tipo-afectacion-buscar", //lista todos los
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/tipo-afectacion-buscar"
        ],
        [
            "endpoint" => "/v1/documento-factura", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-factura"
        ],
        [
            "endpoint" => "/v1/documento-boleta", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-boleta"
        ],
        [
            "endpoint" => "/v1/documento-nota-credito", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-nota-credito"
        ],
        [
            "endpoint" => "/v1/documento-nota-debito", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-nota-debito"
        ],
        [
            "endpoint" => "/v1/documento-gia-remision", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-gia-remision"
        ],
        [
            "endpoint" => "/v1/documento-baja-suntat", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-baja-suntat"
        ],
        [
            "endpoint" => "/v1/documento-resumen-boletas", //actualiza por id
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/documento-resumen-boletas"
        ],

        //////////////endpoint factura -
        [
            "endpoint" => "/v1/factura", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/factura"
        ],
        [
            "endpoint" => "/v1/factura", //lista todos los factura
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/factura"
        ],
        [
            "endpoint" => "/v1/factura/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/factura/[i:id]"
        ],
        [
            "endpoint" => "/v1/factura", //actualiza por id /[i:id]
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/factura"
        ],
        [
            "endpoint" => "/v1/factura/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/factura/[i:id]"
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
        //////////////endpoint  -
        [
            "endpoint" => "/v1/sucursal", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal"
        ],
        [
            "endpoint" => "/v1/sucursal-buscar", //lista todos los 
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal-buscar"
        ],
        [
            "endpoint" => "/v1/sucursal", //lista todos los 
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal"
        ],
        [
            "endpoint" => "/v1/sucursal-empresa", //lista todos los 
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order', 'cod', 'doc'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal-empresa"
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
            "querystring_params" => ['code', 'idempresa'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/sucursal-codigo"
        ],
        //////////////endpoint categoria -
        [
            "endpoint" => "/v1/categoria", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria"
        ],
        [
            "endpoint" => "/v1/categoria-buscar", //lista todos los categoria
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria-buscar"
        ],
        [
            "endpoint" => "/v1/categoria", //lista todos los categoria
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria"
        ],
        [
            "endpoint" => "/v1/categoria/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria/[i:id]"
        ],
        [
            "endpoint" => "/v1/categoria/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria/[i:id]"
        ],
        [
            "endpoint" => "/v1/categoria/[i:id]/habilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/categoria/[i:id]/deshabilitar", //actualiza por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/categoria/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria/[i:id]"
        ],
        [
            "endpoint" => "/v1/categoria-codigo", //elimina por id
            "method" => "GET",
            "querystring_params" => ['code'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/categoria-codigo"
        ],
        //////////////endpoint rol -
        [
            "endpoint" => "/v1/rol", //crea
            "method" => "POST",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol"
        ],
        [
            "endpoint" => "/v1/rol-buscar", //lista todos los rol
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol-buscar"
        ],
        [
            "endpoint" => "/v1/rol", //lista todos los rol
            "method" => "GET",
            "querystring_params" => ['start', 'length', 'search', 'order'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol"
        ],
        [
            "endpoint" => "/v1/rol/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol/[i:id]"
        ],
        [
            "endpoint" => "/v1/rol-detalle/[i:id]", //busca por ID
            "method" => "GET",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol-detalle/[i:id]"
        ],
        [
            "endpoint" => "/v1/rol/[i:id]", //actualiza por id
            "method" => "PUT",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol/[i:id]"
        ],
        [
            "endpoint" => "/v1/rol/[i:id]/habilitar", //habilitar por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol/[i:id]/habilitar"
        ],
        [
            "endpoint" => "/v1/rol/[i:id]/deshabilitar", //deshabilitar por id
            "method" => "PATCH",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol/[i:id]/deshabilitar"
        ],
        [
            "endpoint" => "/v1/rol/[i:id]", //elimina por id
            "method" => "DELETE",
            "querystring_params" => [],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol/[i:id]"
        ],
        [
            "endpoint" => "/v1/rol-codigo", //elimina por id
            "method" => "GET",
            "querystring_params" => ['code'],
            "headers_to_pass" =>  ["Authorization"],
            "url_pattern" => "/rol-codigo"
        ],
    ];
}
