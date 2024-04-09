<?php

namespace App\Auth\Persistence;

use PDO;
use App\Utils\Utils;
use App\Utils\Service\NewSql;

class AuthPersistence
{
    public static function Select($body)
    {
        // Ejemplo de uso
        $user = $body['username'];
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($user) {
            $query = "SELECT *,(select id from token T WHERE T.id_usuario = U.id) as id_token 
                        FROM usuario U 
                        WHERE U.username = :username OR U.email = :email ";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':username', $user);
            $stmt->bindParam(':email', $user);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        });
    }

    public static function selectId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {
            $query = "SELECT U.*, T.token,T.time_exp as expire,T.sid
                        FROM usuario U 
                        INNER JOIN token T ON T.id_usuario=U.id 
                        WHERE U.habilitado=1 && U.id = :id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        });
    }

    public static function Update($jwt, $id)
    {
        $time = Utils::Now();
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($jwt, $id, $time) {
            $query = "UPDATE token SET token = :token, time_exp = :time_exp, `sid` = :sid, last_sesion=:last_sesion WHERE id_usuario = :id_usuario";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':token', $jwt['token']);
            $stmt->bindParam(':time_exp', $jwt['data']["exp"]);
            $stmt->bindParam(':sid', $jwt['sid']);
            $stmt->bindParam(':id_usuario', $id);
            $stmt->bindParam(':last_sesion', $time);

            return $stmt->execute();
        });
    }
    public static function Insert($jwt, $id)
    {
        // Ejemplo de uso
        $time = Utils::Now();
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($jwt, $id, $time) {
            $query = "INSERT INTO token (token, time_exp, id_usuario, `sid`,last_sesion) VALUES (:token, :time_exp, :id_usuario, :sid,:last_sesion)";
            $stmt = $con->prepare($query);
            $stmt->execute(
                [
                    ':token' => $jwt['token'],
                    ':time_exp' => $jwt['data']["exp"],
                    ':sid' => $jwt['sid'],
                    ':id_usuario' => $id,
                    ':last_sesion' => $time
                ]
            );
            return  $stmt->rowCount();
        });
    }

    public static function Logout($data)
    {
        $time = Utils::Now();
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($data, $time) {
            $tx = '1019260800';
            $query = "UPDATE token SET time_exp = :time_exp, last_logout=:last_logout WHERE id_usuario = :id_usuario";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':time_exp', $tx);
            $stmt->bindParam(':last_logout', $time);
            $stmt->bindParam(':id_usuario', $data['user_id']);

            return $stmt->execute();
        });
    }
}
