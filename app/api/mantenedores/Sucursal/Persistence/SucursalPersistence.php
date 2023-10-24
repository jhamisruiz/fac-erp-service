<?php

namespace Mnt\mantenedores\Sucursal\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class SucursalPersistence
{
    public static function Buscar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['nombre', 'codigo', 'email']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $table = 'sucursal';
            $columns = 'id,nombre,codigo,ubigeo,direccion,telefono,email,fecha_creacion,habilitado';

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
            $query = "SELECT * FROM sucursal";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Crear($data)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(
            function ($con) use ($data) {

                $json = json_encode($data);

                $sql = "CALL SP_SUCURSAL_I(:json)";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':json', $json, PDO::PARAM_STR);

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            false
        );
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT
                    s.id,
                    s.nombre,
                    s.id_empresa,
                    s.codigo,
                    s.ubigeo,
                    s.direccion,
                    s.telefono,
                    s.email,
                    s.fecha_creacion,
                    s.habilitado,
                    IFNULL(
                        (
                            SELECT 
                            CONCAT('[',GROUP_CONCAT(
                                JSON_OBJECT(
                                    'id', d.id,
                                    'id_sucursal', d.id_sucursal,
                                    'iddocumento_electronico', d.iddocumento_electronico,
                                    'serie', d.serie,
                                    'correlativo', d.correlativo,
                                    'fecha_creacion', d.fecha_creacion,
                                    'habilitado', true
                                ) SEPARATOR ','
                            ), ']')
                        FROM sucursal_documento d
                        WHERE d.id_sucursal=s.id
                        ), NULL
                    ) AS detalle
            FROM sucursal s
            WHERE s.id =$id";
            $stmt = $con->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Actualizar($id, $data)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $data) {
            $json = json_encode($data);
            $sql = "CALL SP_SUCURSAL_U(:json,:id)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':json', $json, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return 1;
            }
            return $stmt->execute();
        });
    }

    public static function Eliminar($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM sucursal WHERE id=$id";
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

            $query = "UPDATE sucursal SET habilitado =$status WHERE id=$id";
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

            $query = "SELECT * FROM sucursal WHERE codigo=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
