<?php

namespace Mnt\mantenedores\Cliente\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class ClientePersistence
{
    public static function Buscar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['id', 'nombre']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $table = 'cliente';
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
            $query = "SELECT * FROM cliente";
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

            $query = "INSERT INTO cliente
            (id_tipodocumento,numero_documento,
            nombres, apellidos, email,id_ubigeo,
            razon_social, direccion) 
            VALUES (:tipo_documento,:numero_documento,
            :nombres, :apellidos, :email,:id_ubigeo, 
            :razon_social, :direccion) ";
            $stmt = $con->prepare($query);
            $stmt = $con->prepare($query);
            $stmt->bindParam(":tipo_documento", $body["tipo_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_documento", $body["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":nombres", $body["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $body["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $body["email"], PDO::PARAM_STR);
            $stmt->bindParam(":id_ubigeo", $body["id_ubigeo"], PDO::PARAM_STR);
            $stmt->bindParam(":razon_social", $body["razon_social"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $body["direccion"], PDO::PARAM_STR);
            $stmt->execute();
            return $con->lastInsertId();
        });
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT * FROM cliente WHERE id=$id";
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

            $query =
                "UPDATE cliente 
            SET id_tipodocumento = :tipo_documento,numero_documento = :numero_documento,
            nombres=:nombres, apellidos =:apellidos, email=:email,id_ubigeo=:id_ubigeo, 
            razon_social = :razon_social, direccion = :direccion
            WHERE id=:id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(":tipo_documento", $body["tipo_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_documento", $body["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":nombres", $body["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $body["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $body["email"], PDO::PARAM_STR);
            $stmt->bindParam(":id_ubigeo", $body["id_ubigeo"], PDO::PARAM_STR);
            $stmt->bindParam(":razon_social", $body["razon_social"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $body["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Eliminar($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM cliente WHERE id=$id";
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

            $query = "UPDATE cliente SET habilitado =$status WHERE id=$id";
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

            $query = "SELECT * FROM cliente WHERE codigo=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function BuscarPorDoc($doc)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($doc) {

            $query = "SELECT * FROM cliente WHERE numero_documento=$doc";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }
}
