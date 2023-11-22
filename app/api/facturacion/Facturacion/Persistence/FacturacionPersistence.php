<?php

namespace Mnt\facturacion\Facturacion\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class FacturacionPersistence
{
    public static function Listar($body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($body) {
            $query = "SELECT * FROM facturacion";
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

            $query = "INSERT INTO facturacion VALUES ...";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $con->lastInsertId();
        });
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT * FROM facturacion WHERE id=$id";
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

            $query = "UPDATE facturacion SET ...  WHERE id=$id";
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

            $query = "DELETE FROM facturacion WHERE id=$id";
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

            $query = "UPDATE facturacion SET habilitado =$status WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Buscar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['codigo', 'nombre', 'descripcion', 'serie']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(
            function ($con) use ($start, $length, $search, $order) {
                $table = 'documento_electronico';
                $columns = 'id,codigo,nombre,descripcion,habilitado,serie';

                $stmt = $con->prepare("CALL SP_SELECT_ALL(:start,:length,\"$search\",'$table','$columns','$order')");
                $stmt->bindParam("start", $start, PDO::PARAM_INT);
                $stmt->bindParam("length", $length, PDO::PARAM_INT);

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            true
        );
    }
}
