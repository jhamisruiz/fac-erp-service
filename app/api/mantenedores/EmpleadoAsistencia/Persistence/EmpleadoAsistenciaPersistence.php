<?php

namespace Mnt\mantenedores\EmpleadoAsistencia\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class EmpleadoAsistenciaPersistence
{
    public static function Buscar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['id', 'nombre']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $table = 'empleadoasistencia';
            $columns = 'id,nombre,..';

            $stmt = $con->prepare("CALL SP_SELECT_ALL(:start,:length,\"$search\",'$table','$columns','$order')");
            $stmt->bindParam("start", $start, PDO::PARAM_INT);
            $stmt->bindParam("length", $length, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Listar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $query = "SELECT 
                        e.id,
                        e.nombre_completo,
                        e.habilitado,
                        e.es_planilla,
                        e.idgrupo_empleado,
                        IFNULL(
                            (
                                SELECT 
                                GROUP_CONCAT(
                                    JSON_OBJECT(
                                        'id', a.id,
                                        'id_empleado', a.id_empleado,
                                        'valor', CASE WHEN a.valor = 1 THEN true ELSE false END,
                                        'fecha_update', a.fecha_update,
                                        'nombre', a.nombre,
                                        'dia', a.dia,
                                        'fecha', a.fecha
                                    )
                                )
                            FROM asistencia a
                            WHERE a.id_empleado = e.id
                            AND a.fecha like '%$search%'
                            
                            ), NULL
                        ) AS asistencia
                    FROM empleado e
                    where  e.es_planilla=1;
                ";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Crear($body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($body) {

            $query = "INSERT INTO asistencia
            ( id_empleado, valor,  nombre, dia, fecha)
             VALUES ( :id_empleado, :valor,  :nombre, :dia, :fecha)";
            $stmt = $con->prepare($query);
            $stmt->bindParam(":id_empleado", $body["id_empleado"], PDO::PARAM_INT);
            $stmt->bindParam(":valor", $body["valor"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre", $body["nombre"], PDO::PARAM_STR);
            $stmt->bindParam(":dia", $body["dia"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha", $body["fecha"], PDO::PARAM_STR);
            $stmt->execute();
            return $con->lastInsertId();
        });
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT * FROM asistencia WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Actualizar($id, $body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $body) {

            $query = "UPDATE asistencia SET 
            valor = :valor,
            fecha_update = :fecha_update
             WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(":valor", $body["valor"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_update", $body["fecha_update"], PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Eliminar($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM asistencia WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function HabilitarDeshabilitar($id, $status)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $status) {

            $query = "UPDATE empleadoasistencia SET habilitado =$status WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Codigo($codigo)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($codigo) {

            $query = "SELECT * FROM empleadoasistencia WHERE codigo=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
