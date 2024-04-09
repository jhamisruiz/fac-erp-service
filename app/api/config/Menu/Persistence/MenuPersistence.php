<?php

namespace App\config\Menu\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class MenuPersistence
{
    public static function Buscar($iduser, $start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();

        return $sql->Exec(
            function ($con) use ($iduser, $start, $length, $search, $order) {

                $user_id = $iduser;
                $stmt = $con->prepare("CALL SP_MENU_L(:user_id);");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            true
        );
    }

    public static function Listar($iduser, $start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($iduser, $start, $length, $search, $order) {
            $user_id = $iduser;

            $query = "SELECT m.*,
                        IFNULL(
                            (
                                SELECT 
                                CONCAT('[',GROUP_CONCAT(
                                    JSON_OBJECT(
                                        'id', d.id,
                                        'nombre', d.nombre,
                                        'url', d.url,
                                        'icon', d.icon,
                                        'id_menu', d.id_menu,
                                        'orden', d.orden,
                                        'style', d.style,
                                        'id_rol', null,
                                        'idrol_permiso', null,
                                        'user_create', false,
                                        'user_read', false,
                                        'user_update', false,
                                        'user_delete', false
                                    )order by d.orden asc SEPARATOR ','
                                ), ']')
                            FROM menu_submenu d
                            WHERE d.id_menu=m.id 
                            ), NULL
                        ) AS componentes
                    FROM menu m order by m.orden asc;";
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

            $query = "INSERT INTO menu VALUES ...";
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

            $query = "SELECT m.*,
                        IFNULL(
                            (
                                SELECT 
                                CONCAT('[',GROUP_CONCAT(
                                    JSON_OBJECT(
                                        'id', d.id,
                                        'nombre', d.nombre,
                                        'url', d.url,
                                        'icon', d.icon,
                                        'id_menu', d.id_menu,
                                        'orden', d.orden,
                                        'habilitado', 
                                        CASE 
                                            WHEN d.habilitado = 1 THEN TRUE 
                                            ELSE FALSE 
                                        END,
                                        'tabla', d.tabla,
                                        'style', d.style
                                    ) SEPARATOR ','
                                ), ']')
                            FROM menu_submenu d
                            WHERE d.id_menu=m.id order by d.orden asc
                            ), NULL
                        ) AS componentes
                    FROM menu m
                    where m.id=$id
                    order by m.orden asc;";
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

            $sql = "CALL SP_MENU_U(:json,:id)";
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

            $query = "DELETE FROM menu WHERE id=$id";
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

            $query = "UPDATE menu SET habilitado =$status WHERE id=$id";
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

            $query = "SELECT * FROM menu WHERE codigo=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
