<?php

namespace Mnt\mantenedores\EmpleadoAsistencia\Http\Routes;

use Mnt\mantenedores\EmpleadoAsistencia\Http\Controller\EmpleadoAsistenciaController;

class EmpleadoAsistenciaRoutes
{
    public static function Routes($router)
    {
        $ctr = new EmpleadoAsistenciaController();

        // Rutas
        $router->get('/empleado-asistencia-buscar', $ctr->Buscar());
        $router->get('/empleado-asistencia', $ctr->Listar());
        $router->post('/empleado-asistencia', $ctr->Crear());
        $router->get('/empleado-asistencia/[i:id]', $ctr->BuscarPorId());
        $router->put('/empleado-asistencia/[i:id]', $ctr->Actualizar());
        $router->delete('/empleado-asistencia/[i:id]', $ctr->Eliminar());
        $router->patch('/empleado-asistencia/[i:id]/habilitar', $ctr->Habilitar());
        $router->patch('/empleado-asistencia/[i:id]/deshabilitar', $ctr->Deshabilitar());
        $router->get('/empleado-asistencia-codigo', $ctr->Codigo());
    }
}
