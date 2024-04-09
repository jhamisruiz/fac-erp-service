<?php

namespace Mnt\mantenedores\Producto\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class ProductoPersistence
{
    public static function Buscar($start, $length, $search, $order, $user)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['id', 'codigo', 'nombre', 'descripcion']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order, $user) {
            $table = 'producto';
            $columns = 'id,codigo,nombre,descripcion,idunidad_medida,habilitado,cantidad,precio,
                        codigo_unspsc,id_tipoafectacion,afecto_icbper,factor_icbper,id_sucursal';
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
            $query = "SELECT p.*,c.nombre as nombre_categoria, u.codigo as codigo_umedida , u.codigo as descripcion_umedida 
                        FROM producto p
                        INNER JOIN categoria c ON p.id_categoria=c.id
                        INNER JOIN unidad_medida u ON p.idunidad_medida=u.id
                        where id_sucursal=:id_sucursal";
            $stmt = $con->prepare($query);
            $stmt->bindParam("id_sucursal", $user->id_sucursal, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Crear($body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($body) {

            $query = "INSERT INTO producto VALUES ...";
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

            $query = "SELECT * FROM producto WHERE id=$id";
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

            $query = "UPDATE producto SET ...  WHERE id=$id";
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

            $query = "DELETE FROM producto WHERE id=$id";
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

            $query = "UPDATE producto SET habilitado =$status WHERE id=$id";
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

            $query = "SELECT * FROM producto WHERE codigo=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function BuscarUMedida($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['id', 'codigo', 'descripcion']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $table = 'unidad_medida';
            $columns = 'id,codigo,descripcion,habilitado';

            $stmt = $con->prepare("CALL SP_SELECT_ALL(:start,:length,\"$search\",'$table','$columns','$order')");
            $stmt->bindParam("start", $start, PDO::PARAM_INT);
            $stmt->bindParam("length", $length, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }
}
