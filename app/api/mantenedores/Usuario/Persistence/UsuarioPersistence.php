<?php

namespace Mnt\mantenedores\Usuario\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class UsuarioPersistence
{
    public static function Listar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $query = "SELECT * FROM usuario";
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

            $query = "INSERT INTO usuario
            (nombres,apellidos,email,username,password,telefono,photo,fecha_creacion,habilitado)
            VALUES(:nombres,:apellidos,:email,:username,:password,:telefono,:photo,:fecha_creacion,:habilitado)";
            $stmt = $con->prepare($query);

            $stmt->bindParam(":nombres", $body["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $body["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $body["email"], PDO::PARAM_STR);
            $stmt->bindParam(":username", $body["username"], PDO::PARAM_STR);
            $stmt->bindParam(":password", $body["password"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $body["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":photo", $body["photo"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion", $body["fecha_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":habilitado", $body["habilitado"], PDO::PARAM_INT);

            $stmt->execute();
            return $con->lastInsertId();
        });
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT * FROM usuario WHERE id=$id";
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

            $query = "UPDATE usuario SET
                nombres = :nombres,
                apellidos = :apellidos,
                email = :email,
                username = :username,
                password = :password,
                telefono = :telefono,
                photo =:photo,
                habilitado =:habilitado
                WHERE id=:id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(":nombres", $body["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $body["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $body["email"], PDO::PARAM_STR);
            $stmt->bindParam(":username", $body["username"], PDO::PARAM_STR);
            $stmt->bindParam(":password", $body["password"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $body["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":photo", $body["photo"], PDO::PARAM_STR);
            $stmt->bindParam(":habilitado", $body["habilitado"], PDO::PARAM_STR);
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

            $query = "DELETE FROM usuario WHERE id=$id";
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

            $query = "UPDATE usuario SET habilitado =$status WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
