<?php

namespace Mnt\facturacion\Factura\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class FacturaPersistence
{
    public static function Buscar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['id', 'nombre']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $table = 'factura';
            $columns = 'id,nombre,..';

            $stmt = $con->prepare("CALL SP_SELECT_ALL(:start,:length,\"$search\",'$table','$columns','$order')");
            $stmt->bindParam("start", $start, PDO::PARAM_INT);
            $stmt->bindParam("length", $length, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Listar($start, $length, $search, $order, $user)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($start, $length, $search, $order, $user) {
            $query = "SELECT * FROM doc_factura where id_sucursal=:id_sucursal";
            $stmt = $con->prepare($query);
            $stmt->bindParam("id_sucursal", $user->id_sucursal, PDO::PARAM_INT);
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

                $sql = "CALL SP_FACTURA_I(:json)";
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

            $query = "SELECT * FROM doc_factura WHERE id=$id";
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

            $query = "UPDATE factura SET ...  WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Eliminar($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM factura WHERE id=$id";
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

            $query = "UPDATE factura SET habilitado =$status WHERE id=$id";
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

            $query = "SELECT * FROM factura WHERE codigo=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
